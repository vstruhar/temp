<template>
    <div>
        <multiselect v-if="allowedCountries.includes(country) && !this.externalServiceFailed"
                     ref="multiselect"
                     v-model="selection"
                     @input="onInput"
                     @select="fetchCompanyDetails"
                     :options="options"
                     trackBy="business_id"
                     :show-labels="false"
                     :custom-label="customLabel"
                     :option-height="40"
                     :multiple="false"
                     :disabled="disabled"
                     :maxHeight="maxHeight"
                     :placeholder="__('Start typing company name or business ID')"
                     :openDirection="openDirection"
                     :loading="autocompleteRequestPending"
                     :showNoResults="false"
                     :preserveSearch="true"
                     :internalSearch="false"
                     :hideSelected="true"
                     :showNoOptions="options.length > 0"
                     class="autocomplete company block w-full text-2xl"
                     :class="{'multiselect__has-no-options': options.length === 0, 'multiselect__option-is-selected': isSelected}"
                     selectLabel=""
                     selectedLabel=""
                     deselectLabel="">

            <template v-slot:option="{option}">
                <div>
                    <div class="name text-base text-gray-700">{{ option.name }}</div>
                    <div class="details text-sm text-gray-500">{{ option.city }}, {{ __('Business ID') }}: {{ option.business_id }}</div>
                </div>
            </template>
        </multiselect>

        <jet-input v-else type="text" class="mt-1 block w-full text-center text-2xl" v-model="companyName" :disabled="disabled"/>

        <div v-if="this.externalServiceFailed" class="text-red-500 text-center mt-3 text-sm">{{ __('Failed to fetch a list of companies, please try again later') }}</div>
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import Multiselect from 'vue-multiselect'
    import axios from 'axios'
    import {debounce} from 'lodash'
    import {handleError} from "@/Services/RequestService"
    import EventBus from '@/Services/EventBus'
    import JetInput from '@/Jetstream/Input.vue'

    export default defineComponent({
        name: "CompanyAutocomplete",

        emits: ['update:value', 'companySelected'],

        components: {
            Multiselect,
            JetInput,
        },

        props: {
            country: String,
            value: {
                default: null,
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            openDirection: {
                default: 'bottom',
            },
            maxHeight: {
                type: Number,
                default: 240,
            },
        },

        data() {
            return {
                companyName: '',
                selection: null,
                options: [],
                autocompleteRequestPending: false,
                disableAutocompleteFetch: false,
                allowedCountries: ['SK', 'CZ'],
                externalServiceFailed: false,
            };
        },

        computed: {
            isSelected() {
                return this.companyName === this.selection?.name;
            },
        },

        watch: {
            companyName(value) {
                this.$emit('update:value', value);

                if (!this.isSelected) {
                    this.selection = null;
                }
            },

            value() {
                this.setValue();
            },
        },

        mounted() {
            this.setValue();
        },

        methods: {
            onInput(event) {
                this.companyName = event.target.value;
                this.autocomplete(event.target.value);
            },

            setValue() {
                if (this.value) {
                    if (this.$refs.multiselect) {
                        this.disableAutocompleteFetch = true;
                        this.$refs.multiselect.search = this.value;
                        setTimeout(() => this.disableAutocompleteFetch = false, 100);
                    }
                    this.companyName = this.value;
                }
            },

            fetchCompanyDetails(selectedOption) {
                EventBus.emit('loader:show');
                this.disableAutocompleteFetch = true;

                this.$nextTick(() => {
                    this.companyName = selectedOption.name;
                    this.$refs.multiselect.search = selectedOption.name;
                });

                const params = {
                    business_id: selectedOption.business_id,
                    country: this.country,
                };

                axios.get(route('company.autocomplete.details'), {params})
                    .then(response => {
                        this.$emit('companySelected', response.data);
                    })
                    .catch(error => handleError(error))
                    .finally(() => {
                        EventBus.emit('loader:hide');
                        this.disableAutocompleteFetch = false;
                    });
            },

            autocomplete: debounce(function (value) {
                if (value.length > 2 && this.allowedCountries.includes(this.country) && !this.disableAutocompleteFetch) {
                    this.autocompleteRequestPending = true;

                    const params = {
                        name: value,
                        country: this.country,
                    };

                    axios.get(route('company.autocomplete'), {params})
                        .then(response => {
                            this.options = response.data;
                        })
                        .catch(error => {
                            this.externalServiceFailed = true;
                            handleError(error);
                        })
                        .finally(() => this.autocompleteRequestPending = false);
                }
            }, 500),

            customLabel(option) {
                return option.name;
            },
        },
    })
</script>
