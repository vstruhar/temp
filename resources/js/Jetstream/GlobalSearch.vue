<template>
    <div class="relative">
        <search-icon class="w-7 h-7 absolute z-10 top-4 left-4 text-indigo-600"/>

        <div class="absolute top-5 right-5 text-gray-300 flex text-md pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                <path d="M10 16v2.5c0 2.483-2.017 4.5-4.5 4.5-2.484 0-4.5-2.017-4.5-4.5 0-2.484 2.016-4.5 4.5-4.5h2.5v-4h-2.5c-2.484 0-4.5-2.016-4.5-4.5 0-2.483 2.016-4.5 4.5-4.5 2.483 0 4.5 2.017 4.5 4.5v2.5h4v-2.5c0-2.483 2.017-4.5 4.5-4.5 2.484 0 4.5 2.017 4.5 4.5 0 2.484-2.016 4.5-4.5 4.5h-2.5v4h2.5c2.484 0 4.5 2.016 4.5 4.5 0 2.483-2.016 4.5-4.5 4.5-2.483 0-4.5-2.017-4.5-4.5v-2.5h-4zm-2 0h-2.5c-1.379 0-2.5 1.122-2.5 2.5s1.121 2.5 2.5 2.5 2.5-1.122 2.5-2.5v-2.5zm10.5 0h-2.5v2.5c0 1.378 1.121 2.5 2.5 2.5s2.5-1.122 2.5-2.5-1.121-2.5-2.5-2.5zm-4.5-6h-4v4.132h4v-4.132zm-6-2v-2.5c0-1.378-1.121-2.5-2.5-2.5s-2.5 1.122-2.5 2.5 1.121 2.5 2.5 2.5h2.5zm10.5 0c1.379 0 2.5-1.122 2.5-2.5s-1.121-2.5-2.5-2.5-2.5 1.122-2.5 2.5v2.5h2.5z"/>
            </svg>
            <div class="ml-2 -mt-1">+ K</div>
        </div>

        <div>
            <multiselect ref="multiselect"
                         v-model="selection"
                         @input="onInput"
                         @select="openItem"
                         :options="options"
                         trackBy="id"
                         :show-labels="false"
                         :custom-label="customLabel"
                         :option-height="40"
                         :multiple="false"
                         :maxHeight="400"
                         :placeholder="__('Search')"
                         :openDirection="openDirection"
                         :loading="autocompleteRequestPending"
                         :showNoResults="false"
                         :preserveSearch="true"
                         :internalSearch="false"
                         :hideSelected="true"
                         :showNoOptions="options.length > 0"
                         class="autocomplete global-search block w-full text-2xl relative z-0"
                         :class="{'multiselect__has-no-options': options.length === 0}"
                         selectLabel=""
                         selectedLabel=""
                         deselectLabel="">

                <template v-slot:option="{option}">
                    <div class="flex flex-nowrap justify-between">
                        <div>
                            <div class="name text-base text-gray-700">{{ option.name }}</div>
                            <div class="details text-xs text-gray-500">{{ option.client_name }}</div>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="details text-sm font-semibold text-gray-300">{{ option.company_name }}</div>
                            <div class="bg-indigo-500 text-white px-2 py-1 text-xs rounded-lg">{{ getTranslatedType(option.type) }}</div>
                        </div>
                    </div>
                </template>
            </multiselect>
        </div>

        <div v-if="this.externalServiceFailed" class="text-red-500 text-center pt-4 pb-3 text-sm">{{ __('Something went wrong, please try again later') }}</div>
    </div>
</template>

<script>
    import JetInput from '@/Jetstream/Input.vue'
    import Multiselect from 'vue-multiselect'
    import axios from 'axios'
    import {debounce} from 'lodash'
    import {defineComponent} from 'vue'
    import {handleError} from "@/Services/RequestService"
    import {SearchIcon} from '@heroicons/vue/solid'
    import {type} from '@/Data/DropdownOptions/DocumentNumber'
    import EventBus from "@/Services/EventBus";

    export default defineComponent({
        name: "GlobalSearch",

        components: {
            Multiselect,
            JetInput,
            SearchIcon,
        },

        props: {
            openDirection: {
                default: 'bottom',
            },
        },

        data() {
            return {
                selection: null,
                options: [],
                autocompleteRequestPending: false,
                externalServiceFailed: false,
            };
        },

        methods: {
            onInput(event) {
                this.autocomplete(event.target.value);
            },

            openItem(selectedOption) {
                EventBus.emit('global:search:close');
                EventBus.emit('loader:show');
                window.location.href = route('documents.global.open', selectedOption.id);
            },

            autocomplete: debounce(function (value) {
                if (value.length > 2) {
                    this.autocompleteRequestPending = true;

                    axios.get(route('documents.global.search'), {params: {query: value}})
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

            getTranslatedType(value) {
                return type.find(i => i.value === value)?.label || '/';
            },
        },
    })
</script>
