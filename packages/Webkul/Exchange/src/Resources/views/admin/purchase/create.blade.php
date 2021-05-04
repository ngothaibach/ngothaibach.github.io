@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.vpt.inventory.receipt-notes') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="/js/app.js"></script>
@section('content')
    <vpt-receipt-note-form></vpt-receipt-note-form>
@stop

{{-- @push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" />
@endpush

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/2.5.17-beta.0/vue.js"></script>
@endpush --}}

@push('scripts')
    <script type="text/x-template" id="vpt-receipt-note-form-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
            <div class="row" style="margin-top: 20px;">
                <div class="col-8" style="align-self: baseline;">
                    <h2>Tạo phiếu nhập hàng</h2>
                    <div>
                        <product-live-search
                        :url='"{{ route("admin.catalog.products.live-search-products") }}"'
                        @added_product_changed="onAdd"
                        @price_total_changed="onPrice"
                        ></product-live-search>
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
                                <input type="date" class="control" name="created_date" v-model="form.created_date" v-validate="">   
                                <span class="control-error" v-if="errors.has('created_date')">@{{ errors.first('created_date') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="inventory-source" class="form-label">{{ __('admin::app.vpt.inventory.inventory-source') }}</label>
                        <select v-model="form.to_inventory_source" class="form-control" aria-label="{{ __('admin::app.vpt.inventory.inventory-source') }}" name="inventory-source" disabled>
                            <option value="1">Kho Tổng - Công ty</option>
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
                        <input type="text" class="form-control" id="exampleFormControlInput1" placeholder='{{"MNH".$maxId+=1}}' disabled>
                    </div>
                    <div class="mb-3">
                        <label for="exampleFormControlInput1" class="form-label">{{ __('admin::app.vpt.inventory.status') }}</label>
                        <input v-model="form.status" type="text" disable=true class="form-control" id="exampleFormControlInput1" placeholder="{{ __('admin::app.vpt.inventory.status') }}" readonly>
                    </div>
                    <div class="mb-3">
                    {{ __('admin::app.vpt.inventory.total-of-price') }}: <span v-text="formatPrice(form.price_total)"></span> {{ __('admin::app.vpt.inventory.vnd') }}
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
let handleOutsideClick
        Vue.directive('closable', {
            bind(el, binding, vnode) {
                handleOutsideClick = (e) => {
                    e.stopPropagation()
                    const {
                        handler,
                        exclude
                    } = binding.value
                    let clickedOnExcludedEl = false
                    exclude.forEach(refName => {
                        if (!clickedOnExcludedEl) {
                            const excludedEl = vnode.context.$refs[refName]
                            clickedOnExcludedEl = excludedEl.contains(e.target)
                        }
                    })
                    if (!el.contains(e.target) && !clickedOnExcludedEl) {
                        vnode.context[handler]()
                    }
                }
                document.addEventListener('click', handleOutsideClick)
                document.addEventListener('touchstart', handleOutsideClick)
            },

            unbind() {
                document.removeEventListener('click', handleOutsideClick)
                document.removeEventListener('touchstart', handleOutsideClick)
            }
        })

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
                        type: 'receipt',
                        receipt_date: new Date(),
                        created_date: new Date(),
                        user: "auth()->guard('admin')->user()->id",
                        supplier: null,
                        to_inventory_source: 1,
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
            filters: {
                capitalize: function(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1)
                }
            },
            methods: {
                save() {
                    this.form.post("{{ route('admin.exchange.store') }}")
                        .then((response) => {

                            // var attr = document.getElementById("text");
                            // attr.innerHTML = response.data.message;
                            console.error(response);
                            if (response.data.success == true) {
                                console.error("save exchange successfull");
                                window.location.href =
                                    "{{ route('admin.exchange.purchase-order.list') }}";
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
                formatPrice(value) {
                    var formatter = new Intl.NumberFormat('en-US', {
                        minimumFractionDigits: 0
                    });
                    return formatter.format(value);
                },
            },
        });

    </script>
@endpush
