        <!-- popup add inventory --> 
                <div id="add_inventory" style="display:none">
                    <form id="addInventory" method="POST" action="{{ route('admin.catalog.products.addInventory') }}" >
                        @csrf
                        <div class="modal-parent scrollable" >
                            <div class="modal-container">
                                <div class="modal-header">
                                    <slot name="header">
                                        Thêm Kho
                                    </slot>
                                    <i class="icon remove-icon" id="hideAddInventoryButton" ></i>
                                </div>
                                <div class="modal-body">
                                
                                <div class="content">
                                <div class="form-container">
                    @csrf()

                    {!! view_render_event('bagisto.admin.settings.inventory.create.before') !!}

                    <accordian :title="'Thông tin chung'" :active="true">
                        <div slot="body">

                            <div class="control-group" :class="[errors.has('code') ? 'has-error' : '']">
                                <label for="code" class="required">Mã kho</label>
                                <input v-validate="'required'" class="control" id="code" name="code" value="{{ old('code') }}" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.code') }}&quot;" v-code/>
                                <span class="control-error" v-if="errors.has('code')">@{{ errors.first('code') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('name') ? 'has-error' : '']">
                                <label for="inventory-name" class="required">Tên kho</label>
                                <input v-validate="'required'" class="control" id="inventory-name" name="inventory-name" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.name') }}&quot;" value="{{ old('inventory-name') }}"/>
                                <span class="control-error" v-if="errors.has('inventory-name')">@{{ errors.first('inventory-name') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="inventory-description">Mô tả</label>
                                <textarea class="control" id="inventory-description" name="inventory-description">{{ old('inventory-description') }}</textarea>
                            </div>

                            <!-- <div class="control-group">
                                <label for="latitude">{{ __('admin::app.settings.inventory_sources.latitude') }}</label>
                                <input class="control" id="latitude" name="latitude" value="{{ old('latitude') }}"/>
                            </div>

                            <div class="control-group">
                                <label for="longitude">{{ __('admin::app.settings.inventory_sources.longitude') }}</label>
                                <input class="control" id="longitude" name="longitude" value="{{ old('longitude') }}"/>
                            </div>

                            <div class="control-group">
                                <label for="priority">{{ __('admin::app.settings.inventory_sources.priority') }}</label>
                                <input class="control" id="priority" name="priority" value="{{ old('priority') }}"/>
                            </div> -->

                            <div class="control-group">
                                <label for="inventory-status">Trạng thái</label>

                                <label class="switch">
                                    <input type="checkbox" id="inventory-status" name="inventory-status" value="1" {{ old('inventory-status') ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'Thông tin liên hệ'" :active="true">
                        <div slot="body">

                            <div class="control-group" :class="[errors.has('contact_name') ? 'has-error' : '']">
                                <label for="contact_name" class="required">Tên người liên hệ</label>
                                <input class="control" v-validate="'required'" id="contact_name" name="contact_name" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.contact_name') }}&quot;" value="{{ old('contact_name') }}"/>
                                <span class="control-error" v-if="errors.has('contact_name')">@{{ errors.first('contact_name') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('contact_email') ? 'has-error' : '']">
                                <label for="contact_email" class="required">{{ __('admin::app.settings.inventory_sources.contact_email') }}</label>
                                <input class="control" v-validate="'required|email'" id="contact_email" name="contact_email" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.contact_email') }}&quot;" value="{{ old('contact_email') }}"/>
                                <span class="control-error" v-if="errors.has('contact_email')">@{{ errors.first('contact_email') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('contact_number') ? 'has-error' : '']">
                                <label for="contact_number" class="required">Số điện thoại</label>
                                <input class="control" v-validate="'required'" id="contact_number" name="contact_number" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.contact_number') }}&quot;" value="{{ old('contact_number') }}"/>
                                <span class="control-error" v-if="errors.has('contact_number')">@{{ errors.first('contact_number') }}</span>
                            </div>

                            <div class="control-group">
                                <label for="contact_fax">{{ __('admin::app.settings.inventory_sources.contact_fax') }}</label>
                                <input class="control required" id="country" name="contact_fax" value="{{ old('contact_fax') }}"/>
                            </div>

                        </div>
                    </accordian>

                    <accordian :title="'Địa chỉ kho'" :active="true">
                        <div slot="body">

                            @include ('admin::customers.country-state', ['countryCode' => old('country'), 'stateCode' => old('state')])

                            <div class="control-group" :class="[errors.has('city') ? 'has-error' : '']">
                                <label for="city" class="required">Thành phố</label>
                                <input v-validate="'required'" class="control" id="city" name="city" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.city') }}&quot;" value="{{ old('city') }}"/>
                                <span class="control-error" v-if="errors.has('city')">@{{ errors.first('city') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('street') ? 'has-error' : '']">
                                <label for="street" class="required">Đường</label>
                                <input v-validate="'required'" class="control" id="street" name="street" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.street') }}&quot;" value="{{ old('street') }}"/>
                                <span class="control-error" v-if="errors.has('street')">@{{ errors.first('street') }}</span>
                            </div>

                            <div class="control-group" :class="[errors.has('postcode') ? 'has-error' : '']">
                                <label for="postcode" class="required">Mã bưu chinh</label>
                                <input v-validate="'required'" class="control" id="postcode" name="postcode" data-vv-as="&quot;{{ __('admin::app.settings.inventory_sources.postcode') }}&quot;" value="{{ old('postcode') }}"/>
                                <span class="control-error" v-if="errors.has('postcode')">@{{ errors.first('postcode') }}</span>
                            </div>

                        </div>
                    </accordian>

                    {!! view_render_event('bagisto.admin.settings.inventory.create.after') !!}
                </div>
                        </div>
                        <button type="submit" form="addInventory" class="btn btn-lg btn-primary" >
                            Thêm danh mục
                        </button>
                                    
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
<!-- popup add inventory -->
@push('scripts')
<script>
        $(document).ready(function () {
            $('.label .cross-icon').on('click', function(e) {
                $(e.target).parent().remove();
            })

            $('.actions .trash-icon').on('click', function(e) {
                $(e.target).parents('tr').remove();
            })
            $("#showAddInventoryButton").click(function(){
                $("#add_inventory").css("display","block");
            });
            $("#hideAddInventoryButton").click(function(){
                $("#add_inventory").css("display","none");
            });
        });
    </script>
@endpush