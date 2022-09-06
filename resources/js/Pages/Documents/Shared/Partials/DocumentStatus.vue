<template>
    <div class="-m-6 p-4 pt-5 text-white text-center overflow-hidden" :class="[statusClass]">
        <div class="text-xl font-bold">{{ userFriendlyStatus }}</div>

        <div v-if="status === DocumentStatus.WAITING_FOR_PAYMENT || status === DocumentStatus.PARTIAL_PAYMENT" class="text-xs font-semibold opacity-75">
            {{ __('Remains to be paid') }}: {{ money(DocumentModel.remainsToBePaid(payments, total)) }}
        </div>

        <div v-if="status === DocumentStatus.OVER_PAYMENT" class="text-xs font-semibold opacity-75">
            {{ __('Overpayment') }}: {{ money(DocumentModel.overPaid(payments, total)) }}
        </div>
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import {DocumentStatus, readableStatus} from "@/Data/DocumentStatuses"
    import DocumentModel from "@/Models/Document";

    export default defineComponent({
        props: {
            status: String,
            statusClass: String,
            selectedCurrency: Object,
            payments: Array,
            total: Number,
            billingDate: String,
        },

        data() {
            return {
                DocumentModel,
                DocumentStatus,
            };
        },

        computed: {
            userFriendlyStatus() {
                if (this.status === DocumentStatus.CANCELLED) {
                    return readableStatus(this.status);
                }

                if (DocumentModel.billingExpired(this.billingDate, this.status) && this.status !== DocumentStatus.PAYMENT_RECEIVED) {
                    return DocumentModel.billingExpiredDays(this.billingDate) +' '+ this.__('overdue');
                }

                return readableStatus(this.status);
            },
        },
    });
</script>
