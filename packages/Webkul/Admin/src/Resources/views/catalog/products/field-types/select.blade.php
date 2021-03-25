<select v-validate="'{{$validations}}'" class="control" id="{{ $attribute->code }}" name="{{ $attribute->code }}" data-vv-as="&quot;{{ $attribute->admin_name }}&quot;">

    <?php 
    if(isset($product)){
        $selectedOption = old($attribute->code) ?: $product[$attribute->code];
    }
    ?>

    @if ($attribute->code != 'tax_category_id')

        @foreach ($attribute->options()->orderBy('sort_order')->get() as $option)
            <option value="{{ $option->id }}" @if(isset($product)) {{ $option->id == $selectedOption ? 'selected' : ''}} @endif>
                {{ $option->admin_name }}
            </option>
        @endforeach

    @else

        <option value=""></option>

        @foreach (app('Webkul\Tax\Repositories\TaxCategoryRepository')->all() as $taxCategory)
            <option value="{{ $taxCategory->id }}" @if(isset($product)) {{ $taxCategory->id == $selectedOption ? 'selected' : ''}} @endif>
                {{ $taxCategory->name }}
            </option>
        @endforeach

    @endif

</select>