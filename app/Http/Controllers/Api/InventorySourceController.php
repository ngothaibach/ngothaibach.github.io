<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Webkul\Product\Helpers\ProductType;
use Webkul\Core\Contracts\Validations\Slug;
use Webkul\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\Event;
use Webkul\Inventory\Repositories\InventorySourceRepository;
class InventorySourceController extends Controller
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
    /**
     * ProductExchangeNoteRepository object
     *
     * @var \Webkul\Exchange\Repositories\ProductExchangeNoteRepository
     */
    protected $productExchangeNoteRepository;
      /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    public function __construct(InventorySourceRepository $inventorySourceRepository
    // OrderRepository $orderRepository, 
    // CartRepository $cartRepository, 
    // CartItemRepository $cartItemRepository, 
    // OrderItemRepository $orderItemRepository, 
    // CartAddressRepository $cartAddressRepository, 
    // OrderAddressRepository $orderAddressRepository 
    )
    {
        $this->inventorySourceRepository = $inventorySourceRepository;

        // $this->orderRepository = $orderRepository;

        // $this->cartRepository = $cartRepository;

        // $this->cartItemRepository = $cartItemRepository;

        // $this->orderItemRepository = $orderItemRepository;

        // $this->cartAddressRepository = $cartAddressRepository;

        // $this->orderAddressRepository = $orderAddressRepository;

    }


    public function index()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $codeOld = DB::table('inventory_sources')->where('code',$request->code)->first();
        if(!$codeOld){
        if($request->_token == 'COSiRSB6IYZXEDdufVjx6AsVSLZFzpd6ynISp1rK'){
            $this->validate(request(), [
                'code'           => ['required', 'unique:inventory_sources,code', new \Webkul\Core\Contracts\Validations\Code],
                'name'           => 'required',
                'contact_name'   => 'required',
                'contact_email'  => 'required|email',
                'contact_number' => 'required',
                'street'         => 'required',
                'country'        => 'required',
                'state'          => 'required',
                'city'           => 'required',
                'postcode'       => 'required',
            ]);
            $data = request()->all();
            $data['status'] = !isset($data['status']) ? 0 : 1;

            $inventorySource = $this->inventorySourceRepository->create($data);
            return response()->json(
                ['message' =>$data]
            );
        }else{
            return response()->json(['message' => "HTTP Error 401 - Unauthorized"], 401);
        }
    }else{
        return response()->json(['message' => "HTTP Error 409 - Inventory already exists"], 409);
    }
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
    public function update(Request $request, $id)
    {

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
