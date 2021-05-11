<template>
<div>
<div class="datagrid-filters">
    <div class="dropdown-filters">
        <div class="dropdown-toggle">
            <div class="grid-dropdown-header" >
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
                            <option  v-for="field in searchfields" :value="field.key">{{field.name}}</option>
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
                <!-- suitable for boolean columns  -->
                <div v-else-if="selectedType == 'custom' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="customCondition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="customCondition != null">
                        <div class="control-group">
                            <select class="control" v-model="customValue">
                                <option selected disabled>Giá trị</option>
                                <option  v-for="custom in customfields" :value="custom.key">{{custom.name}}</option>
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
<div class="filtered-tags">
    <span class="filter-tag" v-if="filters.length > 0" v-for="filter in filters" style="text-transform: capitalize;">
        <span v-if="filter.column == 'perPage'">perPage</span>
        <span v-else>{{ filter.label }}</span>
        <span class="wrapper" v-if="filter.prettyValue">
            {{ filter.prettyValue }}
            <span class="icon cross-icon" v-on:click="removeFilter(filter)"></span>
        </span>
        <span class="wrapper" v-else>
            {{ decodeURIComponent(filter.val) }}
            <span class="icon cross-icon" v-on:click="removeFilter(filter)"></span>
        </span>
    </span>
</div>
</div>
</template>

<script>
    export default {
        props: {
                searchfields: {
                    type:Array,
                    required: true
                },
                customfields:{
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
                    customCondition:null,
                    stringValue: null,
                    customValue: null,
                    booleanValue: null,
                    datetimeValue: '2000-01-01',
                    numberValue: 0,
                    filters: [],
                    parsedUrl : [],
                    currentUrl:new URL(window.location.href)
                    
                }
            },
            mounted: function () {
                this.setParamsAndUrl();
            },
            methods: {
                setParamsAndUrl: function () {
                    let params = (new URL(window.location.href)).search;
                    if (params.slice(1, params.length).length > 0) {
                        let obj = {};
                        const processedUrl = this.currentUrl.search.slice(1, this.currentUrl.length);
                        let splitted = [];
                        let moreSplitted = [];

                        splitted = processedUrl.split('&');

                        for (let i = 0; i < splitted.length; i++) {
                            moreSplitted.push(splitted[i].split('='));
                        }

                        for (let i = 0; i < moreSplitted.length; i++) {
                            const key = decodeURI(moreSplitted[i][0]);
                            let value = decodeURI(moreSplitted[i][1]);

                            if (value.includes('+')) {
                                value = value.replace('+', ' ');
                            }

                            obj.column = key.replace(']', '').split('[')[0];
                            obj.cond = key.replace(']', '').split('[')[1]
                            obj.val = value;
 
                            for (let colIndex in this.columns) {
                                if (this.columns[colIndex].index === obj.column) {
                                    obj.label = this.columns[colIndex].label;
                                    if (this.columns[colIndex].type === 'boolean') {
                                        if (obj.val === '1') {
                                            obj.val = 'true';
                                        } else {
                                            obj.val = 'false';
                                        }
                                    }
                                }
                            }
                            if (obj.column !== undefined && obj.val !== undefined) {
                                this.filters.push(obj);
                            }

                            obj = {};
                        }
                    }
                },
                getSelectedType(key){
                    this.selectedValue = key;
                    for (let index in this.searchfields) {
                        if (this.searchfields[index].key === this.selectedValue) {
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
                        if (this.searchfields[index].key == this.selectedValue) {
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
                    } else if (this.selectedType === 'custom') {
                        this.formURL(this.selectedValue, this.customCondition, this.customValue, label);
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
                        let condition = '';
                        if (this.filters[i].cond !== undefined) {
                            condition = '[' + this.filters[i].cond + ']';
                        }
                        if (i == 0) {
                            newParams = '?' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        } else {
                            newParams = newParams + '&' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        }
                    }
                    var uri = window.location.href.toString();
                    var clean_uri = uri.substring(0, uri.indexOf("?")).trim();
                    window.location.href = clean_uri + newParams;
                },
                removeFilter: function (filter) {
                    for (let i in this.filters) {
                        if (this.filters[i].column === filter.column
                            && this.filters[i].cond === filter.cond
                            && this.filters[i].val === filter.val) {
                            this.filters.splice(i, 1);
                            this.makeURL();
                        }
                    }
                },
            },
            
           
        }
</script>
<style lang="scss" scoped>
.grid-dropdown-header {
    display: inline-flex;
    justify-content: space-between;
    align-items: center;
    height: 36px;
    width: 500px ;
    border: 2px solid #C7C7C7;
    border-radius: 3px;
    color: #8E8E8E;
    padding: 0px 5px 0px 5px;
}
.control-group .control {
    background: #fff;
    border: 2px solid #C7C7C7;
    border-radius: 3px;
    width: 90%;
    height: 36px;
    display: inline-block;
    vertical-align: middle;
    transition: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    padding: 0px 10px;
    font-size: 15px;
    margin-top: 10px;
    margin-bottom: 5px;
}
</style>