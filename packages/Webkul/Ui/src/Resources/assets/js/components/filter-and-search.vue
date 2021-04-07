<template>
<div>
<div class="datagrid-filters">
    <div class="dropdown-filters">
        <div class="dropdown-toggle">
            <div class="grid-dropdown-header">
                <span class="name">Bộ lọc</span>
                <i class="icon arrow-down-icon active"></i>
            </div>
        </div>
        <div class="dropdown-list dropdown-container" style="display: none;">
            <ul>
                <li>
                    <div class="control-group">
                        <select class="filter-column-select control" v-model="filterColumn" @change="getSelectedType(filterColumn)">
                            <option selected disabled>Chọn cột</option>
                            <option  v-for="field in searchfields" :value="field.value">{{field.name}}</option>
                        </select>
                    </div>
                </li>
                <div v-if="filterColumn != null">
                <!-- suitable for string columns  -->
                <div v-if="selectedType == 'string' ">
                    <li >
                        <div class="control-group">
                            <select class="control" v-model="stringCondition">
                                <option selected
                                        disabled>Chọn điều khiện</option>
                                <option value="like">Chứa</option>
                                <option value="nlike">Không chứa</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                            </select>
                        </div>
                    </li>
                    <!-- Response fields based on the type of columns to be filtered  -->
                    <li v-if="stringCondition != null">
                        <div class="control-group">
                            <input type="text" class="control response-string"
                                placeholder="Giá trị"
                                v-model="stringValue"/>
                        </div>
                    </li>
                </div>
                <!-- suitable for numeric columns  -->
                <div v-else-if="selectedType == 'number' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="numberCondition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                                <option value="gt">Lớn hơn</option>
                                <option value="lt">Nhỏ hơn</option>
                                <option value="gte">Lớn hơn hoặc bằng</option>
                                <option value="lte">Bé hơn hoặc bằng</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="numberCondition != null" >
                        <div class="control-group">
                            <input type="text" class="control response-number" v-on:input="filterNumberInput" placeholder="0"  v-model="numberValue"/>
                        </div>
                    </li>
                </div>
                <!-- suitable for boolean columns  -->
                <div v-else-if="selectedType == 'boolean' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="booleanCondition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="booleanCondition != null">
                        <div class="control-group">
                            <select class="control" v-model="booleanValue">
                                <option selected disabled>Giá trị</option>
                                <option value="1">True/ Active</option>
                                <option value="0">False/ inActive</option>
                            </select>
                        </div>
                    </li>
                </div>
                <!-- datetime column -->
                <div v-else>
                    <li >
                        <div class="control-group">
                            <select class="control" v-model="datetimeCondition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                                <option value="gt">Lớn hơn</option>
                                <option value="lt">Nhỏ hơn</option>
                                <option value="gte">Lớn hơn hoặc bằng</option>
                                <option value="lte">Bé hơn hoặc bằng</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="datetimeCondition != null">
                        <div class="control-group">
                            <input class="control" v-model="datetimeValue" type="date">
                        </div>
                    </li>
                </div>
                <button class="btn btn-sm btn-primary apply-filter" v-on:click="getResponse">Tìm</button>
                </div>
            </ul>
        </div>
    </div>
</div>
</div>
</template>

