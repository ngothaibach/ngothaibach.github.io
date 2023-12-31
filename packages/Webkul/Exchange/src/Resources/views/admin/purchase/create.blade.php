@extends('admin::layouts.content')

@section('page_title')
{{ __('admin::app.vpt.inventory.receipt-notes') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <vpt-receipt-note-form></vpt-receipt-note-form>
@stop

@push('scripts')
    <script type="text/x-template" id="vpt-receipt-note-form-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
            <div class="row" style="margin-top: 20px;">
                <div class="col-8" style="align-self: baseline;">
                    <h2>{{ __('admin::app.vpt.inventory.add-receipt-note') }}</h2>
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
                <div class="col-4">
                    <h2>{{ __('admin::app.vpt.inventory.receipt-note-info') }}</h2>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-5">
                                <select v-model="form.user" name="user" class="form-control" aria-label="User">
                                @foreach ($users as $user)
                                    @if (auth()->guard('admin')->user()->id == $user->id)
                                        <option value="{{ $user->id }}" selected>{{ $user->name }}</option>
                                    @else
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
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
                        <label for="inventory-source" class="form-label">{{ __('admin::app.vpt.inventory.inventory-source') }}</label>
                        <select v-model="form.to_inventory_source" class="form-control" aria-label="{{ __('admin::app.vpt.inventory.inventory-source') }}" name="inventory-source">
                        @foreach ($inventory_sources as $source)
                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="supplierInput" class="form-label">{{ __('admin::app.vpt.inventory.supplier') }}</label>
                        <select v-model="form.supplier" class="form-control" aria-label="{{ __('admin::app.vpt.inventory.supplier') }}" name="supplierInput">
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.receipt-note-code') }}</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="{{ __('admin::app.vpt.inventory.receipt-note-code') }}">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.order-code') }}</label>
                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="{{ __('admin::app.vpt.inventory.order-code') }}">
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.status') }}</label>
                        <input v-model="form.status" type="text" disable=true class="form-control" id="exampleFormControlInput1" placeholder="{{ __('admin::app.vpt.inventory.status') }}" readonly>
                    </div>
                    <div class="mb-3">
                    {{ __('admin::app.vpt.inventory.total-of-price') }}: <span v-text="form.price_total"></span> {{ __('admin::app.vpt.inventory.vnd') }}
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.discount') }}</label>
                        <div class="row">
                            <div class="col-3">
                                <input type="text" disable=true class="form-control" id="exampleFormControlInput1">
                            </div>
                            <div class="col-3">
                                <select class="form-control" aria-label="User">
                                    <option value="1">{{ __('admin::app.vpt.inventory.vnd') }}</option>
                                    <option value="2">%</option>
                                </select>
                            </div>
                            <div class="col-3">
                                <span v-text="form.price_total"></span> {{ __('admin::app.vpt.inventory.vnd') }}
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlTextarea1" class="form-label">{{ __('admin::app.vpt.inventory.note') }}</label>
                        <textarea v-model="form.notes" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <button v-on:click="save()" type="button" class="btn btn-primary">{{ __('admin::app.vpt.inventory.temp-save') }}</button>
                        <button type="submit" class="btn btn-success">{{ __('admin::app.vpt.inventory.finish') }}</button>
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
                        supplier: null,
                        to_inventory_source: null,
                        note_code: null,
                        order_code: null,
                        status: "{{ __('admin::app.vpt.inventory.temp-save') }}",
                        note: ""
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
                },
                remove_product: function (item) {
                    this.form.added_products.splice(this.form.added_products.indexOf(item), 1);
                    this.form.price_total = parseInt(this.form.price_total) - parseInt(item.price);
                },
                update_price: function() {
                    this.form.price_total = 0;
                    for(let i = 0;i < this.form.added_products.length; i++) {
                        this.form.price_total += parseInt(this.form.added_products[i].qty) * parseInt(this.form.added_products[i].price);
                    }
                },
                save () {
                    this.form.post("{{ route('admin.exchange.store') }}")
                        .then(( response ) => {

                            // var attr = document.getElementById("text");
                            // attr.innerHTML = response.data.message;
                            console.error(response);
                            if (response.data.success == true) {
                                console.error("save exchange successfull");
                                window.location.href = "{{ route('admin.exchange.purchase-order.list') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }


                        })
                },
                submit: function () {
                    axios.get("{{ route('admin.exchange.store') }}", { params: { form_data: this.form_data } })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                }
            }
        });
    </script>
@endpush