@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.sales.invoices.title') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.sales.invoices.title') }}</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- <div class="page-content">
            @inject('orderInvoicesGrid', 'Webkul\Admin\DataGrids\OrderInvoicesDataGrid')
            {!! $orderInvoicesGrid->render() !!}
        </div> --}}
        <div class="page-content">
            <vpt-list-receipt-notes></vpt-list-receipt-notes>
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
                    <tbody v-for="(item,index) in pageOfItems">
                        <tr :class="[selected_transfer ===  item.id ? 'table-info' : '']" v-on:click="load_product(item.id)">
                            <td v-text="item.id"></td>
                            <td v-text="item.created_at"></td>
                            <td v-text="item.first_name +' '+ item.last_name"></td>
                            <td v-text="parseFloat(item.base_sub_total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+ ' ₫'"></td>
                            <td v-text="parseFloat(item.base_grand_total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+ ' ₫'"></td>
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
                                                        <input type="text" :value="getValue(item.status)" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Ngày tạo</label>
                                                    {{-- <div class="col-sm-8">
                                                        <vuejs-datepicker   placeholder="Chọn ngày"  v-model="item.receipt_date" :disabled="!updatePermission ? true : form.oldListReceip[index].status == 'temporary' ? false : form.oldListReceip[index].status == 'transfering' ? false : true"></vuejs-datepicker>
                                                    </div> --}}
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.created_at" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Khách hàng                                             </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.first_name +' '+ item.last_name" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4" style="align-self: baseline;">
                                            {{-- <div class="mb-3">
                                                <div class="form-group row">
                                                    <label class="col-sm-4 col-form-label">Khách hàng                                             </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.first_name +' '+ item.last_name" class="form-control" disabled>
                                                    </div>
                                                </div>
                                            </div> --}}
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
                                                    <label class="col-sm-4 col-form-label">Id hóa đơn
                                                    </label>
                                                    <div class="col-sm-8">
                                                        <input type="text" v-model="item.id"  class="form-control" disabled>
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
                                                <td v-text="parseFloat(product.base_price).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+ ' ₫'"></td>
                                                <td v-text="product.qty"></td>
                                                <td v-text="parseFloat(product.base_total).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+ ' ₫'"></td>
                                                <td v-text="parseFloat(product.base_tax_amount).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')+ ' ₫'"></td>
                                                <td v-text="getTotal(product.base_total,product.base_tax_amount)"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <table class="sale-summary" >
                                        <tbody>
                                        <tr>
                                            <td>Thành tiền</td>
                                            <td>:</td>
                                            <td class="txtRight" v-text="numberFormatter(parseFloat(item.base_sub_total))"></td>
                                        </tr>
            
                                        <tr>
                                            <td>Thuế</td>
                                            <td>:</td>
                                            <td class="txtRight" v-text="numberFormatter(parseFloat(item.base_tax_amount))"></td>
                                        </tr>
            
                                            <tr  v-if="parseFloat(item.base_discount_amount) > 0">
                                                <td>Khuyến mại</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(item.base_discount_amount))"></td>
                                            </tr>
            
                                            <tr v-if="parseFloat(item.collection_diff) > 0">
                                                <td>Thu khác</td>
                                                <td>:</td>
                                                <td class="txtRight" v-text="numberFormatter(parseFloat(item.collection_diff))"></td>
                                            </tr>
                                        
            
                                        <tr class="bold">
                                            <td>Tổng cộng</td>
                                            <td>:</td>
                                            <td class="txtRight" v-text="numberFormatter(parseFloat(item.base_grand_total))"></td>
                                        </tr>
                                    </tbody>
                                    </table>
                                    {{-- <span class="font-weight-bold">Tổng tiền hàng:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Phí Ship:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Tổng tiền hóa đơn mua:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Tổng tiền hóa đơn trả:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span><br>
                                    <span class="font-weight-bold">Khách cần trả:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span> --}}

                                    <div class="btn-summit-right">
                                        <button type="button" class="btn btn-primary" style="marginRight : 20px;width: 120px;" v-on:click="print_invoices(item.id)" >In hóa đơn</button>
                                        <button type="button" class="btn btn-success" style="width: 70px;" v-on:click="save_invoices(item.order_id)" >Lưu</button>
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
                    commission: 123456,
                     //pagination
                     sort_list: [
                        "id",
                        "created_at",
                        "last_name",
                        "base_sub_total",
                        "base_grand_total"
                    ],
                    currentSortDir: "desc",
                    sortBy: "id",
                    pageOfItems: [],
                    perPage: 10,
                    arrow: "custom-arrow-icon-down",
                    currentArrow : 0,
                    //pagination
                    form: new Form({
                        invoice_note : {!! json_encode($invoice_note) !!},
                        price_total: 0,
                        type: 'receipt',
                        receipt_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
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
                        'Id hóa đơn',
                        "{{ __('admin::app.vpt.inventory.date') }}",
                        'Khách hàng',
                        'Tổng tiền hàng',
                        // "{{ __('admin::app.vpt.inventory.total-of-price') }}",
                        'Tổng sau giảm giá',
                    ],
                    product_table_headers: [
                        "Mã sản phẩm",
                        "Tên sản phẩm",
                        "Đơn giá",
                        "Số lượng",
                        "Thành tiền",
                        "Tiền thuế",
                        "Tổng cộng"
                    ],
                    product_list: null,
                    selected_transfer: null,
                    price_total: null
                };
            },
            watch: {},
            methods: {
                getValue (val) {
                    if (val == 'processing') {
                        return 'Đang xử lý';
                    } else if (val == 'completed') {
                        return  'Đã hoàn thành';
                    } else if (val == "canceled") {
                        return  'Đã hủy';
                    } else if (val == "closed") {
                        return  'Đã đóng';
                    } else if (val == "pending") {
                        return  'Đang chờ';
                    } else if (val == "pending_payment") {
                        return  'Chờ thanh toán';
                    } else if (val == "fraud") {
                        return  'Gian lận';
                    }
                },
                getMethod (val) {
                    if (val == 'cashondelivery') {
                        return 'COD';
                    } else {
                        return  'Visa';
                    } 
                },
                getTotal (val,val1) {
                   return this.numberFormatter(parseFloat(val+val1) + ' ₫');
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
                print_invoices(get_order_id){
                    // console.log(str.concat("{{ ").concat(linkBefore).concat(" }}"));
                    let link = "{{route('admin.sales.invoices.print_invoices')}}";
                    window.location.href=(link +'/'+ get_order_id);
                },
                save_invoices(get_order_id) {
                    var get_comment = document.getElementById('commentNote').value;
                    axios.get("{{ route('admin.sales.invoices.update_notes') }}", {
                            params: {
                                order_id: get_order_id,
                                comment_content : get_comment
                            }
                        })
                        .then((response) => {
                            if (response.data.success == true) {
                                // console.error("save exchange successfull");
                                window.location.href =
                                    "{{ route('admin.sales.invoices.index') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }
                        })
                },
                load_product(get_invoice_id) {
                    console.log('hihi',this.form.invoice_note);
                    this.product_list = []
                    if(this.selected_transfer == get_invoice_id){
                        this.selected_transfer = null
                    }else{
                        this.selected_transfer = get_invoice_id
                        axios.get("{{ route('admin.sales.invoices.show_detail_invoice') }}", {
                                params: {
                                    invoice_id: get_invoice_id
                                }
                            })
                            .then(response => {
                                this.product_list = response.data.invoice_product;
                                this.form.product_list = response.data.invoice_product;
                                console.log('response',response);
                                console.error(this.product_list);

                                this.price_total = 0;
                            });
                    }
                },
                //pagination
                onChangePage(pageOfItems) {
                    // update page of items
                    this.pageOfItems = pageOfItems;
                },    
                // thêm nếu muốn sort
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
			    // thêm nếu muốn sort
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


{{-- 
@push('scripts')
    @include('admin::export.export', ['gridName' => $orderInvoicesGrid])
@endpush --}}