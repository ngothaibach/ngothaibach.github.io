<?php

namespace Webkul\Exchange\Http\Controllers\Admin;
use Illuminate\Support\Facades\Log;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Webkul\Exchange\Repositories\ExchangeNoteRepository;
use Webkul\Exchange\Repositories\ProductExchangeNoteRepository;
use Webkul\Product\Repositories\ProductInventoryRepository;
// use Webkul\Checkout\Repositories\CartRepository;
// use Webkul\Checkout\Repositories\CartItemRepository;
// use Webkul\Checkout\Models\CartPayment;
// use Webkul\Sales\Models\OrderPayment;
// use Webkul\Sales\Models\Order;
// use Webkul\Sales\Models\OrderComment;
// use Webkul\Product\Models\Product;
// use Webkul\Core\Models\Address;
// use Webkul\Sales\Repositories\OrderItemRepository;
// use Webkul\Sales\Repositories\OrderRepository;
// use Webkul\Sales\Repositories\OrderAddressRepository;
// use Webkul\Checkout\Repositories\CartAddressRepository;


use Illuminate\Support\Facades\DB;
use Webkul\Exchange\Http\Imports\ImportExcel;
use Excel;
use Webkul\Exchange\Http\Models\ImportProduct;
use Webkul\Exchange\Models\ExchangeNote;
use Webkul\Exchange\Models\ProductExchangeNote;
use Session;

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

    // /**
    //  * OrderRepository object
    //  *
    //  * @var \Webkul\Sales\Repositories\OrderRepository;
    //  */
    // protected $orderRepository;

    // /**
    //  * CartRepository object
    //  *
    //  * @var \Webkul\Checkout\Repositories\CartRepository;
    //  */
    // protected $cartRepository;

    // /**
    //  * CartItemRepository object
    //  *
    //  * @var \Webkul\Checkout\Repositories\CartItemRepository;
    //  */
    // protected $cartItemRepository;

    // /**
    //  * OrderItemRepository object
    //  *
    //  * @var \Webkul\Sales\Repositories\OrderItemRepository;
    //  */
    // protected $orderItemRepository;

    // /**
    //  * CartAddressRepository object
    //  *
    //  * @var \Webkul\Checkout\Repositories\CartAddressRepository;
    //  */
    // protected $cartAddressRepository;

    // /**
    //  * OrderAddressRepository object
    //  *
    //  * @var \Webkul\Sales\Repositories\OrderAddressRepository;
    //  */
    // protected $orderAddressRepository;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExchangeNoteRepository $exchangeNoteRepository, ProductExchangeNoteRepository $productExchangeNoteRepository, ProductInventoryRepository $productInventoryRepository
    // OrderRepository $orderRepository, 
    // CartRepository $cartRepository, 
    // CartItemRepository $cartItemRepository, 
    // OrderItemRepository $orderItemRepository, 
    // CartAddressRepository $cartAddressRepository, 
    // OrderAddressRepository $orderAddressRepository 
    )
    {
        $this->exchangeNoteRepository = $exchangeNoteRepository;

        $this->productExchangeNoteRepository = $productExchangeNoteRepository;

        $this->productInventoryRepository = $productInventoryRepository;

        // $this->orderRepository = $orderRepository;

        // $this->cartRepository = $cartRepository;

        // $this->cartItemRepository = $cartItemRepository;

        // $this->orderItemRepository = $orderItemRepository;

        // $this->cartAddressRepository = $cartAddressRepository;

        // $this->orderAddressRepository = $orderAddressRepository;

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
        error_log('Some message here.');

        // var_dump('xxxxxx');
        return view($this->_config['view']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function list_purchases()
    {
        $users = DB::table('admins')->select('id', 'name','inventory_id')->get();
        $role_id = auth()->guard('admin')->user()->role['id'];
        $query = DB::table('exchange_notes')
        ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->join('inventory_sources', 'inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status','exchange_notes.type','exchange_notes.importer', 'exchange_notes.receipt_date', 'suppliers.name as supplier', 'inventory_sources.name as inventory','inventory_sources.id as inventoryID', 'admins.name as created_user','exchange_notes.total')
        ->where('type', '=', 'receipt');
        if( Session::get('inventory') != 0){
            $query = $query->where('to_inventory_source_id','=',Session::get('inventory'));
        }
        $query = $query->orderBy('id', 'desc');
        $receipt_notes=$query->get()->toArray();
        // $productInventoryQty = $this->productInventoryRepository->where('inventory_source_id', '=', request()->from_inventory_source)->where('product_id', '=', $product['id'])

        // $data = DB::table('exchange_notes')->get()->toJson();

        // echo $data;

        // $receipt_notes = DB::table('exchange_notes')->orderBy('id', 'DESC')->get()->toArray();

        return view($this->_config['view'], compact('receipt_notes','users','role_id'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function list_transfers()
    {
        $role_id = auth()->guard('admin')->user()->role['id'];
        $query = DB::table('exchange_notes')
        // ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->leftJoin('inventory_sources as to_inventory_sources', 'to_inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->leftJoin('inventory_sources as from_inventory_sources', 'from_inventory_sources.id', '=', 'exchange_notes.from_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status', 'exchange_notes.receipt_date', 'exchange_notes.transfer_date', 'to_inventory_sources.name as to_inventory', 'from_inventory_sources.name as from_inventory','from_inventory_sources.id as from_inventory_id', 'admins.name as created_user')
        ->where('type', '=', 'transfer');
        if(Session::get('inventory') != 0){
            $query = $query->where('from_inventory_source_id','=',Session::get('inventory'))
            ->orwhere('to_inventory_source_id','=',Session::get('inventory'));
        }
        $query = $query->orderBy('id', 'desc');
        $receipt_notes=$query->get()->toArray();
        return view($this->_config['view'], compact('receipt_notes','role_id'));
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
        //max id
        $notes = DB::table('exchange_notes')->find(DB::table('exchange_notes')->max('id'));
        $maxId= $notes->id;
        // Get suppliers
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'suppliers','maxId'));
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
        
        //max id
        $notes = DB::table('exchange_notes')->find(DB::table('exchange_notes')->max('id'));
        $maxId= $notes->id;

        // Get suppliers
        $suppliers = DB::table('suppliers')->select('id', 'name')->get();

        return view($this->_config['view'], compact('users', 'inventory_sources', 'suppliers','maxId'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {

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
            'total' => request()->price_total,
        ];

        $exchangeNote = $this->exchangeNoteRepository->create($exchangeNoteData);
        if (isset($exchangeNote) && $exchangeNote->id != null) {
            foreach (request()->added_products as $product ) {
                // if ($exchangeNoteData['type'] == "transfer") {
                //     $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', request()->from_inventory_source)->where('product_id', '=', $product['id'])
                //     ->first();
                //     $productInventory->qty = (int)$productInventory->qty - (int)$product['qty'];
                //     $productInventory->save();
                // } else {
                //     $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', request()->to_inventory_source)->where('product_id', '=', $product['id'])
                //     ->first();
                //     $productInventory->qty = (int)$productInventory->qty + (int)$product['qty'];
                //     $productInventory->save();
                // }

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
                'tai smile' =>$productExchangeData,
            ]
        );
    }



    // public function store()
    // {
    //     session()->flash('success', trans('admin::app.settings.inventory_sources.create-success'));
    //     return response()->json(
    //         [
    //             'success' => true,
    //             'message' => request()->added_products,
    //         ]
    //     );
    // }

    public function get_transfered_products() {
        $transfer_id = request()->input('transfer_id');
        $transfered_products = [];

        if (isset($transfer_id) && $transfer_id != null) {
            $transfered_products = DB::table('product_exchange_notes')
            ->leftJoin('products', 'products.id', '=', 'product_exchange_notes.product_id')
            ->leftJoin('product_flat', 'product_flat.product_id', '=', 'product_exchange_notes.product_id')
            ->where('exchange_note_id', $transfer_id)
            // ->where('product_flat.locale', '=', core()->getCurrentLocale()->code)
            ->where('product_flat.locale', '=', 'vi')
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
  
    public function update()
    {
        // $data = request()->all();
        $id = request()->idExchange;
        $note = request() -> note;
        $importer = request() -> importer;
        $status = request() -> status;
        $product_list = request() -> product_list;
        $type = request() -> type;
        $inventoryID = request() -> inventoryID;
        $total = request() -> total;

        // $name = $item->id;
        $exchaneNote = ExchangeNote::find($id);
        $exchaneNote->status = $status;
        $exchaneNote->note = $note;
        $exchaneNote->importer = $importer;
        $exchaneNote->total = $total;
        $exchaneNote->save();
        foreach ($product_list as $product){
            // cập nhật các trường thay đổi số lượng
            $productExchangeNote = DB::table('product_exchange_notes')
            ->where([
                ['exchange_note_id', $id ],
                ['id',$product['id'] ],
            ])
            ->update(['receipt_qty' => $product['receipt_qty']],);
             // cập nhật số lượng trong kho
            if($status == 'received'){
                $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', $inventoryID )->where('product_id', '=', $product['product_id'])
                ->first();
                $productInventory->qty = (int)$productInventory->qty + (int)$product['transfer_qty'];
                $productInventory->save();
            }
        }
        // cập nhật số lượng trong kho
        // if ($exchangeNoteData['type'] == "transfer") {
        //     $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', request()->from_inventory_source)->where('product_id', '=', $product['id'])
        //     ->first();
        //     $productInventory->qty = (int)$productInventory->qty - (int)$product['qty'];
        //     $productInventory->save();
        // } else {
          
         
        // }
        return response()->json(
            [
                'success' => True,
                'transfered_products' => $product_list
            ]
        );
    }


    public function updateTransfer()
    {
        // $data = request()->all();
        $id = request()->idExchange;
        $note = request() -> note;
        $importer = request() -> importer;
        $status = request() -> status;
        $product_list = request() -> product_list;
        $type = request() -> type;
        $from_inventory_id = request() -> from_inventory_id;
        $receipt_date = request() -> receipt_date;
        $total = request() -> total;
        // $name = $item->id;
        $exchaneNote = ExchangeNote::find($id);
        $exchaneNote->status = $status;
        $exchaneNote->note = $note;
        $exchaneNote->importer = $importer;
        $exchaneNote->receipt_date = $receipt_date;
        $exchaneNote->total = $total;
        $exchaneNote->save();
        foreach ($product_list as $product){
            // cập nhật các trường thay đổi số lượng
            $productExchangeNote = DB::table('product_exchange_notes')
            ->where([
                ['exchange_note_id', $id ],
                ['id',$product['id'] ],
            ])
            ->update(['transfer_qty' => $product['transfer_qty']]);
             // cập nhật số lượng trong kho
            if($status == 'received'){
                $productInventory = $this->productInventoryRepository->where('inventory_source_id', '=', $from_inventory_id)->where('product_id', '=', $product['product_id'])
                ->first();
                $productInventory->qty = (int)$productInventory->qty - (int)$product['transfer_qty'];
                $productInventory->save();
            }
        }
        return response()->json(
            [
                'success' => True,
                'transfered_products' => $product_list
            ]
        );
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
}
