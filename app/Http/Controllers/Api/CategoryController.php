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
use Webkul\Category\Repositories\CategoryRepository;
class CategoryController extends Controller
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
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository
    )
    {
        $this->categoryRepository = $categoryRepository;

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
        // $codeOld = DB::table('inventory_sources')->where('code',$request->code)->first();
        // $productAttributes = $this->inventorySourceRepository->findOrFail($id);
        // if(!$codeOld){
        if($request->_token == 'COSiRSB6IYZXEDdufVjx6AsVSLZFzpd6ynISp1rK'){
            // var_dump(request()->all());

            $data1 = [
                "_token"=> $request->_token,
                "locale"=> $request->locale,
                "name"=> $request->name,
                "status"=> $request->status,
                "position"=> $request->position,
                "display_mode"=> $request->display_mode,
                "description"=> $request->description,
                "image" => $request->file('image'),
                "category_icon_path"=>$request->file('image'),
                "attributes" => json_decode($request->attribute),
                "meta_title"=> $request->meta_title,
                "slug"=> $request->slug,
                "meta_description" => $request->channels,
                "meta_keywords" =>  $request->categories,
            ];

            $category = $this->categoryRepository->create( $data1);

            return response()->json(
                [
                    'id' =>$category,
                    ]
            );
        }else{
            return response()->json(['message' => "HTTP Error 401 - Unauthorized"], 401);
        }
    // }else{
    //     return response()->json(['message' => "HTTP Error 409 - Inventory already exists"], 409);
    // }
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
