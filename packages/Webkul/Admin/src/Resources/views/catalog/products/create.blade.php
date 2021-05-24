@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.catalog.products.add-title') }}
@stop

@section('css')
    <style>
        .table td .label {
            margin-right: 10px;
        }
        .table td .label:last-child {
            margin-right: 0;
        }
        .table td .label .icon {
            vertical-align: middle;
            cursor: pointer;
        }
    </style>
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
        <form id="createProduct" method="POST" action="{{ route('admin.catalog.products.store') }}" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = history.length > 1 ? document.referrer : '{{ route('admin.dashboard.index') }}'"></i>

                        {{ __('admin::app.catalog.products.add-title') }}
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
                <div>
                </div>
                <div class="page-action" >
                <button id="showAddInventoryButton" type="button" class="btn btn-lg btn-primary" style="margin-right:20px">
                    Thêm kho
                </button>
                <button id="showAddCategoryButton" type="button" class="btn btn-lg btn-primary" style="margin-right:20px">
                    Thêm danh mục
                </button>
                <button type="submit" form="createProduct" class="btn btn-lg btn-primary" >
                            lưu
                </button>
                </div>
                
            </div>
            
            <div class="page-content">
                @csrf()
                
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
                <?php $familyId = request()->input('family') ?>
                <div style="display:none" class="control-group" :class="[errors.has('type') ? 'has-error' : '']">
                            <label for="type" class="required">{{ __('admin::app.catalog.products.product-type') }}</label>
                            <select class="control" v-validate="'required'" id="type" name="type" {{ $familyId ? 'disabled' : '' }} data-vv-as="&quot;{{ __('admin::app.catalog.products.product-type') }}&quot;">

                                @foreach($productTypes as $key => $productType)
                                    <option value="{{ $key }}" {{ request()->input('type') == $productType['key'] ? 'selected' : '' }}>
                                        {{ $productType['name'] }}
                                    </option>
                                @endforeach

                            </select>

                            @if ($familyId)
                                <input type="hidden" name="type" value="{{ app('request')->input('type') }}"/>
                            @endif
                            <span class="control-error" v-if="errors.has('type')">@{{ errors.first('type') }}</span>
                        </div>

                        <div style="display:none" class="control-group" :class="[errors.has('attribute_family_id') ? 'has-error' : '']">
                            <label for="attribute_family_id" class="required">{{ __('admin::app.catalog.products.familiy') }}</label>
                            <select class="control" v-validate="'required'" id="attribute_family_id" name="attribute_family_id" {{ $familyId ? 'disabled' : '' }} data-vv-as="&quot;{{ __('admin::app.catalog.products.familiy') }}&quot;">
                                <option value=""></option>
                                @foreach ($families as $family)
                                    <!-- <option value="{{ $family->id }}" {{ ($familyId == $family->id || old('attribute_family_id') == $family->id) ? 'selected' : '' }}>{{ $family->name }}</option> -->
                                    <option value="{{ $family->id }}" selected>{{ $family->name }}</option>
                                    @endforeach
                            </select>

                            @if ($familyId)
                                <input type="hidden" name="attribute_family_id" value="{{ $familyId }}"/>
                            @endif
                            <span class="control-error" v-if="errors.has('attribute_family_id')">@{{ errors.first('attribute_family_id') }}</span>
                        </div>

                @foreach($families as $family)
                    @foreach($attributeGroups as $index => $attributeGroup)
                    <?php
                        if (in_array($attributeGroup->name, $hidden_attribute_groups)) {
                            continue;
                        }
                    ?>
                    <?php 
                    $customAttributes = $family->getCustomAttributesAttributebyGroup($attributeGroup->id); 
                    
                    ?>
                    <accordian :title="'{{ __('admin::app.vpt.attribute_group_'.$attributeGroup->id) }}'"
                                   :active="{{$index == 0 ? 'true' : 'false'}}">
                            <div slot="body">
                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.before') !!}

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
                        
                                            </label>
                                            @if($attribute->code == "name")
                                                <input type="text" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="{{ old($attribute->code) }}"data-vv-as="&quot;{{ $attribute->admin_name }}&quot;"v-slugify-target="'url_key'" />
                                            @elseif($attribute->code == "url_key")
                                                <input type="text" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" value="{{ old($attribute->code) }}"data-vv-as="&quot;{{ $attribute->admin_name }}&quot;"v-slugify />
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

                                

                                {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.' . $attributeGroup->name . '.controls.after') !!}
                            </div>
                        </accordian>

                    @endforeach
                
                    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.additional_views.before')!!}
                    @foreach ($family->getSimpleTypeInstance()->getAdditionalViews() as $view)

                        @include ($view)

                    @endforeach
                    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.additional_views.after')!!}
                            <div style="display:none" class="control-group" :class="[errors.has('channels[]') ? 'has-error' : '']">
                                <label for="channels" class="required">{{ __('admin::app.catalog.products.channel') }}</label>
                                <select class="control" name="channels[]" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.catalog.products.channel') }}&quot;" multiple>
                                    @foreach (app('Webkul\Core\Repositories\ChannelRepository')->all() as $channel)
                                        <option value="{{ $channel->id }}" @if($channel->name == "Offline") selected @endif>
                                            {{ $channel->name }}
                                        </option>
                                    @endforeach
                                </select>

                                <span class="control-error" v-if="errors.has('channels[]')">
                                    @{{ errors.first('channels[]') }}
                                </span>
                            </div>
                @endforeach
                
            </div>

        </form>
    </div>
    
@stop

@push('scripts')
<script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script>
            $(document).ready(function () {
                tinymce.init({
                selector: 'textarea#description,textarea#short_description,textarea#categoryDescription',
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