<?php

namespace Webkul\Exchange\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Exchange\Repositories\ExchangeNoteRepository;
use Webkul\Exchange\Repositories\ProductExchangeNoteRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderComment;
use Webkul\Product\Models\Product;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\OrderRepository;


use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * ExchangeNoteRepository object
     *
     * @var \Webkul\Exchange\Repositories\ExchangeNoteRepository
     */
    protected $exchangeNoteRepository;

    /**
     * ProductExchangeNoteRepository object
     *
     * @var \Webkul\Exchange\Repositories\ProductExchangeNoteRepository
     */
    protected $productExchangeNoteRepository;

    /**
     * ProductExchangeNoteRepository object
     *
     * @var \Webkul\Product\Repositories\ProductInventoryRepository;
     */
    protected $productInventoryRepository;

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository;
     */
    protected $orderRepository;

    /**
     * CartRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartRepository;
     */
    protected $cartRepository;

    /**
     * CartItemRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartItemRepository;
     */
    protected $cartItemRepository;

    /**
     * OrderItemRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderItemRepository;
     */
    protected $orderItemRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExchangeNoteRepository $exchangeNoteRepository, ProductExchangeNoteRepository $productExchangeNoteRepository, ProductInventoryRepository $productInventoryRepository, OrderRepository $orderRepository, CartRepository $cartRepository, CartItemRepository $cartItemRepository, OrderItemRepository $orderItemRepository )
    {
        $this->exchangeNoteRepository = $exchangeNoteRepository;

        $this->productExchangeNoteRepository = $productExchangeNoteRepository;

        $this->productInventoryRepository = $productInventoryRepository;

        $this->orderRepository = $orderRepository;

        $this->cartRepository = $cartRepository;

        $this->cartItemRepository = $cartItemRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->middleware('admin');

        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function list_purchases()
    {
        $receipt_notes = DB::table('exchange_notes')
        ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->join('inventory_sources', 'inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status', 'exchange_notes.receipt_date', 'suppliers.name as supplier', 'inventory_sources.name as inventory', 'admins.name as created_user')
        ->where('type', '=', 'receipt')
        ->orderBy('id', 'desc')
        ->get()->toArray();
        return view($this->_config['view'], compact('receipt_notes'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function list_transfers()
    {
        $receipt_notes = DB::table('exchange_notes')
        // ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->leftJoin('inventory_sources as to_inventory_sources', 'to_inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->leftJoin('inventory_sources as from_inventory_sources', 'from_inventory_sources.id', '=', 'exchange_notes.from_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status', 'exchange_notes.receipt_date', 'exchange_notes.transfer_date', 'to_inventory_sources.name as to_inventory', 'from_inventory_sources.name as from_inventory', 'admins.name as created_user')
        ->where('type', '=', 'transfer')
        ->orderBy('id', 'desc')
        ->get()->toArray();
        return view($this->_config['view'], compact('receipt_notes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create_purchase()
    {
        // Get user list
        $users = DB::table('admins')->select('id', 'name')->get();

        // Get inventory sources
        $inventory_sources = DB::table('inventory_sources')->select('id', 'name')->get();

        // Get inventory sources
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'inventory_sources', 'suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create_transfer()
    {
        // Get user list
        $users = DB::table('admins')->select('id', 'name')->get();

        // Get inventory sources
        $inventory_sources = DB::table('inventory_sources')->select('id', 'name')->get();

        // Get inventory sources
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'inventory_sources', 'suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // $this->validate(request(), [
        //     'status' => 'required',
        //     'user' => 'required',
        //     'supplier' => 'required',
        //     'inventory_source' => 'required',
        //     'receipt_date' => 'required',
        // ]);

        $exchangeNoteData = [
            'status' => "temporary",
            'type' => request()->type,
            'created_user_id' => isset(request()->user) ? request()->user : 0,
            'receipt_user_id' => isset(request()->user) ? request()->user : 0,
            'supplier_id' => isset(request()->supplier) ? request()->supplier : 0,
            // 'from_inventory_source_id' => 1,
            'to_inventory_source_id' => isset(request()->to_inventory_source) ? request()->to_inventory_source : 0,
            'from_inventory_source_id' => isset(request()->from_inventory_source) ? request()->from_inventory_source : 0,
            'created_date' => request()->created_date,
            'updated_date' => request()->updated_date,
            'transfer_date' => request()->transfer_date,
            'receipt_date' => request()->receipt_date,
            'note' => request()->notes,
        ];

        $exchangeNote = $this->exchangeNoteRepository->create($exchangeNoteData);

        if (isset($exchangeNote) && $exchangeNote->id != null) {
            foreach (request()->added_products as $product ) {
                if ($exchangeNoteData['type'] == "transfer") {
                    $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', request()->from_inventory_source)->where('product_id', '=', $product['id'])
                    ->first();
                    $productInventory->qty = (int)$productInventory->qty - (int)$product['qty'];
                    $productInventory->save();
                } else {
                    $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', request()->to_inventory_source)->where('product_id', '=', $product['id'])
                    ->first();
                    $productInventory->qty = (int)$productInventory->qty + (int)$product['qty'];
                    $productInventory->save();
                }

                $productExchangeData = [
                    'exchange_note_id' => $exchangeNote->id,
                    'product_id' => $product['id'],
                    'transfer_qty' => $product['qty'],
                    'receipt_qty' => $product['qty'],
                    'price' => $product['price'],
                    'discount' => 0,
                ];
                $productExchange = $this->productExchangeNoteRepository->create($productExchangeData);
            }
        }

        session()->flash('success', trans('admin::app.settings.inventory_sources.create-success'));

        return response()->json(
            [
                'success' => true,
                'message' => 'Save susscessfully',
            ]
        );
    }

    public function get_transfered_products() {
        $transfer_id = request()->input('transfer_id');
        $transfered_products = [];

        if (isset($transfer_id) && $transfer_id != null) {
            $transfered_products = DB::table('product_exchange_notes')
            ->leftJoin('products', 'products.id', '=', 'product_exchange_notes.product_id')
            ->leftJoin('product_flat', 'product_flat.product_id', '=', 'product_exchange_notes.product_id')
            ->where('exchange_note_id', $transfer_id)
            ->where('product_flat.locale', '=', core()->getCurrentLocale()->code)
            ->select('product_exchange_notes.id', 'product_exchange_notes.exchange_note_id', 'product_exchange_notes.product_id', 'product_exchange_notes.transfer_qty', 'product_exchange_notes.receipt_qty', 'product_exchange_notes.price', 'product_exchange_notes.discount', 'product_flat.name'
                , DB::raw('(select path as featured_image from product_images where product_images.product_id  = product_flat.product_id limit 1) as featured_image'))            
            ->distinct()->get();
        }

        return response()->json(
            [
                'success' => True,
                'transfered_products' => $transfered_products
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        return view($this->_config['view']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

    }

    //minhpd
    public function list_orders()
    {
        $receipt_notes = DB::table('exchange_notes')
        ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->join('inventory_sources', 'inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status', 'exchange_notes.receipt_date', 'suppliers.name as supplier', 'inventory_sources.name as inventory', 'admins.name as created_user')
        ->where('type', '=', 'receipt')
        ->orderBy('id', 'desc')
        ->get()->toArray();
        return view($this->_config['view'], compact('receipt_notes'));
    }

    public function create_orders()
    {
        // Get user list
        // $users = DB::table('admins')->select('id', 'name')->get();
        $users = DB::table('customers')->select('id', 'first_name','last_name', 'email', 'phone')->get();

        // Get inventory sources
        $inventory_sources = DB::table('inventory_sources')->select('id', 'name')->get();

        // Get inventory sources
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'inventory_sources', 'suppliers'));
    }

    public function store_orders()
    {
        //lay du lieu khach hang da chon
        $customer_id = isset(request()->user) ? request()->user : 24;
        $customer = DB::table('customers')->find($customer_id);
        $customer_email= $customer->email;
        $customer_first_name= $customer->first_name;
        $customer_last_name= $customer->last_name;

        $list_product = request()->added_products;
        $total_item_count = count($list_product);
        $total_item_qty = 0;
        foreach ($list_product as $product )
        {
            $total_item_qty += $product['qty'];
        }
        //lấy giá trị giảm giá discount
        $discount_amount = 0;
        $discount_percent = 0;
        if(request()->discount != 0) {
            if(request()->discount_type == 1) {
                $discount_amount = request()->discount;
            } else {
                $discount_percent = request()->discount;
                $discount_amount = ($discount_percent * (request()->price_total))/100;
            }
        }

        //lưu dữ liệu vào giỏ hàng
        $cartData = [
            'customer_email' => $customer_email,
            'customer_first_name' => $customer_first_name,
            'customer_last_name' => $customer_last_name,
            'shipping_method' => 'free_free',
            'is_gift' => 0,
            'items_count' => $total_item_count,
            'items_qty' => $total_item_qty,
            'global_currency_code' => 'VND',
            'base_currency_code' => 'VND',
            'channel_currency_code' => 'VND',
            'cart_currency_code' => 'VND',
            'grand_total' => request()->price_must_paid,
            'base_grand_total' => request()->price_must_paid,
            'sub_total' => request()->price_total,
            'base_sub_total' => request()->price_total,
            'tax_total' => 0,
            'base_tax_total' => 0,
            'discount_amount' => $discount_amount,
            'base_discount_amount' => 0,
            'is_guest' => 1,
            'is_active' => 0,
            'customer_id' => $customer_id,
            'channel_id' => 1,
            'created_at' => request()->created_date,
            'updated_at' => request()->created_date,
        ];

        $cart = $this->cartRepository->create($cartData);

        //luu du lieu item trong gio hang
        foreach (request()->added_products as $product ) {
            // $productInventory = $this->productInventoryRepository->where('product_id', '=', $product['id'])->first();    
            $productsObject = DB::table('products')->find($product['id']);   
                $cartItemData = [
                    'quantity' => $product['qty'],
                    'sku' => $productsObject->sku,
                    'type' => 'simple',
                    'name' => $product['name'],
                    'weight' => $product['price'],
                    'total_weight' => $product['price'],
                    'base_total_weight' => $product['price'],
                    'price' => $product['price'],
                    'base_price' => $product['price'],
                    'total' => $product['price'] * $product['qty'],
                    'base_total' => $product['price'] * $product['qty'],
                    'base_tax_amount' => 0,
                    'discount_percent' => $discount_percent,
                    'discount_amount' => $discount_amount,
                    'base_discount_amount' => 0,
                    'product_id' => $product['id'],
                    'cart_id' => $cart->id,
                    'created_at' => request()->created_date,
                    'updated_at' => request()->created_date,
                ];
                $cartItem = $this->cartItemRepository->create($cartItemData);
        }
        
        //luu du lieu cart payment
        $cartPayment = new CartPayment();
        $cartPayment->method = "cashondelivery";
        $cartPayment->cart_id = $cart->id;
        $cartPayment->created_at = request()->created_date;
        $cartPayment->updated_at = request()->updated_date;
        $cartPayment->save();

        //lưu dữ liệu vào orders
        $order = new Order();
        $order->increment_id = $this->orderRepository->generateIncrementId();
        $order->status = 'pending';
        $order->channel_name = 'Offline';
        $order->is_guest = 1;
        $order->customer_email = $customer_email;
        $order->customer_first_name = $customer_first_name;
        $order->customer_last_name = $customer_last_name;
        $order->shipping_method = 'free_free';
        $order->shipping_title = 'Free Shipping - Free Shipping';
        $order->shipping_description = 'Free Shipping';
        $order->is_gift = 0;
        $order->total_item_count = $total_item_count;
        $order->total_qty_ordered = $total_item_qty;
        $order->base_currency_code = 'VND';
        $order->channel_currency_code = 'VND';
        $order->order_currency_code = 'VND';
        $order->grand_total = request()->price_must_paid;
        $order->base_grand_total = request()->price_must_paid;
        $order->sub_total = request()->price_total;
        $order->sub_total = request()->price_total;
        $order->base_sub_total = request()->price_total;
        $order->channel_id = 1;
        $order->channel_type = 'Webkul\Core\Models\Channel';
        $order->created_at = request()->created_date;
        $order->updated_at = request()->created_date;
        $order->discount_amount = $discount_amount;
        $order->discount_percent = $discount_percent;
        $order->cart_id = $cart->id;
        $order->save();

        //luư dữ liệu vào order items

        foreach (request()->added_products as $product ) {
                $productsObject = DB::table('products')->find($product['id']);       
                $orderItemItemData = [
                    'sku' => $productsObject->sku,
                    'name' => $product['name'],
                    'type' => 'simple',
                    'weight' => $product['price'],
                    'total_weight' => $product['price'],
                    'qty_ordered' => $product['qty'],
                    'price' => $product['price'],
                    'base_price' => $product['price'],
                    'total' => $product['price'] * $product['qty'],
                    'base_total' => $product['price'] * $product['qty'],
                    'discount_percent' => $discount_percent,
                    'discount_amount' => $discount_amount,
                    'base_discount_amount' => 0,
                    'product_id' => $product['id'],
                    'product_type' => 'Webkul\Product\Models\Product',
                    'order_id' => $order->id,
                    'created_at' => request()->created_date,
                    'updated_at' => request()->created_date,
                ];
                $orderItem = $this->orderItemRepository->create($orderItemItemData);
        }

        //lưu dữ liệu vào order payment
        $orderPayment = new OrderPayment();
        $orderPayment->method = "cashondelivery";
        $orderPayment->order_id = $order->id;
        $orderPayment->created_at = request()->created_date;
        $orderPayment->updated_at = request()->created_date;
        $orderPayment->save();

        //lưu dữ liệu vào bảng ordercomment
        if(request()->notes != "")
        {
            $orderComent = new OrderComment();
            $orderComent->comment = request()->notes;
            $orderComent->order_id = $order->id;
            $orderComent->customer_notified = 0;
            $orderComent->created_at = request()->created_date;
            $orderComent->updated_at = request()->created_date;
            $orderComent->save();
        }

        session()->flash('success', trans('admin::app.settings.inventory_sources.create-success'));

        return response()->json(
            [
                'success' => true,
                'message' => 'Save susscessfully',
            ]
        );
    }
}
