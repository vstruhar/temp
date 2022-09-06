<template>
    <div class="lg:border lg:border-gray-200 rounded-lg w-full">
        <div class="lg:overflow-x-auto mb-6 lg:mb-0">
            <table class="min-w-full lg:divide-y lg:divide-gray-200">
                <thead class="hidden lg:table-header-group">
                <slot name="thead" class="bg-gray-50"></slot>
                </thead>

                <tbody class="lg:bg-white divide-y divide-gray-200 grid gap-6 grid-cols-1 sm:grid-cols-2 lg:table-row-group">
                <slot name="tbody"></slot>
                </tbody>
            </table>
        </div>

        <jet-pagination v-if="pagination.total > 0" :pagination="pagination"/>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/solid'
import JetPagination from '@/Jetstream/Pagination'
import UrlQueryService from "@/Services/UrlQueryService";
import {pickBy} from "lodash";
import EventBus from "@/Services/EventBus";

export default defineComponent({
    components: {
        ChevronLeftIcon,
        ChevronRightIcon,
        JetPagination,
    },

    props: {
        pagination: {
            type: Object,
            default: () => ({}),
        },
        orderedBy: {
            type: String,
            default: '',
        },
        direction: {
            type: String,
            default: 'desc',
        },
    },

    data() {
        return {
            params: {
                field: null,
                direction: null,
            },
        };
    },

    mounted() {
        UrlQueryService.fresh();
        this.params.field     = window.localStorage.getItem('sort.field') || UrlQueryService.get('field', this.orderedBy);
        this.params.direction = window.localStorage.getItem('sort.direction') || UrlQueryService.get('direction', this.direction);
    },

    methods: {
        sort(field) {
            this.params.field     = field;
            this.params.direction = (this.params.field === field)
                ? this.params.direction === 'asc' ? 'desc' : 'asc'
                : 'asc';

            const params = pickBy(this.params); // remove empty fields
            Object.keys(params).forEach(key => UrlQueryService.set(key, params[key]));

            EventBus.emit('filter');
        },

        isOrderedBy(field) {
            return this.params.field === field;
        },

        isAscendingOrder(field) {
            return this.isOrderedBy(field) && this.params.direction === 'asc';
        },
    },
})
</script>
