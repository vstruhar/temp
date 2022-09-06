<template>
    <app-layout :title="__('Invoice')+': '+document.number">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Invoice') }}: {{ document.number }}
            </h2>
        </template>

        <div class="grid grid-cols-9 gap-6">
            <div class="col-span-9 lg:col-span-7">
                <jet-form-section :with-title="false" :with-card="true">
                    <template #form>
                        <div class="col-span-6 sm:col-span-3 pl-3 pt-3">
                            <div class="text-xl font-medium text-gray-800 font-bold mb-2">{{ __('Supplier') }}</div>

                            <div class="mt-1 text-sm text-gray-600">{{ document.company_details?.name }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ document.company_details?.address }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ document.company_details?.postal_code }} {{ document.company_details?.city }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ __(document.company_details?.country_label) }}</div>

                            <div class="mt-4 text-sm text-gray-600" v-if="document.company_details.organization_id">{{ __('Business ID') +': '+ document.company_details?.organization_id }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.company_details.tax">{{ __('Tax ID') +': '+ document.company_details?.tax }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.company_details.value_added_tax">{{ __('VAT') +': '+ document.company_details?.value_added_tax }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.company_details.register">{{ document.company_details?.register }}</div>
                        </div>

                        <div class="col-span-6 sm:col-span-3 pl-3 sm:pl-0 sm:pt-3">
                            <div class="text-xl font-medium text-gray-800 font-bold mb-2">{{ __('Customer') }}</div>

                            <div class="mt-1 text-sm text-gray-600">
                                <Link v-if="can('client:edit')" :href="route('clients.edit', document.client.id)" class="text-blue-700 hover:underline">{{ document.client.name }}</Link>
                                <Link v-else-if="can('client:show')" :href="route('clients.show', document.client.id)" class="text-blue-700 hover:underline">{{ document.client.name }}</Link>
                            </div>
                            <div class="mt-1 text-sm text-gray-600">{{ document.client?.address }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ document.client?.postal_code }} {{ document.client?.city }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ __(document.client?.country_label) }}</div>

                            <div class="mt-4 text-sm text-gray-600" v-if="document.client.organization_id">{{ __('Business ID') +': '+ document.client?.organization_id }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.client.tax">{{ __('Tax ID') +': '+ document.client?.tax }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.client.value_added_tax">{{ __('VAT') +': '+ document.client?.value_added_tax }}</div>
                        </div>

                        <hr class="col-span-6">

                        <div class="col-span-6 sm:col-span-3 pl-3">
                            <div class="text-xl font-medium text-gray-800 font-bold mb-2">{{ document.name }}</div>
                            <div class="mt-3 text-sm text-gray-600">{{ __('Variable symbol') +': '+ document.variable_symbol }}</div>
                            <div class="mt-1 text-sm text-gray-600" v-if="document.constant_symbol">{{ __('Constant symbol') +': '+ document.constant_symbol }}</div>
                        </div>

                        <div class="col-span-6 sm:col-span-3 pl-3 sm:pl-0">
                            <div class="mt-1 text-sm text-gray-600">{{ __('Date of issue') }}: {{ date(document.date_created) }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ __('Delivery date') }}: {{ date(document.date_added) }}</div>
                            <div class="mt-1 text-sm text-gray-600">{{ __('Due by') }}: {{ date(document.billing_date) }}</div>
                        </div>

                        <template v-if="document.note_before_items || document.note">
                            <hr class="col-span-6">

                            <div class="col-span-3 pl-3" v-if="document.note_before_items">
                                <div class="text-xl font-medium text-gray-800 font-bold mb-2">{{ __('Opening text') }}</div>
                                <div class="mt-4 text-sm text-gray-600">{{ document.note_before_items }}</div>
                            </div>

                            <div class="col-span-3 pl-3" v-if="document.note">
                                <div class="text-xl font-medium text-gray-800 font-bold mb-2">{{ __('Closing text') }}</div>
                                <div class="mt-4 text-sm text-gray-600">{{ document.note }}</div>
                            </div>
                        </template>

                        <!-- Items -->
                        <div class="col-span-6 mt-6">
                            <div class="text-xl font-medium text-gray-800 font-bold mb-3 bg-gray-100 text-center p-3 lg:hidden">{{ __('Items') }}</div>

                            <table class="divide-y divide-gray-200 -mt-4 w-full border-b">
                                <thead class="bg-gray-100 hidden lg:table-header-group">
                                <tr>
                                    <jet-header-cell class="pl-6 pr-2 py-3">{{ __('Item name') }}</jet-header-cell>
                                    <jet-header-cell class="px-2 py-3">{{ __('Quantity') }}</jet-header-cell>
                                    <jet-header-cell class="px-2 py-3">{{ __('Unit') }}</jet-header-cell>
                                    <jet-header-cell class="px-2 py-3">{{ __('Price') }}</jet-header-cell>
                                    <jet-header-cell v-if="hasDiscount" class="px-2 py-3">{{ __('Discount') }}</jet-header-cell>
                                    <jet-header-cell v-if="document.taxable" class="px-2 py-3">{{ __('Tax') }}</jet-header-cell>
                                    <jet-header-cell class="px-2 py-3">{{ document.taxable ? __('Total with VAT') : __('Total') }}</jet-header-cell>
                                </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                <template v-for="(item, index) in document.items" :key="`item-${index}`">
                                    <tr class="grid grid-cols-6 lg:table-row gap-y-2 sm:gap-y-0 p-4 w-full even:bg-gray-50 lg:even:bg-transparent">
                                        <!-- Name -->
                                        <jet-data-cell :name="__('Name')" padding="lg:pl-6 lg:pr-2 lg:py-4" class="col-span-3 sm:col-span-2 align-top">
                                            <div class="flex gap-4">
                                                <img v-if="item.image" :src="item.image" class="border border-gray-300 shadow-sm rounded-md overflow-hidden w-[56px] h-[56px]"/>

                                                <div class="flex flex-col justify-start">
                                                    <div class="font-semibold text-base">{{ item.name }}</div>
                                                    <div v-if="item.description" class="mt-1 text-sm text-gray-400">{{ item.description }}</div>
                                                </div>
                                            </div>
                                        </jet-data-cell>

                                        <!-- Quantity -->
                                        <jet-data-cell :name="__('Quantity')" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 max-w-[80px]">{{ item.quantity }}</jet-data-cell>

                                        <!-- Unit -->
                                        <jet-data-cell :name="__('Unit')" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 min-w-[120px] max-w-[120px]">{{ item.unit }}</jet-data-cell>

                                        <!-- Price -->
                                        <jet-data-cell :name="__('Price')" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 min-w-[100px] max-w-[120px]">{{ money(item.price) }}</jet-data-cell>

                                        <!-- Discount -->
                                        <jet-data-cell :name="__('Discount')" v-if="hasDiscount" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 min-w-[100px] max-w-[120px]">
                                            {{ money(item.discount_amount || 0) }}
                                        </jet-data-cell>

                                        <!-- Tax -->
                                        <jet-data-cell :name="__('Tax')" v-if="document.taxable" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 w-[100px]">{{ item.tax }}%</jet-data-cell>

                                        <!-- Price with tax -->
                                        <jet-data-cell :name="__('Total with VAT')" v-if="document.taxable" padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 min-w-[100px] max-w-[120px]">{{ money(item.price_with_tax) }}</jet-data-cell>

                                        <!-- Price with no tax -->
                                        <jet-data-cell :name="__('Total')" v-else padding="lg:px-2 lg:py-4" class="col-span-3 sm:col-span-2 min-w-[100px] max-w-[120px]">
                                            {{ document.taxable ? money(item.price_with_tax * item.quantity) : money((item.price * item.quantity) - item.discount_amount) }}
                                        </jet-data-cell>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- CALCULATIONS -->
                        <jet-section class="col-start-1 md:col-start-3 lg:col-start-4 col-end-7 mt-2 mb-2 overflow-hidden">
                            <dl class="text-right border-r-8 border-indigo-500 -m-6">
                                <div v-if="document.taxable" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('VAT Base') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">{{ money(document.amount) }}</dd>
                                </div>

                                <div v-if="document.taxable" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Tax') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">{{ money(document.amount_with_tax - document.amount - subtotalOfItemsWithZeroTax) }}</dd>
                                </div>

                                <div v-if="document.taxable && hasItemsWithZeroTax" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                                    <dt class="text-sm font-medium text-gray-500">{{ __('Without tax') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-1">{{ money(subtotalOfItemsWithZeroTax) }}</dd>
                                </div>

                                <div class="text-xl md:text-lg bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                                    <dt class="text-md font-bold text-gray-500">{{ __('Total') }}</dt>
                                    <dd class="mt-1 text-md font-bold text-gray-900 sm:mt-0 sm:col-span-1">
                                        {{ money(DocumentModel.set(document).getTotal()) }}
                                    </dd>
                                </div>
                            </dl>
                        </jet-section>
                    </template>

                    <template #actions>
                        <div class="w-full flex justify-start">
                            <jet-back-button :url="route('documents', 'invoices')"></jet-back-button>
                        </div>
                    </template>
                </jet-form-section>
            </div>

            <div class="col-span-9 lg:col-span-2 grid grid-cols-6 lg:block gap-x-0 sm:gap-x-6 lg:gap-x-0 mb-5">
                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" classes="px-4 py-5 sm:p-6 overflow-hidden">
                    <document-status-component :status="document.status"
                                               :selected-currency="selectedCurrency"
                                               :payments="document.payments"
                                               :billing-date="document.billing_date"
                                               :status-class="DocumentModel.status.getClass(document.status)"
                                               :total="DocumentModel.getTotal()"/>
                </jet-section>

                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" classes="px-4 py-5 sm:p-6 overflow-hidden">
                    <document-payments v-if="can('documents:payments:list')"
                                       :payments="document.payments"
                                       :selected-currency="selectedCurrency"
                                       :invoice-id="document.id"/>
                </jet-section>

                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" no-padding>
                    <div class="divide-y divide-gray-200">
                        <button v-if="can('documents:payments:create')"
                                :disabled="DocumentModel.status.paymentReceived()"
                                @click="modal.addPayment = true"
                                class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100/70 transition"
                                :class="{'opacity-50 pointer-events-none': DocumentModel.status.paymentReceived()}">
                            <span class="font-bold text-2xl leading-none w-5 ml-1 mr-5">€</span> {{ __('Add payment') }}
                        </button>

                        <button v-if="can('documents:invoice:print')" @click="printPdf" :disabled="loadingDocumentPdf" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100/70 transition">
                            <printer-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Print') }}
                        </button>

                        <button v-if="can('documents:invoice:download')" @click="downloadPdf" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100/70 transition">
                            <document-download-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Download') }}
                        </button>

                        <Link v-if="can('documents:invoice:send')" :href="route('documents.send_form', ['invoices', document.id])" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <mail-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Send') }}
                        </Link>
                    </div>
                </jet-section>

                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" v-if="can('documents:invoice:edit') || can('documents:invoice:duplicate') || can('documents:invoice:delete')" no-padding>
                    <div class="divide-y divide-gray-200">
                        <Link v-if="can('documents:invoice:edit')" :href="route('documents.edit', ['invoices', document.id])" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <pencil-alt-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Edit') }}
                        </Link>

                        <button v-if="can('documents:invoice:duplicate')" @click="duplicate" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <document-duplicate-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Duplicate') }}
                        </button>

                        <button v-if="can('documents:invoice:delete')" @click="modal.confirmDeleteDocument = true" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-red-500 hover:text-red-700 hover:bg-red-100 transition">
                            <trash-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Delete') }}
                        </button>
                    </div>
                </jet-section>

                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" v-if="can('documents:credit_note:create') && document.credit_note_id === null" no-padding>
                    <div class="divide-y divide-gray-200">
                        <div class="text-sm tracking-wider font-semibold text-gray-400 py-2 text-center bg-gray-50 rounded-t-lg">{{ __('Issue') }}</div>

                        <Link v-if="can('documents:credit_note:create')" :href="route('documents.issue.credit_note.create', document.id)" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <archive-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Credit note') }}
                        </Link>
                    </div>
                </jet-section>

                <jet-section class="mb-5 col-span-6 sm:col-span-3 lg:col-span-6" v-if="document.proforma_invoice_id || document.credit_note_id || document.quotation_id" no-padding>
                    <div class="divide-y divide-gray-200">
                        <div class="text-sm tracking-wider font-semibold text-gray-400 py-2 text-center bg-gray-50 rounded-t-lg">{{ __('Related documents') }}</div>

                        <Link v-if="can('documents:proforma_invoice:show') && document.proforma_invoice_id" :href="route('documents.show', ['proforma-invoices', document.proforma_invoice_id])" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <document-text-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Proforma invoice') }}
                        </Link>

                        <Link v-if="can('documents:credit_note:show') && document.credit_note_id" :href="route('documents.show', ['credit-notes', document.credit_note_id])" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <document-text-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Credit note') }}
                        </Link>

                        <Link v-if="can('documents:quotation:show') && document.quotation_id" :href="route('documents.show', ['quotations', document.quotation_id])" class="flex items-center px-5 py-4 w-full text-base tracking-wide text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition">
                            <document-text-icon class="w-5 h-5 ml-1 mr-5"/> {{ __('Quotation') }}
                        </Link>
                    </div>
                </jet-section>

                <jet-section class="col-span-6 sm:col-span-3 lg:col-span-6" v-if="document.revisions.length && can('documents:revision:restore')" classes="p-0">
                    <history-of-changes :revisions="document.revisions"
                                        :invoice-id="document.id"
                                        :title="__('Restore invoice')"
                                        :message="__('Are you sure? This action will restore invoice to selected version.')"
                                        :confirm-button-text="__('Restore invoice')"/>
                </jet-section>
            </div>
        </div>

        <jet-delete-confirmation-modal :title="__('Delete invoice')"
                                       :message="__('This action will delete invoice. Are you sure?')"
                                       :confirm-button-text="__('Delete invoice')"
                                       :processing="form.processing"
                                       :show="modal.confirmDeleteDocument"
                                       @confirmed="deleteDocument"
                                       @close="modal.confirmDeleteDocument = false"/>

        <add-new-payment :show="modal.addPayment"
                         :invoice-id="document.id"
                         :invoice-payment-method="document.payment_method"
                         :selected-currency="selectedCurrency"
                         :invoice-amount="DocumentModel.remainsToBePaid()"
                         @close="modal.addPayment = false"/>

        <duplicate-document-modal type="invoices"/>
    </app-layout>
</template>

<script>
    import AddNewPayment from '@/Pages/Modals/Documents/AddNewPayment'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import axios from 'axios';
    import Currencies from "@/Data/Currencies";
    import fileDownload from 'js-file-download';
    import HistoryOfChanges from '../Shared/Partials/HistoryOfChanges.vue'
    import DocumentPayments from '../Shared/Partials/DocumentPayments.vue'
    import DocumentStatusComponent from '../Shared/Partials/DocumentStatus.vue'
    import JetBackButton from '@/Jetstream/BackButton.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetCard from '@/Jetstream/Card.vue'
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetSection from '@/Jetstream/Section.vue'
    import Print from '@/Services/Print'
    import SelectedClientPreview from "../Shared/Partials/SelectedClientPreview"
    import {defineComponent} from 'vue'
    import {handleError} from "@/Services/RequestService"
    import {InformationCircleIcon, PlusIcon, DocumentTextIcon, ArchiveIcon} from '@heroicons/vue/outline'
    import {Link} from '@inertiajs/inertia-vue3'
    import {PencilAltIcon, TagIcon, DocumentDownloadIcon, MailIcon, DocumentDuplicateIcon, TrashIcon, PrinterIcon} from '@heroicons/vue/solid'
    import DocumentModel from '@/Models/Document'
    import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
    import JetDataCell from "@/Jetstream/Table/DataCell";
    import DuplicateDocumentModal from "@/Pages/Modals/Documents/DuplicateDocumentModal";
    import EventBus from "@/Services/EventBus";

    export default defineComponent({
        components: {
            DuplicateDocumentModal,
            JetDataCell,
            JetHeaderCell,
            AddNewPayment,
            AppLayout,
            ArchiveIcon,
            DocumentDownloadIcon,
            DocumentDuplicateIcon,
            HistoryOfChanges,
            InformationCircleIcon,
            DocumentPayments,
            DocumentStatusComponent,
            JetBackButton,
            JetButton,
            JetCard,
            JetDeleteConfirmationModal,
            JetFormSection,
            JetSection,
            Link,
            MailIcon,
            PencilAltIcon,
            PlusIcon,
            PrinterIcon,
            SelectedClientPreview,
            TagIcon,
            TrashIcon,
            DocumentTextIcon,
        },

        props: ['document', 'documentCompanyName'],

        data() {
            return {
                form: this.$inertia.form(),
                loadingDocumentPdf: false,
                modal: {
                    confirmDeleteDocument: false,
                    addPayment: false,
                },
                DocumentModel: DocumentModel.set(this.document),
            };
        },

        computed: {
            selectedCurrency() {
                return Currencies.find(i => i.code === this.document.invoice_currency);
            },

            hasDiscount() {
                return this.document.items.some(i => i.discount_amount);
            },

            hasItemsWithZeroTax() {
                return this.document.items.some(i => i.tax === 0);
            },

            subtotalOfItemsWithZeroTax() {
                return this.document.items.reduce((sum, item) => {
                    const price = (item.tax === 0)
                        ? (item.price * item.quantity - item.discount_amount)
                        : 0;

                    return sum + price;
                }, 0);
            },

            paymentsSum() {
                return this.document.payments.reduce((accumulator, payment) => accumulator + payment.amount, 0);
            },
        },

        methods: {
            printPdf() {
                Print.documentPdf(this.document.id);
            },

            downloadPdf() {
                axios.get(route('documents.download', ['invoices', this.document.id]), {responseType: 'blob'})
                    .then(response => {
                        fileDownload(response.data, `${this.documentCompanyName}_${this.document.number}_${this.document.lang_code}.pdf`, 'application/pdf');
                    });
            },

            deleteDocument() {
                this.$inertia.form().delete(route('documents.delete', ['invoices', this.document.id]), {
                    onError: handleError,
                });
            },

            duplicate() {
                EventBus.emit('document:duplicate:open', this.document);
            },
        },
    })
</script>
