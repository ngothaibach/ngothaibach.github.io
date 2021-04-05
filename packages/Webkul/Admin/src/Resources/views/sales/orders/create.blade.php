@extends('admin::layouts.master_full')

@section('page_title')
Đặt hàng
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
    <vpt-receipt-note-form></vpt-receipt-note-form>
@stop

@push('scripts')
    <script type="text/x-template" id="vpt-receipt-note-form-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
            <div class="row" style="margin-top: 20px;">
                <div class="col-9" style="align-self: baseline;">
                    <h2>Đặt hàng</h2>
                    <div>
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
                                <th v-for="table_header in table_headers" class="grid_head">
                                    <p v-text="table_header"></p>
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-if="form.added_products.length === 0">
                                <td>{{ __('admin::app.vpt.inventory.no-data') }}</td>
                            </tr>
                            <tr v-else v-for="item in form.added_products">
                                {{-- <td v-text="item.id"></td> --}}
                                <td v-text="item.sku"></td>
                                <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + item.featured_image"/></td>
                                <td v-text="item.name"></td>
                                <td v-text="item.price"></td>
                                <td>
                                    <input type="text" class="form-control" v-model="item.qty" v-on:change="update_price">
                                </td>
                                {{-- <td v-text="item.in_stock"></td> --}}
                                <td><button v-on:click="remove_product(item)" type="button" class="btn btn-danger">{{ __('admin::app.vpt.inventory.delete') }}</button></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-3">
                    <h2>Thông tin đặt hàng</h2>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-6">
                                <vuejs-datepicker v-model="form.created_date"></vuejs-datepicker>
                            </div>
                            <div class="col-right">
                                <select v-model="form.user" name="user" class="w3-input" aria-label="User" style="width: 150px">
                                    @foreach ($users as $user)
                                        @if (auth()->guard('admin')->user()->id == $user->id)
                                            <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                        @else
                                            <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row_new">
                            Khách hàng
                            <div class="col-right">
                                <select v-model="form.customer" name="customer" class="w3-input" aria-label="Customer" style="width: 150px">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" >{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="row_new">
                            {{ __('admin::app.vpt.inventory.total-of-price') }}
                            <div class="col-right1">
                                <span v-text="form.price_total_show"></span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row_new">
                            Giảm giá
                            <div class="col-4">
                                <select class="w3-input" aria-label="User" v-model="form.discount_type" v-on:change="update_discount_type" >
                                    <option value="1">{{ __('admin::app.vpt.inventory.vnd') }}</option>
                                    <option value="2">%</option>
                                </select>
                            </div>
                            <div class="col-right">
                                <input type="text" class="w3-input" id="inputDiscount" v-on:change="update_discount" style="text-align: right; width: 150px" onClick="this.select();">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row_new">
                            Thu khác
                            <div class="col-right">
                                <input type="text" class="w3-input" id="collection_diff" v-on:change="fn_update_collection_diff" style="text-align: right; width: 150px" placeholder="0" onClick="this.select();">
                            </div> 
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Khách cần trả
                            <div class="col-right1">
                                <b><span v-text="form.price_must_paid_show" style="color:red"></span></b>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Khách thanh toán
                            <div class="col-right">
                                <input type="text" class="w3-input" id="customer_paid" placeholder="0" v-on:change="fn_update_customer_paid" style="text-align: right; width: 150px" onClick="this.select();">
                            </div> 
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row_new">
                            Tiền thừa trả khách
                            <div class="col-right1">
                                <span v-text="form.customer_remain_show"></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text"  placeholder="Ghi chú" class="w3-input" id="note" v-model="form.notes" >
                    </div>

                    <div class="mb-3">
                        <button v-on:click="save()" type="button" class="btn btn-primary" style="width: 100%; height: 60px">Đặt hàng</button>
                    </div>
                </div>
            </div>
        </form>
    </script>

    <script>
        Vue.component('vpt-receipt-note-form', {
            template: '#vpt-receipt-note-form-template',
            data() {
                return {
                    keywords: null,
                    // number_formatter: null,
                    results: [],
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        "{{ __('admin::app.vpt.inventory.image') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "{{ __('admin::app.vpt.inventory.price') }}",
                        "{{ __('admin::app.vpt.inventory.qty') }}",
                        // "{{ __('admin::app.vpt.inventory.remain') }}",
                        "{{ __('admin::app.vpt.inventory.delete') }}"
                    ],
                    form: new Form({
                        added_products:[],
                        price_total: 0,
                        price_total_show: '0',
                        type: 'receipt',
                        receipt_date: new Date(),
                        created_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
                        customer: "",
                        supplier: null,
                        to_inventory_source: null,
                        note_code: null,
                        order_code: null,
                        status: "{{ __('admin::app.vpt.inventory.temp-save') }}",
                        notes: "",
                        collection_diff: 0,
                        price_must_paid: 0,
                        price_must_paid_show: '0',
                        customer_paid: 0,
                        customer_remain: 0,
                        customer_remain_show: '0',
                        discount: 0,
                        discount_type: 1,
                    }),
                };
            },
            watch: {
                keywords(after, before) {
                    this.fetch();
                },
            },
            methods: {
                fetch() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", { params: { key: this.keywords } })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                    // console.error(this.results);
                },
                add_product: function (result) {
                    added_item = {id:result.id, name:result.name, qty:1, price:result.price, in_stock: 0, featured_image:result.featured_image, sku:result.sku};
                    this.form.added_products.push(added_item);
                    this.results = [];
                    this.form.price_total = parseInt(this.form.price_total) + parseInt(result.price);
                    if (this.form.discount != 0) {
                        if  (this.form.discount_type == 1) {
                            this.form.price_must_paid = this.form.price_total - this.form.discount ;
                        } else {
                            this.form.price_must_paid = this.form.price_total - ((this.form.discount * this.form.price_total)/100);
                        }
                    } else {
                        this.form.price_must_paid = this.form.price_total ;
                    }
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);
                    if (this.form.customer_paid != 0) {
                        this.form.customer_remain = parseInt(this.form.customer_paid) - parseInt(this.form.price_must_paid);
                        this.form.customer_remain_show = this.numberFormatter(this.form.customer_remain);
                    }
                },
                remove_product: function (item) {
                    this.form.added_products.splice(this.form.added_products.indexOf(item), 1);
                    this.form.price_total = parseInt(this.form.price_total) - (parseInt(item.price) * item.qty);
                    if (this.form.discount != 0) {
                        if  (this.form.discount_type == 1) {
                            this.form.price_must_paid = this.form.price_total - this.form.discount ;
                        } else {
                            this.form.price_must_paid = this.form.price_total - ((this.form.discount * this.form.price_total)/100) ;
                        }
                    } else {
                        this.form.price_must_paid = this.form.price_total ;
                    }
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);
                    if (this.form.customer_paid != 0) {
                        this.form.customer_remain = parseInt(this.form.customer_paid) - parseInt(this.form.price_must_paid);
                        this.form.customer_remain_show = this.numberFormatter(this.form.customer_remain);
                    }
                },
                update_price: function() {
                    this.form.price_total = 0;
                    this.form.price_must_paid = 0;
                    for(let i = 0;i < this.form.added_products.length; i++) {
                        this.form.price_total += parseInt(this.form.added_products[i].qty) * parseInt(this.form.added_products[i].price);
                    }
                    if (this.form.discount != 0) {
                        if  (this.form.discount_type == 1) {
                            this.form.price_must_paid = this.form.price_total - this.form.discount ;
                        } else {
                            this.form.price_must_paid = this.form.price_total - ((this.form.discount * this.form.price_total)/100) ;
                        }
                    } else {
                        this.form.price_must_paid = this.form.price_total ;
                    }
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                    this.form.price_total_show = this.numberFormatter(this.form.price_total);
                },
                update_discount_type: function() {
                    this.form.discount = 0;
                    this.form.price_must_paid = this.form.price_total ;
                    this.form.collection_diff = 0;
                    document.getElementById('collection_diff').value = "0";
                    this.form.customer_paid = 0;
                    this.form.customer_remain = 0;
                    document.getElementById('customer_paid').value = "0";
                    this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                    this.form.customer_remain_show = '0';
                    document.getElementById('inputDiscount').value = "0";
                },
                update_discount: function() {
                    var input_discount = document.getElementById('inputDiscount').value;
                    if(isNaN(input_discount)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('inputDiscount').value = '0';
                    } else {
                        this.form.discount = parseInt(input_discount);
                        if(this.form.discount_type == 1){
                            this.form.price_must_paid = this.form.price_total - this.form.discount ;
                            if (this.form.customer_paid != 0){
                                this.form.customer_remain = parseInt(this.form.customer_remain) + parseInt(this.form.discount); 
                            }
                        } else {
                            this.form.price_must_paid = this.form.price_total - ((this.form.discount * this.form.price_total)/100) ;
                            if (this.form.customer_paid != 0){
                                this.form.customer_remain = parseInt(this.form.customer_remain) + parseInt((this.form.discount * this.form.price_total)/100); 
                            }
                        }
                            this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                            this.form.customer_remain_show = this.numberFormatter(this.form.customer_remain);
                            document.getElementById('inputDiscount').value = this.numberFormatter(input_discount);
                    }
                },
                fn_update_customer_paid: function () {
                    var inputCustomer_paid = document.getElementById('customer_paid').value;
                    if(isNaN(inputCustomer_paid)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('customer_paid').value = "";
                    } else {
                        if(inputCustomer_paid == 0) {
                            this.form.customer_remain = 0;
                            this.form.customer_paid = 0;
                        } else {
                            this.form.customer_remain = inputCustomer_paid - this.form.price_must_paid ;
                            this.form.customer_paid = inputCustomer_paid;
                        }
                        document.getElementById('customer_paid').value = this.numberFormatter(inputCustomer_paid);
                        this.form.customer_remain_show = this.numberFormatter(this.form.customer_remain);
                    }
                },
                numberFormatter(num) {
                    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                },
                fn_update_collection_diff: function(){
                    var inputValue = document.getElementById('collection_diff').value;
                    if(isNaN(inputValue)) {
                        alert('Bạn phải nhập số');
                        document.getElementById('collection_diff').value = "";
                    } else {
                        if(inputValue == "" || inputValue == 0){
                        this.form.price_must_paid = parseInt(this.form.price_must_paid) - parseInt(this.form.collection_diff);
                        this.form.collection_diff = 0;
                        } else {
                            this.form.price_must_paid = parseInt(this.form.price_must_paid) - parseInt(this.form.collection_diff) + parseInt(inputValue); 
                            this.form.collection_diff = inputValue;
                        }
                        if(this.form.customer_paid != 0) {
                            this.form.customer_remain = this.form.customer_paid - this.form.price_must_paid;
                        }
                        document.getElementById('collection_diff').value = this.numberFormatter(inputValue);
                        this.form.price_must_paid_show = this.numberFormatter(this.form.price_must_paid);
                        this.form.customer_remain_show = this.numberFormatter(this.form.customer_remain);
                    }
                },
                save () {
                    if(this.form.price_must_paid != 0 && this.form.customer != "") {
                        this.form.post("{{ route('admin.sales.orders.store') }}")
                        .then(( response ) => {
                            console.error(response);
                            if (response.data.success == true) {
                                console.error("Tạo thành công đơn hàng");
                                window.location.href = "{{ route('admin.sales.orders.index') }}";
                            } else {
                                console.debug("Tạo đơn hàng thất bại");
                            }
                        })
                    } else {
                        alert('Bạn chưa nhập đủ thông tin ');
                    }
                    
                },
                submit: function () {
                    axios.get("{{ route('admin.sales.orders.store') }}", { params: { form_data: this.form_data } })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                }
            }
        });
    </script>
@endpush