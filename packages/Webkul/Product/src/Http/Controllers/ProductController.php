<?php

namespace Webkul\Product\Http\Controllers;

use Exception;
use Webkul\Product\Models\Product;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Product\Helpers\ProductType;
use Webkul\Core\Contracts\Validations\Slug;
use Webkul\Product\Http\Requests\ProductForm;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Attribute\Repositories\AttributeFamilyRepository;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Inventory\Repositories\InventorySourceRepository;
use Webkul\Product\Repositories\ProductDownloadableLinkRepository;
use Webkul\Product\Repositories\ProductDownloadableSampleRepository;
use Webkul\Product\Repositories\ProductAttributeValueRepository;
use Illuminate\Http\UploadedFile;
class ProductController extends Controller
{
    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * CategoryRepository object
     *
     * @var \Webkul\Category\Repositories\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * ProductRepository object
     *
     * @var \Webkul\Product\Repositories\ProductRepository
     */
    protected $productRepository;

    /**
     * ProductDownloadableLinkRepository object
     *
     * @var \Webkul\Product\Repositories\ProductDownloadableLinkRepository
     */
    protected $productDownloadableLinkRepository;

    /**
     * ProductDownloadableSampleRepository object
     *
     * @var \Webkul\Product\Repositories\ProductDownloadableSampleRepository
     */
    protected $productDownloadableSampleRepository;

    /**
     * AttributeFamilyRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeFamilyRepository
     */
    protected $attributeFamilyRepository;
    /**
     * AttributeRepository object
     *
     * @var \Webkul\Attribute\Repositories\AttributeRepository
     */
    protected $attributeRepository;
    /**
     * InventorySourceRepository object
     *
     * @var \Webkul\Inventory\Repositories\InventorySourceRepository
     */
    protected $inventorySourceRepository;

    /**
     * ProductAttributeValueRepository object
     *
     * @var \Webkul\Product\Repositories\ProductAttributeValueRepository
     */
    protected $productAttributeValueRepository;

