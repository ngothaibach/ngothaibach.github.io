@extends('admin::layouts.content')

@section('page_title')
{{ __('admin::app.vpt.inventory.receipt-notes') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
<div class="content" style="height: 100%;">
    <div class="page-header">
        <div class="page-title">
            <h1>{{ __('admin::app.vpt.inventory.receipt-notes') }}</h1>
        </div>
        <div class="page-action">
            <a href="{{ route('admin.exchange.purchase-order.create') }}" class="btn btn-lg btn-primary">
                {{ __('admin::app.vpt.inventory.add-receipt-note') }}
            </a>
        </div>
    </div>
    
    {{-- Import Data --}}
    <form action="{{url('/admin/transfer/import-csv')}}" method="POST" enctype="multipart/form-data">
        @csrf
      <input type="file" name="select_file" accept=".xlsx, .xls, .csv"/><br>
     <input type="submit" value="Import file Excel" name="import_csv" class="btn btn-success">
      </form>
    {{-- Import Data --}}

    <div class="page-content">
        <vpt-list-receipt-notes></vpt-list-receipt-notes>
    </div>
</div>
@stop




@push('scripts')
    <script type="text/x-template" id="vpt-list-receipt-notes-template">

        <div>
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th v-for="table_header in table_headers" class="grid_head">
                        <p v-text="table_header"></p>
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach ($receipt_notes as $item)
                    <tr :class="[selected_transfer === {{ $item->id }} ? 'table-info' : '']" @click="load_product({{ $item->id }})">
                        <td>MDH00{{ $item->id }}</td>
                        <td>{{ $item->created_date }}</td>
                        <td>{{ $item->supplier }}</td>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->status }}</td>
                    </tr>
                    <tr v-if="selected_transfer == {{ $item->id }}">
                        <td style="border: 1px solid #b3d7f5;" colspan="5">
                        <div>
                            <div class="tabs">
                                <ul>
                                    <li class="active">
                                        <h4><a>Thông tin</a></h4>
                                    </li>
                                </ul>
                            </div>
                            <div class="tabs-content" style="margin-top:20px;">
                                <div class="row">
                                    <div class="col-4" style="align-self: baseline;">
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Mã nhập hàng</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="MNH00{{ $item->id }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Thời gian</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="{{ $item->created_date }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Nhà cung cấp</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="{{ $item->supplier }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Người tạo</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="{{ $item->created_user }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4" style="align-self: baseline;">
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Trạng thái</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="{{ $item->status }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Chi nhánh</label>
                                                <div class="col-sm-8">
                                                    <input type="text" value="{{ $item->inventory }}" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Người nhập</label>
                                                <div class="col-sm-8">
                                                    <input type="text" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-4" style="align-self: baseline;">
                                        <div class="mb-3">
                                            <div class="form-group row">
                                                <label class="col-sm-4 col-form-label">Ghi chú</label>
                                                <div class="col-sm-8">
                                                    <textarea class="form-control" id="exampleFormControlTextarea1">{{ $item->note }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <h4>Danh sách sản phẩm nhập</h4>
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th v-for="product_table_header in product_table_headers" class="grid_head">
                                            <p v-text="product_table_header"></p>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="product in product_list">
                                            <td v-text="product.id"></td>
                                            <td><img style="width: 60xp; height: 60px;" v-bind:src="'/cache/small/' + product.featured_image"/></td>
                                            <td v-text="product.name"></td>
                                            <td v-text="product.price"></td>
                                            <td v-text="product.transfer_qty"></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <span class="font-weight-bold">Tổng giá trị:</span> <span class="text-danger font-weight-bold" v-text="price_total"></span>
                                <div class="text-right">
                                    <button type="button" class="btn btn-info">In</button>
                                    <button type="button" class="btn btn-success">Lưu</button>
                                </div>
                            </div>
                        </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

    </script>

    <script>
        Vue.component('vpt-list-receipt-notes', {
            template: '#vpt-list-receipt-notes-template',
            data() {
                return {
                    showModal: false,
                    table_headers: [
                        "{{ __('admin::app.vpt.inventory.order-code') }}",
                        "{{ __('admin::app.vpt.inventory.date') }}",
                        "{{ __('admin::app.vpt.inventory.supplier') }}",
                        "{{ __('admin::app.vpt.inventory.total-of-price') }}",
                        "{{ __('admin::app.vpt.inventory.status') }}"
                    ],
                    product_table_headers: [
                        "{{ __('admin::app.vpt.inventory.code') }}",
                        "{{ __('admin::app.vpt.inventory.image') }}",
                        "{{ __('admin::app.vpt.inventory.name') }}",
                        "{{ __('admin::app.vpt.inventory.price') }}",
                        "{{ __('admin::app.vpt.inventory.qty') }}"
                    ],
                    product_list: null,
                    selected_transfer: null,
                    price_total: null,
                };
            },
            watch: {
            },
            methods: {
                update_total_price() {
                    this.price_total = 0;
                    for (product in this.product_list) {
                        this.price_total += product.price * product.qty
                    }
                },
                load_product(exchange_note_id) {
                    this.product_list = []
                    this.selected_transfer = exchange_note_id;

                    axios.get("{{ route('exchange.admin.get_transfered_products') }}", { params: { transfer_id: exchange_note_id } })
                    .then(response => {
                        this.product_list = response.data.transfered_products;
                        console.error(this.product_list);

                        this.price_total = 0;
                        for (product of this.product_list) {
                            this.price_total += product.price * product.receipt_qty;
                        }
                    });

                    // this.update_total_price();
                    // this.price_total = 0;


                    //this.showModal = true;
                },
                closeModal() {
                    this.showModal = false;
                }
            }
        });
    </script>
@endpush