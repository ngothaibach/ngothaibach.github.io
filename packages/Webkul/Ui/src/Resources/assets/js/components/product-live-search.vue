<template>
    <div class="product-live-search">
        <input class="form-control" type="text" v-on:input="onChangeKeywords($event.target.value)"  ref="button"  v-on:click="showPopup = true" >
        <ul class="list-group" v-if="results.length > 0"  v-show="showPopup"
            v-closable="{
              exclude:   ['button'],
              handler: 'onClose'
            }">
            <li v-for="result in pageOfItems" v-if="result.name != null" class="list-group-item"  :key="result.id"  v-on:click="add_product(result)">
                <span v-text="result.name  + ' --- ' + result.sku"></span><br/>
                <img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + result.featured_image"/>
                Giá: <span v-text="formatPrice(result.price)"></span><br/>
            </li>
            <sort-pagination 
            v-bind:items="results"
            v-bind:pageSize = "3"               
            @changePage="onChangePage">
            </sort-pagination>
        </ul>

        <div id="app" >
            <div >
              <div>
                <br>
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
                            <i class="fa fa-cloud-upload"></i>Nhập dữ liệu từ Excel
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
                <th class="grid_head th-sku">
                    <p >Mã</p>
                </th>
                <th class="grid_head th-image">
                    <p >Ảnh</p>
                </th>
                <th class="grid_head th-name">
                    <p >Tên</p>
                </th>
                <th class="grid_head th-price">
                    <p >Giá</p>
                </th>
                <th class="grid_head th-qty">
                    <p >Số lượng</p>
                </th>
                <th class="grid_head th-inven">
                    <p>Tồn kho</p>
                </th>
                <th class="grid_head th-del">
                    <p >Xoá</p>
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-if="added_products.length === 0">
                <td>Không có dữ liệu</td>
            </tr>
            <tr v-else v-for="item in added_products">
                <td class="td-sku" v-text="item.sku"></td>
                <td class="td-image"><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + item.featured_image"/></td>
                <td class="td-name" v-text="item.name"></td>
                <td class="td-price" v-text="formatPrice(item.price)"></td>
                <td class="td-qty">
                    <input type="text" class="form-control" v-model="item.qty" v-on:change="update_price">
                </td>
                <td class="td-inven" v-text="item.in_stock"></td>
                <td class="td-del"><button v-on:click="remove_product(item)" type="button" class="btn btn-danger">Xoá</button></td>
            </tr>
            </tbody>
        </table>
        </div>
</template>

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
    export default {
         props: {
                url: {
                    type: String,
                    required: true
                },
            },
            data () {
            return {
                showPopup: false,
                keywords: null,
                results: [],
                listProduct: [],
                sortKey: '',
                pageOfItems: [],
                perPage: 10,
                added_products: [],
                price_total: 0,
                total_of_qty: 0,

            }
        },
        methods: {
            onClose: function() {
                    this.showPopup = false
                },
            onChangeKeywords: _.debounce(function (input) {
                this.keywords = input;
                if(this.keywords != ""){        
                    this.fetch();
                }else{
                    this.listProduct = [];
                    this.results = [];
                    this.showPopup = false;
                }
                }, 600),
            fetch() {
                let route = this.url
                axios.get(route, {
                        params: {
                            key: this.keywords
                        }
                    })
                    .then(response => {
                        this.results = response.data;
                        this.listProduct = response.data;
                    })
                    .catch(error => {});
                console.error(this.results);
            },
            add_product: function(result) {
                let added_item = {
                    id: result.id,
                    name: result.name,
                    qty: 1,
                    price: result.price,
                    sku: result.sku,
                    in_stock: 0,
                    featured_image: result.featured_image
                };
                this.added_products.push(added_item);
                this.showPopup = false;
                this.price_total = parseInt(this.price_total) + parseInt(result.price);
                this.total_of_qty += 1;
                this.$emit('added_product_changed',this.added_products);
                this.$emit('price_total_changed',this.price_total);
                this.$emit('total_of_qty_changed',this.total_of_qty);
            },
            remove_product: function(item) {
                let index = this.added_products.indexOf(item);
                let qty = this.added_products[index].qty;
                this.price_total = parseInt(this.price_total) - parseInt(item.price)*parseInt(qty);
                this.total_of_qty = parseInt(this.total_of_qty) - parseInt(qty);
                this.added_products.splice(this.added_products.indexOf(item), 1);
                this.$emit('added_product_changed',this.added_products);
                this.$emit('price_total_changed',this.price_total);
                this.$emit('total_of_qty_changed',this.total_of_qty);
            },
            update_price: function() {
                this.price_total = 0;
                this.total_of_qty = 0;
                for (let i = 0; i < this.added_products.length; i++) {
                    this.price_total += parseInt(this.added_products[i].qty) * parseInt(this.added_products[i].price);
                    this.total_of_qty += parseInt(this.added_products[i].qty);
                }
                this.$emit('price_total_changed',this.price_total);
                this.$emit('total_of_qty_changed',this.total_of_qty);
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
            },
            csvJSON: function(csv) {
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
                            this.added_products.push(added_item);
                        }
                    }
                }
                result.pop() // remove the last item because undefined values
                return result // JavaScript object
            },
            //pagination
            onChangePage(pageOfItems) {
            // update page of items
            this.pageOfItems = pageOfItems;
            }, 
            //pagination 
            formatPrice(value) {
                var formatter = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0
                });
                return formatter.format(value);
            },
        },
    }
</script>
<style lang="scss" scoped>
.product-live-search{
    word-wrap: break-word;
    width:auto;
}
.table {
    table-layout: fixed;
    width: 100%;  
}
.th-sku, .td-sku,.th-image, .td-image,.th-name, .td-name,.th-price, .td-price,.th-qty, .td-qty,.th-inven, .td-inven,.th-del, .td-del{
    overflow-x: hidden;
    word-wrap: break-word;
}
@media only screen and (max-width: 600px) {
    .product-live-search{
        width:600px;
    }
    .th-sku, .td-sku{
        width:100px;
    }
    .th-name, .td-name{
        width:150px;
    }
    .th-image, .td-image{
        width:75px;
    }
    .table th{
        padding:0px;
    }
}

/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
    .product-live-search{
        width:500px;
        overflow-x:scroll ;
    }
     .th-sku, .td-sku{
        width:75px;
    }
    .th-name, .td-name{
        width:100px;
    }
    .th-image, .td-image{
        width:75px;
    }
    .th-del, .td-del{
        width:50px;
    }
    .table th{
        padding:0px;
    }
}

/* Large devices (laptops/desktops, 992px and up) */
@media only screen and (min-width: 992px) {
     .product-live-search{
        width:650px;
    }
     .th-sku, .td-sku{
        width:100px;
    }
    .th-name, .td-name{
        width:200px;
    }
    .th-image, .td-image{
        width:100px;
    }
    .th-del, .td-del{
        width:60px;
    }
    .table th{
        padding:0.75rem;
    }
}

/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 1200px) {
     .product-live-search{
        width:100%;
    }
     .th-sku, .td-sku{
        width:100px;
    }
    .th-name, .td-name{
        width:200px;
    }
    .th-image, .td-image{
        width:100px;
    }
}
</style>