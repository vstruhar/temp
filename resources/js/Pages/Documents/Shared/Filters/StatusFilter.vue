<template>
    <div class="hidden md:flex rounded-lg bg-white border border-gray-300 shadow-sm rounded-md overflow-hidden divide-x divide-gray-200">
        <div v-for="(item, index) in statusItems"
             :key="`invoice-status-filter-${index}`"
             type="button"
             class="px-3 py-2 flex items-center text-xs text-gray-600 font-medium focus-visible:ring-2 focus-visible:ring-teal-500 focus-visible:ring-offset-2 focus:outline-none focus-visible:ring-offset-gray-100 cursor-pointer"
             :class="status === item.value ? 'bg-indigo-500 shadow-sm ring-1 ring-black ring-opacity-5 text-white' : 'hover:text-blue-600'"
             @click="filterByStatus(item.value)">

            <span :class="status === item.value ? 'text-white' : 'text-gray-600 hover:text-blue-700'" class="whitespace-nowrap">
                {{ item.label }}
            </span>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import UrlQueryService from '@/Services/UrlQueryService'

export default defineComponent({
    name: "StatusFilter",

    emits: ['changed'],

    data() {
        return {
            status: '',
            statusItems: [
                {value: '', label: this.__('All')},
                {value: 'paid', label: this.__('Paid')},
                {value: 'outstanding', label: this.__('Outstanding')},
                {value: 'overdue', label: this.__('Overdue')},
            ],
        };
    },

    mounted() {
        this.status = UrlQueryService.get('status', '');
    },

    methods: {
        filterByStatus(status) {
            this.status = status;
            UrlQueryService.set('status', status);

            this.$emit('changed');
        },
    },
});
</script>
