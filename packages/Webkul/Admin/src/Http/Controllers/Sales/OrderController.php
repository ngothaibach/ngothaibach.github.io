<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use \Webkul\Sales\Repositories\OrderCommentRepository;

use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderComment;
use Webkul\Product\Models\Product;
use Webkul\Core\Models\Address;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\OrderAddressRepository;
use Webkul\Checkout\Repositories\CartAddressRepository;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected $_config;

    /**
     * OrderRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderRepository
     */
    protected $orderRepository;

    /**
     * OrderCommentRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderCommentRepository
     */
    protected $orderCommentRepository;

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
     * CartAddressRepository object
     *
     * @var \Webkul\Checkout\Repositories\CartAddressRepository;
     */
    protected $cartAddressRepository;

    /**
     * OrderAddressRepository object
     *
     * @var \Webkul\Sales\Repositories\OrderAddressRepository;
     */
    protected $orderAddressRepository;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderCommentRepository  $orderCommentRepository
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        OrderCommentRepository $orderCommentRepository,
        CartRepository $cartRepository, 
        CartItemRepository $cartItemRepository, 
        OrderItemRepository $orderItemRepository, 
        CartAddressRepository $cartAddressRepository, 
        OrderAddressRepository $orderAddressRepository
    )
    {
        $this->middleware('admin');

        $this->_config = request('_config');

        $this->orderRepository = $orderRepository;

        $this->orderCommentRepository = $orderCommentRepository;

        $this->cartRepository = $cartRepository;

        $this->cartItemRepository = $cartItemRepository;

        $this->orderItemRepository = $orderItemRepository;

        $this->cartAddressRepository = $cartAddressRepository;

        $this->orderAddressRepository = $orderAddressRepository;
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
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $result = $this->orderRepository->cancel($id);

        if ($result) {
            session()->flash('success', trans('admin::app.response.cancel-success', ['name' => 'Order']));
        } else {
            session()->flash('error', trans('admin::app.response.cancel-error', ['name' => 'Order']));
        }

        return redirect()->back();
    }

    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment($id)
    {
        $data = array_merge(request()->all(), [
            'order_id' => $id,
        ]);

        $data['customer_notified'] = isset($data['customer_notified']) ? 1 : 0;

        Event::dispatch('sales.order.comment.create.before', $data);

        $comment = $this->orderCommentRepository->create($data);

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

        return redirect()->back();
    }
    //minhpd
    public function create_orders()
    {
        // Get user list
        $users = DB::table('admins')->select('id', 'name')->get();

        $customers = DB::table('customers')->select('id', 'first_name','last_name', 'email', 'phone')->get();

        // Get inventory sources
        $inventory_sources = DB::table('inventory_sources')->select('id', 'name')->get();

        // Get inventory sources
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'inventory_sources', 'suppliers', 'customers'));
    }

    public function store_orders()
    {
        //lay du lieu khach hang da chon
        $customer_id = request()->customer ;
        $customer = DB::table('customers')->find($customer_id);
        $customer_email= $customer->email;
        $customer_first_name= $customer->first_name;
        $customer_last_name= $customer->last_name;

        //lay du lieu address cua customer
        $addresses = DB::table('addresses')->where('customer_id',$customer_id)->where('address_type','=','customer')->first();
        $customer_company = $addresses->company_name;
        $customer_address1 = $addresses->address1;
        $customer_postcode = $addresses->postcode;
        $customer_city = $addresses->city;
        $customer_state = $addresses->state;
        $customer_country = $addresses->country;
        $customer_phone = $addresses->phone;

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
            'base_discount_amount' => $discount_amount,
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
                    'discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                    'base_discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
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
        $cartPayment->updated_at = request()->created_date;
        $cartPayment->save();

        //Lưu dữ liệu cart vào address cart_billing
        $arr_address_cart = array('cart_billing', 'cart_shipping');
        foreach($arr_address_cart as $value) {
            $cartAddressData = [
                'address_type' => $value,
                'cart_id' => $cart->id,
                'first_name' => $customer_first_name,
                'last_name' => $customer_last_name,
                'company_name' => $customer_company,
                'address1' => $customer_address1,
                'postcode' => $customer_postcode,
                'city' => $customer_city,
                'state' => $customer_state,
                'country' => $customer_country,
                'email' => $customer_email,
                'phone' => $customer_phone,
                'default_address' => 0,
                'created_at' => request()->created_date,
                'updated_at' => request()->created_date
            ];
            $cartAddress = $this->cartAddressRepository->create($cartAddressData);
            
        }
        
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
        $order->base_sub_total = request()->price_total;
        // $order->base_grand_total_invoiced = request()->price_must_paid;
        $order->channel_id = 1;
        $order->channel_type = 'Webkul\Core\Models\Channel';
        $order->created_at = request()->created_date;
        $order->updated_at = request()->created_date;
        $order->discount_amount = $discount_amount;
        $order->discount_percent = $discount_percent;
        $order->cart_id = $cart->id;
        $order->collection_diff = request()->collection_diff;
        $order->customer_paid = request()->customer_paid;
        $order->customer_remain = request()->customer_remain;
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
                    'discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
                    'base_discount_amount' => ($discount_percent * $product['price'] * $product['qty'])/100,
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

        //Lưu dữ liệu vào address order_billing, order_shipping
        $arr_address_order = array('order_billing', 'order_shipping');
        foreach($arr_address_order as $value) {
            $orderAddressData = [
                'address_type' => $value,
                'order_id' => $order->id,
                'first_name' => $customer_first_name,
                'last_name' => $customer_last_name,
                'company_name' => $customer_company,
                'address1' => $customer_address1,
                'postcode' => $customer_postcode,
                'city' => $customer_city,
                'state' => $customer_state,
                'country' => $customer_country,
                'email' => $customer_email,
                'phone' => $customer_phone,
                'default_address' => 0,
                'created_at' => request()->created_date,
                'updated_at' => request()->created_date
            ];
            $orderAddress = $this->orderAddressRepository->create($orderAddressData);
        }

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