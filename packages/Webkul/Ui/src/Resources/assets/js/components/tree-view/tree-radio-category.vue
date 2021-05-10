<template>
    <span class="radio-category">
        <input type="radio" :name="nameField" :value="modelValue" :checked="isActive" v-on:input="onSelect($event.target.value)">
        <label class="radio-view" :for="id"></label>
        <span class="" :for="id">{{ label }}</span>
    </span>
</template>

<script>
    import { EventBus } from './event-bus';
    export default {
        name: 'tree-radio-category',

        props: ['id', 'label', 'nameField', 'modelValue', 'value'],

        computed: {
            isActive () {
                if(this.value.length) {
                    return this.value[0] == this.modelValue ? true : false;
                }

                return false
            }
        },
        methods:{
            onSelect(value){
                EventBus.$emit('valueChanged',value);
            }
        }
    }
</script>