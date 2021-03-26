<datetime>
    <input type="text" name="{{ $attribute->code }}" class="control" v-validate="{{ $attribute->is_required ? 'required' : '' }}" @if(isset($product)) value="{{  old($attribute->code) ?: $product[$attribute->code]}}" @endif data-vv-as="&quot;{{ $attribute->admin_name }}&quot;">
</datetime>