<script>
    export default {
        props: {
                url: {
                    type: String,
                    required: false
                },
                searchfields: {
                    type:Array,
                    required: false
                }
            },
            data() {
                return{
                    filterColumn: null,
                    selectedType: null,
                    selectedValue: '',
                    stringCondition: null,
                    booleanCondition: null,
                    numberCondition: null,
                    datetimeCondition: null,
                    stringValue: null,
                    booleanValue: null,
                    datetimeValue: '2000-01-01',
                    numberValue: 0,
                    filters: [],
                    parsedUrl : []
                    
                }
            },
            methods: {
                newRoute(){
                    if(this.filterColumn != null && this.stringValue != null){
                        window.location.href = this.url + '?' + 'field=' + this.filterColumn + '&key=' + this.stringValue;
                    }
                },
                getSelectedType(value){
                    this.selectedValue = value;
                    for (let index in this.searchfields) {
                        if (this.searchfields[index].value === this.selectedValue) {
                            this.selectedType = this.searchfields[index].columnType;
                        }
                    } 
                },
                filterNumberInput: function(e){
                    this.numberValue = e.target.value.replace(/[^0-9\,\.]+/g, '');
                },
                getResponse: function() {
                    let label = '';
                    for (let index in this.searchfields) {
                        if (this.searchfields[index].value == this.selectedValue) {
                            label = this.searchfields[index].name;
                            break;
                        }
                    }
                    if (this.selectedType === 'string' && this.stringValue !== null) {
                        this.formURL(this.selectedValue, this.stringCondition, encodeURIComponent(this.stringValue), label)
                    } else if (this.selectedType === 'number') {
                        let indexConditions = true;
                        if (this.filterIndex === this.selectedValue
                            && (this.numberValue === 0 || this.numberValue < 0)) {
                            indexConditions = false;
                            alert('Index columns can have values greater than zero only');
                        }
                        if (indexConditions) {
                            this.formURL(this.selectedValue, this.numberCondition, this.numberValue, label);
                        }
                    } else if (this.selectedType === 'boolean') {
                        this.formURL(this.selectedValue, this.booleanCondition, this.booleanValue, label);
                    } else if (this.selectedType === 'datetime') {
                        this.formURL(this.selectedValue, this.datetimeCondition, this.datetimeValue, label);
                    } else if (this.selectedType === 'price') {
                        this.formURL(this.selectedValue, this.numberCondition, this.numberValue, label);
                    }
                },
                formURL: function (column, condition, response, label) {
                    var obj = {};
                    if (column === "" || condition === "" || response === ""
                        || column === null || condition === null || response === null) {
                        alert('Some of the required field is null, please check column, condition and value properly');
                        return false;
                    } else {
                        if (this.filters.length > 0) {
                            if (column !== "sort" && column !== "search") {
                                let filterRepeated = false;
                                for (let j = 0; j < this.filters.length; j++) {
                                    if (this.filters[j].column === column) {
                                        if (this.filters[j].cond === condition && this.filters[j].val === response) {
                                            filterRepeated = true;
                                            return false;
                                        } else if (this.filters[j].cond === condition && this.filters[j].val !== response) {
                                            filterRepeated = true;
                                            this.filters[j].val = response;
                                            this.makeURL();
                                        }
                                    }
                                }
                                if (filterRepeated === false) {
                                    obj.column = column;
                                    obj.cond = condition;
                                    obj.val = response;
                                    obj.label = label;
                                    this.filters.push(obj);
                                    obj = {};
                                    this.makeURL();
                                }
                            }
                            if (column === "sort") {
                                let sort_exists = false;
                                for (let j = 0; j < this.filters.length; j++) {
                                    if (this.filters[j].column === "sort") {
                                        if (this.filters[j].column === column && this.filters[j].cond === condition) {
                                            this.findCurrentSort();
                                            if (this.currentSort === "asc") {
                                                this.filters[j].column = column;
                                                this.filters[j].cond = condition;
                                                this.filters[j].val = this.sortDesc;
                                                this.makeURL();
                                            } else {
                                                this.filters[j].column = column;
                                                this.filters[j].cond = condition;
                                                this.filters[j].val = this.sortAsc;
                                                this.makeURL();
                                            }
                                        } else {
                                            this.filters[j].column = column;
                                            this.filters[j].cond = condition;
                                            this.filters[j].val = response;
                                            this.filters[j].label = label;
                                            this.makeURL();
                                        }
                                        sort_exists = true;
                                    }
                                }
                                if (sort_exists === false) {
                                    if (this.currentSort === null)
                                        this.currentSort = this.sortAsc;
                                    obj.column = column;
                                    obj.cond = condition;
                                    obj.val = this.currentSort;
                                    obj.label = label;
                                    this.filters.push(obj);
                                    obj = {};
                                    this.makeURL();
                                }
                            }
                            if (column === "search") {
                                let search_found = false;
                                for (let j = 0; j < this.filters.length; j++) {
                                    if (this.filters[j].column === "search") {
                                        this.filters[j].column = column;
                                        this.filters[j].cond = condition;
                                        this.filters[j].val = encodeURIComponent(response);
                                        this.filters[j].label = label;
                                        this.makeURL();
                                    }
                                }
                                for (let j = 0; j < this.filters.length; j++) {
                                    if (this.filters[j].column === "search") {
                                        search_found = true;
                                    }
                                }
                                if (search_found === false) {
                                    obj.column = column;
                                    obj.cond = condition;
                                    obj.val = encodeURIComponent(response);
                                    obj.label = label;
                                    this.filters.push(obj);
                                    obj = {};
                                    this.makeURL();
                                }
                            }
                        } else {
                            obj.column = column;
                            obj.cond = condition;
                            obj.val = encodeURIComponent(response);
                            obj.label = label;
                            this.filters.push(obj);
                            obj = {};
                            this.makeURL();
                        }
                    }
                },
                makeURL: function () {
                    let newParams = '';
                    for(let i = 0; i < this.filters.length; i++) {
                        if (this.filters[i].column == 'status' || this.filters[i].column == 'value_per_locale' || this.filters[i].column == 'value_per_channel' || this.filters[i].column == 'is_unique') {
                            if (this.filters[i].val.includes("True")) {
                                this.filters[i].val = 1;
                            } else if (this.filters[i].val.includes("False")) {
                                this.filters[i].val = 0;
                            }
                        }
                        let condition = '';
                        if (this.filters[i].cond !== undefined) {
                            condition = '[' + this.filters[i].cond + ']';
                        }
                        if (i == 0) {
                            newParams = '?search=true&' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        } else {
                            newParams = newParams + '&' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        }
                    }
                    var uri = window.location.href.toString();
                    var clean_uri = uri.substring(0, uri.indexOf("?")).trim();
                    window.location.href = clean_uri + newParams;
                },
            },
           
        }
</script>