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
            <div class="page-title">
                {{-- <h1>{{ __('admin::app.sales.orders.title') }}</h1> --}}
                <h1>Đơn hàng</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.sales.orders.create') }}" class="btn btn-lg btn-primary">
                    Tạo đơn
                </a>
            </div>

            <div class="page-action">
                <div class="export-import" v-on:click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{-- {{ __('admin::app.export.export') }} --}}
                        Xuất báo cáo
                    </span>
                </div>
            </div>
        </div>
        <div class="page-content">
            <div class="page-content">
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
                            <tr :class="[selected_transfer ===  item.order_id ? 'table-info' : '']" v-on:click="load_product(item.order_id)">
                                <td v-text="item.order_id"></td>
                                <td v-text="item.updated_at"></td>
                                <td v-text="item.customer_first_name +' '+ item.customer_last_name"></td>
                                <td v-text="parseFloat(item.base_sub_total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')"></td>
                                <td v-text="parseFloat(item.base_grand_total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')"></td>
                                <td v-if="item.status == 'temporary'" >Lưu tạm</td>
                                <td v-if="item.status == 'processing'" >Đang xử lý</td>
                                <td v-if="item.status == 'closed'" >Đã đóng</td>
                                <td v-if="item.status == 'pending'" >Đang chờ</td>
                                <td v-if="item.status == 'completed'" >Hoàn thành</td>
                                <td v-if="item.status == 'canceled'" >Đã hủy</td>
                            </tr>
                            <tr v-if="selected_transfer == item.order_id">
                                <td style="border: 1px solid #b3d7f5;" colspan="6">
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
                                                        <label class="col-sm-4 col-form-label">Mã đặt hàng</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.order_id" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Trạng thái</label>
                                                        <div class="col-sm-8">
                                                            <select v-model="item.status_id" class="form-control" id ="user_selected">
                                                                    <option 
                                                                        v-for="item in form.list_status" :value="item.id" v-text="item.name">
                                                                    </option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Ngày tạo</label>
                                                        <div class="col-sm-8">
                                                            <input id="datePicker" type="date" :value="formatDate(item.updated_at)"  class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Khách hàng                                             </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.customer_first_name +' '+ item.customer_last_name" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Chi nhánh                                          </label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.name_inven" class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Thanh toán</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" :value="getMethod(item.method_payment)"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Người bán</label>
                                                        <div class="col-sm-8">
                                                            <select v-model="item.sale_id" class="form-control" id ="user_selected">
                                                                    <option 
                                                                        v-for="item in form.list_user" :value="item.id" v-text="item.name">
                                                                    </option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Kênh bán</label>
                                                        <div class="col-sm-8">
                                                            <input type="text" v-model="item.channel_name"  class="form-control" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-4" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <label class="col-sm-4 col-form-label">Ghi chú</label>
                                                    <div class="form-group row">
                                                        <div class="col-sm-12">
                                                            <textarea style="height: 200px" class="form-control" id="commentNote" v-model="item.comment" ></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <h4>Danh sách sản phẩm</h4>
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
                                                    <td v-text="product.name"></td>
                                                    <td v-text="parseFloat(product.base_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')"></td>
                                                    <td v-text="product.qty_ordered"></td>
                                                    <td v-text="getTotal(product.base_total,product.base_tax_amount)"></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                        <table class="sale-summary" >
                                            <tbody>
                                            <tr>
                                                <td>Thành tiền</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.base_sub_total)) "></td>
                                            </tr>
                
                
                                                <tr  v-if="parseFloat(form.order_money.discount_amount) > 0">
                                                    <td v-if="form.order_money.coupon_code == null" >Khuyến mại</td>
                                                    <td v-if="form.order_money.coupon_code != null" v-text="'Khuyến mại\n('  + form.order_money.coupon_code + ')'"></td>
                                                    <td>:</td>
                                                    <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.discount_amount)) "></td>
                                                </tr>
                                                
                                                <tr v-if="parseFloat(form.order_money.collection_diff) > 0">
                                                    <td>Thu khác</td>
                                                    <td>:</td>
                                                    <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.collection_diff)) "></td>
                                                </tr>
                
                                            <tr class="bold">
                                                <td>Tổng cộng</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.base_grand_total)) "></td>
                                            </tr>
                                            <tr class="bold">
                                                <td>Tổng hóa đơn mua</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.base_grand_total_invoiced)) "></td>
                                            </tr>
                                            <tr class="bold" v-if="parseFloat(item.refund_id) > 0">
                                                <td>Tiền hoàn lại (<button class="btn-link-style" v-on:click="open_refund_detail(item.refund_id)" v-text="item.refund_id"></button>)</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(form.order_money.base_grand_total_refunded)) "></td>
                                            </tr>
                                           
                                            <tr v-if="parseFloat(item.money_exchange_refund) > 0">
                                                <td>Tổng tiền hóa đơn trả (<button class="btn-link-style" v-on:click="open_refund_detail(item.exchange_refund_id)" v-text="item.exchange_refund_id"></button>)                                                </td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(item.money_exchange_refund)) "></td>
                                            </tr>
                                        </tbody>
                                        </table>
                                        
                                        {{-- <span class="font-weight-bold">Tổng tiền hàng:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Phí Ship:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Tổng tiền hóa đơn mua:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Tổng tiền hóa đơn trả:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    
                                    <span class="font-weight-bold">Khách cần trả:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span> --}}
                                        <div class="btn-summit-right">
                                            <button v-if="canCancel" type="button" class="btn btn-danger" style="marginRight : 20px;width: 120px;" v-on:click="create_cancel(item.order_id)" >Hủy</button>
                                            {{-- <button v-if="canInvoice" type="button" class="btn btn-primary" style="marginRight : 20px;width: 130px;" v-on:click="create_invoice(item.order_id)" >Tạo hóa đơn</button> --}}
                                            {{-- <a href="{{ route('admin.sales.invoices.create', item . id) }}" class="btn btn-lg btn-primary"> --}}

                                            <button v-if="canRefund" type="button" class="btn btn-primary" style="marginRight : 20px;width: 120px;" v-on:click="create_refund(item.order_id)" >Hoàn lại</button>
                                            <button type="button" class="btn btn-primary" style="marginRight : 20px;width: 120px;" v-on:click="print_invoices(item.order_id)" >In vận đơn</button>
                                            <button type="button" class="btn btn-success" style="width: 120px;" v-on:click="update_orders(item.order_id,item.status_id)" >Cập nhật</button>
                                        </div>
                                    </div>
                                </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="card-footer pb-0 pt-3">
                        <sort-pagination 
                        v-bind:items="form.invoice_note"
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
                    date2: '2017-07-04',
                    commission: 123456,
                    //pagination
                    sort_list: [
                        "order_id",
                        "updated_at",
                        "customer_last_name",
                        "base_sub_total",
                        "base_grand_total",
                        "status"
                    ],
                    currentSortDir: "desc",
                    sortBy: "id",
                    pageOfItems: [],
                    perPage: 10,
                    arrow: "custom-arrow-icon-down",
                    currentArrow: 0,
                    //pagination
                    form: new Form({
                        canInvoice: false,
                        canCancel: false,
                        canRefund: false,
                        invoice_note: {!! json_encode($invoice_note) !!},
                        list_user :{!! json_encode($user_sale) !!},
                        list_status :{!! json_encode($status_name) !!},
                        order_money: {},
                        receipt_date: new Date(),
                        created_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
                        importer: "",
                        note: "",
                        idExchange: 1,
                        product_list: null,
                        type: null,
                        selected: "{{ __('admin::app.vpt.inventory.received') }}",
                        status: [{
                                key: 'temporary',
                                value: 'lưu tạm'
                            },
                            {
                                key: 'transfering',
                                value: 'Đang vận chuyển'
                            },
                            {
                                key: 'received',
                                value: 'Đã nhận'
                            },
                            {
                                key: 'cancel',
                                value: 'Hủy'
                            }
                        ]
                    }),
                    showModal: false,
                    table_headers: [
                        'Id hóa đơn',
                        "{{ __('admin::app.vpt.inventory.date') }}",
                        'Khách hàng',
                        'Tổng tiền hàng',
                        // "{{ __('admin::app.vpt.inventory.total-of-price') }}",
                        'Tổng sau giảm giá',
                        'Trạng thái'
                    ],
                    product_table_headers: [
                        "Mã sản phẩm",
                        "Tên sản phẩm",
                        "Đơn giá",
                        "Số lượng",
                        "Tổng cộng"
                    ],
                    product_list: null,
                    selected_transfer: null,
                    price_total: null
                };
            },
            watch: {},
            methods: {
                formatDate(val) {
                    var date = val.substring(0, 10);
                    return date;
                },
                getValue(val) {
                    if (val == 'processing') {
                        return 'Đang xử lý';
                    } else if (val == 'completed') {
                        return 'Đã hoàn thành';
                    } else if (val == "canceled") {
                        return 'Đã hủy';
                    } else if (val == "closed") {
                        return 'Đã đóng';
                    } else if (val == "pending") {
                        return 'Đang chờ';
                    } else if (val == "pending_payment") {
                        return 'Chờ thanh toán';
                    } else if (val == "fraud") {
                        return 'Gian lận';
                    }
                },
                getMethod(val) {
                    if (val == 'cashondelivery') {
                        return 'COD';
                    } else {
                        return 'Visa';
                    }
                },
                getTotal(val, val1) {
                    return this.numberFormatter(parseFloat(val + val1) );
                },
                numberFormatter(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },

                update_total_price() {
                    this.price_total = 0;
                    for (product in this.product_list) {
                        this.price_total += product.price * product.qty
                    }
                },
                print_invoices(id) {
                    let link = "{{ route('admin.sales.orders.print_orders') }}";
                    window.open(link + '/' + id);
                },

                create_refund(order_id) {
                    var link = "{{route('admin.sales.refunds.create_refunds')}}";
                    window.location.href = (link + '/' + order_id);
                },
                open_refund_detail(refund_id) {
                    var link = "{{route('admin.sales.refunds.view_refund')}}";
                    window.location.href = (link + '/' + refund_id);
                },
                create_cancel(order_id) {
                    var link = "{{route('admin.sales.orders.cancel_order')}}";
                    window.location.href = (link + '/' + order_id);
                },
                // create_invoice(order_id) {
                //     var link = "{{route('admin.sales.invoices.create_invoice')}}";
                //     window.location.href = (link + '/' + order_id);
                // },
                update_orders(get_order_id,get_status_id) {
                    var user_selected = document.getElementById('user_selected').value;
                    var get_comment = document.getElementById('commentNote').value;
                    var get_date = document.getElementById('datePicker').value;
                    var hours = new Date().getHours();
                    var minute =new Date().getMinutes();
                    var seconds =new Date().getSeconds();
                    var get_date_time =get_date + " " +hours.toString() + ":" + minute.toString() + ":" + seconds.toString();
                    // console.log('datetime: ',user_selected);
                    axios.get("{{ route('admin.sales.orders.update_notes') }}", {
                            params: {
                                order_id: get_order_id,
                                comment_content: get_comment,
                                date_time : get_date_time,
                                user_selected : user_selected,
                                status_id : get_status_id
                            }
                        })
                        .then((response) => {
                            if (response.data.success == true) {
                                // console.error("save exchange successfull");
                                window.location.href =
                                    "{{ route('admin.sales.orders.index') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }
                        })
                },
                load_product(get_order_id) {
                    // console.log('hihi', this.form.invoice_note);
                    this.product_list = []
                    this.selected_transfer = get_order_id;
                    axios.get("{{ route('admin.sales.orders.show_detail_order') }}", {
                            params: {
                                order_id: get_order_id
                            }
                        })
                        .then(response => {
                            this.product_list = response.data.order_product;
                            this.form.product_list = response.data.order_product;
                            this.form.order_money = response.data.order_money;
                            this.canCancel = response.data.canCancel;
                            this.canRefund = response.data.canRefund;
                            this.canInvoice = response.data.canInvoice;
                            console.log('response',response.data.order_product);
                            // console.error(this.product_list);

                            this.price_total = 0;
                        });
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
                        this.arrow = this.arrow === 'custom-arrow-icon-down' ? 'custom-arrow-icon-up' :
                            'custom-arrow-icon-down';
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
