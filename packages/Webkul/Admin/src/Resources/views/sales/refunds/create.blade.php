@extends('admin::layouts.content')

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
@section('content')
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
                        
                            <product-live-search
                            :url='"{{ route("admin.catalog.products.live-search-products") }}"'
                            @added_product_changed="onAdd"
                            @price_total_changed="onPrice"
                            ></product-live-search>
                        
                    </div>
                </div>

                <div class="col-3" style="position: absolute; right: -12px; top: 2px;">
                    <h2>Trả hàng <a href="{{ route('admin.sales.orders.view', $order->id) }}">{{ $order->increment_id }}</a> - {{ $order->customer_full_name }}</h2>
                <div>
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
                <div v-if="form.added_products.length > 0" style="">
                    <h2>Mua hàng</h2>
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
                        if(inputRefundFee == '') {
                            this.form.refund_fee = 0;
                            document.getElementById('refund_fee').value = 0;
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff));
                        } else {
                            this.form.refund_fee = inputRefundFee;
                            document.getElementById('refund_fee').value = this.numberFormatter(inputRefundFee);
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                        }
                    }
                },
                fn_change_discount: function() {
                    var inputDiscount = document.getElementById('discount').value;
                    if(isNaN(inputDiscount)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('discount').value = this.numberFormatter({{$order->discount_amount}});
                    } else {
                        if(inputDiscount == '') {
                            this.form.discount = 0;
                            document.getElementById('discount').value = 0;
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                        } else {
                            this.form.discount = inputDiscount;
                            document.getElementById('discount').value = this.numberFormatter(inputDiscount);
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                        }
                        
                    }
                },
                fn_change_collection_diff: function() {
                    var inputCollectionDiff = document.getElementById('collection_diff').value;
                    if(isNaN(inputCollectionDiff)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('collection_diff').value = this.numberFormatter({{$order->collection_diff}});
                    } else {
                        if (inputCollectionDiff == '') {
                            this.form.collection_diff = 0;
                            document.getElementById('collection_diff').value = 0;
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) - parseInt(this.form.refund_fee));
                        } else {
                            this.form.collection_diff = inputCollectionDiff;
                            document.getElementById('collection_diff').value = this.numberFormatter(inputCollectionDiff);
                            this.form.money_must_back = this.numberFormatter(parseInt(this.form.refund_total) - parseInt(this.form.discount) + parseInt(this.form.collection_diff) - parseInt(this.form.refund_fee));
                        }
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
                onAdd(added_item){
                    this.form.added_products = added_item;
                    if(this.form.added_products.length == 0) {
                        this.form.discount_orders = 0;
                        this.form.collection_diff_orders = 0;
                        this.form.price_must_paid = 0;
                    }
                },
                onPrice(price_total){
                    this.form.price_total = price_total;
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
                        if (input_discount_orders == '') {
                            this.form.discount_orders = 0;
                            this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                            this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                            this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                            this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                            document.getElementById('discount_orders').value = 0;
                        } else {
                            this.form.discount_orders = parseInt(input_discount_orders);
                            this.form.price_sum_total = this.form.price_total - this.form.discount_orders + this.form.collection_diff_orders;
                            this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                            this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                            this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                            document.getElementById('discount_orders').value = this.numberFormatter(input_discount_orders);
                        }
                    }
                },
                fn_change_collection_diff_orders: function() {
                    var input_collection_diff_orders = document.getElementById('collection_diff_orders').value;
                    if(isNaN(input_collection_diff_orders)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('collection_diff_orders').value = '0';
                    } else {
                        if(input_collection_diff_orders == '') {
                            this.form.collection_diff_orders = 0;
                            this.form.price_sum_total = (this.form.price_total + this.form.collection_diff_orders - this.form.discount_orders);
                            this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                            this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                            this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                            document.getElementById('collection_diff_orders').value = 0;
                        } else {
                            this.form.collection_diff_orders = parseInt(input_collection_diff_orders);
                            this.form.price_sum_total = (this.form.price_total + this.form.collection_diff_orders - this.form.discount_orders);
                            this.form.price_sum_total_show = this.numberFormatter(this.form.price_sum_total);

                            this.form.price_must_paid = Math.abs(this.form.price_sum_total - parseInt((this.form.money_must_back).replace(',','')));
                            this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                            document.getElementById('collection_diff_orders').value = this.numberFormatter(input_collection_diff_orders);
                        }
                        
                    }
                },
            }
        });
    </script>
@endpush