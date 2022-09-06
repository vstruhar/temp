<template>
    <app-layout :title="__('Invoices')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">{{ __('Invoices') }}</h2>
        </template>

        <div class="flex justify-between flex-col-reverse lg:flex-row items-end gap-5 mb-6">
            <documents-filters/>

            <jet-create-link-button v-if="can('documents:invoice:create')" :href="route('documents.create', 'invoices')">{{ __('New invoice') }}</jet-create-link-button>
        </div>

        <jet-table :pagination="documents" id="invoices" ordered-by="date_created" direction="desc">
            <template #thead>
                <th class="w-2"></th>
                <jet-header-cell sortable name="number">{{ __('Number') }}</jet-header-cell>
                <jet-header-cell>{{ __('Client') }}</jet-header-cell>
                <jet-header-cell>{{ __('Sum') }}</jet-header-cell>
                <jet-header-cell sortable name="date_created">{{ __('Created') }}</jet-header-cell>
                <jet-header-cell sortable name="billing_date">{{ __('Payment due') }}</jet-header-cell>
                <th class="no-stretch"></th>
            </template>

            <template #tbody>
                <jet-row v-for="document in documents.data" :key="`table-row-${document.id}`" @click="open($event, document.id)">

                    <td :class="DocumentModel.set(document).status.getClass(document.status)" class="h-2 col-span-2 -m-5 lg:m-0">&nbsp;</td>

                    <jet-data-cell :name="__('Number')" class="font-semibold" inline>
                        <Link :href="route('documents.show', ['invoices', document.id])" class="text-blue-700 hover:underline">{{ document.number }}</Link>
                    </jet-data-cell>

                    <jet-data-cell :name="__('Client')" inline>
                        <Link v-if="can('client:edit')" :href="route('clients.edit', document.client.id)" class="text-blue-700 hover:underline">
                            {{ document.client.name }}
                        </Link>
                        <Link v-else-if="can('client:show')" :href="route('clients.show', document.client.id)" class="text-blue-700 hover:underline">
                            {{ document.client.name }}
                        </Link>
                    </jet-data-cell>

                    <jet-data-cell :name="__('Sum')" events-none>
                        <div class="text-gray-600 pointer-events-none">{{ moneyDocument(document.amount_with_tax || document.amount, document) }}</div>
                        <div class="text-gray-400 pointer-events-none">({{ moneyDocument(document.amount, document) }})</div>
                    </jet-data-cell>

                    <jet-data-cell :name="__('Created')" events-none>
                        <div>{{ date(document.date_created) }}</div>
                    </jet-data-cell>

                    <jet-data-cell :name="__('Payment due')" events-none>
                        <div class="pointer-events-none">{{ date(document.billing_date) }}</div>
                        <div v-if="DocumentModel.billingExpired(document.billing_date, document.status)" class="pointer-events-none text-red-500 text-xs mt-1">
                            {{ DocumentModel.billingExpiredDays(document.billing_date) }} {{ __('overdue') }}
                        </div>
                    </jet-data-cell>

                    <jet-action-cell>
                        <div class="flex justify-end px-2 py-3 whitespace-nowrap">
                            <jet-table-button v-if="can('documents:credit_note:show') && DocumentModel.status.cancelled(document.status)"
                                              @click="openCreditNote(document)"
                                              color="blue"
                                              class="font-bold text-base mr-2"
                                              v-tooltip="__('Credit note')">
                                <archive-icon class="w-5 h-4"/>
                            </jet-table-button>

                            <template v-if="!DocumentModel.status.cancelled(document.status)">
                                <jet-table-button v-if="can('documents:payments:create') && !DocumentModel.status.paymentReceived(document.status)"
                                                  @click="openAddPaymentModal(document)"
                                                  color="green"
                                                  class="font-bold text-base mr-2">
                                    <div class="px-1">€</div>
                                </jet-table-button>

                                <div v-else class="px-2 mr-2 flex items-center" v-tooltip="{content: getPaymentsTooltip(document), html: true}">
                                    <check-icon class="w-6 h-6 text-green-500"/>
                                </div>
                            </template>

                            <jet-edit-button v-if="can('documents:invoice:edit')" :url="route('documents.edit', ['invoices', document.id])" class="mr-2"/>
                            <jet-open-button v-else-if="can('documents:invoice:show')" :href="route('documents.show', ['invoices', document.id])" class="mr-2"/>

                            <document-options :invoice="document"
                                              :company-name="documentCompanyName"
                                              @delete="openDeleteConfirmation($event)"/>
                        </div>
                    </jet-action-cell>
                </jet-row>

                <tr v-if="documents.total == 0">
                    <td class="px-6 py-12 whitespace-nowrap text-lg text-gray-400 text-center" colspan="9">{{ __('No items were found') }}</td>
                </tr>
            </template>
        </jet-table>

        <jet-delete-confirmation-modal :title="__('Delete invoice')"
                                       :message="__('This action will delete invoice. Are you sure?')"
                                       :confirm-button-text="__('Delete invoice')"
                                       :processing="form.processing"
                                       :show="modal.deleteDocument.show"
                                       @confirmed="deleteDocument"
                                       @close="modal.deleteDocument.show = false"/>

        <add-new-payment :show="modal.addPayment.show"
                         :invoice-id="modal.addPayment.documentId"
                         :invoice-payment-method="modal.addPayment.paymentMethod"
                         :selected-currency="modal.addPayment.currency"
                         :invoice-amount="modal.addPayment.amount"
                         @close="modal.addPayment.show = false"/>

        <duplicate-document-modal type="invoices"/>
    </app-layout>
