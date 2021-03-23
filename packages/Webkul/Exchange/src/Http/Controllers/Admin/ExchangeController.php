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
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExchangeNoteRepository $exchangeNoteRepository, ProductExchangeNoteRepository $productExchangeNoteRepository, ProductInventoryRepository $productInventoryRepository)
    {
        $this->exchangeNoteRepository = $exchangeNoteRepository;

        $this->productExchangeNoteRepository = $productExchangeNoteRepository;

        $this->productInventoryRepository = $productInventoryRepository;

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
}
