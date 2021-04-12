@extends('admin::layouts.master_full')

@section('page_title')
Đặt hàng
@stop
<link href="/css/app.css" rel="stylesheet">
{{-- <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}"> --}}
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                        <div class="row_new1">
                            <i class="fa fa-search" style="position: absolute;"></i>
                            <input type="text" class="w3-input" v-model="keywords_customer" id="input_customer" placeholder="Tìm khách hàng" style="padding-left:20px"  onClick="this.select();">
                            <i class="fa fa-plus" style="position: absolute; right: 17px; "></i>
                                {{-- <select v-model="form.customer" name="customer" class="w3-input">
                                    @foreach ($customers as $customer)
                                        <option value="{{ $customer->id }}" >{{ $customer->first_name }} {{ $customer->last_name }}</option>
                                    @endforeach
                                </select> --}}
                            </div>
                        <div>
                            <ul class="list-group" v-if="list_customer_result.length > 0">
                                <li class="list-group-item" v-for="result in list_customer_result" :key="result.id"  v-on:click="add_customer(result)" >
                                    <span v-text="'ID: ' + result.id + ' - ' +result.first_name + ' ' + result.last_name + ' - ' + result.phone"></span>
                                </li>
                            </ul>
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
                    results: [],
                    keywords_customer: null,
                    list_customer_result: [],
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
                        list_customer: {!! ($customers) !!},
                    }),
                };
            },
            watch: {
                keywords(after, before) {
                    this.fetch();
                },
                keywords_customer(after, before){
                    this.search_customer();
                }
            },
            methods: {
                fetch() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", { params: { key: this.keywords } })
                    .then(response => this.results = response.data)
                    .catch(error => {});
                    // console.error(this.results);
                },
                search_customer: function() {
                    // var keySearch = document.getElementById('input_customer').value;
                    var keySearch = this.keywords_customer;
                    if (keySearch != '') {
                        toSearch = this.stringToASCII(keySearch.toLowerCase());
                        var lstCustomer = this.form.list_customer;
                        var lstResult = [];
                        for (var i = 0; i < lstCustomer.length; i++) {
                            for (var key in lstCustomer[i]) {
                                if (this.stringToASCII(this.xoa_dau(lstCustomer[i][key] != null ? lstCustomer[i][key].toString() : '').toLowerCase().trim()).indexOf(toSearch) != -1) {
                                    if (!this.itemExists(lstResult, lstCustomer[i])) lstResult.push(lstCustomer[i]);
                                }
                            }
                        }
                        this.list_customer_result = lstResult;
                    } else {
                        this.list_customer_result = [];
                    }
                    
                },
                add_customer: function(result) {
                    this.form.customer = result.id;
                    this.keywords_customer = result.first_name + ' ' + result.last_name;
                    this.list_customer_result = [];
                },
                stringToASCII: function(str){
                    try {
                        return str.replace(/[àáảãạâầấẩẫậăằắẳẵặ]/g, 'a')
                        .replace(/[èéẻẽẹêềếểễệ]/g, 'e')
                        .replace(/[đ]/g, 'd')
                        .replace(/[ìíỉĩị]/g, 'i')
                        .replace(/[òóỏõọôồốổỗộơờớởỡợ]/g, 'o')
                        .replace(/[ùúủũụưừứửữự]/g, 'u')
                        .replace(/[ỳýỷỹỵ]/g, 'y')
                        .replace(/\s/g, '')
                    } catch {
                        return 'no result'
                    }
                },
                xoa_dau: function(str){
                    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
                    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
                    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
                    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
                    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
                    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
                    str = str.replace(/đ/g, "d");
                    str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
                    str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
                    str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
                    str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
                    str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
                    str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
                    str = str.replace(/Đ/g, "D");
                    return str;
                },
                itemExists: function(haystack, needle){
                    for (var i = 0; i < haystack.length; i++) if (this.compareObjects(haystack[i], needle)) return true;
                    return false;
                },
                compareObjects: function(o1, o2) {
                    var k = '';
                    for (k in o1) if (o1[k] != o2[k]) return false;
                    for (k in o2) if (o1[k] != o2[k]) return false;
                    return true;
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
                    if(this.form.price_must_paid != 0) {
                        if(this.form.customer != "") {
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
                            alert('Khách hàng không tồn tại trong danh sách khách hàng');
                        }
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