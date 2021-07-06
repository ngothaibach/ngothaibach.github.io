@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.users.users.title') }}
@stop
<link href="/css/app.css" rel="stylesheet">
<script src="/js/app.js"></script>
@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.users.users.title') }}</h1>
            </div>
            <div class="page-action">
                <a href="{{ route('admin.users.create') }}" class="btn btn-lg btn-primary">
                    {{ __('Add User') }}
                </a>
            </div>
        </div>
        <div class="page-content">
            <div class="page-content">
                <vpt-list-users></vpt-list-users>
            </div>
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
    <script type="text/x-template" id="vpt-list-users-template">
        <form action="#" class="form newtopic" @submit.prevent="save">
                <div>
                <filter-and-search 
                :searchfields = "[
                {name: 'Id hoá đơn', key: 'order_id', columnType: 'number' },
                {name: 'Thời gian', key: 'updated_at', columnType: 'datetime'}, 
                {name: 'Họ Khách hàng', key: 'customer_first_name', columnType: 'string'},
                {name: 'Tên Khách hàng', key: 'customer_last_name', columnType: 'string'},
                {name: 'Tổng tiền hàng', key:'base_sub_total', columnType: 'number'},
                {name: 'Tổng sau giảm giá', key:'base_grand_total', columnType: 'number'},
                {name: 'Trạng thái', key:'status', columnType: 'custom'},
                ]"
                :customfields = "[
                {name: 'Lưu tạm', key: 'temporary' },
                {name: 'Đang xử lý', key: 'processing'}, 
                {name: 'Đã đóng', key: 'closed'},
                {name: 'Đang chờ', key: 'pending'},
                {name: 'Hoàn thành', key: 'completed'},
                {name: 'Đã hủy', key: 'canceled'},
                ]"
                :items="users_list"
                @changeFilter="onChangeFilter"
            ></filter-and-search>
                </div>
                <div>
                    <div v-if="modalOpen" class="modal-parent scrollable" >
                        <div class="modal-container">
                            <div class="modal-header">
                                <slot name="header">
                                    Cảnh báo
                                </slot>
                                <i class="icon remove-icon" v-on:click="closeModal()" ></i>
                            </div>
                            <div class="modal-body">
                                <div class="content">
                                    <p>Bạn có chắc chắn muốn xoá @{{selectedDeleteName}}</p>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" v-on:click="closeModal()">Huỷ</button>
                                <button type="button" class="btn btn-secondary" v-on:click="deleteUser()">Xoá</button>
                            </div>
                        </div>
                    </div>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th v-for="(table_header,index) in table_headers" class="grid_head">
                            <p  v-text="table_header" v-on:click = "sort(sort_list[index]);showArrow(index)" ></p><p :class="arrow" v-if="currentArrow == index" ></p>
                            </th>
                        </tr>
                        </thead>
                        <tbody id='hover-row' v-for="(item,index) in pageOfItems">
                            <tr :class="[form.selected_user ===  item.user_id ? 'table-info' : '']" v-on:click="load_user(item.user_id,item.inventory_id,item.status)">
                                <td v-text="item.user_id"></td>
                                <td v-text="item.user_name"></td>
                                <td v-if="item.status == 0" >Chưa kích hoạt </td>
                                <td v-if="item.status == 1" >Kích hoạt</td>
                                <td v-text="item.email"></td>
                                <td v-text="item.role_name"></td>
                                <td v-text="item.inventory_name"></td>
                                <td>
                                    <i class='icon pencil-lg-icon' v-on:click="editUser(item.user_id)"></i>
                                    <i class='icon trash-icon' v-on:click="openModal(item.user_id,item.user_name)"></i>
                                </td>
                            </tr>
                            <tr v-if="form.selected_user == item.user_id">
                                <td style="border: 1px solid #b3d7f5;" colspan="6">
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
                                            <div class="col-lg-6 col-md-6 col-sm-12" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Kho</label>
                                                        <div class="col-sm-8">
                                                            <select v-model="form.inventory_id" class="form-control">
                                                                    <option 
                                                                        v-for="item in inventories_list" :value="item.id" v-text="item.name">
                                                                    </option>
                                                                </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 col-sm-12" style="align-self: baseline;">
                                                <div class="mb-3">
                                                    <div class="form-group row">
                                                        <label class="col-sm-4 col-form-label">Trạng thái</label>
                                                        <div class="col-sm-8 control-group">
                                                            <label for="status">Kích hoạt</label>
                                                            <label class="switch">
                                                                <input type="checkbox" v-model="checked" v-on:change = "changeStatus">
                                                                <span class="slider round" ></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                            <button type="button" class="btn btn-success" style="width: 120px;" v-on:click = "updateUser(item.user_id)" >Cập nhật</button>
                                    </div>
                                </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="card-footer pb-0 pt-3">
                        <sort-pagination 
                        v-bind:items="filteredItems"
                        v-bind:pageSize = "perPage"
                        v-bind:sortBy ="sortBy"
                        v-bind:currentSortDir ="currentSortDir"
                        @changePage="onChangePage">
                        </sort-pagination>
                    </div>
                </div>
              </form>
            </script>

    <script>
        Vue.component('vpt-list-users', {
            template: '#vpt-list-users-template',
            data() {
                return {
                    date2: '2017-07-04',
                    commission: 123456,
                    //pagination
                    sort_list: [
                        "order_id",
                        "updated_at",
                        "customer_last_name",
                        "base_sub_total",
                        "base_grand_total",
                        "status"
                    ],
                    currentSortDir: "desc",
                    sortBy: "id",
                    pageOfItems: [],
                    perPage: 10,
                    arrow: "custom-arrow-icon-down",
                    currentArrow: 0,
                    filteredItems:{!! json_encode($users_list) !!},
                    inventories_list :{!! json_encode($inventory_list) !!},
                    users_list: {!! json_encode($users_list) !!},
                    //pagination
                    form: new Form({
                        inventory_id:null,
                        status:null,
                        selected_user:null,
                    }),
                    showModal: false,
                    table_headers: [
                        'ID',
                        "Tên",
                        'Trạng thái',
                        'Email',
                        'Role',
                        'Kho',
                        'Hành động'
                    ],
                    product_list: null,
                    price_total: null,
                    checked:false,
                    modalOpen:false,
                    selectedDeleteID:null,
                    selectedDeleteName:null,
                };
            },
            watch: {},
            methods: {
                load_user(userId,invenId,status) {
                    if(this.form.selected_user == userId){
                        this.form.selected_user = null;
                        this.form.inventory_id = null;
                        this.form.status = null;
                        this.checked = false;
                    }else{
                        this.form.selected_user = userId;
                        this.form.inventory_id = invenId;
                        if(status == 1){
                            this.checked = true;
                            this.form.status = 1;
                        }else{
                            this.checked = false;
                            this.form.status = 0;
                        }
                    }
                },
                changeStatus(){
                    if(this.checked){
                        this.form.status = 1
                    }else{
                        this.form.status = 0
                    }
                },
                updateUser() {
                    this.form.post("{{ route('admin.users.quickUpdate') }}")
                        .then((response) => {
                            if (response.data.success == true) {
                                window.location.href =
                                    "{{ route('admin.users.index') }}";
                            } else {
                                console.error("save user NOT successfull");
                            }
                        })
                },
                editUser(id){
                    let url = window.location.href+ "/edit/" + id;
                    window.open(url);
                },
                deleteUser(){
                   axios.get("{{ route('admin.users.delete') }}", {
                                params: {
                                    id: this.selectedDeleteID
                                }
                            })
                            .then(function (response) {
                                this.result = response;

                                if (response.data.redirect) {
                                    window.location.href = response.data.redirect;
                                } else {
                                    location.reload();
                                }
                            }).catch(function (error) {
                                location.reload();
                            });
                    
                },
                openModal(id,name){
                    this.modalOpen = true;
                    this.selectedDeleteID = id;
                    this.selectedDeleteName = name;
                },
                closeModal(){
                    this.modalOpen = false;
                    this.selectedDeleteID = null;
                    this.selectedDeleteName = null;
                },
                //pagination
                onChangePage(pageOfItems) {
                    // update page of items
                    this.pageOfItems = pageOfItems;
                },
                onChangeFilter(filteredItems){
                    this.filteredItems = filteredItems;
                },
                sort(name) {
                    if (this.sortBy != name) {
                        this.sortBy = name;
                        this.currentSortDir = 'desc';
                        this.arrow = 'custom-arrow-icon-down';

                    } else {
                        this.currentSortDir = this.currentSortDir === 'asc' ? 'desc' : 'asc';
                        this.arrow = this.arrow === 'custom-arrow-icon-down' ? 'custom-arrow-icon-up' :
                            'custom-arrow-icon-down';
                    }
                },
                showArrow(number) {
                    this.currentArrow = number;
                },
                //pagination
            }
        });

    </script>
@endpush
