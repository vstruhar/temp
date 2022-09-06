<template>
    <JetModal :show="show" max-width="sm" :closeable="true" @close="close">
        <jet-form-section @submitted="update" :with-title="false">
            <template #form>
                <div class="col-span-6 bg-gray-50 -m-6 mb-1 p-5 flex justify-between rounded-t-lg">
                    <div class="font-semibold text-2xl text-gray-800">{{ __('VAT settings') }}</div>
                    <x-icon class="w-6 h-6 cursor-pointer opacity-50 hover:opacity-75" @click="close"/>
                </div>

                <div class="col-span-6">
                    <jet-label :value="__('Default vat')" required class="mb-2"/>
                    <div class="flex rounded-md shadow-sm">
                        <jet-numeric-input v-allow.number
                                           :min="0"
                                           :max="99"
                                           :precision="0"
                                           :empty-value="20"
                                           class="w-full rounded-r-none"
                                           v-model="data.default_tax_percentage"
                                           :value="data.default_tax_percentage"
                                           :read-only="!canEdit"/>

                        <span class="inline-flex items-center px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">%</span>
                    </div>
                    <jet-input-error :message="errors.default_tax_percentage" class="mt-2"/>
                </div>

                <div class="col-span-6 flex justify-end">
                    <jet-secondary-button @click="close" :disabled="requestPending" class="h-10">{{ __('Cancel') }}</jet-secondary-button>

                    <jet-button :class="{'opacity-25': requestPending}" :disabled="requestPending" class="justify-center px-6 h-10 ml-4">
                        {{ __('Save') }}
                    </jet-button>
                </div>
            </template>
        </jet-form-section>
    </JetModal>
</template>

<script>
import {defineComponent} from 'vue'
import JetButton from '@/Jetstream/Button.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetModal from '@/Jetstream/Modal.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetNumericInput from '@/Jetstream/NumericInput.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import {PlusIcon, XIcon} from '@heroicons/vue/solid'
import {handleError} from "@/Services/RequestService";
import axios from 'axios';

export default defineComponent({
    name: "TaxSettingsModal",

    emits: ['updated'],

    props: {
        show: {
            default: false,
        },
        defaultTaxPercentage: {
            default: 0,
            type: Number,
        },
    },

    components: {
        JetButton,
        JetSecondaryButton,
        JetModal,
        JetFormSection,
        JetNumericInput,
        JetLabel,
        JetInputError,
        PlusIcon,
        XIcon,
    },

    data() {
        return {
            data: {
                default_tax_percentage: 0,
            },
            errors: {},
            canEdit: this.can('settings:contact_and_invoice:edit'),
            requestPending: false,
        };
    },

    watch: {
        show(value) {
            if (value === true) {
                this.data.default_tax_percentage = this.defaultTaxPercentage;
                this.errors = {};
            }
        }
    },

    methods: {
        update() {
            this.requestPending = true;

            axios.put(route('settings.contact_and_invoice.tax_settings.update'), this.data)
                .then(() => {
                    this.$emit('updated', this.data.default_tax_percentage);
                    this.close();
                })
                .catch(error => {
                    if (error.response && error.response.status === 422) {
                        Object.keys(error.response.data.errors).forEach(key => {
                            this.errors[key] = error.response.data.errors[key][0];
                        });
                    } else {
                        handleError(error);
                        alert('Stala sa chyba');
                    }
                })
                .finally(() => this.requestPending = false);
        },

        close() {
            this.$emit('close');
        },
    },
})
</script>
