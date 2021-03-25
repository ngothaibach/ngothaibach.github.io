@if(isset($product))
    @if ($product[$attribute->code])
        <a href="{{ route('admin.catalog.products.file.download', [$product->product_id, $attribute->id] )}}">
            <img src="{{ Storage::url($product[$attribute->code]) }}" class="configuration-image"/>
        </a>
    @endif
@endif
<input type="file" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" @if(isset($product)) value="{{ old($attribute->code) ?: $product[$attribute->code] }}" @endif data-vv-as="&quot;{{ $attribute->admin_name }}&quot;" style="padding-top: 5px;"/>
@if(isset($product))
    @if ($product[$attribute->code])
        <div class="control-group" style="margin-top: 5px;">
            <span class="checkbox">
                <input type="checkbox" id="{{ $attribute->code }}[delete]"  name="{{ $attribute->code }}[delete]" value="1">

                <label class="checkbox-view" for="delete"></label>
                    {{ __('admin::app.configuration.delete') }}
            </span>
        </div>
    @endif
@endif