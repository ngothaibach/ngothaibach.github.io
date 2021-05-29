@if(isset($product)) 
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.before', ['product' => $product]) !!}
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.before') !!}
@endif
<accordian :title="'{{ __('admin::app.catalog.products.inventories') }}'" :active="false">
    <div slot="body" style="height: 500px;  overflow: scroll;">
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.controls.before', ['product' => $product]) !!}
            @foreach ($inventorySources as $inventorySource)
          
        
       
            <?php
                $qty = 0;
                if(isset($product)){
                    foreach ($product->inventories as $inventory) {
                        if ($inventory->inventory_source_id == $inventorySource->id) {
                            $qty = $inventory->qty;
                            break;
                        }
                    }

                    $qty = old('inventories[' . $inventorySource->id . ']') ?: $qty;
                }        
            ?>
            <div class="control-group" :class="[errors.has('inventories[{{ $inventorySource->id }}]') ? 'has-error' : '']" style="display:flex; flex-direction: row; justify-content: center; align-items: center; margin-bottom:5px !important" >
            <label>{{ $inventorySource->name }}</label>
            <input style="width: 200px;margin-right:200px;" type="text" v-validate="'numeric|min:0'" name="inventories[{{ $inventorySource->id }}]" class="control" value="{{ $qty }}" data-vv-as="&quot;{{ $inventorySource->name }}&quot;"/>
            <span class="control-error" v-if="errors.has('inventories[{{ $inventorySource->id }}]')">@{{ errors.first('inventories[{!! $inventorySource->id !!}]') }}</span>
            </div>
            @endforeach
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
            <div class="row">
                <div class="search-wrapper panel-heading col-sm-12">
                    <input class="form-control" type="text" v-on:input="onChangeKeywords($event.target.value)" placeholder="Search" />
                </div>                        
            </div>
            <div v-for="item in listInventories" class="control-group" >
                <label>@{{item.name}}</label>
                <input style="width: 200px;margin-right:200px;" type="number" v-validate="'numeric|min:0'" :name="'inventories['+ item.id +']'"  class="control" value="0" data-vv-as="&quot;item.name&quot;"/>
            </div>
             <!-- <span class="control-error" v-if="errors.has('inventories[{{ 22}}]')">dữ liệu không hợp lệ</span> -->
        </div>
    </script>

    <script>
        Vue.component('inventories', {

        template: '#inventories-template',
        props:{
                productInventories:{
                    required:false,
                    default: null,
                }
        },
        data: function() {
            return {
                fullList: @json($inventorySources),
                keywords: null,
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
                this.listInventories = this.fullList;
            },
            onValueChanged(value){
                this.selectedValue = value;
            },
            defaultValue(){
                if(this.value !=null){
                    this.selectedValue = this.value;
                }
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
        },
        beforeMount() {
                this.defaultList();
                this.defaultValue();
        },

        })
    </script>

@endpush