@if(isset($product)) 
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.before', ['product' => $product]) !!}
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.before') !!}
@endif
<accordian :title="'{{ __('admin::app.catalog.products.inventories') }}'" :active="false">
    <div slot="body" style="height: 500px;  overflow: scroll;">
        @if(isset($product))
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.controls.before', ['product' => $product]) !!}
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.controls.before') !!}
        @endif
        @foreach ($inventorySources as $inventorySource)
            <?php

                $qty = 0;
                if(isset($product)){
                    foreach ($product->inventories as $inventory) {
                        if ($inventory->inventory_source_id == $inventorySource->id) {
                            $qty = $inventory->qty;
                            break;
                        }
                    }

                    $qty = old('inventories[' . $inventorySource->id . ']') ?: $qty;
                }        
            ?>
            <div class="control-group" :class="[errors.has('inventories[{{ $inventorySource->id }}]') ? 'has-error' : '']" style="display:flex; flex-direction: row; justify-content: center; align-items: center" >
                <label>{{ $inventorySource->name }}</label>

                <input style="width: 200px;margin-right:200px;" type="text" v-validate="'numeric|min:0'" name="inventories[{{ $inventorySource->id }}]" class="control" value="{{ $qty }}" data-vv-as="&quot;{{ $inventorySource->name }}&quot;"/>
                
                <span class="control-error" v-if="errors.has('inventories[{{ $inventorySource->id }}]')">@{{ errors.first('inventories[{!! $inventorySource->id !!}]') }}</span>
            </div>
        
        @endforeach

        @if(isset($product)) 
            {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.controls.after', ['product' => $product]) !!} 
        @else
            {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.controls.after') !!}
        @endif

    </div>
</accordian>

@if(isset($product)) 
    {!! view_render_event('bagisto.admin.catalog.product.edit_form_accordian.inventories.after', ['product' => $product]) !!} 
@else
    {!! view_render_event('bagisto.admin.catalog.product.create_form_accordian.inventories.after') !!}
@endif