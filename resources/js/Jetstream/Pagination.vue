<template>
    <div class="bg-white p-4 px-6 flex w-full items-center justify-between lg:border-t border-gray-200 rounded-b-lg mt-10 lg:mt-0 shadow-md lg:shadow-none">
        <div class="flex-1 flex items-center justify-center">
            <div class="hidden lg:block">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" @mouseenter="refreshParams">
                    <Link :href="pagination.prev_page_url + getParams"
                          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                          :class="(pagination.current_page === 1) ? 'cursor-default pointer-events-none text-gray-300' : ''"
                          :disabled="pagination.current_page === 1">
                        <chevron-left-icon class="w-5 h-5"/>
                    </Link>

                    <Link :href="link.url + getParams" v-for="(link, index) in pageLinks"
                          :key="`pagination-page-number-${index}`"
                          :class="link.active ? 'z-10 bg-indigo-500 border-indigo-600 text-white relative inline-flex items-center px-4 py-2 border text-sm font-medium': 'bg-white border-gray-300 text-gray-500 hover:bg-gray-100 relative inline-flex items-center px-4 py-2 border text-sm font-medium'">
                        {{ link.label }}
                    </Link>

                    <Link :href="pagination.next_page_url + getParams"
                          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                          :class="(pagination.current_page === pagination.last_page) ? 'cursor-default pointer-events-none text-gray-300' : ''"
                          :disabled="pagination.current_page === pagination.last_page">
                        <chevron-right-icon class="w-5 h-5"/>
                    </Link>
                </nav>
            </div>

            <div class="lg:hidden">
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px my-4" @mouseenter="refreshParams">
                    <Link :href="pagination.prev_page_url + getParams"
                          class="relative inline-flex items-center p-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                          :class="(pagination.current_page === 1) ? 'cursor-default pointer-events-none text-gray-300' : ''"
                          :disabled="pagination.current_page === 1">
                        <chevron-left-icon class="w-5 h-5"/>
                    </Link>

                    <Link :href="link.url + getParams" v-for="(link, index) in mobilePageLinks"
                          :key="`pagination-page-number-${index}`"
                          :class="link.active
                            ? 'z-10 bg-indigo-500 border-indigo-600 text-white relative inline-flex items-center px-4 py-3 border text-sm font-medium'
                            : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-100 relative inline-flex items-center px-4 py-3 border text-sm font-medium'
                          ">
                        {{ link.label }}
                    </Link>

                    <Link :href="pagination.next_page_url + getParams"
                          class="relative inline-flex items-center p-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                          :class="(pagination.current_page === pagination.last_page) ? 'cursor-default pointer-events-none text-gray-300' : ''"
                          :disabled="pagination.current_page === pagination.last_page">
                        <chevron-right-icon class="w-5 h-5"/>
                    </Link>
                </nav>
            </div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'
import {Link} from '@inertiajs/inertia-vue3'
import {ChevronLeftIcon, ChevronRightIcon} from '@heroicons/vue/solid'
import UrlQueryService from "@/Services/UrlQueryService";

export default defineComponent({
    name: 'Pagination',

    components: {
        Link,
        ChevronLeftIcon,
        ChevronRightIcon,
    },

    props: ['pagination'],

    data() {
        return {
            getParams: '',
        };
    },

    computed: {
        pageLinks() {
            return this.pagination.links.slice(1, (this.pagination.links.length - 1));
        },

        mobilePageLinks() {
            const links       = [];
            const activeIndex = this.pageLinks.findIndex(i => i.active);

            for (let i = -2; i <= 2; i++) {
                if ((activeIndex + i) >= 0 && (activeIndex + i) < this.pageLinks.length) {
                    links.push(this.pageLinks[activeIndex + i]);
                }
            }

            if (links.length < 5) {
                const count = 5 - links.length;

                for (let i = 0; i < count; i++) {
                    if (activeIndex < 2) {
                        if (this.pageLinks[links.length]) links.push(this.pageLinks[links.length]);
                    } else {
                        if (this.pageLinks[links[0].label - 3]) links.unshift(this.pageLinks[links[0].label - 3]);
                    }
                }
            }

            return links;
        },
    },

    methods: {
        refreshParams() {
            UrlQueryService.fresh();
            UrlQueryService.remove('page');
            const params = UrlQueryService.encodedString();

            this.getParams = params ? '&' + params : '';
        },
    },
});
</script>
