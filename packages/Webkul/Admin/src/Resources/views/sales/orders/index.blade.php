@extends('admin::layouts.content')

@section('page_title')
    {{-- {{ __('admin::app.sales.orders.title') }} --}}
    Bán hàng
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                {{-- <h1>{{ __('admin::app.sales.orders.title') }}</h1> --}}
                <h1>Đơn hàng</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{-- {{ __('admin::app.export.export') }} --}}
                        Xuất báo cáo
                    </span>
                </div>
            </div>
        </div>

        <div class="page-content">
            @inject('orderGrid', 'Webkul\Admin\DataGrids\OrderDataGrid')
            {!! $orderGrid->render() !!}
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

@stop

@push('scripts')
    @include('admin::export.export', ['gridName' => $orderGrid])
@endpush