@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.catalog.products.edit-title') }}
@stop

@section('content')
    <div class="content">
        <?php $locale = request()->get('locale') ?: app()->getLocale(); ?>
        <?php $channel = request()->get('channel') ?: core()->getDefaultChannelCode(); ?>
<!-- popup add category -->
@include ('admin::catalog.products.popup.add-category')
<!-- popup add category -->
<!-- popup add inventory -->
        @include ('admin::catalog.products.popup.add-inventory')
<!-- popup add inventory -->
        {!! view_render_event('bagisto.admin.catalog.product.edit.before', ['product' => $product]) !!}

        <form id="editProduct" method="POST" action=""  enctype="multipart/form-data" >

            <div class="page-header">

                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link"
                           onclick="window.location = history.length > 1 ? document.referrer : '{{ route('admin.dashboard.index') }}'"></i>

                        {{ __('admin::app.catalog.products.edit-title') }}
                    </h1>
                    <div class="control-group" style="display:none;">
                        <select class="control" id="channel-switcher" name="channel">
                            @foreach (core()->getAllChannels() as $channelModel)

                                <option
                                    value="{{ $channelModel->code }}" {{ ($channelModel->code) == $channel ? 'selected' : '' }}>
                                    {{ $channelModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="control-group" style="display:none;">
                        <select class="control" id="locale-switcher" name="locale">
                            @foreach (core()->getAllLocales() as $localeModel)

                                <option
                                    value="{{ $localeModel->code }}" {{ ($localeModel->code) == $locale ? 'selected' : '' }}>
                                    {{ $localeModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="page-action">
                    <button id="showAddInventoryButton" type="button" class="btn btn-lg btn-primary" style="margin-right:20px">
                        Thêm kho
                    </button>
                    <button id="showAddCategoryButton" type="button" class="btn btn-lg btn-primary" style="margin-right:20px">
                        Thêm danh mục
                    </button>
                    <button type="submit" form="editProduct" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.products.save-btn-title') }}
                    </button>
                </div>
            </div>

            <div class="page-content">
                @csrf()

                <input name="_method" type="hidden" value="PUT">
                <?php

                    // Hide some unused attributes
                    $hidden_attribute_groups = array(
                        "Meta Description",
                        "Shipping"
                    );
                    $hidden_attributes = array(
                        "guest_checkout",
                        "size",
                        "color",
                        "depth",
                        "height",
                        "width",
                        "meta_keywords",
                        "meta_title",
                        "special_price_to",
                        "special_price_from",
                        "special_price",
                        "status",
                        "visible_individually",
                        "featured",
                        "new",
                        "tax_category_id",
                    );
                ?>
                @foreach ($product->attribute_family->attribute_groups as $index => $attributeGroup)
                    <?php
                        if (in_array($attributeGroup->name, $hidden_attribute_groups)) {
                            continue;
                        }
                    ?>
                    <?php $customAttributes = $product->getEditableAttributes($attributeGroup); ?>

                    @if (count($customAttributes))

                        {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.before', ['product' => $product]) !!}

                        <accordian :title="'{{ __('admin::app.vpt.attribute_group_'.$attributeGroup->id) }}'"
                                   :active="{{$index == 0 ? 'true' : 'false'}}">
                            <div slot="body">
                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.before', ['product' => $product]) !!}

                                @foreach ($customAttributes as $attribute)

                                    <?php
                                        if ($attribute->code == "status") {
                                            ?>
                                            <input type="hidden"  id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="1">
                                            <?php
                                        }
                                        if (in_array($attribute->code, $hidden_attributes)) {
                                            continue;
                                        }

                                        if ($attribute->code == 'guest_checkout' && ! core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')) {
                                            continue;
                                        }

                                        $validations = [];

                                        if ($attribute->is_required) {
                                            array_push($validations, 'required');
                                        }

                                        if ($attribute->type == 'price') {
                                            array_push($validations, 'decimal');
                                        }

                                        if ($attribute->type == 'file') {
                                            $retVal = (core()->getConfigData('catalog.products.attribute.file_attribute_upload_size')) ? core()->getConfigData('catalog.products.attribute.file_attribute_upload_size') : '2048' ;
                                            array_push($validations, 'size:' . $retVal);
                                        }

                                        if ($attribute->type == 'image') {
                                            $retVal = (core()->getConfigData('catalog.products.attribute.image_attribute_upload_size')) ? core()->getConfigData('catalog.products.attribute.image_attribute_upload_size') : '2048' ;
                                            array_push($validations, 'size:' . $retVal . '|mimes:bmp,jpeg,jpg,png,webp');
                                        }

                                        array_push($validations, $attribute->validation);

                                        $validations = implode('|', array_filter($validations));
                                    ?>

                                    @if (view()->exists($typeView = 'admin::catalog.products.field-types.' . $attribute->type))

                                        <div class="control-group {{ $attribute->type }}"
                                             @if ($attribute->type == 'multiselect') :class="[errors.has('{{ $attribute->code }}[]') ? 'has-error' : '']"
                                             @else :class="[errors.has('{{ $attribute->code }}') ? 'has-error' : '']" @endif>

                                            <label
                                                for="{{ $attribute->code }}" {{ $attribute->is_required ? 'class=required' : '' }}>
                                                {{ $attribute->admin_name }}

                                                @if ($attribute->type == 'price')
                                                    <!-- <span class="currency-code">({{ core()->currencySymbol(core()->getBaseCurrencyCode()) }})</span> -->
                                                @endif

                                                <?php
                                                $channel_locale = [];

                                                if ($attribute->value_per_channel) {
                                                    array_push($channel_locale, $channel);
                                                }

                                                if ($attribute->value_per_locale) {
                                                    array_push($channel_locale, $locale);
                                                }
                                                ?>

                                                @if (count($channel_locale))
                                                    <!-- <span class="locale">[{{ implode(' - ', $channel_locale) }}]</span> -->
                                                @endif
                                            </label>

                                            @if($attribute->code == "name")
                                                <input type="text" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="{{ old($attribute->code) ?: $product[$attribute->code] }}"data-vv-as="&quot;{{ $attribute->admin_name }}&quot;"v-slugify-target="'url_key'" />
                                            @elseif($attribute->code == "url_key")
                                                <input type="text" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="{{ old($attribute->code) ?: $product[$attribute->code] }}"data-vv-as="&quot;{{ $attribute->admin_name }}&quot;"v-slugify />
                                            @else
                                                @include ($typeView)
                                            @endif

                                            <span class="control-error"
                                                  @if ($attribute->type == 'multiselect') v-if="errors.has('{{ $attribute->code }}[]')"
                                                  @else  v-if="errors.has('{{ $attribute->code }}')"  @endif>
                                                @if ($attribute->type == 'multiselect')
                                                    @{{ errors.first('{!! $attribute->code !!}[]') }}
                                                @else
                                                    @{{ errors.first('{!! $attribute->code !!}') }}
                                                @endif
                                            </span>
                                        </div>

                                    @endif

                                @endforeach

                                <!-- @if ($attributeGroup->name == 'Price')

                                    @include ('admin::catalog.products.accordians.customer-group-price')

                                @endif -->

                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.after', ['product' => $product]) !!}
                            </div>
                        </accordian>

                        {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.after', ['product' => $product]) !!}

                    @endif

                @endforeach

                {!! view_render_event(
                  'bagisto.admin.catalog.product.edit_form_accordian.additional_views.before',
                   ['product' => $product])
                !!}
                @foreach ($product->getTypeInstance()->getAdditionalViews() as $view)

                    @include ($view)

                @endforeach

                {!! view_render_event(
                  'bagisto.admin.catalog.product.edit_form_accordian.additional_views.after',
                   ['product' => $product])
                !!}
            </div>

        </form>

        {!! view_render_event('bagisto.admin.catalog.product.edit.after', ['product' => $product]) !!}
    </div>
@stop

@push('scripts')
    <script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script>
        document.getElementById("menu-span").style.verticalAlign= "super";
        $(document).ready(function () {
            tinymce.init({
                selector: 'textarea#description, textarea#short_description,textarea#categoryDescription',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true
            });
        });
    </script>
    <!-- template description -->
<script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

<script type="text/x-template" id="description-template">

    <div class="control-group" :class="[errors.has('categoryDescription') ? 'has-error' : '']">
        <label for="categoryDescription" :class="isRequired ? 'required' : ''">{{ __('admin::app.catalog.categories.description') }}</label>
        <textarea v-validate="isRequired ? 'required' : ''"  class="control" id="categoryDescription" name="categoryDescription" data-vv-as="&quot;{{ __('admin::app.catalog.categories.description') }}&quot;">{{ old('categoryDescription') }}</textarea>
        <span class="control-error" v-if="errors.has('categoryDescription')">@{{ errors.first('categoryDescription') }}</span>
    </div>

</script>
<!-- end template description -->
<!-- parent category-->
<script type="text/x-template" id="parent-category-template">
        <div>
            <input class="form-control" type="text" v-on:input="onChangeKeywords($event.target.value)">
            <tree-view value-field="id" name-field="parent_id" input-type="radio-category" v-bind:items='listCategories' :value='selectedValue' @valueChanged="onValueChanged" ></tree-view>
        </div>
    </script>

    <script>
        Vue.component('description', {

            template: '#description-template',

            inject: ['$validator'],

            data: function() {
                return {
                    isRequired: true,
                }
            },

            created: function () {
                var this_this = this;

                $(document).ready(function () {
                    $('#display_mode').on('change', function (e) {
                        if ($('#display_mode').val() != 'products_only') {
                            this_this.isRequired = true;
                        } else {
                            this_this.isRequired = false;
                        }
                    })
                });
            }
        })

        Vue.component('parent-category', {

        template: '#parent-category-template',

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
            }
        },
        beforeMount() {
                this.defaultList();
        },

        })
    </script>
@endpush
