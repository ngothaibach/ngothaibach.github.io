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
            <div class="page-action">
                <a href="{{ route('admin.exchange.transfer.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.vpt.inventory.transfer') }}
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
                        <th v-for="table_header in table_headers" class="grid_head">
                            <p v-text="table_header"></p>
                        </th>
                    </tr>
                    </thead>
                    <tbody v-for="(item,index) in form.listReceiptNotes">
                        <tr :class="[selected_transfer ===  item.id ? 'table-info' : '']" v-on:click="load_product(item.id)">
                            <td v-text="'MDH' + item.id"></td>
                            <td v-text="item.transfer_date"></td>
                            <td v-text="item.from_inventory"></td>
                            <td v-text="item.to_inventory"></td>
                            <td v-text="item.status"></td>
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
                                                        <select v-model="form.listReceiptNotes[index].status" class="form-control">
                                                                <option v-for="item in form.status" :value="item" v-text="item">
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
                                                        <vuejs-datepicker v-model="form.created_date"></vuejs-datepicker>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4" style="align-self: baseline;">
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Ghi chú</label>
                                                    <div class="col-sm-8">
                                                        <textarea class="form-control" id="exampleFormControlTextarea1" v-model="item.note"></textarea>
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
                                            <tr v-for="(product,index) in product_list">
                                                <td v-text="product.product_id"></td>
                                                <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + product.featured_image"/></td>
                                                <td v-text="product.name"></td>
                                                <td v-text="product.price"></td>
                                                <td>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="product_list[index].transfer_qty" class="form-control">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <span class="font-weight-bold">Tổng giá trị:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span>
                                    <div class="text-right">
                                        <button type="button" class="btn btn-success" v-on:click="save_inventory(item.id,item.note,item.status,item.importer)">Lưu</button>
                                    </div>
                                </div>
                            </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
          </form>
        </script>

    <script>
        Vue.component('vpt-list-receipt-notes', {
            template: '#vpt-list-receipt-notes-template',
            data() {
                return {
                    form: new Form({
                        listReceiptNotes: {!! json_encode($receipt_notes) !!},
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
                        selected : "{{ __('admin::app.vpt.inventory.received') }}",
                        status: [
                            'temporary','transfering', 'received', 'cancel',
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
                save_inventory(exchange_note_id, note, status, importer) {

                    var sites = {!! json_encode($receipt_notes) !!};
                    this.form.idExchange = exchange_note_id;
                    this.form.note = note;
                    this.form.importer = importer;
                    this.form.status = status;
                    console.log('dataSource', this.form.product_list)


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
                    this.product_list = []
                    this.selected_transfer = exchange_note_id;

                    axios.get("{{ route('exchange.admin.get_transfered_products') }}", {
                            params: {
                                transfer_id: exchange_note_id
                            }
                        })
                        .then(response => {
                            this.product_list = response.data.transfered_products;
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
                closeModal() {
                    this.showModal = false;
                }
            }
        });

    </script>
@endpush

