<template>
    <div class="relative flex rounded-md shadow-sm max-w-[180px] sm:max-w-[210px]">
        <x-icon v-if="value.length"
                class="w-4 h-4 absolute text-gray-400 z-20 cursor-pointer"
                style="top: 11px; right: 13px;"
                @click="clear"/>

        <div class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">
            <search-icon class="w-4 h-4 text-gray-400"/>
        </div>

        <input v-model="value"
               @keyup="triggerFilter"
               type="text"
               class="z-10 pl-4 flex-1 block w-full rounded-none rounded-r-md text-sm border-gray-300 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none"
               :placeholder="placeholder || __('Find invoices')"
               @keyup.enter="filter">
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import UrlQueryService from '@/Services/UrlQueryService'
import {SearchIcon, XIcon} from '@heroicons/vue/solid'
import {debounce} from 'lodash'

export default defineComponent({
    emits: ['changed'],

    components: {
        SearchIcon, XIcon,
    },

    props: {
        placeholder: String,
    },

    data() {
        return {
            value: '',
        };
    },

    mounted() {
        this.value = UrlQueryService.get('filter', '');
    },

    methods: {
        triggerFilter: debounce(function () {
            this.filter();
        }, 400),

        filter() {
            UrlQueryService.set('filter', this.value);

            UrlQueryService.remove('field');
            UrlQueryService.remove('direction');

            this.$emit('changed');
        },

        clear() {
            this.value = '';
            this.filter();
        },
    },
});
</script>
