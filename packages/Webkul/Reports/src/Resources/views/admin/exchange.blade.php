@extends('admin::layouts.content')

@section('page_title')
    Báo cáo bán hàng
@stop

<link href="/css/app.css" rel="stylesheet">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="{{ asset('vendor/webkul/admin/assets/js/tinyMCE/tinymce.min.js') }}"></script>
<script src="/js/app.js"></script>
<style>
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>

@section('content')

    <div class="content" style="height: 100%;">
        <div class="page-header">
            <div class="page-title">
                <h1>Báo cáo xuất nhập hàng</h1>
            </div>
        </div>

        <div class="page-content">
            <report-form></report-form>
        </div>
    </div>

@stop

@push('scripts')
    <script type="text/x-template" id="report-form-template">
        <form action="{{ route('reports.admin.export_exchange_report')}}" class="form newtopic" method="POST" @submit.prevent="onSubmit">
            @csrf()
            <div>
                <div class="mb-3">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Từ ngày</label>
                        <div class="col-sm-8">
                            <input type="date" id="from_date" name="from_date" class="form-control" style="width: 200px">
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Đến ngày</label>
                        <div class="col-sm-8">
                            <input type="date" id="to_date" name="to_date" class="form-control" style="width: 200px">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Từ chi nhánh</label>
                        <div class="col-sm-8">
                            <select class="w3-input" name="from_inventory_id" style="width: 200px">
                                <option value="0">Tất cả</option>
                                <option 
                                    v-for="item in form.list_inventory" :value="item.id" v-text="item.name">
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Tới chi nhánh</label>
                        <div class="col-sm-8">
                            <select class="w3-input" name="to_inventory_id" style="width: 200px">
                                <option value="0">Tất cả</option>
                                <option 
                                    v-for="item in form.list_inventory" :value="item.id" v-text="item.name">
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="form-group row">
                        <label class="col-sm-4 col-form-label">Loại phiếu</label>
                        <div class="col-sm-8">
                            <select class="w3-input" id="exchange_id" name ="exchange_id" style="width: 200px">
                                <option value="0">Tất cả</option>
                                <option 
                                    v-for="item in form.list_type_exhange" :value="item.type" v-text="item.type == 'receipt'?'Nhận hàng' : 'Chuyển hàng'">
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" style="marginRight : 20px;width: 120px;"  >Xuất báo cáo</button>
            </div>
        </form>
    </script>
    <script>
        Vue.component('report-form',{
            template: '#report-form-template',
            data() {
                return {
                    form: new Form({
                        from_date: '',
                        to_date: '',
                        list_inventory: {!! json_encode($inventory_sources) !!},
                        list_type_exhange: {!! json_encode($typeExchange) !!},
                    })
                }
            },
            methods: {
                onSubmit: function(e) {
                    var fromDate = document.getElementById('from_date').value;
                    var toDate = document.getElementById('to_date').value;
                    if (fromDate == '' || toDate == '') {
                        alert('Bạn chưa chọn ngày');
                    } else {
                        var this_this = this;
                        e.target.submit();
                    }
                }
            }
        });
    </script>
@endpush