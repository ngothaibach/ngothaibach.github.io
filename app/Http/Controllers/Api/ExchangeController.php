<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Model\Exchange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Webkul\Exchange\Repositories\ProductExchangeNoteRepository;
use Webkul\Exchange\Repositories\ExchangeNoteRepository;
use Carbon\Carbon;
use Webkul\Product\Helpers\ProductType;
use Webkul\Core\Contracts\Validations\Slug;
use Webkul\Product\Repositories\ProductRepository;
use Storage;

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
      /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    public function __construct(ExchangeNoteRepository $exchangeNoteRepository, ProductExchangeNoteRepository $productExchangeNoteRepository,
    ProductRepository $productRepository
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

        $this->productRepository = $productRepository;

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
        $codeOld = DB::table('products')->where('sku',$request->sku)->first();
        if(!$codeOld){
        if($request->_token == 'bIHy1ZvVOkZOVYlf0aGQtwxNwfXwLQjJ0ky7JRWM'){

        $product = $this->productRepository->create( [
            // '_token'              => 'gvJCsaanpxNrVuUJimDudaWapb4AOUXpk8HWK22k',
            'type'                => $request->type,
            'attribute_family_id' => $request->attribute_family_id,
            'sku'                 => $request->sku
        ]);
            
        $id = $product['id'];
        
        $productAttributes = $this->productRepository->findOrFail($id);

        // $data = request()->all();

        $multiselectAttributeCodes = array();


        foreach ($productAttributes->attribute_family->attribute_groups as $attributeGroup) {
            $customAttributes = $productAttributes->getEditableAttributes($attributeGroup);

            if (count($customAttributes)) {
                foreach ($customAttributes as $attribute) {
                    if ($attribute->type == 'multiselect') {
                        array_push($multiselectAttributeCodes, $attribute->code);
                    }
                }
            }
        }

        if (count($multiselectAttributeCodes)) {
            foreach ($multiselectAttributeCodes as $multiselectAttributeCode) {
                if (! isset($data[$multiselectAttributeCode])) {
                    $data[$multiselectAttributeCode] = array();
                }
            }
        }

        // $filename = "userUpdate.jpg";
        // $path = $request->file('images')->move(public_path("/"),$filename);
        // $photoURL = url('/'.$filename);


        $data1 = [
            "channel"=> $request->channel,
            "locale"=> $request->locale,
            "sku" => $product['sku'],
            "product_number"=> $request->product_number,
            "name"=> $request->name,
            "url_key"=> $request->url_key,
            "status"=> $request->status,
            "short_description"=> $request->short_description,
            "description"=> $request->description,
            "price"=> $request->price,
            "cost"=> $request->cost,
            "inventories" => json_decode($request->inventories),
            "channels" => json_decode($request->channels),
            "categories" =>  json_decode($request->categories),
            "_token" => $request->_token,
            "imageURL" =>json_decode( $request->imageURL),
        ];
            $product1 = $this->productRepository->update($data1, $id);
            // var_dump($request->file('images'));
            return response()->json(
                [
                'id' => $id,
                'message' =>$data1,
                ]
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

     

        if($request->_token == 'bIHy1ZvVOkZOVYlf0aGQtwxNwfXwLQjJ0ky7JRWM'){
        $productAttributes = $this->productRepository->findOrFail($id);

        $data = request()->all();

        $multiselectAttributeCodes = array();


        foreach ($productAttributes->attribute_family->attribute_groups as $attributeGroup) {
            $customAttributes = $productAttributes->getEditableAttributes($attributeGroup);

            if (count($customAttributes)) {
                foreach ($customAttributes as $attribute) {
                    if ($attribute->type == 'multiselect') {
                        array_push($multiselectAttributeCodes, $attribute->code);
                    }
                }
            }
        }

        if (count($multiselectAttributeCodes)) {
            foreach ($multiselectAttributeCodes as $multiselectAttributeCode) {
                if (! isset($data[$multiselectAttributeCode])) {
                    $data[$multiselectAttributeCode] = array();
                }
            }
        }
        $data1 = [
            "channel"=> $request->channel,
            "locale"=> $request->locale,
            "_method"=> $request->_method,
            "product_number"=> $request->product_number,
            "name"=> $request->name,
            "url_key"=> $request->url_key,
            "status"=> $request->status,
            "short_description"=> $request->short_description,
            "description"=> $request->description,
            "price"=> $request->price,
            "cost"=> $request->cost,
            "inventories" => json_decode($request->inventories),
            "channels" => json_decode($request->channels),
            "categories" =>  json_decode($request->categories),
            "_token" => $request->_token,
            "imageURL" =>json_decode( $request->imageURL),
        ];

            $product = $this->productRepository->update($data1, $id);

            return response()->json([  'id' => $id,
            'message' =>$data1], 200);
        }else{
            return response()->json(['message' => "HTTP Error 401 - Unauthorized"], 401);
        }
       

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
