        <!-- popup add category --> 
                <div id="add_category" style="display:none">
                    <form id="addCategory" method="POST" action="{{ route('admin.catalog.products.addCategory') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-parent scrollable" >
                            <div class="modal-container">
                                <div class="modal-header">
                                    <slot name="header">
                                        Thêm danh mục
                                    </slot>
                                    <i class="icon remove-icon" id="hideAddCategoryButton" ></i>
                                </div>
                                <div class="modal-body">
                                
                                <div class="content">
                            <div class="form-container">
                                <input type="hidden" name="locale" value="all" />

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.general.before') !!}

                                <accordian :title="'{{ __('admin::app.catalog.categories.general') }}'" :active="true">
                                    <div slot="body">

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.general.controls.before') !!}

                                        <div class="control-group" :class="[errors.has('categoryName') ? 'has-error' : '']">
                                            <label for="categoryName" class="required">{{ __('admin::app.catalog.categories.name') }}</label>
                                            <input type="text" v-validate="'required'" class="control" id="categoryName" name="categoryName" value="{{ old('categoryName') }}" data-vv-as="&quot;{{ __('admin::app.catalog.categories.name') }}&quot;" v-slugify-target="'slug'" />
                                            <span class="control-error" v-if="errors.has('categoryName')">@{{ errors.first('categoryName') }}</span>
                                        </div>

                                        <div class="control-group" :class="[errors.has('categoryStatus') ? 'has-error' : '']">
                                            <label for="categoryStatus" class="required">{{ __('admin::app.catalog.categories.visible-in-menu') }}</label>
                                            <select class="control" v-validate="'required'" id="categoryStatus" name="categoryStatus" data-vv-as="&quot;{{ __('admin::app.catalog.categories.visible-in-menu') }}&quot;">
                                                <option value="1">
                                                    {{ __('admin::app.catalog.categories.yes') }}
                                                </option>
                                                <option value="0">
                                                    {{ __('admin::app.catalog.categories.no') }}
                                                </option>
                                            </select>
                                            <span class="control-error" v-if="errors.has('categoryStatus')">@{{ errors.first('categoryStatus') }}</span>
                                        </div>

                                        <div class="control-group" :class="[errors.has('position') ? 'has-error' : '']" style="display:none;">
                                            <label for="position" class="required">{{ __('admin::app.catalog.categories.position') }}</label>
                                            <input type="text" v-validate="'required|numeric'" class="control" id="position" name="position" value="1" data-vv-as="&quot;{{ __('admin::app.catalog.categories.position') }}&quot;" />
                                            <span class="control-error" v-if="errors.has('position')">@{{ errors.first('position') }}</span>
                                        </div>

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.general.controls.after') !!}

                                    </div>
                                </accordian>

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.general.after') !!}


                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.description_images.before') !!}

                                <accordian :title="'{{ __('admin::app.catalog.categories.description-and-images') }}'" :active="true">
                                    <div slot="body">

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.description_images.controls.before') !!}

                                        <div class="control-group" :class="[errors.has('display_mode') ? 'has-error' : '']">
                                            <label for="display_mode" class="required">{{ __('admin::app.catalog.categories.display-mode') }}</label>
                                            <select class="control" v-validate="'required'" id="display_mode" name="display_mode" data-vv-as="&quot;{{ __('admin::app.catalog.categories.display-mode') }}&quot;">
                                                <option value="products_and_description">
                                                    {{ __('admin::app.catalog.categories.products-and-description') }}
                                                </option>
                                                <option value="products_only">
                                                    {{ __('admin::app.catalog.categories.products-only') }}
                                                </option>
                                                <option value="description_only">
                                                    {{ __('admin::app.catalog.categories.description-only') }}
                                                </option>
                                            </select>
                                            <span class="control-error" v-if="errors.has('display_mode')">@{{ errors.first('display_mode') }}</span>
                                        </div>

                                        <description></description>

                                        <div class="control-group {!! $errors->has('image.*') ? 'has-error' : '' !!}">
                                            <label>{{ __('admin::app.catalog.categories.image') }}</label>

                                            <image-wrapper :button-label="'{{ __('admin::app.catalog.products.add-image-btn-title') }}'" input-name="image" :multiple="false"></image-wrapper>

                                            <span class="control-error" v-if="{!! $errors->has('image.*') !!}">
                                                @foreach ($errors->get('image.*') as $key => $message)
                                                @php echo str_replace($key, 'Image', $message[0]); @endphp
                                                @endforeach
                                            </span>

                                        </div>

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.description_images.controls.after') !!}

                                    </div>
                                </accordian>

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.description_images.after') !!}


                                @if ($categories->count())

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.parent_category.before') !!}

                                <accordian :title="'{{ __('admin::app.catalog.categories.parent-category') }}'" :active="true">
                                    <div slot="body" style="height: 500px;  overflow: scroll;">

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.parent_category.controls.before') !!}

                                        <parent-category></parent-category>

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.parent_category.controls.after') !!}

                                    </div>
                                </accordian>

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.parent_category.after') !!}

                                @endif

                                <accordian :title="'{{ __('admin::app.catalog.categories.filterable-attributes') }}'" :active="true" style="display:none;">
                                    <div slot="body">

                                        <?php $selectedaAtributes = old('attributes') ? old('attributes') : ['11']  ?>

                                        <div class="control-group" :class="[errors.has('attributes[]') ? 'has-error' : '']">
                                            <label for="attributes" class="required">{{ __('admin::app.catalog.categories.attributes') }}</label>
                                            <select class="control" name="attributes[]" v-validate="'required'" data-vv-as="&quot;{{ __('admin::app.catalog.categories.attributes') }}&quot;" multiple>

                                                @foreach ($attributes as $attribute)
                                                <option value="{{ $attribute->id }}" {{ in_array($attribute->id, $selectedaAtributes) ? 'selected' : ''}}>
                                                    {{ $attribute->name ? $attribute->name : $attribute->admin_name }}
                                                </option>
                                                @endforeach

                                            </select>
                                            <span class="control-error" v-if="errors.has('attributes[]')">
                                                @{{ errors.first('attributes[]') }}
                                            </span>
                                        </div>
                                    </div>
                                </accordian>

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.seo.before') !!}

                                <accordian :title="'{{ __('admin::app.catalog.categories.seo') }}'" :active="true" style="display:none;">
                                    <div slot="body">

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.seo.controls.before') !!}

                                        <div class="control-group">
                                            <label for="meta_title">{{ __('admin::app.catalog.categories.meta_title') }}</label>
                                            <input type="text" class="control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}" />
                                        </div>

                                        <div class="control-group" :class="[errors.has('slug') ? 'has-error' : '']">
                                            <label for="slug" class="required">{{ __('admin::app.catalog.categories.slug') }}</label>
                                            <input type="text" v-validate="'required'" class="control" id="slug" name="slug" value="{{ old('slug') }}" data-vv-as="&quot;{{ __('admin::app.catalog.categories.slug') }}&quot;" v-slugify />
                                            <span class="control-error" v-if="errors.has('slug')">@{{ errors.first('slug') }}</span>
                                        </div>

                                        <div class="control-group">
                                            <label for="meta_description">{{ __('admin::app.catalog.categories.meta_description') }}</label>
                                            <textarea class="control" id="meta_description" name="meta_description">{{ old('meta_description') }}</textarea>
                                        </div>

                                        <div class="control-group">
                                            <label for="meta_keywords">{{ __('admin::app.catalog.categories.meta_keywords') }}</label>
                                            <textarea class="control" id="meta_keywords" name="meta_keywords">{{ old('meta_keywords') }}</textarea>
                                        </div>

                                        {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.seo.controls.after') !!}

                                    </div>
                                </accordian>

                                {!! view_render_event('bagisto.admin.catalog.category.create_form_accordian.seo.after') !!}

                            </div>
                        </div>
                        <button type="submit" form="addCategory" class="btn btn-lg btn-primary" >
                            Thêm danh mục
                        </button>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
<!-- popup add category -->
@push('scripts')
    @parent
<script>
        $(document).ready(function () {
            $('.label .cross-icon').on('click', function(e) {
                $(e.target).parent().remove();
            })

            $('.actions .trash-icon').on('click', function(e) {
                $(e.target).parents('tr').remove();
            })
            $("#showAddCategoryButton").click(function(){
                $("#add_category").css("display","block");
            });
            $("#hideAddCategoryButton").click(function(){
                $("#add_category").css("display","none");
            });
        });
    </script>
@endpush