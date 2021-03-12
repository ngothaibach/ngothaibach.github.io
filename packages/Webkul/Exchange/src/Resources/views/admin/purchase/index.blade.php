@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.vpt.inventory.receipt-notes') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content" style="height: 100%;">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.vpt.inventory.receipt-notes') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.exchange.purchase-order.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.vpt.inventory.add-receipt-note') }}
                </a>
            </div>
        </div>
        <div class="page-content">
            <vpt-list-receipt-notes></vpt-list-receipt-notes>
        </div>
    </div>
@stop




@push('scripts')
    <script type="text/x-template" id="vpt-list-receipt-notes-template">
        <form action="#" class="form newtopic" @submit.prevent="save">

                                        <div>
                                                                                        <table class="table table-bordered">
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th v-for="(table_header,index) in table_headers" class="grid_head">
                                                                                                    <p v-text="table_header" v-on:click = sort(sort_list[index])></p>
                                                                                                </th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody v-for="(item,index) in pageOfItems" :key="item.id">
                                                                                            
                                                                                                <tr :class="[selected_transfer ===  item.id ? 'table-info' : '']" v-on:click="load_product(item.id)">
                                                                                                    <td v-text="'MDH00' + item.id"></td>
                                                                                                    <td v-text="item.created_date"></td>
                                                                                                    <td v-text="item.supplier"></td>
                                                                                                    <td v-text="item.id"></td>
                                                                                                    <td v-if="item.status == 'temporary'" >Lưu tạm</td>
                                                                                                    <td v-if="item.status == 'received'" >Đã nhận</td>
                                                                                                    <td v-if="item.status == 'cancel'" >Hủy</td>
                                                                                                </tr>
                                                                                                <tr v-if="selected_transfer == item.id">
                                                                                                    <td style="border: 1px solid #b3d7f5;" colspan="5">
                                                                                                    <div>
                                                                                                        <div class="tabs">
                                                                                                            <ul>
                                                                                                                <li class="active">
                                                                                                                    <h4><a>Thông tin</a></h4>
                                                                                                                </li>
                                                                                                            </ul>
                                                                                                        </div>
                                                                                                        <div class="tabs-content" style="margin-top:20px;">
                                                                                                            <div class="row">
                                                                                                                <div class="col-4" style="align-self: baseline;">
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Mã nhập hàng</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="'MNH00' +item.id" class="form-control" disabled>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Thời gian</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="item.created_date" class="form-control" disabled>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Nhà cung cấp</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="item.supplier" class="form-control" disabled>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Người tạo</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="item.created_user" class="form-control" disabled>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-4" style="align-self: baseline;">
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Trạng thái</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <select v-model="form.listReceiptNotes[index].status" class="form-control" :disabled="form.oldListReceip[index].status == 'temporary' ? false : true">
                                                                                                                                        <option v-for="item in form.status" :value="item.key" v-text="item.value">
                                                                                                                                        </option>
                                                                                                                                    </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Chi nhánh</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="item.inventory" class="form-control" disabled>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label" >Người nhập</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <select v-model="item.importer" name="user" class="form-control" aria-label="User" :disabled="form.oldListReceip[index].status == 'temporary' ? false : true">
                                                                                                                                    @foreach ($users as $user)
                                                                                                                                        @if (auth()
            ->guard('admin')
            ->user()->id == $user->id)
                                                                                                                                            <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                                                                                                                        @else
                                                                                                                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                                                                                                                        @endif
                                                                                                                                    @endforeach
                                                                                                                                    </select>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                                <div class="col-4" style="align-self: baseline;">
                                                                                                                    <div class="mb-3">
                                                                                                                        <div class="form-group row">
                                                                                                                            <label class="col-sm-4 col-form-label">Ghi chú</label>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <textarea class="form-control" id="exampleFormControlTextarea1" v-model="item.note" :disabled="form.oldListReceip[index].status == 'temporary' ? false : true"></textarea>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            <h4>Danh sách sản phẩm nhập</h4>
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
                                                                                                                        <td v-text="product.id"></td>
                                                                                                                        <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + product.featured_image"/></td>
                                                                                                                        <td v-text="product.name"></td>
                                                                                                                        <td v-text="product.price"></td>
                                                                                                                        <td>
                                                                                                                            <div class="col-sm-8">
                                                                                                                                <input type="text" v-model="product_list[index1].receipt_qty" class="form-control" :disabled="form.oldListReceip[index].status == 'temporary' ? false : true" >
                                                                                                                            </div>
                                                                                                                        </td>
                                                                                                                    </tr>
                                                                                                                </tbody>
                                                                                                            </table>
                                                                                                            <span class="font-weight-bold">Tổng giá trị:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span>
                                                                                                            <div class="text-right">
                                                                                                                <button type="button" class="btn btn-success" v-on:click="save_inventory(item.id,item.note,item.status,item.importer,item.type,item.inventoryID)" :disabled="form.oldListReceip[index].status == 'temporary' ? false : true" >Lưu</button>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                    </td>
                                                                                                </tr>
                                                                                            </tbody>
                                                                                        </table>
                                                                                        <div class="card-footer pb-0 pt-3">
                                                                                            <sort-pagination 
                                                                                            v-bind:items="form.listReceiptNotes"
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
                        "id",
                        "created_date",
                        "supplier",
                        "",
                        "status"
                    ],
                    currentSortDir: "",
                    sortBy: "",
                    pageOfItems: [],
                    perPage: 4,
                    //pagination
                    form: new Form({
                        listReceiptNotes: {!! json_encode($receipt_notes) !!},
                        oldListReceip: {!! json_encode($receipt_notes) !!},
                        price_total: 0,
                        type: 'receipt',
                        receipt_date: new Date(),
                        created_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
                        supplier: null,
                        inventoryID: null,
                        to_inventory_source: null,
                        note_code: null,
                        order_code: null,
                        importer: "",
                        type: "",
                        note: "",
                        idExchange: 1,
                        product_list: null,
                        selected: "{{ __('admin::app.vpt.inventory.received') }}",
                        status: [{
                                key: 'temporary',
                                value: 'lưu tạm'
                            },
                            {
                                key: 'received',
                                value: 'Đã nhận'
                            },
                            {
                                key: 'cancel',
                                value: 'Hủy'
                            }
                        ],
                        user: "auth()->guard('admin')->user()->id"
                    }),
                    showModal: false,
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.order-code') }}",
                        "{{ __('admin::app.vpt.inventory.date') }}",
                        "{{ __('admin::app.vpt.inventory.supplier') }}",
                        "{{ __('admin::app.vpt.inventory.total-of-price') }}",
                        "{{ __('admin::app.vpt.inventory.status') }}"
                    ],
                    product_table_headers: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        "{{ __('admin::app.vpt.inventory.image') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "{{ __('admin::app.vpt.inventory.price') }}",
                        "{{ __('admin::app.vpt.inventory.qty') }}"
                    ],

                    product_list: null,
                    selected_transfer: null,
                    price_total: null,
                };
            },
            watch: {},
            methods: {
                update_total_price() {
                    this.price_total = 0;
                    for (product in this.product_list) {
                        this.price_total += product.price * product.qty
                    }
                },
                save_inventory(exchange_note_id, note, status, importer, type, inventoryID) {

                    var sites = {!! json_encode($receipt_notes) !!};
                    this.form.idExchange = exchange_note_id;
                    this.form.note = note;
                    this.form.importer = importer;
                    this.form.status = status;
                    this.form.type = type;
                    this.form.inventoryID = inventoryID;

                    console.log('dataSource', this.form.inventoryID)


                    this.form.post("{{ route('admin.exchange.update') }}")
                        .then((response) => {

                            // var attr = document.getElementById("text");
                            // attr.innerHTML = response.data.message;
                            console.error('loix ne ', response);
                            if (response.data.success == true) {
                                console.error("save exchange successfull");
                                window.location.href =
                                    "{{ route('admin.exchange.purchase-order.list') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }
                        })
                },
                load_product(exchange_note_id) {
                    this.product_list = [];
                    this.selected_transfer = exchange_note_id;

                    axios.get("{{ route('exchange.admin.get_transfered_products') }}", {
                            params: {
                                transfer_id: exchange_note_id
                            }
                        })
                        .then(response => {
                            this.product_list = response.data.transfered_products;
                            this.form.product_list = response.data.transfered_products;
                            console.log('this.product_list', this.product_list)
                            console.error(this.product_list);
                            this.price_total = 0;
                            for (product of this.product_list) {
                                this.price_total += product.price * product.receipt_qty;
                            }
                        });
                    // this.update_total_price();
                    // this.price_total = 0;
                    //this.showModal = true;
                },
                //pagination
                onChangePage(pageOfItems) {
                    // update page of items
                    this.pageOfItems = pageOfItems;
                },    
                sort(name){
                    if(this.sortBy != name){
                        this.sortBy = name;
                        this.currentSortDir = 'asc';
                    }else{
                        this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                    }
                },
                //pagination
                closeModal() {
                    this.showModal = false;
                }
            },
           
        });

    </script>
@endpush
