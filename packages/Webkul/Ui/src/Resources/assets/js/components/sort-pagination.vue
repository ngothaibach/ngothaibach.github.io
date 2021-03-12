<template>
    <ul v-if="pager.pages && pager.pages.length" class="pagination" :style="ulStyles" >
        <li class="page-item first" :class="{'disabled': pager.currentPage === 1}" :style="liStyles">
            <a class="page-link" @click="setPage(1)" :style="aStyles">{{labels.first}}</a>
        </li>
        <li class="page-item previous" :class="{'disabled': pager.currentPage === 1}" :style="liStyles">
            <a class="page-link" @click="setPage(pager.currentPage - 1)" :style="aStyles">{{labels.previous}}</a>
        </li>
        <li v-for="page in pager.pages" :key="page" class="page-item page-number" :class="{'active': pager.currentPage === page}" :style="liStyles">
            <a class="page-link" @click="setPage(page)" :style="aStyles">{{page}}</a>
        </li>
        <li class="page-item next" :class="{'disabled': pager.currentPage === pager.totalPages}" :style="liStyles">
            <a class="page-link" @click="setPage(pager.currentPage + 1)" :style="aStyles">{{labels.next}}</a>
        </li>
        <li class="page-item last" :class="{'disabled': pager.currentPage === pager.totalPages}" :style="liStyles">
            <a class="page-link" @click="setPage(pager.totalPages)" :style="aStyles">{{labels.last}}</a>
        </li>
    </ul>
</template>

<script>
    import paginate from 'jw-paginate';
    const defaultLabels = {
        first: 'First',
        last: 'Last',
        previous: 'Previous',
        next: 'Next'
    };
    const defaultStyles = {
        ul: {
            margin: 0,
            padding: 0,
            display: 'inline-block'
        },
        li: {
            listStyle: 'none',
            display: 'inline',
            textAlign: 'center'
        },
        a: {
            cursor: 'pointer',
            padding: '6px 12px',
            display: 'block',
            float: 'left'
        }
    };
    export default {
        props: {
            items: {
                type: Array,
                required: true
            },
            initialPage: {
                type: Number,
                default: 1
            },
            pageSize: {
                type: Number,
                default: 10
            },
            maxPages: {
                type: Number,
                default: 10
            },
            labels: {
                type: Object,
                default: () => defaultLabels
            },
            styles: {
                type: Object
            },
            disableDefaultStyles: {
                type: Boolean,
                default: false
            },
            sortBy: {
                type: String
            },
            currentSortDir: {
                type: String
            }
        },
        data () {
            return {
                pager: {},
                ulStyles: {},
                liStyles: {},
                aStyles: {},
                currentSort:""
            }
        },
        created () {
            if (!this.$listeners.changePage) {
                throw 'Missing required event listener: "changePage"';
            }
            // set default styles unless disabled
            if (!this.disableDefaultStyles) {
                this.ulStyles = defaultStyles.ul;
                this.liStyles = defaultStyles.li;
                this.aStyles = defaultStyles.a;
            }
            // merge custom styles with default styles
            if (this.styles) {
                this.ulStyles = { ...this.ulStyles, ...this.styles.ul };
                this.liStyles = { ...this.liStyles, ...this.styles.li };
                this.aStyles = { ...this.aStyles, ...this.styles.a };
            }
            // set to initial page
            this.setPage(this.initialPage);
        },
        methods: {
            setPage(page) {
                const { items, pageSize, maxPages } = this;
                // get new pager object for specified page
                const pager = paginate(items.length, page, pageSize, maxPages);
                // get new page of items from items array
                const pageOfItems = items.slice(pager.startIndex, pager.endIndex + 1);
                // update pager
                this.pager = pager;
                // emit change page event to parent component
                ///
                this.$emit('changePage', pageOfItems);
            },
            
        },
        computed: {
            changeSort() {
                return  this.sortBy, this.currentSortDir
            }
        },
        watch: {
            items () {
                this.setPage(this.initialPage);
            },
            changeSort(){
                return this.items.sort((a,b) => {
                    let modifier = 1;

                    if(this.currentSortDir === 'desc') modifier = -1;
                    if(a[this.sortBy] < b[this.sortBy]) return -1 * modifier;
                    if(a[this.sortBy] > b[this.sortBy]) return 1 * modifier;
                    return 0;
                })    
            }
        }
    }
</script>
<style lang="scss" scoped>
    .pagination .page-item {
        background: none !important;
        border: none !important;
        border-radius: 3px;
        padding: 7px 14px;
        margin-right: 5px;
        font-size: 16px;
        display: inline-block;
        color: #8e8e8e;
        vertical-align: middle;
        text-decoration: none;
    }
</style>