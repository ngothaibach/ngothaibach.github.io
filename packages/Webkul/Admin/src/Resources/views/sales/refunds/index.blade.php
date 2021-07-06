@extends('admin::layouts.content')

@section('page_title')
{{ __('admin::app.sales.refunds.title') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content" style="height: 100%;">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.sales.refunds.title') }}</h1>
            </div>
            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
            </div>
        </div>
        <div class="page-content">
               
            <vpt-list-refunds></vpt-list-refunds>
        </div>
    </div>
@stop

@push('scripts')
    <script type="text/x-template" id="vpt-list-refunds-template">
        <form action="#" class="form newtopic">
            <div>
            <filter-and-search 
                :searchfields = "[
                {name: 'Id đơn hàng', key: 'increment_id', columnType: 'number' },
                {name: 'Số tiền hoàn', key: 'base_grand_total', columnType: 'number'}, 
                {name: 'Đơn của', key: 'billed_to', columnType: 'string'},
                {name: 'Ngày hoàn trả', key:'created_at', columnType: 'datetime'},
                ]"
                :items="form.refunds_list"
                @changeFilter="onChangeFilter"
                ></filter-and-search>
            </div>
            <div>                                               
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th v-for="(table_header,index) in table_headers" >
                            <p  v-text="table_header" v-on:click = "sort(sort_list[index]);showArrow(index)" ></p><i :class="arrow" v-if="currentArrow == index" ></i>
                        </th>
                    </tr>
                    </thead>
                    <tbody v-for="(item,index) in pageOfItems" :key="item.id">
                        <tr>
                            <td v-text="item.increment_id"></td>
                            <td v-text="formatPrice(item.base_grand_total)"></td>
                            <td v-text="item.billed_to"></td>
                            <td v-text="item.created_at"></td>
                            <td> <button type="button" class="btn btn-success" v-on:click="openRefundDetail(item.id)">Xem</button></td>
                        </tr>
                    </tbody>
                </table>
                    <div class="card-footer pb-0 pt-3">
                        <sort-pagination 
                        v-bind:items="filteredItems"
                        v-bind:pageSize = "perPage"
                        v-bind:sortBy ="sortBy"
                        v-bind:sortFieldType="sortFieldType"
                        v-bind:currentSortDir ="currentSortDir"
                        @changePage="onChangePage">
                        </sort-pagination>
                    </div>
            </div>
        </form> 
    </script> 

    <script>
    Vue.component('vpt-list-refunds', {
        template: '#vpt-list-refunds-template',
        data() {
            return {
                //pagination
                sort_list: [
                    {name:"increment_id",type:"number"},
                    {name:"base_grand_total",type:"number"},
                    {name:"billed_to",type:"string"},
                    {name:"created_at",type:"string"},
                ],
                currentSortDir: "desc",
                sortBy: "id",
                sortFieldType:"number",
                pageOfItems: [],
                perPage: 10,
                arrow: "icon arrow-down-icon",
                currentArrow : 0,
                filteredItems:{!! json_encode($refunds_list) !!},
                //pagination
                form: new Form({
                    refunds_list: {!! json_encode($refunds_list) !!},
                }),
                showModal: false,
                table_headers: [
                    "Id đơn hàng",
                    "Số tiền hoàn",
                    "Đơn của",
                    "Ngày hoàn trả",
                    "Hành động"
                ],
            };
        },
        watch: {},
        methods: {
            openRefundDetail(id) {
               let url = window.location.href+ "/view/" + id;
               window.open(url);
            },
            //pagination
            onChangePage(pageOfItems) {
                // update page of items
                this.pageOfItems = pageOfItems;
            },    
            onChangeFilter(filteredItems){
                this.filteredItems = filteredItems;
            },
            sort(column){
                if(this.sortBy != column.name){
                    this.sortBy = column.name;
                    this.sortFieldType = column.type;
                    this.currentSortDir = 'desc';
                    this.arrow = 'icon arrow-down-icon';
                    
                }else{
                    this.currentSortDir = this.currentSortDir==='asc'?'desc':'asc';
                    this.arrow = this.arrow=== 'icon arrow-down-icon' ? 'icon arrow-up-icon' : 'icon arrow-down-icon';
                }
            },
            showArrow(number) {
                this.currentArrow = number;
            },
            //pagination
            formatPrice(value) {
                var formatter = new Intl.NumberFormat('en-US', {
                    minimumFractionDigits: 0
                });
                return formatter.format(value);
            },
        },
    });
    </script>
@endpush