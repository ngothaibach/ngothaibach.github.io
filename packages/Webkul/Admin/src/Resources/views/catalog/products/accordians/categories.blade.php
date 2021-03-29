@if ($categories->count())
@if(isset($product))
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.before', ['product' => $product]) !!}
@else
{!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.before') !!}
@endif
<accordian :title="'{{ __('admin::app.catalog.products.categories') }}'" :active="false">
    <div slot="body">
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.categories.controls.before', ['product' => $product]) !!}
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.categories.controls.before') !!}
        @endif
        <tree-view behavior="normal" value-field="id" name-field="categories" input-type="checkbox" items='@json($categories)' @if(isset($product)) value='@json($product->categories->pluck("id"))' @endif ></tree-view>
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