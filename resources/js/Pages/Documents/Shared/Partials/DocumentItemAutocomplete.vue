<template>
    <div>
        <multiselect v-if="!this.externalServiceFailed"
                     ref="multiselect"
                     v-model="selection"
                     @input="onInput"
                     @select="selectItem"
                     :options="options"
                     trackBy="business_id"
                     :show-labels="false"
                     :custom-label="customLabel"
                     :option-height="30"
                     :multiple="false"
                     :disabled="disabled"
                     :maxHeight="maxHeight"
                     placeholder=""
                     :openDirection="openDirection"
                     :loading="autocompleteRequestPending"
                     :showNoResults="false"
                     :preserveSearch="true"
                     :internalSearch="false"
                     :hideSelected="true"
                     :showNoOptions="options.length > 0"
                     class="autocomplete w-full"
                     :class="{'multiselect__has-no-options': options.length === 0, 'multiselect__option-is-selected': isSelected}"
                     selectLabel=""
                     selectedLabel=""
                     deselectLabel="">

            <template v-slot:option="{option}">
                <div class="name text-base text-gray-700">
                    {{ option.name }}
                    <span class="text-gray-400/75">({{ option.price }} {{ currencySymbol }})</span>
                </div>
            </template>
        </multiselect>

        <jet-input v-else type="text" class="w-full pr-9" v-model="itemName" :disabled="disabled"/>
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import Multiselect from 'vue-multiselect'
    import axios from 'axios'
    import {debounce} from 'lodash'
    import {handleError} from "@/Services/RequestService"
    import JetInput from '@/Jetstream/Input.vue'

    export default defineComponent({
        name: "DocumentItemAutocomplete",

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
            currencySymbol: {
                type: String,
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
                itemName: '',
                selection: null,
                options: [],
                autocompleteRequestPending: false,
                externalServiceFailed: false,
            };
        },

        computed: {
            isSelected() {
                return this.itemName === this.selection?.name;
            },
        },

        watch: {
            itemName(value) {
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
                this.itemName = event.target.value;
                this.autocomplete(event.target.value);
            },

            setValue() {
                if (this.value) {
                    if (this.$refs.multiselect) {
                        this.$refs.multiselect.search = this.value;
                    }
                    this.itemName = this.value;
                }
            },

            selectItem(selectedOption) {
                this.$nextTick(() => {
                    this.itemName = selectedOption.name;
                    this.$refs.multiselect.search = selectedOption.name;
                });

                this.$emit('itemSelected', selectedOption);
            },

            autocomplete: debounce(function (value) {
                if (value.length > 2) {
                    this.autocompleteRequestPending = true;

                    axios.get(route('documents.items.autocomplete', route().params.type || 'invoices'), {params: {name: value}})
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
    });
</script>
