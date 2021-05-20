@extends('admin::layouts.content')
@section('page_title')
    {{-- {{ __('admin::app.sales.orders.title') }} --}}
    Bán hàng
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-header">
                <div class="page-title">
                    <h1>{{ __('admin::app.catalog.products.title') }}</h1>
                </div>
                
                <div class="page-action">
                    <div class="export-import" v-on:click="showModal('downloadDataGrid')">
                        <i class="export-icon"></i>
                        <span >
                            {{ __('admin::app.export.export') }}
                        </span>
                    </div>
                @if(checkPermission('catalog.products.create'))
                    <a href="{{ route('admin.catalog.products.create') }}" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.products.add-product-btn-title') }}
                    </a>
                @endif
                </div>
                
            </div>
        </div>
        <div class="page-content">
            <div class="page-content">
                <filter-and-search 
                :searchfields = "[
                {name: 'ID', key: 'product_id', columnType: 'number' },
                {name: 'Mã hàng hóa', key: 'product_sku', columnType: 'string' },
                {name: 'Tên', key: 'product_name', columnType: 'string'}, 
                {name: 'Giá', key: 'price', columnType: 'number'},
                {name: 'Trạng thái', key:'status', columnType: 'custom'}
                ]"
                :customfields = "[
                {name: 'chưa kích hoạt', key: '0' },
                {name: 'Đã kích hoạt', key: '1'}, 
                ]"
                ></filter-and-search>
                <vpt-list-receipt-notes></vpt-list-receipt-notes>
            </div>
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

@stop
@push('scripts')
@inject('products', 'Webkul\Admin\DataGrids\ProductDataGrid')

    @include('admin::export.export', ['gridName' => $products])
    <script>

        function reloadPage(getVar, getVal) {
            let url = new URL(window.location.href);
            url.searchParams.set(getVar, getVal);

            window.location.href = url.href;
        }

    </script>
@endpush

{{-- @push('scripts')
    @include('admin::export.export', ['gridName' => $orderGrid])
@endpush --}}

