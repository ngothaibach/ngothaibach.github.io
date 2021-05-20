@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.settings.inventory_sources.title') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>Danh sách kho</h1>
            </div>

            <div class="page-action">
                <a href="{{ route('admin.inventory_sources.create') }}" class="btn btn-lg btn-primary">
                    Thêm kho
                </a>
            </div>
        </div>

        <div class="page-content">
            @inject('inventory_sources_products','Webkul\Admin\DataGrids\InventorySourcesProductsDataGrid')
            {!! $inventory_sources_products->render() !!}
        </div>
    </div>
@stop