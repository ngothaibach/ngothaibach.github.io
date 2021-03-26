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
        <form method="POST" action="" @submit.prevent="onSubmit" enctype="multipart/form-data">

            <div class="page-header">
                <div class="page-title">
                    <h1>
                        <i class="icon angle-left-icon back-link" onclick="window.location = history.length > 1 ? document.referrer : '{{ route('admin.dashboard.index') }}'"></i>

                        {{ __('admin::app.catalog.products.add-title') }}
                    </h1>
                
                <div class="control-group">
                        <select class="control" id="channel-switcher" name="channel">
                            @foreach (core()->getAllChannels() as $channelModel)

                                <option
                                    value="{{ $channelModel->code }}" {{ ($channelModel->code) == $channel ? 'selected' : '' }}>
                                    {{ $channelModel->name }}
                                </option>

                            @endforeach
                        </select>
                    </div>

                    <div class="control-group">
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
                    <button type="submit" class="btn btn-lg btn-primary">
                        {{ __('admin::app.catalog.products.save-btn-title') }}
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

                                            @include ($typeView)

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
                @endforeach
            </div>

        </form>
    </div>
@stop

@push('scripts')
<script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('#channel-switcher, #locale-switcher').on('change', function (e) {
                $('#channel-switcher').val()
                var query = '?channel=' + $('#channel-switcher').val() + '&locale=' + $('#locale-switcher').val();

                window.location.href = "{{ route('admin.catalog.products.create')  }}" + query;
            })

            tinymce.init({
                selector: 'textarea#description, textarea#short_description',
                height: 200,
                width: "100%",
                plugins: 'image imagetools media wordcount save fullscreen code table lists link hr',
                toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor link hr | alignleft aligncenter alignright alignjustify | numlist bullist outdent indent  | removeformat | code | table',
                image_advtab: true
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.label .cross-icon').on('click', function(e) {
                $(e.target).parent().remove();
            })

            $('.actions .trash-icon').on('click', function(e) {
                $(e.target).parents('tr').remove();
            })
        });
    </script>
@endpush