<input type="text" v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" @isset($product) value="{{ old($attribute->code) ?: $product[$attribute->code] }}" @endisset {{ in_array($attribute->code, ['sku', 'url_key']) ? 'v-slugify' : '' }} data-vv-as="&quot;{{ $attribute->admin_name }}&quot;" @if(isset($product)) {{ $attribute->code == 'name' && ! $product[$attribute->code]  ? 'v-slugify-target=\'url_key\'' : ''  }} @endif />



