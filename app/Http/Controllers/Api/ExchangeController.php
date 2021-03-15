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
        //
        // $date = Carbon::now()->toISOString();
        // $exchangeNoteData = [
        //     'status' => "temporary",
        //     'type' => "receipt",
        //     'created_user_id' => 1,
        //     'receipt_user_id' => 1,
        //     'supplier_id' => $request->supplier_id,
        //     // 'from_inventory_source_id' => 1,
        //     'to_inventory_source_id' => $request->to_inventory_source_id,
        //     'from_inventory_source_id' =>  $request->from_inventory_source_id,
        //     'created_date' =>$date,
        //     'updated_date' =>$date,
        //     'transfer_date' =>$date,
        //     'receipt_date' =>$date,
        //     'note' => $request->notes,
        // ];

        // $exchangeNote = $this->exchangeNoteRepository->create($exchangeNoteData);
        //         $productExchangeData = [

        //             'exchange_note_id' => $exchangeNote->id,
        //             'product_id' => $request->product_id,
        //             'transfer_qty' => $request->transfer_qty,
        //             'receipt_qty' => $request->receipt_qty,
        //             'price' => $request->price,
        //             'discount' => 0,
        //         ];
        //         $productExchange = $this->productExchangeNoteRepository->create($productExchangeData);
          
        // return response()->json(
        //     [
        //         'success' => true,
        //         'message' => 'Save susscessfully',
        //         'tai smile' =>$productExchangeData,
        //         'minh smile' => Carbon::now()->toISOString()
        //     ]
        // );


         
            // if (! request()->get('family')
            //     && ProductType::hasVariants(request()->input('type'))
            //     && request()->input('sku') != ''
            // ) {
            //     return redirect(url()->current() . '?type=' . request()->input('type') . '&family=' . request()->input('attribute_family_id') . '&sku=' . request()->input('sku'));
            // }

            // if (ProductType::hasVariants(request()->input('type'))
            //     && (! request()->has('super_attributes')
            //         || ! count(request()->get('super_attributes')))
            // ) {
            //     session()->flash('error', trans('admin::app.catalog.products.configurable-error'));

            //     return back();
            // }

            // $this->validate(request(), [
            //     'type'                => 'required',
            //     'attribute_family_id' => 'required',
            //     'sku'                 => ['required', 'unique:products,sku', new Slug],
            // ]);

        // Set default values
        // request()->all()['status'] = "Active";

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
        $data1 = [
            "channel"=> $request->channel,
            "locale"=> $request->locale,
            // "_method"=> $request->_method,
            "product_number"=> $request->product_number,
            "name"=> $request->name,
            "url_key"=> $request->url_key,
            "status"=> $request->status,
            "short_description"=> $request->short_description,
            "description"=> $request->description,
            "price"=> $request->price,
            "cost"=> $request->cost,
            "inventories" => json_decode($request->inventories),
            "images" => [
                "0" => "https://scontent.fhan5-7.fna.fbcdn.net/v/t1.0-9/54729419_2238982889750879_76837982832689152_o.png?_nc_cat=100&ccb=1-3&_nc_sid=09cbfe&_nc_ohc=0SCh36nA-XcAX8SUU7e&_nc_ht=scontent.fhan5-7.fna&oh=3086523d7c420440aadea0a2456decc9&oe=6075E036"
            ]
        ];
        $product1 = $this->productRepository->update($data1, $id);

        
        return response()->json(
            ['id' => $request->all() ]
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
    public function update(Request $request, $id)
    {
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
            // "_method"=> $request->_method,
            "product_number"=> $request->product_number,
            "name"=> $request->name,
            "url_key"=> $request->url_key,
            "status"=> $request->status,
            "short_description"=> $request->short_description,
            "description"=> $request->description,
            "price"=> $request->price,
            "cost"=> $request->cost,
            "inventories" => json_decode($request->inventories),
            "images" => [
                "0" => "https://scontent.fhan5-7.fna.fbcdn.net/v/t1.0-9/54729419_2238982889750879_76837982832689152_o.png?_nc_cat=100&ccb=1-3&_nc_sid=09cbfe&_nc_ohc=0SCh36nA-XcAX8SUU7e&_nc_ht=scontent.fhan5-7.fna&oh=3086523d7c420440aadea0a2456decc9&oe=6075E036"
            ]
        ];
        $product = $this->productRepository->update($data1, $id);


        // session()->flash('success', trans('admin::app.response.update-success', ['name' => 'Product']));

        // return redirect()->route($this->_config['redirect']);
             return response()->json(['message' => $data], 200);

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
