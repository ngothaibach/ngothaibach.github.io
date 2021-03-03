@extends('admin::layouts.master')

@section('page_title')
Đặt hàng
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content-wrapper')
    <vpt-receipt-note-form></vpt-receipt-note-form>
@stop

@push('scripts')
    <script type="text/x-template" id="vpt-receipt-note-form-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
            <div class="row" style="margin-top: 20px;">
                <div class="col-9" style="align-self: baseline;">
                    <h2 style="padding-left: 5px">Đặt hàng</h2>
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
                                <td v-text="item.id"></td>
                                <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + item.featured_image"/></td>
                                <td v-text="item.name"></td>
                                <td v-text="item.price"></td>
                                <td>
                                    <input type="text" class="form-control" v-model="item.qty" v-on:change="update_price">
                                </td>
                                <td v-text="item.in_stock"></td>
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
                            <div class="col-5">
                                <select v-model="form.user" name="user" class="form-control" aria-label="User">
                                @foreach ($users as $user)
                                    @if (auth()->guard('admin')->user()->id == $user->id)
                                        <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                    @else
                                        <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                    @endif
                                @endforeach
                                </select>
                            </div>
                            <div class="col-7">
                                <vuejs-datepicker v-model="form.created_date"></vuejs-datepicker>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput2" class="form-label">Khách hàng</label>
                        <div class="row">
                            <div class="col-12">
                                <select v-model="form.customer" name="customer" class="form-control" aria-label="User">
                                @foreach ($customers as $customer)
                                    <!--@if (auth()->guard('admin')->user()->id == $customer->id)
                                        <option value="{{ $customer->id }}" selected>{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                    @else -->
                                        <option value="{{ $customer->id }}" >{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                    <!-- @endif -->
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        {{ __('admin::app.vpt.inventory.total-of-price') }}: <span v-text="form.price_total"></span>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.discount') }}</label>
                        <div class="row">
                            <div class="col-7">
                                <input type="number" disable=true class="form-control" v-model="form.discount" id="exampleFormControlInput1" v-on:change="update_discount">
                            </div>
                            <div class="col-4">
                                <select class="form-control" aria-label="User" v-model="form.discount_type" v-on:change="update_discount_type" >
                                    <option value="1">{{ __('admin::app.vpt.inventory.vnd') }}</option>
                                    <option value="2">%</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Thu khác:</label>
                        <input type="number" class="form-control" id="collection_diff" v-model="form.collection_diff" v-on:change="fn_update_collection_diff">
                    </div>

                    <div class="mb-3">
                        Khách cần trả:
                        <span v-text="form.price_must_paid"></span> 
                    </div>

                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">Khách thanh toán </label>
                        <input type="number" class="form-control" id="customer_paid" v-model="form.customer_paid" v-on:change="fn_update_customer_paid">
                    </div>

                    <div class="mb-3">
                        Tiền thừa trả khách: <span v-text="form.customer_remain"></span> 
                    </div>
                    
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">{{ __('admin::app.vpt.inventory.note') }}</label>
                        <textarea v-model="form.notes" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <!--<button v-on:click="save()" type="button" class="btn btn-primary">In</button>
                        <button type="submit" class="btn btn-success">Đặt hàng</button>
                        <button type="button" class="btn btn-primary">In</button>-->
                        <button v-on:click="save()" type="button" class="btn btn-primary">Đặt hàng</button>
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
                    results: [],
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        "{{ __('admin::app.vpt.inventory.image') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "{{ __('admin::app.vpt.inventory.price') }}",
                        "{{ __('admin::app.vpt.inventory.qty') }}",
                        "{{ __('admin::app.vpt.inventory.remain') }}",
                        "{{ __('admin::app.vpt.inventory.delete') }}"
                    ],
                    form: new Form({
                        added_products:[],
                        price_total: 0,
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
                        customer_paid: 0,
                        customer_remain: 0,
                        discount: 0,
                        discount_type: 1,
                    }),
                };
            },
            watch: {
                keywords(after, before) {
                    this.fetch();
                }
            },
            methods: {
                fetch() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", { params: { key: this.keywords } })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                    console.error(this.results);
                },
                add_product: function (result) {
                    added_item = {id:result.id, name:result.name, qty:1, price:result.price, in_stock: 0, featured_image:result.featured_image};
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
                },
                update_discount_type: function() {
                    this.form.discount = 0;
                    this.form.price_must_paid = this.form.price_total ;
                    this.form.collection_diff = 0;
                },
                update_discount: function() {
                    if(this.form.discount_type == 1){
                        this.form.price_must_paid = this.form.price_total - this.form.discount ;
                    } else {
                        this.form.price_must_paid = this.form.price_total - ((this.form.discount * this.form.price_total)/100) ;
                    }
                },
                fn_update_customer_paid: function () {
                    this.form.customer_remain = this.form.customer_paid - this.form.price_must_paid ;
                },
                fn_update_collection_diff: function(){
                    if(this.form.collection_diff == ""){
                        this.form.collection_diff = 0;
                    } else {
                        this.form.price_must_paid = parseInt(this.form.price_must_paid) + parseInt(this.form.collection_diff); 
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