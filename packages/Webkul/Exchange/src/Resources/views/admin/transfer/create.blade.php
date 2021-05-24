@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.vpt.inventory.transfer') }}
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
                                <div class="col-lg-8 col-md-8 col-sm-12 product-live-search" style="align-self: baseline;">
                                    <h2>Tạo phiếu chuyển hàng</h2>
                                    <div>
                                    <product-live-search
                                        :url='"{{ route("admin.catalog.products.live-search-products") }}"'
                                        @added_product_changed="onAdd"
                                        @price_total_changed="onPrice"
                                        @total_of_qty_changed="onQty"
                                        ></product-live-search>  
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-12">
                                    <h2>{{ __('admin::app.vpt.inventory.transfer-info') }}</h2>
                                    <div class="mb-3">
                                        <div class="row">
                                            <div class="col-5">
                                                <select v-model="form.user" name="user" class="form-control" aria-label="User">
                                                @foreach ($users as $user)       
                                                        <option value="{{ $user->id }}">{{ $user->name }}</option>     
                                                @endforeach
                                                </select>
                                            </div>
                                            <div class="col-7">
                                                <input type="date" class="control" name="transfer_date" v-model="form.transfer_date" v-validate="">
                                                <span class="control-error" v-if="errors.has('transfer_date')">@{{ errors.first('transfer_date') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="from-inventory-source" class="form-label">{{ __('admin::app.vpt.inventory.from-inventory-source') }}</label>
                                        <select v-model="form.from_inventory_source" class="form-control" aria-label="{{ __('admin::app.vpt.inventory.inventory-source') }}" name="from-inventory-source">
                                        @foreach ($inventory_sources as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="inventory-source" class="form-label">{{ __('admin::app.vpt.inventory.to-inventory-source') }}</label>
                                        <select v-model="form.to_inventory_source" class="form-control" aria-label="{{ __('admin::app.vpt.inventory.inventory-source') }}" name="to-inventory-source">
                                        @foreach ($inventory_sources as $source)
                                            <option value="{{ $source->id }}">{{ $source->name }}</option>
                                        @endforeach
                                        </select>
                                    </div>                    
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.transfer-code') }}</label>
                                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder='{{"MCH".$maxId++}}' disabled>
                                    </div>                    
                                    <div class="mb-3">
                                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.status') }}</label>
                                        <input v-model="form.status" type="text" disable=true class="form-control" id="exampleFormControlInput1" placeholder="{{ __('admin::app.vpt.inventory.status') }}" readonly>
                                    </div>
                                    <div class="mb-3">
                                    {{ __('admin::app.vpt.inventory.total-of-qty') }}: <span v-text="form.total_of_qty"></span>
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
            el: "#app",
            data() {
                return {
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
                        added_products: [],
                        price_total: 0,
                        total_of_qty: 0,
                        type: 'transfer',
                        transfer_date: null,
                        user: {!! json_encode(auth()->guard('admin')->user()->id) !!},
                        supplier: null,
                        from_inventory_source: null,
                        to_inventory_source: null,
                        note_code: null,
                        order_code: null,
                        status: "{{ __('admin::app.vpt.inventory.temp-save') }}",
                        note: ""
                    }),
                    channel_name: '',
                    channel_fields: [],
                    channel_entries: [],
                    parse_header: [],
                    parse_csv: [],
                    sortOrders: {},
                    sortKey: '',
                };
            },
            methods: {
                save() {
                    this.form.post("{{ route('admin.exchange.store') }}")
                        .then((response) => {

                            // var attr = document.getElementById("text");
                            // attr.innerHTML = response.data.message;
                            // console.error(response);
                            if (response.data.success == true) {
                                // console.error("save exchange successfull");
                                window.location.href = "{{ route('admin.exchange.transfer.list') }}";
                            } else {
                                console.debug("save exchange NOT successfull");
                            }


                        })
                },
                submit: function() {
                    axios.get("{{ route('admin.exchange.store') }}", {
                            params: {
                                form_data: this.form_data
                            }
                        })
                        .then(response => this.results = response.data)
                        .catch(error => {});
                },
                sortBy: function(key) {
                    var vm = this
                    vm.sortKey = key
                    vm.sortOrders[key] = vm.sortOrders[key] * -1
                },
                onAdd(added_item){
                    this.form.added_products = added_item;
                },
                onPrice(price_total){
                    this.form.price_total = price_total;
                },
                onQty(total_of_qty){
                    this.form.total_of_qty = total_of_qty;
                },
            },
            mounted(){
                    month = new Date().getMonth()+1;
                    if(month <10){
                        this.form.transfer_date =  new Date().getFullYear()+'-'+'0'+(new Date().getMonth()+1)+'-'+new Date().getDate()
                    }else{
                        this.form.transfer_date =  new Date().getFullYear()+'-'+(new Date().getMonth()+1)+'-'+new Date().getDate()
                    }
            }
        });

    </script>
@endpush
