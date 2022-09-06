<template>
    <app-layout :title="__('Edit invoice')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Edit invoice') }}: {{ form.number }}
            </h2>
        </template>

        <jet-form-section @submitted="update" :with-title="false" :with-card="false">
            <template #form>
                <!-- GENERAL INFO -->
                <jet-card :title="__('General information')" class="order-1 col-span-6 lg:col-span-4 sm:pb-8">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Documents number -->
                        <div class="col-span-6 sm:col-span-3">
                            <jet-label for="inputInvoiceNumber" :value="__('Invoice number')" required/>
                            <jet-input id="inputInvoiceNumber" type="text" class="mt-1 w-full disabled:bg-gray-100" v-model="form.number"/>
                            <jet-input-error :message="form.errors.number" class="mt-2"/>
                        </div>

                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-3">
                            <jet-label for="inputName" :value="__('Invoice name')"/>
                            <jet-input id="inputName" type="text" class="mt-1 w-full" v-model="form.name"/>
                            <jet-input-error :message="form.errors.name" class="mt-2"/>
                        </div>

                        <!-- Date created -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputDateCreated" :value="__('Date of issue')" required/>
                            <jet-input id="inputDateCreated" type="date" class="mt-1 w-full" v-model="form.date_created"/>
                            <jet-input-error :message="form.errors.date_created" class="mt-2"/>
                        </div>

                        <!-- Added date -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputAddedDate" :value="__('Delivery date')"/>
                            <jet-input id="inputAddedDate" type="date" class="mt-1 w-full" v-model="form.date_added"/>
                            <jet-input-error :message="form.errors.date_added" class="mt-2"/>
                        </div>

                        <!-- Billing date -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputBillingDate" :value="__('Due by')"/>
                            <jet-input type="date" id="inputBillingDate" class="mt-1 w-full" v-model="form.billing_date"/>
                            <jet-input-error :message="form.errors.billing_date" class="mt-2"/>
                        </div>

                        <!-- Payment method -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label :value="__('Payment method')"/>
                            <jet-dropdown-select class="mt-1 w-full" :options="paymentMethods" v-model:value="form.payment_method"/>
                            <jet-input-error :message="form.errors.payment_method" class="mt-2"/>
                        </div>

                        <!-- Documents currency -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label :value="__('Billing currency')"/>
                            <jet-dropdown-select class="mt-1 w-full" :options="currencies" v-model:value="form.invoice_currency"/>
                            <jet-input-error :message="form.errors.invoice_currency" class="mt-2"/>
                        </div>

                        <!-- Order number -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputOrderNumber" :value="__('Order number')"/>
                            <jet-input id="inputOrderNumber" type="text" class="mt-1 w-full" v-model="form.order_number"/>
                            <jet-input-error :message="form.errors.order_number" class="mt-2"/>
                        </div>

                        <!-- Variable symbol -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputVariableSymbol" :value="__('Variable symbol')"/>
                            <jet-input id="inputVariableSymbol" type="text" class="mt-1 w-full" v-model="form.variable_symbol"/>
                            <jet-input-error :message="form.errors.variable_symbol" class="mt-2"/>
                        </div>

                        <!-- Constant symbol -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputConstantSymbol" :value="__('Constant symbol')"/>
                            <jet-input id="inputConstantSymbol" type="text" class="mt-1 w-full" v-model="form.constant_symbol"/>
                            <jet-input-error :message="form.errors.constant_symbol" class="mt-2"/>
                        </div>

                        <!-- Specific symbol -->
                        <div class="col-span-6 sm:col-span-3 md:col-span-2">
                            <jet-label for="inputSpecificSymbol" :value="__('Specific symbol')"/>
                            <jet-input id="inputSpecificSymbol" type="text" class="mt-1 w-full" v-model="form.specific_symbol"/>
                            <jet-input-error :message="form.errors.specific_symbol" class="mt-2"/>
                        </div>

                        <!-- OPTIONS -->
                        <!-- Language code -->
                        <div class="col-span-6 md:col-span-2">
                            <jet-label :value="__('Invoice language')"/>
                            <jet-dropdown-select class="mt-1 w-full" :options="langCodes" v-model:value="form.lang_code"/>
                            <jet-input-error :message="form.errors.lang_code" class="mt-2"/>
                        </div>

                        <!-- Modify billing data -->
                        <div class="col-span-3 md:col-span-2 pt-1">
                            <jet-button type="button" @click="modal.modifyBillingData = true" :disabled="form.processing" text-color="text-gray-600" color="gray" :shade="100" block class="h-11 md:mt-5 tracking-wide truncate">
                                {{ __('Modify billing data') }}
                            </jet-button>
                        </div>

                        <!-- Generate QR CODE -->
                        <div class="col-span-3 md:col-span-2 pt-1">
                            <label class="flex items-center mt-3 md:mt-8">
                                <jet-checkbox v-model:checked="form.generate_qr_code" :disabled="!canGenerateQrCode || form.processing"/>
                                <span class="ml-2 text-sm text-gray-600" :class="{'opacity-50': !canGenerateQrCode}">{{ __('Generate QR code') }}</span>
                                <information-circle-icon v-if="!canGenerateQrCode" class="w-4 h-4 text-gray-500 ml-2 -mt-1" v-tooltip="__('Only for clients from Slovakia')"/>
                            </label>
                        </div>
                    </div>
                </jet-card>

                <!-- CLIENT -->
                <client-card :form="form"
                             :clients="clients"
                             :country-dropdown-items="countryDropdownItems"
                             :clients-dropdown-items="clientsDropdownItems"
                             @set-client="setClient($event)"
                             class="order-2"/>

                <!-- INVOICE ITEMS -->
                <jet-card :title="__('Items')" class="order-3 col-span-6 pb-8 rounded-b-none" :padding="false">
                    <!-- Items -->
                    <table id="invoice-items" class="divide-y divide-gray-200 -mt-4 w-full">
                        <thead class="bg-gray-100 hidden lg:table-header-group">
                        <tr>
                            <jet-header-cell class="pl-6 pr-2 py-3">{{ __('Item name') }}</jet-header-cell>
                            <jet-header-cell class="px-2 py-3">{{ __('Quantity') }}</jet-header-cell>
                            <jet-header-cell class="px-2 py-3">{{ __('Unit') }}</jet-header-cell>
                            <jet-header-cell class="px-2 py-3">{{ __('Price') }}</jet-header-cell>
                            <jet-header-cell v-if="taxable" class="px-2 py-3 w-10">{{ __('Tax') }}</jet-header-cell>
                            <jet-header-cell class="px-2 py-3">{{ taxable ? __('Total with VAT') : __('Total') }}</jet-header-cell>
                            <th scope="col" class="relative px-2 py-3"></th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        <template v-for="(item, index) in form.items" :key="`item-${index}`">
                            <tr class="grid grid-cols-6 gap-x-3 gap-y-3 lg:table-row p-4 w-full">
                                <!-- Name -->
                                <jet-data-cell :name="__('Item name')" class="col-span-6 whitespace-nowrap align-top" padding="lg:pl-6 lg:pr-2 lg:py-4">
                                    <item-name :item="item"
                                               :currency="selectedCurrency.symbol"
                                               @selected="populateItem($event.item, $event.event)"
                                               :error="form.errors[`items.${index}.name`]"/>
                                </jet-data-cell>

                                <!-- Quantity -->
                                <jet-data-cell :name="__('Quantity')" class="col-span-2 whitespace-nowrap align-top lg:w-[80px]" padding="lg:px-2 lg:py-4">
                                    <jet-numeric-input v-allow.number.comma @input="calculateItem(item)" :min="1" :precision="2" precision-when-needed :empty-value="1" separator="." class="w-full" v-model="item.quantity" :value="item.quantity"/>
                                    <jet-input-error :message="form.errors[`items.${index}.quantity`]" class="mt-1"/>
                                </jet-data-cell>

                                <!-- Unit -->
                                <jet-data-cell :name="__('Unit')" class="col-span-2 whitespace-nowrap align-top lg:w-[120px]" padding="lg:px-2 lg:py-4">
                                    <jet-dropdown-select :options="units" v-model:value="item.unit" style="margin-top: 1px;"/>
                                    <jet-input-error :message="form.errors[`items.${index}.unit`]" class="mt-1"/>
                                </jet-data-cell>

                                <!-- Price -->
                                <jet-data-cell :name="__('Price')" class="col-span-2 whitespace-nowrap align-top lg:min-w-[100px] lg:max-w-[140px]" padding="lg:px-2 lg:py-4">
                                    <div class="flex rounded-md shadow-sm">
                                        <jet-numeric-input v-allow.number.comma.minus @input="calculateItem(item)" :minus="true" :precision="selectedCurrency.precision" :empty-value="0" separator="." class="w-full rounded-r-none" v-model="item.price" :value="item.price"/>
                                        <span class="inline-flex items-center px-1.5 md:px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">{{ selectedCurrency.symbol }}</span>
                                    </div>
                                    <jet-input-error :message="form.errors[`items.${index}.price`]" class="mt-1"/>
                                </jet-data-cell>

                                <!-- Tax -->
                                <jet-data-cell :name="__('Tax')" v-if="taxable" class="col-span-2 whitespace-nowrap align-top lg:w-[100px]" padding="lg:px-2 lg:py-4">
                                    <div class="flex rounded-md shadow-sm">
                                        <jet-numeric-input v-allow.number @input="calculateItem(item)" :min="0" :max="99" :precision="0" :empty-value="0" class="w-full rounded-r-none" v-model="item.tax" :value="item.tax"/>
                                        <span class="inline-flex items-center px-1.5 md:px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">%</span>
                                    </div>
                                    <jet-input-error :message="form.errors[`items.${index}.tax`]" class="mt-1"/>
                                </jet-data-cell>

                                <!-- Price with tax -->
                                <jet-data-cell :name="__('Total with VAT')" v-if="taxable" class="col-span-2 whitespace-nowrap align-top lg:min-w-[100px] lg:max-w-[140px]" padding="lg:px-2 lg:py-4">
                                    <div class="flex rounded-md shadow-sm">
                                        <jet-numeric-input v-allow.number.comma.minus @input="calculateItem(item, true)" :minus="true" :precision="selectedCurrency.precision" :empty-value="0" separator="." class="w-full rounded-r-none" v-model="item.price_with_tax" :value="item.price_with_tax"/>
                                        <span class="inline-flex items-center px-1.5 md:px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">{{ selectedCurrency.symbol }}</span>
                                    </div>
                                    <jet-input-error :message="form.errors[`items.${index}.price_with_tax`]" class="mt-2"/>
                                </jet-data-cell>

                                <!-- Price with no tax -->
                                <jet-data-cell :name="__('Total')" v-else class="col-span-2 whitespace-nowrap align-top" padding="lg:px-2 lg:py-4 lg:min-w-[100px] lg:max-w-[120px]">
                                    <div class="flex rounded-md shadow-sm">
                                        <jet-numeric-input v-allow.number.comma.minus @input="calculateItem(item, true)" :minus="true" :precision="selectedCurrency.precision" :empty-value="0" separator="." class="w-full rounded-r-none" v-model="item.subtotal_without_tax" :value="item.subtotal_without_tax"/>
                                        <span class="inline-flex items-center px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">{{ selectedCurrency.symbol }}</span>
                                    </div>
                                    <jet-input-error :message="form.errors[`items.${index}.price_with_tax`]" class="mt-2"/>
                                </jet-data-cell>

                                <jet-data-cell class="col-span-2 whitespace-nowrap align-top" padding="pr-6 pt-[22px] lg:pl-2 lg:pt-4 lg:w-[100px]">
                                    <jet-button type="button" class="items-center justify-center bg-green-500 hover:bg-green-600 active:bg-green-500 active:border-green-600 focus:border-green-600 h-10 w-10 px-1 py-1 mr-2" @click="toggleItemDiscount(item)" :disabled="form.processing">
                                        <tag-icon class="h-4 w-4"/>
                                    </jet-button>

                                    <jet-danger-button class="h-10 w-10 px-1 py-1" @click="confirmItemDelete(index)" :disabled="form.processing || form.items.length === 1">
                                        <trash-icon class="h-4 w-4"/>
                                    </jet-danger-button>
                                </jet-data-cell>
                            </tr>

                            <tr v-if="item.descriptionToggled || item.discountToggled" class="item-extra-row grid gap-y-2 lg:table-row px-4 lg:p-4 w-full">
                                <jet-data-cell :name="item.descriptionToggled ? __('Description') : null" class="lg:pl-6 pt-0 whitespace-nowrap align-top" :padding="null">
                                    <!-- Description -->
                                    <JetTextarea v-if="item.descriptionToggled" class="w-full text-sm" v-model="item.description" :placeholder="__('Description')"/>
                                </jet-data-cell>

                                <jet-data-cell colspan="5" class="lg:px-2 align-top" :padding="null">
                                    <!-- Discount -->
                                    <div v-if="item.discountToggled" class="grid grid-cols-11 gap-4 m-0 font-semibold bg-green-50 ring-2 ring-green-100 p-3 rounded-md h-16 mb-4 lg:max-w-[515px]">
                                        <div class="col-span-3">
                                            <div class="flex rounded-md shadow-sm">
                                                <jet-numeric-input v-allow.number.comma @input="calculateDiscount('percentage');calculateItem(item)" :min="0" :precision="2" :empty-value="0" separator="." class="w-full rounded-r-none" v-model="item.discount_percent" :value="item.discount_percent"/>
                                                <span class="inline-flex items-center px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">%</span>
                                            </div>
                                            <jet-input-error :message="form.errors[`item.${index}.discount_percent`]" class="mt-2"/>
                                        </div>

                                        <div class="col-span-4">
                                            <div class="flex rounded-md shadow-sm">
                                                <jet-numeric-input v-allow.number.comma @input="calculateDiscount('number');calculateItem(item)" :min="0" :precision="selectedCurrency.precision" :empty-value="0" separator="." class="w-full rounded-r-none" v-model="item.discount_amount" :value="item.discount_amount"/>
                                                <span class="inline-flex items-center px-3 rounded-r-md border border-r-1 border-l-0 border-gray-300 bg-gray-50 text-gray-500 text-sm">{{ selectedCurrency.symbol }}</span>
                                            </div>
                                            <jet-input-error :message="form.errors[`item.${index}.discount_amount`]" class="mt-2"/>
                                        </div>

                                        <div class="col-span-4 h-16 -mt-3 -mr-3 p-4 pt-5 bg-green-100 text-center text-lg text-green-500">
                                            <span class="hidden sm:inline">{{ __('Discount') }}:</span> {{ money(item.discount_amount) }}
                                        </div>
                                    </div>
                                </jet-data-cell>
                            </tr>
                        </template>
                        </tbody>
                    </table>

                    <div class="flex justify-end">
                        <jet-button type="button" color="indigo" :shade="600" class="h-10 mt-1 mr-4 lg:mr-6" @click="addItem()" :disabled="form.processing">
                            <plus-icon class="h-4 w-4 mr-2 -ml-1"/>
                            <span class="hidden sm:inline-block">{{ __('Add item') }}</span>
                            <span class="sm:hidden">{{ __('Add') }}</span>
                        </jet-button>
                    </div>
                </jet-card>

                <!-- NOTES -->
                <jet-card :title="__('Notes')" class="order-5 md:order-4 col-span-6 md:col-span-3 lg:col-span-4 pb-8">
                    <div class="grid grid-cols-6 gap-2">
                        <!-- Note before items -->
                        <div class="col-span-6">
                            <jet-label for="inputNoteItems" :value="__('Opening text')"/>
                            <JetTextarea id="inputNoteItems" class="mt-1 w-full h-12" v-model="form.note_before_items"/>
                            <jet-input-error :message="form.errors.note_before_items" class="mt-2"/>
                        </div>

                        <!-- Note -->
                        <div class="col-span-6">
                            <jet-label for="inputNote" :value="__('Closing text')"/>
                            <JetTextarea id="inputNote" class="mt-1 w-full h-12" v-model="form.note"/>
                            <jet-input-error :message="form.errors.note" class="mt-2"/>
                        </div>
                    </div>
                </jet-card>

                <!-- CALCULATIONS -->
                <jet-card id="invoice-summary" class="order-4 md:order-5 col-span-6 md:col-span-3 lg:col-span-2 overflow-hidden">
                    <dl class="text-right border-r-8 border-indigo-500 -m-6">
                        <div v-if="document.taxable" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                            <dt class="font-medium text-gray-500">{{ __('VAT Base') }}</dt>
                            <dd class="mt-1 text-gray-900 mt-0 col-span-1">{{ money(form.amount) }}</dd>
                        </div>

                        <div v-if="document.taxable" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                            <dt class="font-medium text-gray-500">{{ __('Tax') }}</dt>
                            <dd class="mt-1 text-gray-900 mt-0 col-span-1">{{ money(form.tax) }}</dd>
                        </div>

                        <div v-if="form.subtotal_without_tax" class="text-base md:text-sm odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                            <dt class="font-medium text-gray-500">{{ __('Without tax') }}</dt>
                            <dd class="mt-1 text-gray-900 mt-0 sm:col-span-1">{{ money(form.subtotal_without_tax) }}</dd>
                        </div>

                        <div class="text-xl md:text-lg odd:bg-gray-50 px-4 py-5 grid grid-cols-2 gap-4 px-6">
                            <dt class="font-bold text-gray-500">{{ __('Total') }}</dt>
                            <dd class="mt-1 font-bold text-gray-900 mt-0 col-span-1">
                                {{ document.taxable ? money(form.amount_with_tax) : money(form.amount) }}
                            </dd>
                        </div>
                    </dl>
                </jet-card>

                <!-- INVOICE ISSUED BY -->
                <jet-card :title="__('Invoice issued by')" class="order-6 col-span-6 pb-8 mb-6">
                    <div class="grid grid-cols-6 gap-6">
                        <!-- Documents issued -->
                        <div class="col-span-2">
                            <jet-label for="inputInvoiceIssued" :value="__('Name')"/>
                            <jet-input id="inputInvoiceIssued" type="text" class="mt-1 w-full" v-model="form.issued_by"/>
                            <jet-input-error :message="form.errors.issued_by" class="mt-2"/>
                        </div>

                        <!-- Phone -->
                        <div class="col-span-2">
                            <jet-label for="inputIssuedByPhone" :value="__('Phone')"/>
                            <jet-input id="inputIssuedByPhone" type="text" class="mt-1 w-full" v-model="form.issued_by_phone"/>
                            <jet-input-error :message="form.errors.issued_by_phone" class="mt-2"/>
                        </div>

                        <!-- Email -->
                        <div class="col-span-2">
                            <jet-label for="inputIssuedByEmail" :value="__('Email')"/>
                            <jet-input id="inputIssuedByEmail" type="text" class="mt-1 w-full" v-model="form.issued_by_email"/>
                            <jet-input-error :message="form.errors.issued_by_email" class="mt-2"/>
                        </div>
                    </div>
                </jet-card>
            </template>

            <template #actions>
                <jet-back-button :url="route('documents', 'invoices')" :disabled="form.processing"></jet-back-button>

                <jet-action-message :on="form.recentlySuccessful" class="w-full mr-2">{{ __('Saved successfully') }}</jet-action-message>

                <jet-button @click="downloadPdf = false" :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-5 h-10">
                    <document-add-icon class="w-4 h-4 -ml-1 mr-2"/>{{ __('Save') }}
                </jet-button>

                <jet-button @click="downloadPdf = true" :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-5 h-10 ml-3 hidden sm:flex">
                    <document-download-icon class="w-4 h-4 -ml-1 mr-2"/>{{ __('Save & download') }}
                </jet-button>
            </template>
        </jet-form-section>

        <add-new-client-modal v-if="modal.addClient"
                              :show="modal.addClient"
                              :country-dropdown-items="countryDropdownItems"
                              @close="modal.addClient = false"
                              @set-client="setClient($event)"/>

        <modify-billing-data-modal v-if="modal.modifyBillingData"
                                   :show="modal.modifyBillingData"
                                   :with-logo="form.billing_data.with_logo"
                                   :company-details="form.billing_data.company_details"
                                   :bank-accounts="form.billing_data.bank_accounts"
                                   :country-dropdown-items="countryDropdownItems"
                                   @update-billing-data="updateBillingData($event)"
                                   @close="modal.modifyBillingData = false"/>

        <jet-delete-confirmation-modal :title="__('Delete item')"
                                       :message="__('This action will delete item. Are you sure?')"
                                       :confirm-button-text="__('Delete item')"
                                       :processing="form.processing"
                                       :show="documentItemDeleteIndex !== null"
                                       @confirmed="deleteItem"
                                       @close="documentItemDeleteIndex = null"/>
    </app-layout>
