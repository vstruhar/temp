<template>
    <app-layout :title="__('Client')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Client') }}
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-0">
            <jet-form-section :with-title="false" :with-card="false">
                <template #form>
                    <jet-card :title="__('Contact and invoice data')" class="col-span-6">
                        <!-- Name -->
                        <div class="w-full mb-7">
                            <jet-label for="name" :value="__('Name')"/>
                            <jet-input id="name" type="text" class="mt-1 block w-full" v-model="client.name" disabled/>
                        </div>

                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 lg:col-span-2 grid grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                                <!-- Address -->
                                <div>
                                    <jet-label for="address" :value="__('Address')"/>
                                    <jet-input id="address" type="text" class="mt-1 block w-full" v-model="client.address" disabled/>
                                </div>

                                <!-- Postal Code -->
                                <div>
                                    <jet-label for="postal_code" :value="__('Zip')"/>
                                    <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="client.postal_code" disabled/>
                                </div>

                                <!-- City -->
                                <div>
                                    <jet-label for="city" :value="__('City')"/>
                                    <jet-input id="city" type="text" class="mt-1 block w-full" v-model="client.city" disabled/>
                                </div>

                                <!-- Country -->
                                <div>
                                    <jet-label :value="__('Country')"/>
                                    <jet-input type="text" class="mt-1 block w-full" v-model="client.country_label" disabled/>
                                </div>
                            </div>

                            <div class="col-span-6 md:col-span-3 lg:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                                <!-- company ID -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="organization_id" :value="__('Business ID')"/>
                                    <jet-input id="organization_id" type="text" class="mt-1 block w-full" v-model="client.organization_id" disabled/>
                                </div>

                                <!-- Tax ID -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="tax" :value="__('Tax ID')"/>
                                    <jet-input id="tax" type="text" class="mt-1 block w-full" v-model="client.tax" disabled/>
                                </div>

                                <!-- Value Added Tax ID-->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="value_added_tax" :value="__('VAT')"/>
                                    <jet-input id="value_added_tax" type="text" class="mt-1 block w-full" v-model="client.value_added_tax" disabled/>
                                </div>
                            </div>

                            <div class="col-span-6 md:col-span-3 lg:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5">
                                <!-- E-mail -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="email" :value="__('Email')"/>
                                    <jet-input id="email" type="text" class="mt-1 block w-full" v-model="client.email" disabled/>
                                </div>

                                <!-- Phone -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="phone" :value="__('Phone')"/>
                                    <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="client.phone" disabled/>
                                </div>

                                <!-- Fax -->
                                <div class="col-span-6 sm:col-span-2">
                                    <jet-label for="fax" :value="__('Fax')"/>
                                    <jet-input id="fax" type="text" class="mt-1 block w-full" v-model="client.fax" disabled/>
                                </div>
                            </div>

                            <hr class="col-span-6 border-gray-100 border-2 my-2">

                            <div class="grid grid-cols-4 gap-x-6 gap-y-5 col-span-6 mb-2">
                                <!-- Number account -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="number_account" :value="__('Account number')"/>
                                    <jet-input id="number_account" type="text" class="mt-1 block w-full" v-model="client.number_account" disabled/>
                                </div>

                                <!-- Bank code -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="bank_code" :value="__('Bank code')"/>
                                    <jet-input id="bank_code" type="text" class="mt-1 block w-full" v-model="client.bank_code" disabled/>
                                </div>

                                <!-- IBAN -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="iban" :value="__('IBAN')"/>
                                    <jet-input id="iban" type="text" class="mt-1 block w-full" v-model="client.iban" disabled/>
                                </div>

                                <!-- SWIFT -->
                                <div class="col-span-4 sm:col-span-2 lg:col-span-1">
                                    <jet-label for="swift" :value="__('SWIFT')"/>
                                    <jet-input id="swift" type="text" class="mt-1 block w-full" v-model="client.swift" disabled/>
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
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="client.shipping_address.name" disabled/>
                            </div>

                            <!-- Postal Code -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="postal_code" :value="__('Zip')"/>
                                <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="client.shipping_address.postal_code" disabled/>
                            </div>

                            <!-- City -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="city" :value="__('City')"/>
                                <jet-input id="city" type="text" class="mt-1 block w-full" v-model="client.shipping_address.city" disabled/>
                            </div>

                            <!-- Address -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="address" :value="__('Address')"/>
                                <jet-input id="address" type="text" class="mt-1 block w-full" v-model="client.shipping_address.address" disabled/>
                            </div>

                            <!-- Phone -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label for="phone" :value="__('Phone')"/>
                                <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="client.shipping_address.phone" disabled/>
                            </div>

                            <!-- Country -->
                            <div class="col-span-2 sm:col-span-1 lg:col-span-2">
                                <jet-label :value="__('Country')"/>
                                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="client.shipping_address.country"/>
                            </div>
                        </div>
                    </jet-card>

                    <!-- Contacts -->
                    <jet-card :title="__('Contacts')" class="col-span-6 lg:col-span-4 mb-6" :padding="false">
                        <div class="p-5 lg:p-0">
                            <jet-table class="border-none -mt-3">
                                <template #thead>
                                    <jet-header-cell class="px-6">{{ __('Name') }}</jet-header-cell>
                                    <jet-header-cell>{{ __('Email') }}</jet-header-cell>
                                    <jet-header-cell>{{ __('Phone') }}</jet-header-cell>
                                </template>

                                <template #tbody>
                                    <jet-row v-for="contact in client.contacts" :key="`contact-table-row-${contact.id}`" class="cursor-auto">
                                        <jet-data-cell :name="__('Name')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5" slot-classes="lg:pl-2">
                                            <jet-input type="text" class="w-full" v-model="contact.name" disabled/>
                                        </jet-data-cell>

                                        <jet-data-cell :name="__('Email')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5">
                                            <jet-input type="text" class="w-full" v-model="contact.email" disabled/>
                                        </jet-data-cell>

                                        <jet-data-cell :name="__('Phone')" class="col-span-2 px-0 py-0 lg:px-4 lg:py-5">
                                            <jet-input type="text" class="w-full" v-model="contact.phone" disabled/>
                                        </jet-data-cell>
                                    </jet-row>
                                </template>
                            </jet-table>
                        </div>
                    </jet-card>
                </template>

                <template #actions>
                    <div class="w-full flex justify-start">
                        <jet-back-button :url="route('clients')"/>
                    </div>
                </template>
            </jet-form-section>
        </div>
    </app-layout>
</template>

<script>
import AppLayout from '@/Layouts/AppLayout.vue'
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetBackButton from '@/Jetstream/BackButton.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetCard from '@/Jetstream/Card.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetCreateLinkButton from '@/Jetstream/CreateLinkButton.vue'
import JetDataCell from "@/Jetstream/Table/DataCell";
import JetDropdownSelectFilter from '@/Jetstream/DropdownSelectFilter.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetRow from "@/Jetstream/Table/Row";
import JetTable from '@/Jetstream/Table.vue'
import JetTextarea from '@/Jetstream/Textarea.vue'
import {Link} from '@inertiajs/inertia-vue3'
import {PlusIcon} from '@heroicons/vue/solid'
import {defineComponent} from 'vue'

export default defineComponent({
    props: ['client', 'countryDropdownItems'],

    components: {
        AppLayout,
        JetActionMessage,
        JetBackButton,
        JetButton,
        JetCard,
        JetCheckbox,
        JetCreateLinkButton,
        JetDataCell,
        JetDropdownSelectFilter,
        JetFormSection,
        JetHeaderCell,
        JetInput,
        JetLabel,
        JetRow,
        JetTable,
        JetTextarea,
        Link,
        PlusIcon,
    },
})
</script>
