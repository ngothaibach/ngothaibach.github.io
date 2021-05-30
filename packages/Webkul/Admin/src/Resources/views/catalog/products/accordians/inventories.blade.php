@if(isset($product)) 
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.before', ['product' => $product]) !!}
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.before') !!}
@endif
<accordian :title="'{{ __('admin::app.catalog.products.inventories') }}'" :active="false">
    <div slot="body" style="height: 500px;  overflow: scroll;">
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.controls.before', ['product' => $product]) !!}
            <inventories :productinventories='@json($product->inventories)'></inventories>
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.controls.after', ['product' => $product]) !!} 
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.controls.before') !!}
            @foreach ($inventorySources as $inventorySource)
            <!-- 
            <div class="control-group" :class="[errors.has('inventories[{{ $inventorySource->id }}]') ? 'has-error' : '']" style="display:flex; flex-direction: row; justify-content: center; align-items: center; margin-bottom:5px !important" >
            <label>{{ $inventorySource->name }}</label>
            <input style="width: 200px;margin-right:200px;" type="text" v-validate="'numeric|min:0'" name="inventories[{{ $inventorySource->id }}]" class="control"  data-vv-as="&quot;{{ $inventorySource->name }}&quot;"/>
            <span class="control-error" v-if="errors.has('inventories[{{ $inventorySource->id }}]')">@{{ errors.first('inventories[{!! $inventorySource->id !!}]') }}</span>
            </div>
             -->
            @endforeach
            <inventories></inventories>
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.controls.after') !!}
        @endif

    </div>
</accordian>

@if(isset($product)) 
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.after', ['product' => $product]) !!} 
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.after') !!}
@endif
@push('scripts')

  <script type="text/x-template" id="inventories-template">
        <div>
            <div>
                <div class="control-group">
                    <input class="control" type="text" v-on:input="onChangeKeywords($event.target.value)" placeholder="Tìm kiếm" />
                </div>                        
            </div>
            <div style="display:none" class="hidden">
                <div v-for="item in fullList" >
                    <input type="number" v-validate="'numeric|min:0'" :name="'inventories['+ item.id +']'" class="control" v-model="item.qty"/>
                </div>
            </div>
            <div class="input-show" style="display:flex; flex-wrap:wrap">
                <div v-for="item in listInventories" class="control-group group-inventory" v-if='this.keywords != ""' style="flex:45%" >
                    <label style="max-width:250px;">@{{item.name}}</label>
                    <input type="number" v-validate="'numeric|min:0'"  :name="item.id" class="control input-inventory" v-model="item.qty" v-on:change.lazy="force()" :data-vv-as="&quot;item.name&quot;"/>
                </div>
            </div>
             <!-- <span class="control-error" v-if="errors.has('inventories[{{ 22}}]')">dữ liệu không hợp lệ</span> -->
        </div>
    </script>

    <script>
        Vue.component('inventories', {

        template: '#inventories-template',
        props:{
                productinventories:{
                    type: Array,
                    required:false,
                    default: null,
                },
        },
        data: function() {
            return {
                fullList: @json($inventorySources),
                keywords: "",
                listInventories: [],
                selectedValue:null,
            }
        },
        methods:{
            onChangeKeywords: _.debounce(function (input) {
                    this.keywords = input;
                    if(this.keywords != ""){        
                        let keySearch = this.stringToASCII(this.keywords);
                        console.log(keySearch);
                        this.listInventories =  this.fullList.filter((item)=>{
                            return keySearch.toLowerCase().split(' ').every(v => this.stringToASCII(item.name).toLowerCase().includes(v))
                    })
                    }else{
                        this.listInventories = this.fullList;
                    }
                    }, 600),
            fetch() {
                axios.get("{{ route('admin.catalog.categories.live_search_category') }}", {
                        params: {
                            key: this.keywords
                        }
                    })
                    .then(response => {
                        this.listCategories = response.data;
                        console.log('response.data', response.data);
                    })
                    .catch(error => {});
                console.error(this.listCategories);
            },
            defaultList() { 
                
            },
            onValueChanged(value){
                this.selectedValue = value;
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
            createQty(){
                for(inventories of this.fullList){
                    inventories.qty = 0;
                }
            },
            force(){
                this.$forceUpdate();
            }
        },  
        mounted() {
            this.createQty();
            this.listInventories = this.fullList;
            if(this.productinventories !=null){
                for(index in this.productinventories){
                    this.fullList[index].qty = this.productinventories[index].qty
                }
            };
            
        },
    })
</script>

@endpush