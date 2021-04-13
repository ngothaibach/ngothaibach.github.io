@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.vpt.inventory.transfer-note') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content" style="height: 100%;">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.vpt.inventory.transfer-note') }}</h1>
            </div>
            @if(checkPermission('exchange.list_transfer.create'))
            <div class="page-action">
                <a href="{{ route('admin.exchange.transfer.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.vpt.inventory.transfer') }}
                </a>
            </div>
            @endif
        </div>
        <div class="page-content">
        <filter-and-search 
                :url='"{{ route("admin.exchange.transfer.list") }}"'
                :searchfields = "[
                {name: 'Mã đơn hàng', key: 'id', columnType: 'number' },
                {name: 'Thời gian', key: 'transfer_date', columnType: 'datetime'}, 
                {name: 'Từ chi nhánh', key: 'from_inventory', columnType: 'string'},
                {name: 'Tới Chi Nhánh', key:'to_inventory', columnType: 'string'},
                {name: 'Trạng thái', key:'status', columnType: 'string'}
                ]"
                ></filter-and-search>
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
                        <p  v-text="table_header" v-on:click = "sort(sort_list[index]);showArrow(index)" ></p><p :class="arrow" v-if="currentArrow == index" ></p>
                        </th>
                    </tr>
                    </thead>
                    <tbody v-for="(item,index) in form.listReceiptNotes">
                        <tr :class="[selected_transfer ===  item.id ? 'table-info' : '']" v-on:click="load_product(item.id)">
                            <td v-text="'MDH' + item.id"></td>
                            <td v-text="item.transfer_date"></td>
                            <td v-text="item.from_inventory"></td>
                            <td v-text="item.to_inventory"></td>
                            <td v-if="item.status == 'temporary'" >Lưu tạm</td>
                            <td v-if="item.status == 'received'" >Đã nhận</td>
                            <td v-if="item.status == 'cancel'" >Hủy</td>
                            <td v-if="item.status == 'transfering'" >Đang vận chuyển</td>
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
                                                    <label class="col-sm-4 col-form-label">Mã chuyển</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="'MNH00' + item.id" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Từ chi nhánh</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.from_inventory" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Ngày chuyển</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.transfer_date"  class="form-control" disabled>
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
                                                        <select v-model="form.listReceiptNotes[index].status" class="form-control" :disabled="role_id != 1 ? true : !updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : form.oldListReceip[index].status == 'transfering' ? false : true">
                                                                <option v-for="item in form.status" :value="item.key" v-text="item.value">
                                                                </option>
                                                            </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Tới chi nhánh</label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.to_inventory"  class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Ngày nhận</label>
                                                    <div class="col-sm-8">
                                                        <vuejs-datepicker v-model="item.receipt_date" :disabled="role_id != 1 ? true : !updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : form.oldListReceip[index].status == 'transfering' ? false : true"></vuejs-datepicker>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4" style="align-self: baseline;">
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Ghi chú</label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" id="exampleFormControlTextarea1" v-model="item.note" :disabled="role_id != 1 ? true : !updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : form.oldListReceip[index].status == 'transfering' ? false : true"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <h4>Danh sách sản phẩm chuyển</h4>
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
                                                <td v-text="product.product_id"></td>
                                                <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + product.featured_image"/></td>
                                                <td v-text="product.name"></td>
                                                <td v-text="product.price"></td>
                                                <td>
                                                    <div class="col-sm-8">
                                                        <input type="text" :value="product.transfer_qty" @change.lazy="update_total_price(parseInt($event.target.value),product.transfer_qty,product.price,index1)" class="form-control" :disabled="!updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : true " >
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <span class="font-weight-bold">Tổng giá trị:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-success" v-on:click="save_inventory(item.id,item.note,item.status,item.importer,item.type,item.from_inventory_id,item.receipt_date,price_total)" :disabled="!updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : form.oldListReceip[index].status == 'transfering' ? false : true" >Lưu</button>
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
                        "transfer_date",
                        "from_inventory",
                        "to_inventory",
                        "status"
                    ],
                    currentSortDir: "desc",
                    sortBy: "id",
                    pageOfItems: [],
                    perPage: 10,
                    arrow: "custom-arrow-icon-down",
                    currentArrow : 0,
                    //pagination
                    //check permission
                    updatePermission: Boolean(Number('{{checkPermission('exchange.list_transfer.update')}}')),
                    role_id: {!! json_encode($role_id) !!},
                    //check permission
                    form: new Form({
                        listReceiptNotes: {!! json_encode($receipt_notes) !!},
                        oldListReceip : {!! json_encode($receipt_notes) !!},
                        price_total: 0,
                        type: 'receipt',
                        receipt_date: new Date(),
                        created_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
                        supplier: null,
                        to_inventory_source: null,
                        note_code: null,
                        order_code: null,
                        importer: "",
                        note: "",
                        idExchange: 1,
                        product_list: null,
                        type : null,
                        from_inventory_id : null,
                        selected : "{{ __('admin::app.vpt.inventory.received') }}",
                        status: [
                            {
                                key :  'temporary',
                                value : 'lưu tạm'
                            },
                            {
                                key :  'transfering',
                                value : 'Đang vận chuyển'
                            },
                            {
                                key :  'received',
                                value : 'Đã nhận'
                            },
                            {
                                key :  'cancel',
                                value : 'Hủy'
                            }
                    ]
                    }),
                    showModal: false,
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.order-code') }}",
                        "{{ __('admin::app.vpt.inventory.date') }}",
                        "{{ __('admin::app.vpt.inventory.from-inventory-source') }}",
                        "{{ __('admin::app.vpt.inventory.to-inventory-source') }}",
                        // "{{ __('admin::app.vpt.inventory.total-of-price') }}",
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
                    price_total: null
                };
            },
            watch: {},
            methods: {
                update_total_price(newQty, oldQty, price,pos){
                    this.price_total += price * (newQty - oldQty);
                    this.product_list[pos].transfer_qty = newQty;
                    this.product_list.push();
                },
                save_inventory(exchange_note_id, note, status, importer,type,from_inventory_id,receipt_date,price_total) {

                    var sites = {!! json_encode($receipt_notes) !!};
                    this.form.idExchange = exchange_note_id;
                    this.form.note = note;
                    this.form.importer = importer;
                    this.form.status = status;
                    this.form.type = type;
                    this.form.from_inventory_id = from_inventory_id;
                    this.form.receipt_date = receipt_date;
                    this.form.total = price_total;

                    this.form.post("{{ route('admin.exchange.updateTransfer') }}")
                        .then((response) => {

                            // var attr = document.getElementById("text");
                            // attr.innerHTML = response.data.message;
                            console.error('loix ne ', response);
                            if (response.data.success == true) {
                                console.error("save exchange successfull");
                                window.location.href =
                                    "{{ route('admin.exchange.transfer.list') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }


                        })
                },
                load_product(exchange_note_id) {
                    this.product_list = []
                    this.selected_transfer = exchange_note_id;

                    axios.get("{{ route('exchange.admin.get_transfered_products') }}", {
                            params: {
                                transfer_id: exchange_note_id
                            }
                        })
                        .then(response => {
                            this.product_list = response.data.transfered_products;
                            this.form.product_list = response.data.transfered_products;
                    // console.log('dataSource', this.form.product_list)

                            console.error(this.product_list);

                            this.price_total = 0;
                            for (product of this.product_list) {
                                this.price_total += product.price * product.transfer_qty;
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
                        this.currentSortDir = 'desc';
                        this.arrow = 'custom-arrow-icon-down';
                        
                    }else{
                        this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                        this.arrow = this.arrow=== 'custom-arrow-icon-down' ? 'custom-arrow-icon-up' : 'custom-arrow-icon-down';
                    }
                },
                showArrow(number) {
                    this.currentArrow = number;
                },
                //pagination
                closeModal() {
                    this.showModal = false;
                }
            }
        });

    </script>
@endpush

