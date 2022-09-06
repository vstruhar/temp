<template>
    <app-layout :title="__('Edit client')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Edit client') }}
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-0">
            <jet-form-section @submitted="updateClient" :with-title="false" :with-card="false">
                <template #form>
                    <jet-card :title="__('Contact and invoice data')" class="col-span-6">
                        <!-- Name -->
                        <div class="w-full mb-7">
                            <jet-label :value="__('Name')" required/>
                            <jet-company-autocomplete class="mt-1" v-model:value="form.name" :value="form.name" :country="form.country" @companySelected="populateFields"/>
                            <jet-input-error :message="form.errors.name" class="mt-2"/>
                        </div>

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 lg:col-span-2 grid grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                                <!-- Address -->
                                <div>
                                    <jet-label for="address" :value="__('Address')" required/>
                                    <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address"/>
                                    <jet-input-error :message="form.errors.address" class="mt-2"/>
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <jet-label for="postal_code" :value="__('Zip')" required/>
                                    <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="form.postal_code"/>
                                    <jet-input-error :message="form.errors.postal_code" class="mt-2"/>
                                </div>

                                <!-- City -->
                                <div>
                                    <jet-label for="city" :value="__('City')" required/>
                                    <jet-input id="city" type="text" class="mt-1 block w-full" v-model="form.city"/>
                                    <jet-input-error :message="form.errors.city" class="mt-2"/>
                                </div>

                                <!-- Country -->
                                <div>
                                    <jet-label :value="__('Country')" required/>
                                    <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="form.country"/>
                                    <jet-input-error :message="form.errors.country" class="mt-2"/>
                                </div>
                            </div>

                            <div class="col-span-6 md:col-span-3 lg:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                                <!-- company ID -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="organization_id" :value="__('Business ID')"/>
                                    <jet-input id="organization_id" type="text" class="mt-1 block w-full" v-model="form.organization_id"/>
                                    <jet-input-error :message="form.errors.organization_id" class="mt-2"/>
                                </div>

                                <!-- Tax ID -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="tax" :value="__('Tax ID')"/>
                                    <jet-input id="tax" type="text" class="mt-1 block w-full" v-model="form.tax"/>
                                    <jet-input-error :message="form.errors.tax" class="mt-2"/>
                                </div>

                                <!-- Value Added Tax ID-->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="value_added_tax" :value="__('VAT')"/>
                                    <jet-input-vat id="value_added_tax" type="text" class="mt-1 block w-full" v-model="form.value_added_tax"/>
                                </div>
                            </div>

                            <div class="col-span-6 md:col-span-3 lg:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5">
                                <!-- E-mail -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="email" :value="__('Email')"/>
                                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email"/>
                                    <jet-input-error :message="form.errors.email" class="mt-2"/>
                                </div>

                                <!-- Phone -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="phone" :value="__('Phone')"/>
                                    <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone"/>
                                    <jet-input-error :message="form.errors.phone" class="mt-2"/>
                                </div>

                                <!-- Fax -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="fax" :value="__('Fax')"/>
                                    <jet-input id="fax" type="text" class="mt-1 block w-full" v-model="form.fax"/>
                                    <jet-input-error :message="form.errors.fax" class="mt-2"/>
                                </div>
                            </div>

                            <hr class="col-span-6 border-gray-100 border-2 my-2">

                            <div class="grid grid-cols-4 gap-x-6 gap-y-5 col-span-6 mb-2">
                                <!-- Number account -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="number_account" :value="__('Account number')"/>
                                    <jet-input id="number_account" type="text" class="mt-1 block w-full" v-model="form.number_account"/>
                                    <jet-input-error :message="form.errors.number_account" class="mt-2"/>
                                </div>

                                <!-- Bank code -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="bank_code" :value="__('Bank code')"/>
                                    <jet-input id="bank_code" type="text" class="mt-1 block w-full" v-model="form.bank_code"/>
                                    <jet-input-error :message="form.errors.bank_code" class="mt-2"/>
                                </div>

                                <!-- IBAN -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="iban" :value="__('IBAN')"/>
                                    <jet-input id="iban" type="text" class="mt-1 block w-full" v-model="form.iban"/>
                                    <jet-input-error :message="form.errors.iban" class="mt-2"/>
                                </div>

                                <!-- SWIFT -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="swift" :value="__('SWIFT')"/>
                                    <jet-input id="swift" type="text" class="mt-1 block w-full" v-model="form.swift"/>
                                    <jet-input-error :message="form.errors.swift" class="mt-2"/>
                                </div>
                            </div>
                        </div>
                    </jet-card>

                    <!-- Shipping address -->
                    <jet-card :title="__('Delivery address')" class="col-span-6 lg:col-span-2 md:mb-6">
                        <div class="grid grid-cols-2 lg:grid-cols-1 gap-5 mb-2">
                            <!-- Name -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="name" :value="__('Name')"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.shipping_address.name"/>
                            </div>

                            <!-- Postal Code -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="postal_code" :value="__('Zip')"/>
                                <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="form.shipping_address.postal_code"/>
                            </div>

                            <!-- City -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="city" :value="__('City')"/>
                                <jet-input id="city" type="text" class="mt-1 block w-full" v-model="form.shipping_address.city"/>
                            </div>

                            <!-- Address -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="address" :value="__('Address')"/>
                                <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.shipping_address.address"/>
                            </div>

                            <!-- Phone -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="phone" :value="__('Phone')"/>
                                <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.shipping_address.phone"/>
                            </div>

                            <!-- Country -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label :value="__('Country')"/>
                                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="form.shipping_address.country"/>
                            </div>
                        </div>
                    </jet-card>

                    <!-- Contacts -->
                    <jet-card class="col-span-6 lg:col-span-4 mb-6" :padding="false">
                        <template #title>
                            <div class="flex justify-between">
                                <div class="mt-1.5">{{ __('Contacts') }}</div>
                                <jet-button type="button" class="bg-indigo-700 hover:bg-indigo-800 h-10" @click="addContact" :disabled="form.processing">
                                    <plus-icon class="h-3 w-3 mr-2 -ml-1"/>
                                    {{ __('Add') }}
                                </jet-button>
                            </div>
                        </template>

                        <div class="p-5 lg:p-0">
                            <jet-table class="border-none -mt-3">
                                <template #thead>
                                    <jet-header-cell class="px-6">{{ __('Name') }}</jet-header-cell>
                                    <jet-header-cell>{{ __('Email') }}</jet-header-cell>
                                    <jet-header-cell>{{ __('Phone') }}</jet-header-cell>
                                    <th scope="col"></th>
                                </template>

                                <template #tbody>
                                    <jet-row v-for="(contact, index) in form.contacts" :key="`contact-table-row-${contact.id}`" class="cursor-auto">
                                        <jet-data-cell :name="__('Name')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5" slot-classes="lg:pl-2">
                                            <jet-input type="text" class="w-full" v-model="contact.name"/>
                                        </jet-data-cell>

                                        <jet-data-cell :name="__('Email')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5">
                                            <jet-input type="text" class="w-full" v-model="contact.email"/>
                                        </jet-data-cell>

                                        <jet-data-cell :name="__('Phone')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5">
                                            <jet-input type="text" class="w-full" v-model="contact.phone"/>
                                        </jet-data-cell>

                                        <jet-action-cell class="self-end">
                                            <div class="flex justify-end px-2 py-3 whitespace-nowrap">
                                                <jet-danger-button class="h-11 w-11 mt-auto px-3 mx-3 justify-self-end"
                                                                   @click="confirmContactDeletion(index)"
                                                                   :class="{'opacity-25': form.processing}"
                                                                   :disabled="form.processing || form.contacts.length === 1">
                                                    <trash-icon class="h-4 w-4"/>
                                                </jet-danger-button>
                                            </div>
                                        </jet-action-cell>
                                    </jet-row>
                                </template>
                            </jet-table>
                        </div>
                    </jet-card>
                </template>

                <template #actions>
                    <jet-back-button :url="route('clients')" :disabled="form.processing"></jet-back-button>

                    <jet-action-message :on="form.recentlySuccessful" class="w-full mr-2">
                        {{ __('Saved successfully') }}
                    </jet-action-message>

                    <jet-button :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                        {{ __('Save') }}
                    </jet-button>
                </template>
            </jet-form-section>

            <jet-delete-confirmation-modal :title="__('Delete contact')"
                                           :message="__('This action will delete your contact. Are you sure?')"
                                           :confirm-button-text="__('Delete contact')"
                                           :processing="form.processing"
                                           :show="confirmModal"
                                           @confirmed="deleteContact"
                                           @close="confirmModal = null"></jet-delete-confirmation-modal>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import JetActionCell from "@/Jetstream/Table/ActionCell";
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetBackButton from '@/Jetstream/BackButton.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetCard from '@/Jetstream/Card.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetCompanyAutocomplete from '@/Jetstream/CompanyAutocomplete.vue'
import JetCreateLinkButton from '@/Jetstream/CreateLinkButton.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetDataCell from "@/Jetstream/Table/DataCell";
import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
import JetDropdownSelectFilter from '@/Jetstream/DropdownSelectFilter.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
import JetInput from '@/Jetstream/Input.vue'
import JetInputVat from '@/Jetstream/InputVat.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetRow from "@/Jetstream/Table/Row";
import JetTable from '@/Jetstream/Table.vue'
import JetTextarea from '@/Jetstream/Textarea.vue'
import {Link} from '@inertiajs/inertia-vue3'
import {PlusIcon, TrashIcon} from '@heroicons/vue/solid'
import {defineComponent} from 'vue'
import {handleError} from '@/Services/RequestService'

