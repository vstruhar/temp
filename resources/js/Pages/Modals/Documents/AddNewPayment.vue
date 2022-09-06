<template>
    <modal :show="show" max-width="sm" :closeable="true" @close="$emit('close')">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <jet-form-section @submitted="createPayment" :with-title="false">
                    <template #form>
                        <div class="col-span-6 bg-gray-50 -m-6 mb-0 p-5 flex justify-between rounded-t-lg">
                            <div class="font-semibold text-2xl text-gray-800">{{ __('Add payment') }}</div>
                        </div>

                        <div class="col-span-6 grid grid-cols-1 grid-rows-3 gap-3">
                            <!-- Payment method -->
                            <div>
                                <jet-label :value="__('Payment method')"/>
                                <jet-dropdown-select class="mt-1 w-full" :options="paymentMethods" v-model:value="form.payment_method"/>
                                <jet-input-error :message="form.errors.payment_method" class="mt-2"/>
                            </div>

                            <!-- Amount -->
                            <div>
                                <jet-label for="amount" :value="__('Sum')" required/>
                                <div class="flex rounded-md shadow-sm">
                                    <jet-numeric-input v-allow.number.comma.minus :precision="selectedCurrency.precision" separator="." :minus="true" class="w-full rounded-r-none" v-model="form.amount" :value="form.amount"/>
                                    <span class="inline-flex items-center px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">{{ selectedCurrency.symbol }}</span>
                                </div>
                                <jet-input-error :message="form.errors.amount" class="mt-2"/>
                            </div>

                            <!-- Payment date -->
                            <div>
                                <jet-label for="amount" :value="__('Payment date')" required/>
                                <jet-input type="date" class="mt-1 w-full" v-model="form.payment_date"/>
                                <jet-input-error :message="form.errors.payment_date" class="mt-2"/>
                            </div>

                            <div>
                                <jet-label for="comment" :value="__('Comment')"/>
                                <jet-textarea id="comment" v-model="form.comment" :rows="2"/>
                                <jet-input-error :message="form.errors.comment" class="mt-2"/>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <jet-secondary-button @click="$emit('close')" :disabled="form.processing" class="justify-center w-24 h-10 mr-4">{{ __('Cancel') }}
                        </jet-secondary-button>

                        <jet-button :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                            {{ __('Add') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </modal>
</template>

<script>
    import JetButton from '@/Jetstream/Button.vue'
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetNumericInput from '@/Jetstream/NumericInput.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetTextarea from '@/Jetstream/Textarea.vue'
    import Modal from '../../../Jetstream/Modal.vue'
    import moment from 'moment'
    import PaymentMethods from "@/Data/DropdownOptions/PaymentsMethods"
    import {defineComponent} from 'vue'
    import {handleError} from "@/Services/RequestService";

    export default defineComponent({
        emits: ['close'],

        components: {
            JetButton,
            JetDropdownSelect,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetNumericInput,
            JetSecondaryButton,
            JetTextarea,
            Modal,
        },

        props: {
            show: {
                default: false,
            },
            invoiceId: {
                default: null,
            },
            invoiceAmount: {
                default: 0,
            },
            invoicePaymentMethod: {
                default: null,
            },
            selectedCurrency: Object,
        },

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'POST',
                    payment_method: '',
                    amount: 0,
                    payment_date: moment().format('YYYY-MM-DD'),
                    comment: '',
                }),
                paymentMethods: PaymentMethods,
                errors: {},
            }
        },

        watch: {
            invoiceAmount: {
                immediate: true,
                handler(value) {
                    this.form.amount = value;
                },
            },

            invoicePaymentMethod: {
                immediate: true,
                handler(value) {
                    this.form.payment_method = value;
                },
            },
        },

        methods: {
            createPayment() {
                this.form.post(route('documents.payments.store', ['invoices', this.invoiceId]), {
                    onSuccess: () => {
                        this.form.amount = 0;
                        this.form.payment_date = moment().format('YYYY-MM-DD');
                        this.$emit('close');
                    },
                    onError: handleError,
                });
            },
        },
    })
</script>
