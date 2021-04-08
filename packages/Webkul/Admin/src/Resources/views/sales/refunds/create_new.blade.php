@extends('admin::layouts.master_full')

@section('page_title')
Trả hàng
@stop
<link href="/css/app.css" rel="stylesheet">
{{-- <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}"> --}}
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<script src="/js/app.js"></script>
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
@section('content-wrapper')
    <refund-items></refund-items>
@stop

@push('scripts')
    <script type="text/x-template" id="refund-items-template">
        <form action="#" class="form newtopic" @submit.prevent="save">    
            <div class="row" style="margin-top: 5px;">
                <div class="col-9" style="align-self: baseline;">
                    <h3>Hàng trả</h3>
                    <div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th v-for="table_header in table_headers" class="grid_head">
                                        <p v-text="table_header"></p>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td style="padding-top: 23px;">{{ Webkul\Product\Helpers\ProductType::hasVariants($item->type) ? $item->child->sku : $item->sku }}</td>
                                    <td style="padding-top: 23px;">{{ $item->name }}</td>
                                    <td style="padding-top: 23px;">{{ $item->base_price }}</td>
                                    <td style="padding-top: 23px;">{{ $item->qty_ordered }}</td>
                                    <td style="padding-top: 23px;">{{ $item->base_total  }}</td>
                                    <td>
                                        <div class="control-group" :class="[errors.has('refund[items][{{ $item->id }}]') ? 'has-error' : '']">
                                            <input type="text" v-validate="'required|numeric|min:0'" v-on:change="updateQty" onClick="this.select();" 
                                                class="w3-input" style="text-align: right; width: 60px;" id="refund[items][{{ $item->id }}]" name="refund[items][{{ $item->id }}]" 
                                                v-model="refund.items[{{ $item->id }}]" data-vv-as="&quot;{{ __('admin::app.sales.refunds.qty-to-refund') }}&quot;"
                                            />
    
                                            <span class="control-error" v-if="errors.has('refund[items][{{ $item->id }}]')">
                                                @verbatim
                                                    {{ errors.first('refund[items][<?php echo $item->id ?>]') }}
                                                @endverbatim
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="padding-top: 100px">
                        <h3>Hàng đổi</h3>
                        <input class="form-control" type="text" v-model="keywords">
                        <ul class="list-group" v-if="results.length > 0">
                            <li v-if="result.name.length" class="list-group-item" v-for="result in results" :key="result.id"  v-on:click="add_product(result)">
                                <div class="row">
                                    <div class="col-4">
                                        <img style="width: 60xp; height: 60px;" :src="'/cache/small/' + result.featured_image"/>
                                    </div>
                                    <div class="col-8">
                                        <span v-text="result.name"></span><br/>
                                        {{ __('admin::app.vpt.inventory.price') }}: <span v-text="result.price"></span><br/>
                                        {{ __('admin::app.vpt.inventory.remain') }}: <span v-text="result.qty"></span>
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <table class="table">
                            <thead>
                            <tr>
                                <th v-for="table_header_doi in table_headers_doi" class="grid_head">
                                    <p v-text="table_header_doi"></p>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in form.added_products">
                                <td v-text="item.sku"></td>
                                {{-- <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + item.featured_image"/></td> --}}
                                <td v-text="item.name"></td>
                                <td v-text="item.price"></td>
                                <td>
                                    <input type="text" class="form-control" v-model="item.qty" v-on:change="update_price">
                                </td>
                                <td><button v-on:click="remove_product(item)" type="button" class="btn btn-danger">{{ __('admin::app.vpt.inventory.delete') }}</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-3" style="">
                    <h3>Trả hàng <a href="{{ route('admin.sales.orders.view', $order->id) }}">{{ $order->increment_id }}</a> - {{ $order->customer_full_name }}</h3>
                <div style="">
                    <div class="mb-3">
                        <div class="row_new">
                            Tổng giá gốc hàng mua
                            <div class="col-right1">
                                <span v-text="form.ordered_total_no_sale"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Tổng tiền hàng trả
                            <div class="col-right1">
                                <span v-text="form.refund_total_show"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row_new">
                            Giảm giá
                            <div class="col-right">
                                <input type="text" class="w3-input" id="discount" v-on:change="fn_change_discount" style="text-align: right; width: 150px" onClick="this.select();">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Phí trả hàng
                            <div class="col-right">
                                <input type="text" class="w3-input" id="refund_fee" v-on:change="fn_change_refund_fee" style="text-align: right; width: 150px" placeholder="0" onClick="this.select();">
                            </div> 
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Hoàn thu khác
                            <div class="col-right">
                                <input type="text" class="w3-input" id="collection_diff" v-on:change="fn_change_collection_diff" style="text-align: right; width: 150px" placeholder="0" onClick="this.select();">
                            </div> 
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            <div v-if="form.added_products.length == 0">Cần trả khách</div>
                            <div v-if="form.added_products.length > 0">Tổng tiền trả</div>
                            <div class="col-right1">
                                <b><span v-text="form.money_must_back" style="color:red"></span></b>
                            </div>
                        </div>
                    </div>
                </div>
                    {{-- <div class="mb-3">
                        <div class="row_new">
                            Tiền trả khách
                            <div class="col-right">
                                <input type="text" class="w3-input" id="money_back" v-on:change="fn_change_money_back" placeholder="0" style="text-align: right; width: 150px" onClick="this.select();">
                            </div> 
                        </div>
                    </div> --}}

                    <div v-if="form.added_products.length > 0">
                        <h3>Mua hàng</h3>

                        <div class="mb-3">
                            <div class="row_new">
                                Tổng tiền hàng
                                <div class="col-right1">
                                    <span v-text="form.price_total_show"></span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row_new">
                                Giảm giá
                                <div class="col-right">
                                    <input type="text" class="w3-input" id="discount_orders" placeholder="0" v-on:change="fn_change_discount_orders" style="text-align: right; width: 150px" onClick="this.select();">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row_new">
                                Thu khác
                                <div class="col-right">
                                    <input type="text" class="w3-input" id="collection_diff_orders" v-on:change="fn_change_collection_diff_orders" style="text-align: right; width: 150px" placeholder="0" onClick="this.select();">
                                </div> 
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row_new">
                                Tổng tiền mua
                                <div class="col-right1">
                                    <b><span v-text="form.price_sum_total_show" style="color:red"></span></b>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="row_new">
                                <div v-if="form.price_sum_total > parseInt((form.money_must_back).replace(',','')) ">Khách cần trả</div>
                                <div v-else="">Cần trả khách</div>
                                <div class="col-right1">
                                    <span v-text="form.price_must_paid_show" style="color: green"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text"  placeholder="Ghi chú" class="w3-input" id="note" v-model="form.notes" >
                    </div>

                    <div class="mb-3">
                        <button v-on:click="save()" type="button" class="btn btn-primary" style="width: 100%; height: 60px">Trả hàng</button>
                    </div>
                </div>
            </div>
        </form>
    </script>

    <script>
        Vue.component('refund-items', {
            template: '#refund-items-template',

            inject: ['$validator'],

            data() {
                return {
                    keywords: null,
                    results: [],
                    table_headers_doi: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        // "{{ __('admin::app.vpt.inventory.image') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "Đơn giá",
                        "{{ __('admin::app.vpt.inventory.qty') }}",
                        "{{ __('admin::app.vpt.inventory.delete') }}"
                    ],
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "Đơn giá",
                        "Đã đặt",
                        "Thành tiền",
                        "Trả lại"
                    ],
                    form: new Form({
                        added_products:[],
                        type: 'receipt',
                        notes: "",
                        ordered_total_no_sale: this.numberFormatter({{$order->sub_total}}),
                        refund_total : 0,
                        refund_total_show : '0',
                        discount: {{$order->discount_amount}},
                        refund_fee: 0,
                        collection_diff: {{$order->collection_diff}},
                        money_must_back: this.numberFormatter({{$order->grand_total}}),
                        // money_back: 0,
                        refund: {},
                        //phần mua hàng
                        price_total: 0, // tổng tiền hàng
                        price_total_show: '0',
                        discount_orders: 0, // giảm giá đơn mua 
                        collection_diff_orders: 0, // thu khác đơn mua
                        price_sum_total: 0, // tổng tiền mua
                        price_sum_total_show: '0',
                        price_must_paid: 0, // khách cần trả
                        price_must_paid_show: '0',
                    }),
                    refund: {
                        items: {},
                        summary: null
                    },
                };
            },

            mounted: function() {
                @foreach ($order->items as $item)
                    this.refund.items[{{$item->id}}] = {{ $item->qty_to_refund }};
                @endforeach
                document.getElementById('discount').value = this.numberFormatter({{$order->discount_amount}});
                document.getElementById('collection_diff').value = this.numberFormatter({{$order->collection_diff}});
                this.updateQty();
            },
            watch: {
                keywords(after, before) {
                    this.fetch();
                },
            },
            methods: {
                updateQty: function() {
                    var this_this = this;
                    this.$http.post("{{ route('admin.sales.refunds.update_qty', $order->id) }}", this.refund.items)
                        .then(function(response) {
                            if (! response.data) {
                                window.flashMessages = [{
                                    'type': 'alert-error',
                                    'message': "{{ __('admin::app.sales.refunds.invalid-qty') }}"
                                }];

                                this_this.$root.addFlashMessages()
                            } else {
                                this_this.refund.summary = response.data;
                                this_this.form.refund_total = response.data.subtotal.price;
                                this_this.form.refund_total_show = this_this.numberFormatter(this_this.form.refund_total);
                                this_this.form.money_must_back = this_this.numberFormatter(parseInt(this_this.form.refund_total) - parseInt(this_this.form.discount) + parseInt(this_this.form.collection_diff) - parseInt(this_this.form.refund_fee));
                            }
                        })
                        .catch(function (error) {})
                },
                numberFormatter(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },
                fn_change_refund_fee: function() {
                    var inputRefundFee = document.getElementById('refund_fee').value;
                    if(isNaN(inputRefundFee)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('refund_fee').value = "0";
                    } else {
                        this.form.refund_fee = inputRefundFee;
                        document.getElementById('refund_fee').value = this.numberFormatter(inputRefundFee);
                        this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                    }
                },
                fn_change_discount: function() {
                    var inputDiscount = document.getElementById('discount').value;
                    if(isNaN(inputDiscount)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('discount').value = this.numberFormatter({{$order->discount_amount}});
                    } else {
                        this.form.discount = inputDiscount;
                        document.getElementById('discount').value = this.numberFormatter(inputDiscount);
                        this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                    }
                },
                // fn_change_money_back: function() {
                //     var inputMoneyBack = document.getElementById('money_back').value;
                //     if(isNaN(inputMoneyBack)) {
                //         alert('Bạn phải nhập số');
                //         document.getElementById('money_back').value = '0';
                //     } else {
                //         this.form.money_back = inputMoneyBack;
                //         document.getElementById('money_back').value = this.numberFormatter(inputMoneyBack);
                //     }
                // },
                fn_change_collection_diff: function() {
                    var inputCollectionDiff = document.getElementById('collection_diff').value;
                    if(isNaN(inputCollectionDiff)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('collection_diff').value = this.numberFormatter({{$order->collection_diff}});
                    } else {
                        this.form.collection_diff = inputCollectionDiff;
                        document.getElementById('collection_diff').value = this.numberFormatter(inputCollectionDiff);
                        this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                    }
                },
                save() {
                    this.form.refund = this.refund;
                    this.form.post("{{ route('admin.sales.refunds.store', $order->id) }}")
                    .then((response) => {
                            if (response.data.success == true) {
                                console.error("Trả hàng thành công");
                                window.location.href = "{{ route('admin.sales.refunds.index') }}";
                            } else {
                                console.debug("trả hàng thất bại");
                            }
                        })
                    .catch(function (error) {
                        alert('loi roi: ' + error);
                    });
                },
                fetch() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", { params: { key: this.keywords } })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                },
                add_product: function (result) {
                    added_item = {id:result.id, name:result.name, qty:1, price:result.price, in_stock: 0, featured_image:result.featured_image, sku:result.sku};
                    this.form.added_products.push(added_item);
                    this.results = [];
                    this.form.price_total = parseInt(this.form.price_total) + parseInt(result.price);
                    
                    this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                    this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);

                    this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);

                },
                remove_product: function (item) {
                    this.form.added_products.splice(this.form.added_products.indexOf(item), 1);
                    this.form.price_total = parseInt(this.form.price_total) - (parseInt(item.price) * item.qty);
                    
                    this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                    this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);

                    this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);

                    if(this.form.added_products.length == 0) {
                        this.form.discount_orders = 0;
                        this.form.collection_diff_orders = 0;
                        this.form.price_must_paid = 0;
                    }
                
                },
                update_price: function() {
                    this.form.price_total = 0;
                    this.form.price_sum_total = 0;
                    for(let i = 0;i < this.form.added_products.length; i++) {
                        this.form.price_total += parseInt(this.form.added_products[i].qty) * parseInt(this.form.added_products[i].price);
                    }
                    
                    this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                    this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);

                    this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                },
                fn_change_discount_orders: function() {
                    var input_discount_orders = document.getElementById('discount_orders').value;
                    if(isNaN(input_discount_orders)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('discount_orders').value = '0';
                    } else {
                        this.form.discount_orders = parseInt(input_discount_orders);
                        this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                        this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                        this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                        this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                        document.getElementById('discount_orders').value = this.numberFormatter(input_discount_orders);
                    }
                },
                fn_change_collection_diff_orders: function() {
                    var input_collection_diff_orders = document.getElementById('collection_diff_orders').value;
                    if(isNaN(input_collection_diff_orders)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('collection_diff_orders').value = '0';
                    } else {
                        this.form.collection_diff_orders = parseInt(input_collection_diff_orders);
                        this.form.price_sum_total = (this.form.price_total + this.form.collection_diff_orders - this.form.discount_orders);
                        this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                        this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                        this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                        document.getElementById('collection_diff_orders').value = this.numberFormatter(input_collection_diff_orders);
                    }
                },
            }
        });
    </script>
@endpush