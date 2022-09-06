<template>
    <div class="relative flex rounded-md shadow-sm max-w-[200px]">
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
               class="z-10 pl-4 flex-1 block w-full rounded-none rounded-r-md sm:text-sm border-gray-300 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none"
               :placeholder="placeholder"
               @keyup.enter="filter">
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import UrlQueryService from '@/Services/UrlQueryService'
    import {SearchIcon, XIcon} from '@heroicons/vue/solid'
    import {debounce} from 'lodash'
    import EventBus from "@/Services/EventBus";

    export default defineComponent({
        components: {
            SearchIcon, XIcon,
        },

        props: {
            url: String,
            placeholder: String,
        },

        data() {
            return {
                value: '',
            };
        },

        mounted() {
            this.value = UrlQueryService.get('filter', '');

            EventBus.on('filter', this.filter);
        },

        beforeUnmount() {
            EventBus.off('filter', this.filter);
        },

        methods: {
            triggerFilter: debounce(function () {
                this.filter();
            }, 400),

            filter() {
                UrlQueryService.set('filter', this.value);
                UrlQueryService.set('page', 1);

                const params = UrlQueryService.all();

                this.$inertia.get(this.url, params, {preserveState: true})
            },

            clear() {
                this.value = '';
                this.filter();
            },
        },
    });
</script>
