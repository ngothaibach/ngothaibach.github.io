<template>
    <div>
        <input class="form-control" type="text" v-on:input="onChangeKeywords($event.target.value)"  ref="button"  v-on:click="showPopup = true" >
        <ul class="list-group" v-if="results.length > 0"  v-show="showPopup"
            v-closable="{
              exclude:   ['button'],
              handler: 'onClose'
            }">
            <li v-for="result in pageOfItems" v-if="result.name != null" class="list-group-item"  :key="result.id"  v-on:click="add_product(result)">
                <span v-text="result.name  + ' --- ' + result.sku"></span><br/>
                <img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + result.featured_image"/>
                Giá: <span v-text="result.price"></span><br/>
            </li>
            <sort-pagination 
            v-bind:items="results"
            v-bind:pageSize = "3"               
            @changePage="onChangePage">
            </sort-pagination>
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
            <tr v-if="added_products.length === 0">
                <td>Không có dữ liệu</td>
            </tr>
            <tr v-else v-for="item in added_products">
                <td v-text="item.id"></td>
                <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + item.featured_image"/></td>
                <td v-text="item.name"></td>
                <td v-text="item.price"></td>
                <td>
                    <input type="text" class="form-control" v-model="item.qty" v-on:change="update_price">
                </td>
                <td v-text="item.in_stock"></td>
                <td><button v-on:click="remove_product(item)" type="button" class="btn btn-danger">Xoá</button></td>
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
                table_headers: [
                    "Mã",
                    "Ảnh",
                    "Tên",
                    "Giá",
                    "Số lượng",
                    "Tồn kho",
                    "Xoá"
                ],
                price_total: 0,

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
                        console.log('response.data', response.data)
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
                    in_stock: 0,
                    featured_image: result.featured_image
                };
                this.added_products.push(added_item);
                this.showPopup = false;
                this.price_total = parseInt(this.price_total) + parseInt(result.price);
            },
            remove_product: function(item) {
                let index = this.added_products.indexOf(item);
                let qty = this.added_products[index].qty;
                this.price_total = parseInt(this.price_total) - parseInt(item.price)*parseInt(qty);
                this.added_products.splice(this.added_products.indexOf(item), 1);
                
            },
            update_price: function() {
                this.price_total = 0;
                for (let i = 0; i < this.added_products.length; i++) {
                    this.price_total += parseInt(this.added_products[i].qty) * parseInt(
                        this.added_products[i].price);
                }
            },
            //pagination
            onChangePage(pageOfItems) {
                // update page of items
                this.pageOfItems = pageOfItems;
            }, 
            //pagination 
            
        },
    }
</script>
<style lang="scss" scoped>

</style>