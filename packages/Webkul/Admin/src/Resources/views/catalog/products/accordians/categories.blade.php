@if ($categories->count())
@if(isset($product))
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.before', ['product' => $product]) !!}
@else
{!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.before') !!}
@endif
<accordian :title="'{{ __('admin::app.catalog.products.categories') }}'" :active="false">
    <div slot="body" style="height: 500px;  overflow: scroll;">
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.controls.before', ['product' => $product]) !!}
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.controls.before') !!}
        @endif
        <!-- <tree-view behavior="normal" value-field="id" name-field="categories" input-type="checkbox" items='@json($categories)' @if(isset($product)) value='@json($product->categories->pluck("id"))' @endif ></tree-view> -->
        <categories @if(isset($product)) value='@json($product->parent_id)' @endif></categories>
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.controls.after', ['product' => $product]) !!}
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.controls.after') !!}
        @endif
    </div>
</accordian>
@if(isset($product))
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.after', ['product' => $product]) !!}
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.after') !!}
@endif
@endif
@push('scripts')

  <script type="text/x-template" id="categories-template">
        <div>
            <input class="form-control" type="text" v-on:input="onChangeKeywords($event.target.value)">
            <tree-view value-field="id" name-field="parent_id" input-type="radio-category" v-bind:items='listCategories' :value='selectedValue' @valueChanged="onValueChanged" ></tree-view>
        </div>
    </script>

    <script>
        Vue.component('categories', {

        template: '#categories-template',
        props:{
                value:{
                    required:false,
                    default: null,
                }
        },
        data: function() {
            return {
                fullList: @json($categories),
                keywords: null,
                listCategories: [],
                selectedValue:null,
            }
        },
        methods:{
            onChangeKeywords: _.debounce(function (input) {
                    this.keywords = input;
                    if(this.keywords != ""){        
                        this.fetch();
                    }else{
                        this.listCategories = this.fullList;
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
                this.listCategories = this.fullList;
            },
            onValueChanged(value){
                this.selectedValue = value;
            },
            defaultValue(){
                if(this.value !=null){
                    this.selectedValue = this.value;
                }
            }
        },
        beforeMount() {
                this.defaultList();
                this.defaultValue();
        },

        })
    </script>

@endpush