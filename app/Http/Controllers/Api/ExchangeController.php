<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webkul\Exchange\Repositories\ProductExchangeNoteRepository;
use Webkul\Exchange\Repositories\ExchangeNoteRepository;
use Carbon\Carbon;
class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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

    public function __construct(ExchangeNoteRepository $exchangeNoteRepository, ProductExchangeNoteRepository $productExchangeNoteRepository
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

        // $this->orderRepository = $orderRepository;

        // $this->cartRepository = $cartRepository;

        // $this->cartItemRepository = $cartItemRepository;

        // $this->orderItemRepository = $orderItemRepository;

        // $this->cartAddressRepository = $cartAddressRepository;

        // $this->orderAddressRepository = $orderAddressRepository;

    }


    public function index()
    {
        $users = DB::table('admins')->select('id', 'name')->get();
        $receipt_notes = DB::table('exchange_notes')
        ->join('suppliers', 'suppliers.id', '=', 'exchange_notes.supplier_id')
        ->join('inventory_sources', 'inventory_sources.id', '=', 'exchange_notes.to_inventory_source_id')
        ->join('admins', 'admins.id', '=', 'exchange_notes.created_user_id')
        ->select('exchange_notes.id', 'exchange_notes.created_date', 'exchange_notes.note', 'exchange_notes.status','exchange_notes.type','exchange_notes.importer', 'exchange_notes.receipt_date', 'suppliers.name as supplier', 'inventory_sources.name as inventory','inventory_sources.id as inventoryID', 'admins.name as created_user')
        ->where('type', '=', 'receipt')
        ->orderBy('id', 'desc')
        ->get()->toArray();
        return $receipt_notes;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $date = Carbon::now()->toISOString();
        $exchangeNoteData = [
            'status' => "temporary",
            'type' => "receipt",
            'created_user_id' => 1,
            'receipt_user_id' => 1,
            'supplier_id' => $request->supplier_id,
            // 'from_inventory_source_id' => 1,
            'to_inventory_source_id' => $request->to_inventory_source_id,
            'from_inventory_source_id' =>  $request->from_inventory_source_id,
            'created_date' =>$date,
            'updated_date' =>$date,
            'transfer_date' =>$date,
            'receipt_date' =>$date,
            'note' => $request->notes,
        ];

        $exchangeNote = $this->exchangeNoteRepository->create($exchangeNoteData);
                $productExchangeData = [

                    'exchange_note_id' => $exchangeNote->id,
                    'product_id' => $request->product_id,
                    'transfer_qty' => $request->transfer_qty,
                    'receipt_qty' => $request->receipt_qty,
                    'price' => $request->price,
                    'discount' => 0,
                ];
                $productExchange = $this->productExchangeNoteRepository->create($productExchangeData);
          
        return response()->json(
            [
                'success' => true,
                'message' => 'Save susscessfully',
                'tai smile' =>$productExchangeData,
                'minh smile' => Carbon::now()->toISOString()
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Model\Exchange  $exchange
     * @return \Illuminate\Http\Response
     */
    public function show(Exchange $exchange)
    {
        //
        return $exchange;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Model\Exchange  $exchange
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Exchange $exchange)
    {
        //
        return $exchange->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Model\Exchange  $exchange
     * @return \Illuminate\Http\Response
     */
    public function destroy(Exchange $exchange)
    {
        //
        $exchange->delete();
    }
}
