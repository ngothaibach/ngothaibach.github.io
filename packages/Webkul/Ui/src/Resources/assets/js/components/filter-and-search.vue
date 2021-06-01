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
                                <option value="like">Bằng</option>
                                <option value="nlike">Khác</option>
                            </select>
                        </div>
                    </li>
                    <li v-if="modelArray[index].condition != null">
                        <div class="control-group">
                            <select class="control" v-model="modelArray[index].vmoel">
                                <option selected disabled>Giá trị</option>
                                <option value="True">True/ Active</option>
                                <option value="False">False/ inActive</option>
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
                                <option value="like">Bằng</option>
                                <option value="nlike">Khác</option>
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
    <button type="button" class="btn btn-sm btn-primary apply-filter" v-on:click="changeFilter">Tìm</button>
    <button type="button" class="btn btn-sm btn-primary apply-filter" v-on:click="defaultFilter">Đặt lại</button>
</div>

<div class="filtered-tags">
    <span class="filter-tag" v-if="filters.length > 0" v-for="filter in filters" style="text-transform: capitalize;">
        <span >{{ filter.fieldname }}</span>
        <span class="wrapper">
            {{  displayCondition(filter.condition) }}
        </span>
        <span class="wrapper" >
            {{filter.value }}
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
                },
                items: {
                    type: Array,
                    required: true
                },
            },
            data() {
                return{  
                    filters: [],
                    modelArray:[],
                    filteredItems:[],
                    conditionName:[
                        {name:"Bằng",value:"eq"},
                        {name:"Lớn hơn",value:"gt"},
                        {name:"Khác",value:"neqs"},
                        {name:"Nhỏ hơn",value:"lt"},
                        {name:"Lớn hơn hoặc bằng",value:"gte"},
                        {name:"Bé hơn hoặc bằng",value:"lte"},
                        {name:"Chứa",value:"like"},
                        {name:"Không chứa",value:"nlike"},
                        {name:"True",value:"True"},
                        {name:"False",value:"False"},
                    ]                    
                }
            },
            mounted: function () {
                this.createModelArray();
                this.defaultFilter();
            },
            methods: {
                createModelArray(){
                   for(let index in this.searchfields){
                       this.modelArray.push({ condition: null,vmodel:null,type: this.searchfields[index].columnType});
                   }
                },
                stringToASCII: function(str){
                    try {
                        return str.replace(/[àáảãạâầấẩẫậăằắẳẵặ]/g, 'a')
                        .replace(/[èéẻẽẹêềếểễệ]/g, 'e')
                        .replace(/[đ]/g, 'd')
                        .replace(/[ìíỉĩị]/g, 'i')
                        .replace(/[òóỏõọôồốổỗộơờớởỡợ]/g, 'o')
                        .replace(/[ùúủũụưừứửữự]/g, 'u')
                        .replace(/[ỳýỷỹỵ]/g, 'y')
                        .replace(/\s/g, '')
                    } catch {
                        return 'no result'
                    }
                },
                defaultFilter(){
                    this.filteredItems = this.items;
                    this.$emit('changeFilter',this.filteredItems)
                },
                filterNumberInput: function(e){
                    this.numberValue = e.target.value.replace(/[^0-9\,\.]+/g, '');
                },
                filterString(condition,value,fieldname) {
                    let keySearch = this.stringToASCII(value);
                    if(condition == "like"){
                        this.filteredItems =  this.filteredItems.filter((item)=>{
                            return keySearch.toLowerCase().split(' ').every(v => this.stringToASCII(item[fieldname]).toLowerCase().includes(v))
                        })
                    }else{
                         this.filteredItems =  this.filteredItems.filter((item)=>{
                            return keySearch.toLowerCase().split(' ').every(v => !this.stringToASCII(item[fieldname]).toLowerCase().includes(v))
                        })
                    }
                },
                filterCustom(condition,value,fieldname) {
                    let keySearch = this.stringToASCII(value);
                    if(condition == "like"){
                        this.filteredItems =  this.filteredItems.filter((item)=>{
                            return keySearch.toLowerCase().split(' ').every(v => this.stringToASCII(item[fieldname]).toLowerCase().includes(v))
                        })
                    }else{
                         this.filteredItems =  this.filteredItems.filter((item)=>{
                            return keySearch.toLowerCase().split(' ').every(v => !this.stringToASCII(item[fieldname]).toLowerCase().includes(v))
                        })
                    }
                },
                filterNumber(condition,value,fieldname) {
                    let input = parseInt(value)
                    if(condition == "eq"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) == input
                        })
                    }else if(condition == "neqs"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) != input
                        })
                    }else if(condition == "gt"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) > input
                        })
                    }else if(condition == "lt"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) < input
                        })
                    }else if(condition == "gte"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) >= input
                        })
                    }else if(condition == "lte"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return parseInt(item[fieldname]) <= input
                        })
                    }
                },
                filterDatetime(condition,value,fieldname){
                    let input = new Date(value).getTime()
                    if(condition == "eq"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() == input
                        })
                    }else if(condition == "neqs"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() != input
                        })
                    }else if(condition == "gt"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() > input
                        })
                    }else if(condition == "lt"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() < input
                        })
                    }else if(condition == "gte"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() >= input
                        })
                    }else if(condition == "lte"){
                        this.filteredItems =this.filteredItems.filter(function(item){
                            return new Date(item[fieldname]).getTime() <= input
                        })
                    }
                },
                changeFilter(){
                    for(let index in this.modelArray){
                        if(this.modelArray[index].condition != null){
                            this.addToFilter(this.modelArray[index].condition,this.modelArray[index].vmodel,this.searchfields[index].name,index);
                        }
                    }
                    this.searchByFilter();
                },
                searchByFilter(){
                    this.filteredItems = this.items;
                    for (let i in this.filters) {
                        this.modelArray[this.filters[i].position].condition = this.filters[i].condition;
                        this.modelArray[this.filters[i].position].vmodel = this.filters[i].value;
                        this.filterItems(this.filters[i].position)
                    }
                    this.$emit('changeFilter',this.filteredItems);
                },
                filterItems: function(index) {
                    if (this.modelArray[index].type === 'string' || this.modelArray[index].type === 'custom' || this.modelArray[index].type === 'boolean' && this.modelArray[index].vmodel !== null) {
                        this.filterString(this.modelArray[index].condition,this.modelArray[index].vmodel,this.searchfields[index].key)
                    } else if (this.modelArray[index].type === 'number') {
                        let indexConditions = true;
                        if (this.filterIndex === this.searchfields[index].key
                            && (this.modelArray[index].vmodel === 0 || this.modelArray[index].vmodel < 0)) {
                            indexConditions = false;
                            alert('Index columns can have values greater than zero only');
                        }
                        if (indexConditions) {
                            this.filterNumber(this.modelArray[index].condition,this.modelArray[index].vmodel,this.searchfields[index].key)
                        }
                    }else if(this.modelArray[index].type === 'datetime'){
                        this.filterDatetime(this.modelArray[index].condition,this.modelArray[index].vmodel,this.searchfields[index].key)
                    }
                },
                addToFilter(condition,value,fieldname,position){
                    let filterRepeated = false;
                    for(let item of this.filters){
                        if(item.condition === condition && item.value === value && item.fieldname === fieldname ){
                            filterRepeated = true;
                            break;
                        }
                    };
                    if(!filterRepeated){
                        let obj={};
                        obj.condition = condition;
                        obj.value = value;
                        obj.fieldname = fieldname;
                        obj.position = position;
                        this.filters.push(obj);
                    }
                },
                removeFilter: function (filter) {
                    for (let i in this.filters) {
                        if (this.filters[i].fieldname === filter.fieldname
                            && this.filters[i].condition === filter.condition
                            && this.filters[i].value === filter.value) {
                            this.filters.splice(i, 1);
                            this.modelArray[filter.position].condition = null;
                            this.modelArray[filter.position].vmodel = null;
                            break;
                        }
                    }
                    this.searchByFilter();
                },
                displayCondition(con){
                    for(let condition of this.conditionName){
                        if(condition.value == con){
                            return condition.name
                        }
                    }
                }
            },
            
           
        }
</script>
<style lang="scss" scoped>
.datagrid-filters{
    display:inline-block;
}
.datagrid-filters .dropdown-filters {
    margin-right: 5px;
    margin-left: 5px;
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
/* Extra small devices (phones, 600px and down) */
@media only screen and (max-width: 600px) {
    .datagrid-filters{
        max-width:100vw;
    }
}

/* Small devices (portrait tablets and large phones, 600px and up) */
@media only screen and (min-width: 600px) {
    .datagrid-filters{
        display:inline-block;
    }
}

/* Medium devices (landscape tablets, 768px and up) */
@media only screen and (min-width: 768px) {
    .content-container {
        max-width: 100%;
    }
}

/* Large devices (laptops/desktops, 992px and up) */
@media only screen and (min-width: 992px) {
    .content-container {
        max-width: 100%;
    }
}

/* Extra large devices (large laptops and desktops, 1200px and up) */
@media only screen and (min-width: 1200px) {
    .content-container {
        max-width: 100%;
    }
}

</style>