    /**
     * Create a new controller instance.
     *
     * @param \Webkul\Category\Repositories\CategoryRepository                 $categoryRepository
     * @param \Webkul\Product\Repositories\ProductRepository                   $productRepository
     * @param \Webkul\Product\Repositories\ProductDownloadableLinkRepository   $productDownloadableLinkRepository
     * @param \Webkul\Product\Repositories\ProductDownloadableSampleRepository $productDownloadableSampleRepository
     * @param \Webkul\Attribute\Repositories\AttributeFamilyRepository         $attributeFamilyRepository
     * @param \Webkul\Inventory\Repositories\InventorySourceRepository         $inventorySourceRepository
     * @param \Webkul\Product\Repositories\ProductAttributeValueRepository     $productAttributeValueRepository
     *
     * @return void
     */
    public function __construct(
        CategoryRepository $categoryRepository,
        ProductRepository $productRepository,
        ProductDownloadableLinkRepository $productDownloadableLinkRepository,
        ProductDownloadableSampleRepository $productDownloadableSampleRepository,
        AttributeFamilyRepository $attributeFamilyRepository,
        InventorySourceRepository $inventorySourceRepository,
        ProductAttributeValueRepository $productAttributeValueRepository,
        AttributeRepository $attributeRepository
    )
    {
        $this->_config = request('_config');

        $this->categoryRepository = $categoryRepository;

        $this->productRepository = $productRepository;

        $this->productDownloadableLinkRepository = $productDownloadableLinkRepository;

        $this->productDownloadableSampleRepository = $productDownloadableSampleRepository;

        $this->attributeFamilyRepository = $attributeFamilyRepository;

        $this->inventorySourceRepository = $inventorySourceRepository;

        $this->productAttributeValueRepository = $productAttributeValueRepository;
        
        $this->attributeRepository = $attributeRepository;
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
    public function inventory($id)
    {
        return view($this->_config['view'], compact("id"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $families = $this->attributeFamilyRepository->all();
        $attributeGroups=[];
        foreach($families as $family){
            $attributeGroups = $family->attribute_groups;
        }
        $configurableFamily = null;
        $categories = $this->categoryRepository->getCategoryTree();
        $attributes = $this->attributeRepository->findWhere(['is_filterable' =>  1]);
        if(auth()->guard('admin')->user()->role['id'] == 1){
            $inventorySources = $this->inventorySourceRepository->findWhere(['status' => 1]);
        }else{
            $inventorySources = $this->inventorySourceRepository->findWhere(['status' => 1])->find(['id'=>auth()->guard('admin')->user()->inventory_id]);
        }
        if ($familyId = request()->get('family')) {
            $configurableFamily = $this->attributeFamilyRepository->find($familyId);
        }

        return view($this->_config['view'], compact('families','attributeGroups', 'configurableFamily','categories','attributes', 'inventorySources'));
    }

    public function addCategory(){
        
        $this->validate(request(), [
            'slug'                => ['required', 'unique:category_translations,slug', new \Webkul\Core\Contracts\Validations\Slug],
            'categoryName'        => 'required',
            'image.*'             => 'mimes:bmp,jpeg,jpg,png,webp',
            'categoryDescription' => 'required_if:display_mode,==,description_only,products_and_description',
        ]);
        $data = request()->all();
        $data['name'] = $data['categoryName'];
        $data['status'] = $data['categoryStatus'];
        $data['description'] = $data['categoryDescription'];
        $category = $this->categoryRepository->create($data);
        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Category']));
        return redirect()->route('admin.catalog.products.create');
    }

    public function addInventory()
    {
        $this->validate(request(), [
            'code'           => ['required', 'unique:inventory_sources,code', new \Webkul\Core\Contracts\Validations\Code],
            'inventory-name'           => 'required',
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
        $data['name'] = $data['inventory-name'];
        $data['status'] = !isset($data['inventory-status']) ? 0 : 1;
        $data['description'] = $data['inventory-description'];
        Event::dispatch('inventory.inventory_source.create.before');

        $inventorySource = $this->inventorySourceRepository->create($data);

        Event::dispatch('inventory.inventory_source.create.after', $inventorySource);

        session()->flash('success', trans('admin::app.settings.inventory_sources.create-success'));

        return redirect()->route('admin.catalog.products.create');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $data = request()->all();
        
        $multiselectAttributeCodes = array();
        $families = $this->attributeFamilyRepository->all();
        $attributeGroups=[];
        foreach($families as $family){
            $attributeGroups = $family->attribute_groups;

            foreach ($attributeGroups as  $attributeGroup) {
                $customAttributes = $family->getCustomAttributesAttributebyGroup($attributeGroup->id);
                if (count($customAttributes)) {
                    foreach ($customAttributes as $attribute) {
                        if ($attribute->type == 'multiselect') {
                            array_push($multiselectAttributeCodes, $attribute->code);
                        }
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
        $this->validate(request(), [
            'type'                => 'required',
            'attribute_family_id' => 'required',
            'sku'                 => ['required', 'unique:products,sku', new Slug],
        ]);
        //default value
        

        request()->all()['status'] = "Active";
        $product = $this->productRepository->create($data);

        session()->flash('success', trans('admin::app.response.create-success', ['name' => 'Product']));

        return redirect()->route($this->_config['redirect'], ['id' => $product->id]);
        // return response()->json(
        //    ['id' => $product->id]
        // );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\View\View
     */

    public function edit($id)
    {
        
        $product = $this->productRepository->with(['variants', 'variants.inventories'])->findOrFail($id);
        $categories = $this->categoryRepository->getCategoryTree();
        $attributes = $this->attributeRepository->findWhere(['is_filterable' =>  1]);
        if(auth()->guard('admin')->user()->role['id'] == 1){
            $inventorySources = $this->inventorySourceRepository->findWhere(['status' => 1]);
        }else{
            $inventorySources = $this->inventorySourceRepository->findWhere(['status' => 1])->find(['id'=>auth()->guard('admin')->user()->inventory_id]);
        }

        return view($this->_config['view'], compact('product', 'categories', 'inventorySources','attributes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Webkul\Product\Http\Requests\ProductForm $request
     * @param int                                       $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $data = request()->all();
        $multiselectAttributeCodes = array();

        $productAttributes = $this->productRepository->findOrFail($id);

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
        
        
        $product = $this->productRepository->update($data, $id);

        session()->flash('success', trans('admin::app.response.update-success', ['name' => 'Product']));

        return redirect()->route($this->_config['redirect']);
            //  return response()->json(['message' => $data], 200);

    }

    /**
     * Uploads downloadable file
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadLink($id)
    {
        return response()->json(
            $this->productDownloadableLinkRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Copy a given Product.
     */
    public function copy(int $productId)
    {
        $originalProduct = $this->productRepository->findOrFail($productId);

        if (! $originalProduct->getTypeInstance()->canBeCopied()) {
            session()->flash('error',
                trans('admin::app.response.product-can-not-be-copied', [
                    'type' => $originalProduct->type,
                ]));

            return redirect()->to(route('admin.catalog.products.index'));
        }

        if ($originalProduct->parent_id) {
            session()->flash('error',
                trans('admin::app.catalog.products.variant-already-exist-message'));

            return redirect()->to(route('admin.catalog.products.index'));
        }

        $copiedProduct = $this->productRepository->copy($originalProduct);

        if ($copiedProduct instanceof Product && $copiedProduct->id) {
            session()->flash('success', trans('admin::app.response.product-copied'));
        } else {
            session()->flash('error', trans('admin::app.response.error-while-copying'));
        }

        return redirect()->to(route('admin.catalog.products.edit', ['id' => $copiedProduct->id]));
    }

    /**
     * Uploads downloadable sample file
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function uploadSample($id)
    {
        return response()->json(
            $this->productDownloadableSampleRepository->upload(request()->all(), $id)
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = $this->productRepository->findOrFail($id);

        try {
            $this->productRepository->delete($id);

            session()->flash('success', trans('admin::app.response.delete-success', ['name' => 'Product']));

            return response()->json(['message' => true], 200);
        } catch (Exception $e) {
            report($e);

            session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Product']));
        }

        return response()->json(['message' => false], 400);
    }

    /**
     * Mass Delete the products
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $productIds = explode(',', request()->input('indexes'));

        foreach ($productIds as $productId) {
            $product = $this->productRepository->find($productId);

            if (isset($product)) {
                $this->productRepository->delete($productId);
            }
        }

        session()->flash('success', trans('admin::app.catalog.products.mass-delete-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * 
     * Mass updates the products
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (! isset($data['massaction-type'])) {
            return redirect()->back();
        }

        if (! $data['massaction-type'] == 'update') {
            return redirect()->back();
        }

        $productIds = explode(',', $data['indexes']);

        foreach ($productIds as $productId) {
            $this->productRepository->update([
                'channel' => null,
                'locale'  => null,
                'status'  => $data['update-options'],
            ], $productId);
        }

        session()->flash('success', trans('admin::app.catalog.products.mass-update-success'));

        return redirect()->route($this->_config['redirect']);
    }

    /**
     * To be manually invoked when data is seeded into products
     *
     * @return \Illuminate\Http\Response
     */
    public function sync()
    {
        Event::dispatch('products.datagrid.sync', true);

        return redirect()->route('admin.catalog.products.index');
    }

    /**
     * Result of search product.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\Response
     */
    public function productLinkSearch()
    {
        if (request()->ajax()) {
            $results = [];

            foreach ($this->productRepository->searchProductByAttribute(request()->input('query')) as $row) {
                $results[] = [
                    'id'   => $row->product_id,
                    'sku'  => $row->sku,
                    'name' => $row->name,
                ];
            }

            return response()->json($results);
        } else {
            return view($this->_config['view']);
        }
    }

    /**
     * Download image or file
     *
     * @param int $productId
     * @param int $attributeId
     *
     * @return \Illuminate\Http\Response
     */
    public function download($productId, $attributeId)
    {
        $productAttribute = $this->productAttributeValueRepository->findOneWhere([
            'product_id'   => $productId,
            'attribute_id' => $attributeId,
        ]);

        return Storage::download($productAttribute['text_value']);
    }

    /**
     * Search simple products
     *
     * @return \Illuminate\Http\Response
     */
    public function searchSimpleProducts()
    {
        return response()->json(
            $this->productRepository->searchSimpleProducts(request()->input('query'))
        );
    }

    /**
     * Search simple products
     *
     * @return \Illuminate\Http\Response
     */
    public function liveSearchSimpleProducts()
    {
        return response()->json(
            $this->productRepository->searchSimpleProducts(request()->input('key'))
        );
    }
}