@push('scripts')
    <script type="text/x-template" id="vpt-list-receipt-notes-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
                <div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th v-for="(table_header,index) in table_headers" class="grid_head">
                            <p  v-text="table_header" v-on:click = "sort(sort_list[index]);showArrow(index)" ></p><p :class="arrow" v-if="currentArrow == index" ></p>
                            </th>
                        </tr>
                        </thead>
                        <tbody id='hover-row' v-for="(item,index) in pageOfItems">
                            <tr :class="[selected_transfer ===  item.product_id ? 'table-info' : '']" v-on:click="load_product(item.product_id)">
                                <td v-text="item.product_id"></td>
                                <td v-text="item.product_sku"></td>
                                <td v-text="item.product_name"></td>
                                <td v-text="item.price != null ? parseFloat(item.price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',') : ''"></td>
                                <td v-text="item.quantity != null ? parseFloat(item.quantity).toString() : ''"></td>
                                <td v-if="item.status == 0" >Chưa kích hoạt</td>
                                <td v-if="item.status == 1" >Đã kích hoạt</td>
                            </tr>
                            <tr v-if="selected_transfer == item.product_id">
                                <td style="border: 1px solid #b3d7f5;" colspan="6">
                                <div>
                                    <div class="tabs">
                                        <ul>
                                            <li v-bind:class="{active: isActiveInfo}"
                                                v-on:click="showTabs(0)" >
                                                <h4><a>Thông tin</a></h4>
                                            </li>
                                            <li  v-bind:class="{active: isActiveInven}"
                                            v-on:click="showTabs(1)" >
                                                <h4><a>Thẻ kho</a></h4>
                                            </li>
                                        </ul>
                                    </div>
                                    <div v-if="currentTab == 0" class="tabs-content" style="margin-top:20px;">
                                            <h3 style="    font-size: 18px;
                                                        font-weight: 700;
                                                         color: #006fa9;
                                                         margin-bottom: 12px;
                                                         font-weight : bold">
                                                 <p v-text="item.product_name"></p>
                                             </h3>

                                        <div class="row">
                                            <div class="col-4" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Hình ảnh</label>

                                                        <div class="col-sm-12">
                                                            <img :src="item.product_image != null ? '{{Config::get('app.url')}}/storage/' + item.product_image : '{{Config::get('app.url')}}/' + 'noimage.png'" style="position: relative;height: 250px;width: 200px;">    
                                                                                                            </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Mã hàng                                                     </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.product_sku" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Mã vạch                                                        </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.product_number" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Ngày tạo</label>
                                                        <div class="col-sm-8">
                                                            <input id="datePicker" type="date" :value="formatDate(item.created_at)"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Số lượng</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.quantity" class="form-control" disabled> 
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Mô tả ngắn</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.short_description" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Thương hiệu</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" :value="item.brand_label"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Trọng lượng</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.weight"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Trạng thái</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.status == 0 ? 'Chưa kích hoạt' : 'Đã kích hoạt'"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                          
                                        </div>
                                            <button type="button" class="btn btn-primary" style="width: 120px;" v-on:click="update_orders(item.product_id)" >Chỉnh sửa</button>
                                        </div>
                                    </div>
                                    <div v-if="currentTab == 1" class="tabs-content" style="margin-top:20px;">
                                        <h3 style="    font-size: 18px;
                                        font-weight: 700;
                                         color: #006fa9;
                                         margin-bottom: 12px;
                                         font-weight : bold">
                                 <p>Danh sách thẻ kho</p>
                             </h3>
                                        <table class="table table-bordered">
                                            <thead>
                                            <tr>
                                                <th v-for="product_table_header in product_table_headers" class="grid_head">
                                                    <p v-text="product_table_header"></p>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(product,index1) in product_list">
                                                    <td v-text="product.sku" style="color : #056bd3;"></td>
                                                    <td>Cập nhật giá vốn</td>
                                                    <td v-text="product.created_at"></td>
                                                    <td v-text="product.brand_label"></td>
                                                    <td v-text="parseFloat(product.cost).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')"></td>
                                                    <td v-text="item.quantity"></td>
                                                    <td>0</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    <div class="card-footer pb-0 pt-3">
                        <sort-pagination 
                        v-bind:items="form.product"
                        v-bind:pageSize = "perPage"
                        v-bind:sortBy ="sortBy"
                        v-bind:currentSortDir ="currentSortDir"
                        @changePage="onChangePage">
                        </sort-pagination>
                    </div>
                </div>
              </form>
            </script>

    <script>
        Vue.component('vpt-list-receipt-notes', {
            template: '#vpt-list-receipt-notes-template',
            data() {
                return {
                    //pagination
                    sort_list: [
                        "product_id",
                        "product_sku",
                        "product_name",
                        "price",
                        "quantity",
                        "status"
                    ],
                    currentSortDir: "desc",
                    sortBy: "id",
                    pageOfItems: [],
                    perPage: 10,
                    arrow: "custom-arrow-icon-down",
                    currentArrow: 0,
                    currentTab : 0,
                    isActiveInfo : true,
                    isActiveInven : false,
                    //pagination
                    form: new Form({
                        product: {!! json_encode($product) !!},
                    }),
                    showModal: false,
                    table_headers: [
                        'ID',
                        "Mã hàng hóa",
                        'Tên',
                        'Giá',
                        'Số lượng',
                        'Trạng thái'
                    ],
                    product_table_headers: [
                        "Chứng từ",
                        "Phương thức",
                        "Thời gian",
                        "Nhãn hiệu",
                        "Giá vốn",
                        "Số lượng",
                        "Tồn cuối",
                    ],
                    product_list: null,
                    selected_transfer: null,
                };
            },
            watch: {},
            methods: {
                formatDate(val) {
                    var date = val.substring(0, 10);
                    return date;
                },
                update_orders(get_order_id) {
                    let link = "{{ route('admin.catalog.products.edit_product') }}";
                    window.open(link + '/' + get_order_id);
                },
                load_product(get_order_id) {
                    this.product_list = [];
                    
                    if(this.selected_transfer == get_order_id){
                        this.selected_transfer = null
                    }else{
                        this.selected_transfer = get_order_id;
                        axios.get("{{ route('admin.catalog.products.show_detail_product') }}", {
                                params: {
                                    product_id: get_order_id
                                }
                            })
                            .then(response => {
                                this.product_list = response.data.transfered_products;
                            });
                    }
                },
                //pagination
                onChangePage(pageOfItems) {
                    // update page of items
                    this.pageOfItems = pageOfItems;
                },
                sort(name) {
                    if (this.sortBy != name) {
                        this.sortBy = name;
                        this.currentSortDir = 'desc';
                        this.arrow = 'custom-arrow-icon-down';

                    } else {
                        this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                        this.arrow = this.arrow === 'custom-arrow-icon-down' ? 'custom-arrow-icon-up' : 'custom-arrow-icon-down';
                    }
                },
                showArrow(number) {
                    this.currentArrow = number;
                },
                showTabs(number){
                    this.currentTab = number;
                    if(number == 0){
                        this.isActiveInfo = true;
                        this.isActiveInven = false;
                    }else{
                        this.isActiveInfo = false;
                        this.isActiveInven = true;
                    }
                },
                //pagination
                closeModal() {
                    this.showModal = false;
                }
            }
        });

    </script>
@endpush
