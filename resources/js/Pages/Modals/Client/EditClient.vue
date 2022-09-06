<template>
    <modal :show="show" max-width="4xl" :closeable="true" @close="$emit('close')">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <jet-form-section @submitted="updateClient" :with-title="false">
                    <template #form>
                        <div class="col-span-6 bg-gray-50 -m-6 mb-0 p-5 flex justify-between rounded-t-lg">
                            <div class="font-semibold text-2xl text-gray-800">{{ __('Edit client') }}</div>
                        </div>

                        <!-- Name -->
                        <div class="col-span-6 sm:col-span-6">
                            <jet-label for="name" :value="__('Name')" required/>
                            <jet-company-autocomplete class="mt-1" v-model:value="form.name" :value="form.name" :country="form.country" @companySelected="populateFields"/>
                            <jet-input-error :message="errors.name" class="mt-2"/>
                        </div>

                        <div class="col-span-6 md:col-span-2 grid grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                            <!-- Address -->
                            <div>
                                <jet-label for="address" :value="__('Address')" required/>
                                <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.address"/>
                                <jet-input-error :message="errors.address" class="mt-2"/>
                            </div>

                            <!-- Postal Code -->
                            <div>
                                <jet-label for="postal_code" :value="__('Zip')" required/>
                                <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="form.postal_code"/>
                                <jet-input-error :message="errors.postal_code" class="mt-2"/>
                            </div>

                            <!-- City -->
                            <div>
                                <jet-label for="city" :value="__('City')" required/>
                                <jet-input id="city" type="text" class="mt-1 block w-full" v-model="form.city"/>
                                <jet-input-error :message="errors.city" class="mt-2"/>
                            </div>

                            <!-- Country -->
                            <div>
                                <jet-label :value="__('Country')" required/>
                                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="form.country"/>
                                <jet-input-error :message="errors.country" class="mt-2"/>
                            </div>
                        </div>

                        <div class="col-span-6 md:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5 border-gray-100 border-b-4 md:border-r-4 md:border-b-0 md:pr-6 pb-7 md:pb-0">
                            <!-- company ID -->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="organization_id" :value="__('Business ID')"/>
                                <jet-input id="organization_id" type="text" class="mt-1 block w-full" v-model="form.organization_id"/>
                                <jet-input-error :message="errors.organization_id" class="mt-2"/>
                            </div>

                            <!-- Tax ID -->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="tax" :value="__('Tax ID')"/>
                                <jet-input id="tax" type="text" class="mt-1 block w-full" v-model="form.tax"/>
                                <jet-input-error :message="errors.tax" class="mt-2"/>
                            </div>

                            <!-- Value Added Tax ID-->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="value_added_tax" :value="__('VAT')"/>
                                <jet-input id="value_added_tax" type="text" class="mt-1 block w-full" v-model="form.value_added_tax"/>
                                <jet-input-error :message="errors.value_added_tax" class="mt-2"/>
                            </div>
                        </div>

                        <div class="col-span-6 md:col-span-2 grid grid-rows-3 md:grid-rows-4 gap-5">
                            <!-- E-mail -->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="email" :value="__('Email')"/>
                                <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email"/>
                                <jet-input-error :message="errors.email" class="mt-2"/>
                            </div>

                            <!-- Phone -->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="phone" :value="__('Phone')"/>
                                <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone"/>
                                <jet-input-error :message="errors.phone" class="mt-2"/>
                            </div>

                            <!-- Fax -->
                            <div class="col-span-6 sm:col-span-2">
                                <jet-label for="fax" :value="__('Fax')"/>
                                <jet-input id="fax" type="text" class="mt-1 block w-full" v-model="form.fax"/>
                                <jet-input-error :message="errors.fax" class="mt-2"/>
                            </div>
                        </div>

                        <hr class="col-span-6 border-gray-100 border-2">

                        <div class="flex">
                            <jet-checkbox id="delivery_address" v-model:checked="deliveryAddressEnabled"/>
                            <jet-label for="delivery_address" :value="__('Delivery address')" class="text-base ml-3 -mt-1 whitespace-nowrap"/>
                        </div>

                        <div v-if="deliveryAddressEnabled" class="col-span-6 grid grid-cols-1 md:grid-cols-3 gap-y-2 gap-x-9 mb-2">
                            <!-- Name -->
                            <div class="col-span-1">
                                <jet-label for="name" :value="__('Name')"/>
                                <jet-input id="name" type="text" class="mt-1 block w-full" v-model="form.shipping_address.name"/>
                            </div>

                            <!-- Postal Code -->
                            <div class="col-span-1">
                                <jet-label for="shipping_postal_code" :value="__('Zip')"/>
                                <jet-input id="shipping_postal_code" type="text" class="mt-1 block w-full" v-model="form.shipping_address.postal_code"/>
                            </div>

                            <!-- City -->
                            <div class="col-span-1">
                                <jet-label for="shipping_city" :value="__('City')"/>
                                <jet-input id="shipping_city" type="text" class="mt-1 block w-full" v-model="form.shipping_address.city"/>
                            </div>

                            <!-- Address -->
                            <div class="col-span-1">
                                <jet-label for="shipping_address" :value="__('Address')"/>
                                <jet-input id="shipping_address" type="text" class="mt-1 block w-full" v-model="form.shipping_address.address"/>
                            </div>

                            <!-- Phone -->
                            <div class="col-span-1">
                                <jet-label for="shipping_phone" :value="__('Phone')"/>
                                <jet-input id="shipping_phone" type="text" class="mt-1 block w-full" v-model="form.shipping_address.phone"/>
                            </div>

                            <!-- Country -->
                            <div class="col-span-1">
                                <jet-label :value="__('Country')"/>
                                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="form.shipping_address.country"/>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <jet-secondary-button @click="$emit('close')" :disabled="requestPending" class="justify-center w-24 h-10 mr-4">{{ __('Cancel') }}</jet-secondary-button>

                        <jet-button :class="{ 'opacity-25': requestPending }" :disabled="requestPending" class="justify-center px-6 h-10">
                            {{ __('Save') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </modal>
</template>

<script>
import {defineComponent} from 'vue'
import Modal from '../../../Jetstream/Modal.vue'
import {Link} from '@inertiajs/inertia-vue3'
import JetButton from '@/Jetstream/Button.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetActionMessage from '@/Jetstream/ActionMessage.vue'
import JetDropdownSelectFilter from '@/Jetstream/DropdownSelectFilter.vue'
import axios from 'axios';
import JetCompanyAutocomplete from '@/Jetstream/CompanyAutocomplete.vue'
import JetCheckbox from "@/Jetstream/Checkbox";

export default defineComponent({
    emits: ['close', 'setClient'],

    components: {
        JetCheckbox,
        Modal,
        Link,
        JetInput,
        JetInputError,
        JetActionMessage,
        JetSecondaryButton,
        JetButton,
        JetFormSection,
        JetLabel,
        JetDropdownSelectFilter,
        JetCompanyAutocomplete,
    },

    props: {
        show: {
            type: Boolean,
            default: false,
        },
        countryDropdownItems: {
            default: () => [],
        },
        client: {
            type: Object,
            default: {},
        },
    },

    data() {
        return {
            deliveryAddressEnabled: false,
            form: {
                name: this.client.name,
                organization_id: this.client.organization_id,  // IČO
                tax: this.client.tax,                          // DIČ
                value_added_tax: this.client.value_added_tax,  // IČ DPH
                address: this.client.address,
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
            },
            requestPending: false,
            errors: {},
        }
    },

    mounted() {
        this.deliveryAddressEnabled = ['name', 'postal_code', 'city', 'address', 'phone'].some(key => this.form.shipping_address[key]);
    },

    methods: {
        updateClient() {
            this.requestPending = true;

            if (this.deliveryAddressEnabled === false) {
                this.form.shipping_address = {name: null, postal_code: null, city: null, address: null, phone: null, country: null};
            }

            axios.put(route('clients.update', this.client.id), this.form)
                .then(({data}) => {
                    this.$emit('setClient', data.client);
                    this.$emit('close');
                })
                .catch(({response}) => {
                    let errors = {};
                    Object.keys(response.data.errors)
                        .forEach(key => errors[key] = response.data.errors[key][0]);

                    this.errors = errors;
                })
                .finally(() => this.requestPending = false);
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