</template>

<script>
import AddNewPayment from '@/Pages/Modals/Documents/AddNewPayment'
import AppLayout from '@/Layouts/AppLayout.vue'
import DocumentModel from "@/Models/Document"
import DocumentOptions from './Documents/Shared/Partials/DocumentOptions.vue'
import DocumentsFilters from '@/Pages/Documents/Shared/DocumentsFilters'
import JetActionCell from "@/Jetstream/Table/ActionCell";
import JetCreateLinkButton from '@/Jetstream/CreateLinkButton.vue'
import JetDataCell from "@/Jetstream/Table/DataCell";
import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
import JetEditButton from '@/Jetstream/EditButtonLink.vue'
import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
import JetOpenButton from '@/Jetstream/OpenButtonLink.vue'
import JetRow from "@/Jetstream/Table/Row";
import JetTable from '@/Jetstream/Table.vue'
import JetTableButton from '@/Jetstream/TableButton.vue'
import {CheckIcon, ArchiveIcon} from '@heroicons/vue/solid'
import {Link} from '@inertiajs/inertia-vue3'
import {defineComponent} from 'vue'
import {handleError} from "@/Services/RequestService"
import Currencies from "@/Data/Currencies";
import DuplicateDocumentModal from "@/Pages/Modals/Documents/DuplicateDocumentModal";

export default defineComponent({
    components: {
        DuplicateDocumentModal,
        AddNewPayment,
        AppLayout,
        DocumentOptions,
        DocumentsFilters,
        JetActionCell,
        JetCreateLinkButton,
        JetDataCell,
        JetDeleteConfirmationModal,
        JetEditButton,
        JetHeaderCell,
        JetOpenButton,
        JetRow,
        JetTable,
        JetTableButton,
        Link,
        CheckIcon,
        ArchiveIcon,
    },

    props: ['documents', 'documentCompanyName'],

    data() {
        return {
            form: this.$inertia.form(),
            modal: {
                deleteDocument: {
                    show: false,
                    documentId: null,
                },
                addPayment: {
                    show: false,
                    documentId: null,
                    paymentMethod: null,
                    currency: null,
                    amount: 0,
                },
            },
            DocumentModel,
        };
    },

    methods: {
        open(event, documentId) {
            if (event.target.nodeName === 'TD' || event.target.nodeName === 'TR') {
                this.$inertia.visit(route('documents.show', ['invoices', documentId]));
            }
        },

        deleteDocument() {
            this.form.delete(route('documents.delete', ['invoices', this.modal.deleteDocument.documentId]), {
                onSuccess: () => this.modal.deleteDocument.show = false,
                onError: handleError,
            });
        },

        openAddPaymentModal(document) {
            this.modal.addPayment.show          = true;
            this.modal.addPayment.documentId    = document.id;
            this.modal.addPayment.paymentMethod = document.payment_method;
            this.modal.addPayment.currency      = Currencies.find(i => i.code === document.invoice_currency);
            this.modal.addPayment.amount        = DocumentModel.set(document).remainsToBePaid();
        },

        openDeleteConfirmation(documentId) {
            this.modal.deleteDocument.show       = true;
            this.modal.deleteDocument.documentId = documentId;
        },

        getPaymentsTooltip(invoice) {
            const paymentsHtml = invoice.payments
                .reduce((acc, payment) => {
                    const amount = this.money(payment.amount, {symbol: invoice.currency_symbol});
                    const date   = this.date(payment.payment_date);

                    return acc + `<div class="py-2">${amount} <span class="opacity-50">(${date})</span></div>`;
                }, '');

            return `<div class="font-semibold my-1">${this.__('Paid')}</div>
                    <hr class="border-t-2 border-white border-opacity-25">
                    <div class="divide-y divide-gray-500 divide-opacity-25 text-xs">${paymentsHtml}</div>`;
        },

        openCreditNote(document) {
            this.$inertia.visit(route('documents.show', ['credit-notes', document.credit_note_id]));
        },
    },
})
</script>
