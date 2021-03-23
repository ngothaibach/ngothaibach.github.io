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
                                                                                <h2>{{ __('admin::app.vpt.inventory.add-receipt-note') }}</h2>
                                                                                <div>
                                                                                    <input class="form-control" type="text" v-model="keywords"  ref="button"  v-on:click="showPopup = true" >
                                                                                    <ul class="list-group" v-if="results.length > 0"  v-show="showPopup"
                                                                                        v-closable="{
                                                                                          exclude:   ['button'],
                                                                                          handler: 'onClose'
                                                                                        }">
                                                                                        <li v-if="result.name.length" class="list-group-item" v-for="result in results" :key="result.id"  v-on:click="add_product(result)">
                                                                                            <span v-text="result.name"></span><br/>
                                                                                            <img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + result.featured_image"/>
                                                                                            {{ __('admin::app.vpt.inventory.price') }}: <span v-text="result.price"></span><br/>
                                                                                            {{ __('admin::app.vpt.inventory.remain') }}: <span v-text="result.qty"></span>
                                                                                        </li>
                                                                                    </ul>

                                                                                    <div id="app" >
                                                                                        <div >
                                                                                          <div>
                                                                                            <div class="panel-heading"> 
                                                                                              <h4>Nhập file excel( *.csv)</h4>
                                                                                            </div>
                                                                                            <div class="panel-body">
                                                                                              <div class="form-group" >
                                                                                                <div class="col-sm-12">
                                                                                                    <label for="csv_file" class="custom-file-upload" style=" border: 1px solid #ccc;
                                                                                                    display: inline-block;
                                                                                                    padding: 6px 12px;
                                                                                                    cursor: pointer;
                                                                                                    margin-left : 40%;
                                                                                                    "
                                                                                                    >
                                                                                                        <i class="fa fa-cloud-upload"></i> Chọn file excel
                                                                                                    </label>
                                                                                                  <input type="file" id="csv_file" name="csv_file" class="form-control" v-on:change="loadCSV($event)" style="display: none;">
                                                                                                </div>
                                                                                              </div>
                                                                                            </div>
                                                                                          </div>
                                                                                        </div>
                                                                                      </div>

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
                                                                                                @if (auth()
            ->guard('admin')
            ->user()->id == $user->id)
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
                    showPopup: false,
                    keywords: null,
                    results: [],
                    listProduct: [],
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
                    sortKey: ''
                };
            },
            watch: {
                keywords(after, before) {
                    this.fetch();
                }
            },
            filters: {
                capitalize: function(str) {
                    return str.charAt(0).toUpperCase() + str.slice(1)
                }
            },
            methods: {
                onClose: function() {
                    this.showPopup = false
                },
                fetchDataNotShow() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", {
                            params: {
                                key: this.keywords
                            }
                        })
                        .then(response => {
                            this.listProduct = response.data;
                            // alert(response.data.length);
                        })
                        .catch(error => {});
                    console.error(this.results);
                },

                fetch() {
                    axios.get("{{ route('admin.catalog.products.live-search-products') }}", {
                            params: {
                                key: this.keywords
                            }
                        })
                        .then(response => {
                            this.results = response.data;
                            this.listProduct = response.data;
                            console.log('response.data', response.data)
                        })
                        .catch(error => {});
                    console.error(this.results);
                },
                add_product: function(result) {
                    added_item = {
                        id: result.id,
                        name: result.name,
                        qty: 1,
                        price: result.price,
                        in_stock: 0,
                        featured_image: result.featured_image
                    };
                    this.form.added_products.push(added_item);
                    this.results = [];
                    this.form.price_total = parseInt(this.form.price_total) + parseInt(result.price);
                },
                remove_product: function(item) {
                    this.form.added_products.splice(this.form.added_products.indexOf(item), 1);
                    this.form.price_total = parseInt(this.form.price_total) - parseInt(item.price);
                },
                update_price: function() {
                    this.form.price_total = 0;
                    for (let i = 0; i < this.form.added_products.length; i++) {
                        this.form.price_total += parseInt(this.form.added_products[i].qty) * parseInt(
                            this.form
                            .added_products[i].price);
                    }
                },
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
                csvJSON: function(csv) {
                    // console.log("added_item", this.form.added_products)
                    var vm = this
                    var lines = csv.split("\n")
                    var result = []
                    var headers = lines[0].split(",")
                    vm.parse_header = lines[0].split(",")
                    console.log('parse_header', vm.parse_header);
                    lines[0].split(",").forEach(function(key) {
                        vm.sortOrders[key] = 1
                    })

                    lines.map(function(line, indexLine) {
                        if (indexLine < 1) return // Jump header line

                        var obj = {}
                        var currentline = line.split(",")

                        headers.map(function(header, indexHeader) {
                            obj[header] = currentline[indexHeader]
                        })

                        result.push(obj)
                    })



                    var skuProduct = vm.listProduct.map(function(item) {
                        return item.sku
                    });
                    var imgProduct = vm.listProduct.map(function(item) {
                        return item.featured_image
                    });

                    // var data = vm.listProduct.find(function(ele) {
                    //     return ele.id === '21';
                    // });

                    console.log('fetchData', vm.listProduct);

                    // console.log("added_item", this.results)
                    for (var item = 0; item < result.length - 1; item++) {
                        // console.log("SKU",result[item].Sku);
                        for (var i = 0; i < vm.listProduct.length; i++) {
                            // console.log(vm.listProduct[i])
                            if (vm.listProduct[i].sku == (result[item].Sku)) {
                                let added_item = {
                                    id: vm.listProduct[i].id,
                                    sku: result[item].Sku,
                                    name: result[item].Name,
                                    qty: result[item].Amount,
                                    price: result[item].Price,
                                    in_stock: result[item].Remain,
                                    featured_image: vm.listProduct[i].featured_image
                                }
                                this.form.added_products.push(added_item);
                                // console.log('this.form.added_products',this.form.added_products)
                            }
                        }
                    }
                    result.pop() // remove the last item because undefined values
                    return result // JavaScript object
                },
                loadCSV(e) {
                    var vm = this;
                    if (window.FileReader) {
                        var reader = new FileReader();
                        reader.readAsText(e.target.files[0]);
                        // Handle errors load
                        reader.onload = function(event) {
                            var csv = event.target.result;
                            vm.parse_csv = vm.csvJSON(csv)
                        };
                        reader.onerror = function(evt) {
                            if (evt.target.error.name == "NotReadableError") {
                                alert("Canno't read file !");
                            }
                        };
                    } else {
                        alert('FileReader are not supported in this browser.');
                    }
                }
            },
            beforeMount() {
                this.fetchDataNotShow();
            },
        });

    </script>
@endpush
