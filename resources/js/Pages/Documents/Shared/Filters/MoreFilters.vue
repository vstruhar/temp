<template>
    <jet-dropdown ref="jetDropdown" align="right" width="64" :close-on-content-click="false">
        <template #trigger>
            <button type="button"
                    class="inline-flex justify-center w-full rounded-lg shadow-sm px-2 lg:px-4 py-2 text-sm font-medium border border-gray-300 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none transition-colors"
                    :class="{'text-gray-200 bg-indigo-500 hover:bg-indigo-500/90': hasFilters, 'text-gray-700 bg-white hover:bg-gray-50': !hasFilters}">
                {{ __('More') }}
                <svg class="hidden lg:block -mr-1 ml-2 h-5 w-5" :class="{'text-gray-200': hasFilters, 'text-gray-500': !hasFilters}" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </template>

        <template #content>
            <div class="px-4 py-3">
                <div class="grid grid-cols-1 gap-4">
                    <div class="col-span-1" v-if="!without.includes('document_numbers')">
                        <jet-label :value="__('Document numbers')"/>
                        <jet-dropdown-select-filter class="relative mt-1 block w-full"
                                                    :max-height="220"
                                                    :options="documentNumbersDropdownItems"
                                                    v-model:value="params.documentNumbers"/>
                    </div>

                    <div class="col-span-1" v-if="!without.includes('payment_methods')">
                        <jet-label :value="__('Payment method')"/>
                        <jet-dropdown-select-filter class="relative mt-1 block w-full"
                                                    :max-height="220"
                                                    :options="paymentMethods"
                                                    v-model:value="params.paymentMethod"/>
                    </div>

                    <div class="col-span-1">
                        <jet-label :value="__('Sum')"/>
                        <div class="grid grid-cols-2 gap-3">
                            <jet-input v-allow.number.comma.minus type="text" class="mt-1 w-full" :placeholder="__('From')" v-model="params.amountFrom"/>
                            <jet-input v-allow.number.comma.minus type="text" class="mt-1 w-full" :placeholder="__('To')" v-model="params.amountTo"/>
                        </div>
                    </div>

                    <div class="cursor-pointer text-center text-sm text-white p-3 bg-indigo-500 rounded-md border border-gray-200 hover:bg-indigo-500/90 transition"
                         :class="{'bg-indigo-500 text-white': false}"
                         @click="filter">
                        {{ __('Filter') }}
                    </div>
                </div>
            </div>
        </template>
    </jet-dropdown>
</template>

<script>
import {defineComponent} from 'vue'
import UrlQueryService from '@/Services/UrlQueryService'
import JetDropdown from '@/Jetstream/Dropdown'
import JetLabel from '@/Jetstream/Label'
import JetInput from '@/Jetstream/Input'
import {FilterIcon} from '@heroicons/vue/solid'
import JetDropdownSelectFilter from "@/Jetstream/DropdownSelectFilter";
import axios from 'axios';
import PaymentMethods from "@/Data/DropdownOptions/PaymentsMethods";

export default defineComponent({
    name: 'MoreFilters',

    emits: ['changed'],

    components: {
        JetDropdownSelectFilter,
        JetDropdown,
        JetLabel,
        JetInput,
        FilterIcon,
    },

    props: {
        without: {
            type: Array,
            default: () => [],
        },
    },

    data() {
        return {
            params: {
                documentNumbers: null,
                paymentMethod: null,
                amountFrom: null,
                amountTo: null,
            },
            documentNumbersDropdownItems: [],
            paymentMethods: PaymentMethods,
            hasFilters: false,
        };
    },

    computed: {
        hasSomeFilters() {
            return Object.keys(this.params).some(key => this.params[key] !== null);
        },

        hasNoFilters() {
            return Object.keys(this.params).every(key => this.params[key] === null);
        },
    },

    mounted() {
        const type = route().params.type.slice(0, -1); // remove last character (should be singular)

        axios.get(route('filters.data'), {params: {document_type: type}})
            .then(resp => {
                this.documentNumbersDropdownItems = resp.data.documentNumbersDropdownItems;

                this.initFiltersState();
            })
            .catch(error => {
                console.error('Failed to fetch filter data', error);
            });
    },

    methods: {
        filter() {
            Object.keys(this.params).forEach(key => {
                if (this.params[key] !== null) {
                    UrlQueryService.set(key, this.params[key]);
                } else {
                    UrlQueryService.remove(key);
                }
            });

            this.checkFiltersState();

            this.$emit('changed');

            this.$refs.jetDropdown.close();
        },

        initFiltersState() {
            UrlQueryService.fresh();

            Object.keys(this.params).forEach(key => {
                if (UrlQueryService.has(key)) {
                    this.params[key] = UrlQueryService.get(key);
                }
            });

            this.checkFiltersState();
        },

        checkFiltersState() {
            if (this.hasSomeFilters) this.hasFilters = true;
            if (this.hasNoFilters)   this.hasFilters = false;
        },
    },
});
</script>