</template>

<script>
    import AddNewClientModal from "@/Pages/Modals/Client/AddClient.vue"
    import AppLayout from '@/Layouts/AppLayout.vue'
    import ClientCard from "@/Pages/Documents/Shared/Partials/ClientCard";
    import Currencies from "@/Data/DropdownOptions/Currencies"
    import DocumentLanguages from "@/Data/DropdownOptions/DocumentLanguages"
    import DocumentMixin from '@/Mixins/Document'
    import EventBus from '@/Services/EventBus'
    import ItemName from "@/Pages/Documents/Shared/Item/ItemName";
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetBackButton from '@/Jetstream/BackButton.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetCard from '@/Jetstream/Card.vue'
    import JetCheckbox from '@/Jetstream/Checkbox.vue'
    import JetCreateLinkButton from '@/Jetstream/CreateLinkButton.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDataCell from "@/Jetstream/Table/DataCell";
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetNumericInput from '@/Jetstream/NumericInput.vue'
    import JetTextarea from '@/Jetstream/Textarea.vue'
    import ModifyBillingDataModal from "../../Modals/Client/ModifyBillingData.vue"
    import PaymentMethods from "@/Data/DropdownOptions/PaymentsMethods"
    import Units from "@/Data/DropdownOptions/Units"
    import axios from 'axios'
    import cloneDeep from 'lodash/cloneDeep'
    import fileDownload from 'js-file-download'
    import moment from 'moment'
    import {Link} from '@inertiajs/inertia-vue3'
    import {TagIcon, DocumentAddIcon, DocumentDownloadIcon, InformationCircleIcon} from '@heroicons/vue/solid'
    import {TrashIcon, PlusIcon} from '@heroicons/vue/outline'
    import {defineComponent} from 'vue'
    import {handleError} from "@/Services/RequestService"

    export default defineComponent({
        mixins: [DocumentMixin],

        components: {
            JetDataCell,
            JetHeaderCell,
            ItemName,
            ClientCard,
            AddNewClientModal,
            AppLayout,
            DocumentAddIcon,
            DocumentDownloadIcon,
            InformationCircleIcon,
            JetActionMessage,
            JetBackButton,
            JetButton,
            JetCard,
            JetCheckbox,
            JetCreateLinkButton,
            JetDangerButton,
            JetDeleteConfirmationModal,
            JetDropdownSelect,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetTextarea,
            Link,
            ModifyBillingDataModal,
            PlusIcon,
            TagIcon,
            TrashIcon,
            JetNumericInput,
        },

        props: ['document', 'bankAccounts', 'clients', 'companyDetails', 'documentCompanyName', 'countryDropdownItems', 'issuingRealInvoice'],

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'POST',
                    number: '',
                    name: '',
                    amount: 0,
                    amount_with_tax: 0,
                    tax: 0,
                    discount: 0,
                    date_created: '',
                    date_added: '',
                    billing_date: '',
                    variable_symbol: '',
                    constant_symbol: '',
                    specific_symbol: '',
                    order_number: '',
                    invoice_currency: 'EUR',
                    payment_method: '',
                    note: '',
                    note_before_items: '',
                    client_id: '',
                    issued_by: '',
                    issued_by_phone: '',
                    issued_by_email: '',
                    lang_code: null,
                    generate_qr_code: null,
                    items: [],
                    client: {},
                    billing_data: {
                        with_logo: true,
                        bank_accounts: [],
                        company_details: {},
                    },
                }),
                downloadPdf: false,
                currencies: Currencies,
                paymentMethods: PaymentMethods,
                units: Units,
                documentItemDeleteIndex: null,
                langCodes: DocumentLanguages,
                modal: {
                    modifyBillingData: false,
                },
            };
        },

        computed: {
            canGenerateQrCode() {
                return this.form.client.country === 'SK' || this.form.client_id === null;
            },
        },

        mounted() {
            this.form.number = this.document.number;
            this.form.name = this.document.name;
            this.form.date_created = this.document.date_created
                ? moment(this.document.date_created).format('YYYY-MM-DD')
                : moment().format('YYYY-MM-DD');
            this.form.date_added = this.document.date_added
                ? moment(this.document.date_added).format('YYYY-MM-DD')
                : moment().format('YYYY-MM-DD');
            this.form.billing_date = this.document.billing_date
                ? moment(this.document.billing_date).format('YYYY-MM-DD')
                : moment().add(14, 'days').format('YYYY-MM-DD');
            this.form.variable_symbol = this.document.variable_symbol;
            this.form.constant_symbol = this.document.constant_symbol;
            this.form.specific_symbol = this.document.specific_symbol;
            this.form.order_number = this.document.order_number;
            this.form.invoice_currency = this.document.invoice_currency;
            this.form.payment_method = this.document.payment_method;
            this.form.note = this.document.note;
            this.form.note_before_items = this.document.note_before_items;
            this.form.client_id = this.document.client_id;
            this.form.client = this.document.client;
            this.form.issued_by = this.document.issued_by;
            this.form.issued_by_phone = this.document.issued_by_phone;
            this.form.issued_by_email = this.document.issued_by_email;
            this.form.lang_code = this.document.lang_code;
            this.form.generate_qr_code = this.document.generate_qr_code === 1;

            this.form.billing_data.with_logo = this.document.with_logo;
            this.form.billing_data.bank_accounts = cloneDeep(this.bankAccounts);
            this.form.billing_data.company_details = cloneDeep(this.document.company_details);

            // items
            this.form.items = this.document.items
                .map(item => {
                    item.discountToggled = item.discount_amount;
                    item.descriptionToggled = item.description && item.description.length > 0;
                    return item;
                });

            if (!this.document.taxable) {
                this.form.items[0].tax = 0;
            }

            this.calculateAll();
        },

        watch: {
            'form.client_id'() {
                this.form.generate_qr_code = this.canGenerateQrCode;
            },
        },

        methods: {
            update() {
                EventBus.emit('loader:show');

                this.form.put(route('documents.update', ['invoices', this.document.id]), {
                    onSuccess: () => {
                        if (this.downloadPdf) {
                            return axios.get(route('documents.download', ['invoices', this.document.id]), {responseType: 'blob'})
                                .then(response => {
                                    fileDownload(response.data, `${this.documentCompanyName}_${this.form.number}_${this.form.lang_code}.pdf`, 'application/pdf');
                                });
                        }
                    },
                    onError: handleError,
                    onFinish: () => EventBus.emit('loader:hide'),
                })
            },

            confirmItemDelete(index) {
                this.documentItemDeleteIndex = index;
            },

            setClient(client) {
                const index = this.clients.findIndex(i => i.id === client.id);

                if (index >= 0) this.clients[index] = client;
                else            this.clients.push(client);

                this.form.client = client;
                this.form.client_id = client.id;
            },

            updateBillingData(payload) {
                this.form.billing_data.with_logo = payload.withLogo;
                this.form.billing_data.bank_accounts = payload.bankAccounts;
                this.form.billing_data.company_details = payload.companyDetails;
            },
        },
    })
</script>
