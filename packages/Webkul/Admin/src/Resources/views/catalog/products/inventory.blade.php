@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.catalog.products.title') }}
@stop

@section('content')
    <div class="content" style="height: 100%;">
        <div class="page-header">
            <div class="page-title">
                <h1>Tồn kho</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span >
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>

                <a href="{{ route('admin.catalog.products.create') }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.catalog.products.add-product-btn-title') }}
                </a>
            </div>
        </div>

        {!! view_render_event('bagisto.admin.catalog.products.list.before') !!}

        <div class="page-content">
            @inject('products_by_inventory', 'Webkul\Admin\DataGrids\ProductByInventoryDataGrid')
            {!! $products_by_inventory->setInventory($id) !!}
            {!! $products_by_inventory->render() !!}
        </div>

        {!! view_render_event('bagisto.admin.catalog.products.list.after') !!}

    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>
@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => $products_by_inventory])
    <script>

        function reloadPage(getVar, getVal) {
            let url = new URL(window.location.href);
            url.searchParams.set(getVar, getVal);

            window.location.href = url.href;
        }

    </script>
@endpush