export default defineComponent({
    name: "EditClient",

    components: {
        AppLayout,
        JetActionCell,
        JetActionMessage,
        JetBackButton,
        JetButton,
        JetCard,
        JetCheckbox,
        JetCompanyAutocomplete,
        JetCreateLinkButton,
        JetDangerButton,
        JetDataCell,
        JetDeleteConfirmationModal,
        JetDropdownSelectFilter,
        JetFormSection,
        JetHeaderCell,
        JetInput,
        JetInputVat,
        JetInputError,
        JetLabel,
        JetRow,
        JetTable,
        JetTextarea,
        Link,
        PlusIcon,
        TrashIcon,
    },

    props: ['client', 'countryDropdownItems'],

    data() {
        return {
            form: this.$inertia.form({
                _method: 'PUT',
                name: this.client.name,
                address: this.client.address,
                organization_id: this.client.organization_id,  // IČO
                tax: this.client.tax,                          // DIČ
                value_added_tax: this.client.value_added_tax,  // IČ DPH
                postal_code: this.client.postal_code,
                email: this.client.email,
                phone: this.client.phone,
                city: this.client.city,
                fax: this.client.fax,
                country: this.client.country,
                shipping_address: {
                    name: this.client.shipping_address?.name,
                    postal_code: this.client.shipping_address?.postal_code,
                    city: this.client.shipping_address?.city,
                    address: this.client.shipping_address?.address,
                    phone: this.client.shipping_address?.phone,
                    country: this.client.shipping_address?.country,
                },
                number_account: this.client.number_account,
                bank_code: this.client.bank_code,
                iban: this.client.iban,
                swift: this.client.swift,
                contacts: this.client.contacts || [{
                    name: null,
                    phone: null,
                    email: null,
                }],
            }),
            confirmModal: false,
            deleteContactIndex: null,
        }
    },

    methods: {
        addContact() {
            this.form.contacts.push({
                name: null,
                phone: null,
                email: null,
            });
        },

        confirmContactDeletion(index) {
            this.deleteContactIndex = index;
            this.confirmModal       = true;
        },

        deleteContact() {
            this.form.contacts.splice(this.deleteContactIndex, 1);
            this.confirmModal = false;
        },

        updateClient() {
            // remove empty contacts
            this.form.contacts = this.form.contacts.filter(i => i.name || i.phone || i.email);

            if (this.form.contacts.length === 0) {
                this.form.contacts = null;
            }

            this.form.post(route('clients.update', this.client), {
                onError: handleError,
            })
        },

        populateFields(data) {
            this.form.name            = data.name;
            this.form.address         = data.address;
            this.form.postal_code     = data.postal_code;
            this.form.city            = data.city;
            this.form.organization_id = data.organization_id;
            this.form.tax             = data.tax;
            this.form.value_added_tax = data.value_added_tax;
        },
    },
})
</script>
