<template>
    <div>
        <div class="-m-6 bg-indigo-600 divide-y divide-indigo-700 text-white text-sm overflow-hidden">
            <div v-for="payment in payments"
                 :key="`payment-${payment.id}`"
                 class="px-5 py-3 flex justify-between relative hover:bg-indigo-700/50"
                 :class="{group: can('documents:payments:delete')}">

                <div class="opacity-50 cursor-default group-hover:ml-5 group-hover:opacity-25 transition-all" v-tooltip="{content: getTooltip(payment), html: true}">
                    {{ date(payment.payment_date) }}
                </div>
                <div class="font-bold">{{ money(payment.amount) }}</div>

                <x-icon v-if="can('documents:payments:delete')"
                        @click="openDeleteConfirmation(payment.id)"
                        class="w-4 h-4 absolute left-[14px] top-[13px] text-white cursor-pointer opacity-0 group-hover:opacity-100 transition"/>
            </div>

            <div class="px-5 py-3 bg-indigo-700 flex flex-col items-end">
                <div class="text-xs opacity-50">{{ __('Paid') }}</div>
                <div class="text-2xl font-bold">{{ money(paymentsSum) }}</div>
            </div>
        </div>

        <jet-delete-confirmation-modal :title="__('Delete payment')"
                                       :message="__('This action will delete payment. Are you sure?')"
                                       :confirm-button-text="__('Delete payment')"
                                       :processing="form.processing"
                                       :show="modal.deletePayment.show"
                                       @confirmed="deletePayment"
                                       @close="modal.deletePayment.show = false"/>
    </div>
</template>

<script>
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import PaymentsMethods from "@/Data/DropdownOptions/PaymentsMethods"
    import {defineComponent} from 'vue'
    import {XIcon} from '@heroicons/vue/solid'
    import {handleError} from "@/Services/RequestService";

    export default defineComponent({
        components: {XIcon, JetDeleteConfirmationModal},

        props: {
            payments: Array,
            selectedCurrency: Object,
            invoiceId: Number,
        },

        data() {
            return {
                form: this.$inertia.form(),
                modal: {
                    deletePayment: {
                        show: false,
                        paymentId: null,
                    },
                },
                requestPending: false,
            };
        },

        computed: {
            paymentsSum() {
                return this.payments.reduce((accumulator, payment) => accumulator + payment.amount, 0);
            },
        },

        methods: {
            getTooltip(payment) {
                const label = PaymentsMethods.find(i => i.value === payment.payment_method).label;
                const translatedLabel = this.__(label);

                const createdAt = this.datetime(payment.created_at);

                const commentElement = payment.comment
                    ? `<div class="my-3 text-sm max-w-[200px]">${payment.comment}</div>`
                    : '';

                return this.can('documents:payments:created_by_user')
                    ? `<div class="font-bold my-1">${translatedLabel}</div>
                       <hr class="border-t-2 border-white border-opacity-25">
                       ${commentElement}
                       <div class="divide-y divide-gray-500 divide-opacity-25 text-sm mt-1">${payment.created_by.name}</div>
                       <div class="divide-y divide-gray-500 divide-opacity-25 text-xs opacity-50">${createdAt}</div>`
                    : `${translatedLabel}`;
            },

            openDeleteConfirmation(paymentId) {
                this.modal.deletePayment.show = true;
                this.modal.deletePayment.paymentId = paymentId;
            },

            deletePayment() {
                this.form.delete(route('documents.payments.delete', [route().params.type, this.invoiceId, this.modal.deletePayment.paymentId]), {
                    onSuccess: () => this.modal.deletePayment.show = false,
                    onError: handleError,
                });
            },
        },
    });
</script>
