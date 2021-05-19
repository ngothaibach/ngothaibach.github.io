<template>
<div>
<div  class="datagrid-filters">
    <div  v-for="(field,index) in searchfields" class="dropdown-filters">
        <div class="dropdown-toggle">
            <div class="grid-dropdown-header" >
                <span class="name">{{field.name}}</span>
                <i class="icon arrow-down-icon active"></i>
            </div>
        </div>
        <div class="dropdown-list dropdown-container" style="display: none;">
            <ul>
                <div >
                <!-- suitable for string columns  -->
                <div v-if="field.columnType == 'string' ">
                    <li >
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].condition">
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
                    <li v-if="modelArray[index].condition != null">
                        <div class="control-group">
                            <input type="text" class="control response-string"
                                placeholder="Giá trị"
                                v-model="modelArray[index].vmodel"/>
                        </div>
                    </li>
                </div>
                <!-- suitable for numeric columns  -->
                <div v-else-if="field.columnType == 'number' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].condition">
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
                    <li v-if="modelArray[index].condition != null" >
                        <div class="control-group">
                            <input type="text" class="control response-number" v-on:input="filterNumberInput" placeholder="0"  v-model="modelArray[index].vmodel"/>
                        </div>
                    </li>
                </div>
                <!-- suitable for boolean columns  -->
                <div v-else-if="field.columnType == 'boolean' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].condition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="modelArray[index].condition != null">
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].vmoel">
                                <option selected disabled>Giá trị</option>
                                <option value="1">True/ Active</option>
                                <option value="0">False/ inActive</option>
                            </select>
                        </div>
                    </li>
                </div>
                <!-- suitable for boolean columns  -->
                <div v-else-if="field.columnType == 'custom' ">
                    <li>
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].condition">
                                <option selected
                                        disabled>Chọn điều kiện</option>
                                <option value="eq">Bằng</option>
                                <option value="neqs">Khác</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="modelArray[index].condition != null">
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].vmodel">
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
                            <select class="control" v-model="modelArray[index].condition">
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
                    <li v-if="modelArray[index].condition != null">
                        <div class="control-group">
                            <input class="control" v-model="modelArray[index].vmodel" type="date">
                        </div>
                    </li>
                </div>
                </div>
            </ul>
        </div>
    </div>
    <button class="btn btn-sm btn-primary apply-filter" v-on:click="getResponse">Tìm</button>
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
                    currentUrl:new URL(window.location.href),
                    modelArray:[],
                    newParams : '',
                    
                }
            },
            mounted: function () {
                this.setParamsAndUrl();
                this.createModelArray();
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
                createModelArray(){
                    for(let index in this.searchfields){
                        this.modelArray.push({ condition: null,vmodel:null,type: this.searchfields[index].columnType});
                    }
                },
                filterNumberInput: function(e){
                    this.numberValue = e.target.value.replace(/[^0-9\,\.]+/g, '');
                },
                getResponse: function() {
                    for(let index in this.modelArray){
                        if(this.modelArray[index].condition != null){
                            if (this.modelArray[index].type === 'string' && this.modelArray[index].vmodel !== null) {
                                this.formURL(this.searchfields[index].key, this.modelArray[index].condition, encodeURIComponent(this.modelArray[index].vmodel), this.searchfields[index].name)
                            } else if (this.modelArray[index].type === 'number') {
                                let indexConditions = true;
                                if (this.filterIndex === this.searchfields[index].key
                                    && (this.modelArray[index].vmodel === 0 || this.modelArray[index].vmodel < 0)) {
                                    indexConditions = false;
                                    alert('Index columns can have values greater than zero only');
                                }
                                if (indexConditions) {
                                    this.formURL(this.searchfields[index].key, this.modelArray[index].condition, this.modelArray[index].vmodel, this.searchfields[index].name);
                                }
                            } else if (this.modelArray[index].type === 'boolean' || this.modelArray[index].type === 'datetime' || this.modelArray[index].type === 'price' || this.modelArray[index].type === 'custom') {
                                this.formURL(this.searchfields[index].key, this.modelArray[index].condition, this.modelArray[index].vmodel, this.searchfields[index].name);
                            }
                        }  
                    };
                    var uri = window.location.href.toString();
                    var clean_uri = uri.substring(0, uri.indexOf("?")).trim();
                    window.location.href = clean_uri + this.newParams;   
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
                    for(let i = 0; i < this.filters.length; i++) {
                        let condition = '';
                        if (this.filters[i].cond !== undefined) {
                            condition = '[' + this.filters[i].cond + ']';
                        }
                        if (i == 0) {
                            this.newParams = '?' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        } else {
                            this.newParams = this.newParams + '&' + this.filters[i].column + condition + '=' + this.filters[i].val;
                        }
                    }
                },
                removeFilter: function (filter) {
                    for (let i in this.filters) {
                        if (this.filters[i].column === filter.column
                            && this.filters[i].cond === filter.cond
                            && this.filters[i].val === filter.val) {
                            this.filters.splice(i, 1);
                            this.makeURL();
                            var uri = window.location.href.toString();
                            var clean_uri = uri.substring(0, uri.indexOf("?")).trim();
                            window.location.href = clean_uri + this.newParams;   
                        }
                    }
                },
            },
            
           
        }
</script>
<style lang="scss" scoped>
.datagrid-filters{
    display:inline-block;
}
.datagrid-filters .dropdown-filters {
    margin-right: 10px;
    padding-top: 5px;
}
.grid-dropdown-header {
    display: inline-flex;
    justify-content: space-between;
    align-items: center;
    height: 36px;
    width: 150px ;
    border: 2px solid #C7C7C7;
    border-radius: 3px;
    color: #8E8E8E;
    padding: 0px 5px 0px 5px;
    overflow-x:auto;
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
.dropdown-list.dropdown-container {
    padding: 15px;
        width: 150%;
    top: 43px;
}
.btn.btn-primary {
    width: 100px;
    height: 36px;
}
</style>