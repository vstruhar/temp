<template>
    <jet-form-section @submitted="saveCompanyDetails" :with-title="false">
        <template #form>
            <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
                <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('Contact and invoice data') }}</div>
            </div>

            <!-- Name -->
            <div class="col-span-6 sm:col-span-6 mt-6">
                <jet-label :value="__('Company name')" :required="canEdit"/>
                <jet-company-autocomplete class="mt-1" v-model:value="form.name" :value="form.name" :disabled="!canEdit" :country="form.country" @companySelected="populateFields"/>
                <jet-input-error :message="form.errors.name" class="mt-2"/>
            </div>

            <!-- Type -->
            <div class="col-span-6 sm:col-span-3">
                <jet-label :value="__('Type')" :required="canEdit"/>
                <jet-dropdown-select class="mt-1 block w-full" :options="types" v-model:value="form.type" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.type" class="mt-2"/>
            </div>

            <!-- Register -->
            <div class="col-span-6 sm:col-span-3">
                <jet-label for="register" :value="__('Registered in')"/>
                <jet-input id="register" type="text" class="mt-1 block w-full" v-model="form.register" placeholder="Napr. Okr. súd BA 1, odd. SRO, vl. č 1234/B" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.register" class="mt-2"/>
            </div>

            <!-- Address -->
            <div class="col-span-6 sm:col-span-6">
                <jet-label for="address" :value="__('Address')"/>
                <jet-textarea id="address" v-model="form.address" :rows="3" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.address" class="mt-2"/>
            </div>

            <!-- Postal Code -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="postal_code" :value="__('Zip')"/>
                <jet-input id="postal_code" type="text" class="mt-1 block w-full" v-model="form.postal_code" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.postal_code" class="mt-2"/>
            </div>

            <!-- City -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="city" :value="__('City')"/>
                <jet-input id="city" type="text" class="mt-1 block w-full" v-model="form.city" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.city" class="mt-2"/>
            </div>

            <!-- Country -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label :value="__('Country')" :required="canEdit"/>
                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="form.country" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.country" class="mt-2"/>
            </div>

            <!-- Phone -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="phone" :value="__('Phone')"/>
                <jet-input id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.phone" class="mt-2"/>
            </div>

            <!-- Fax -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="fax" :value="__('Fax')"/>
                <jet-input id="fax" type="text" class="mt-1 block w-full" v-model="form.fax" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.fax" class="mt-2"/>
            </div>

            <!-- Web -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="web" :value="__('Web')"/>
                <jet-input id="web" type="text" class="mt-1 block w-full" v-model="form.web" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.web" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-6 border-b-8 border-gray-100 my-3"></div>

            <div class="space-y-4 col-span-6">
                <jet-label :value="__('Payer type')" class="font-semibold text-xl text-gray-800 -mt-2 mb-4" :required="canEdit"/>

                <div class="flex items-center">
                    <input type="radio" id="radioTaxProfile1" :class="{'bg-gray-100': !canEdit}" name="tax_profile" v-model="form.tax_profile" :value="0" :disabled="!canEdit">
                    <label for="radioTaxProfile1" class="ml-3 block text-sm font-medium text-gray-700">
                        {{ __('VAT non - payer') }}
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="radio" id="radioTaxProfile2" :class="{'bg-gray-100': !canEdit}" name="tax_profile" v-model="form.tax_profile" :value="1" :disabled="!canEdit">
                    <label for="radioTaxProfile2" class="ml-3 block text-sm font-medium text-gray-700">
                        {{ __('VAT payer') }}
                    </label>
                </div>
                <div class="flex items-center">
                    <input type="radio" id="radioTaxProfile3" :class="{'bg-gray-100': !canEdit}" name="tax_profile" v-model="form.tax_profile" :value="2" :disabled="!canEdit">
                    <label for="radioTaxProfile3" class="ml-3 block text-sm font-medium text-gray-700">
                        {{ __('Person registered in accordance with §7 and §7a of the VAT Act (partial VAT payer)') }}
                    </label>
                </div>

                <jet-input-error :message="form.errors.tax_profile" class="mt-2"/>
            </div>

            <div v-if="form.tax_profile !== 0 && can('settings:contact_and_invoice:edit')"
                 @click="modal.taxSettings = true"
                 class="font-medium text-indigo-600 hover:text-indigo-500 hover:underline text-sm cursor-pointer whitespace-nowrap">{{ __('VAT settings') }}</div>

            <div class="col-span-6 sm:col-span-6 border-b-8 border-gray-100 mt-1 mb-3"></div>

            <!-- Company ID -->
            <div class="col-span-6 sm:col-span-2 mb-5">
                <jet-label for="organization_id" :value="__('Business ID')" :required="canEdit"/>
                <jet-input id="organization_id" type="text" class="mt-1 block w-full" v-model="form.organization_id" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.organization_id" class="mt-2"/>
            </div>

            <!-- Tax ID -->
            <div class="col-span-6 sm:col-span-2">
                <jet-label for="tax" :value="__('Tax ID')"/>
                <jet-input id="tax" type="text" class="mt-1 block w-full" v-model="form.tax" :disabled="!canEdit"/>
                <jet-input-error :message="form.errors.tax" class="mt-2"/>
            </div>

            <!-- Value Added Tax ID -->
            <div v-if="form.tax_profile != 0" class="col-span-6 sm:col-span-2">
                <jet-label for="value_added_tax" :value="__('VAT')" :required="canEdit"/>
                <jet-input-vat id="value_added_tax" type="text" class="mt-1 block w-full" v-model="form.value_added_tax" :disabled="!canEdit"/>
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="w-full mr-2">
                {{ __('Saved successfully') }}
            </jet-action-message>

            <jet-button v-if="canEdit" :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                {{ __('Save') }}
            </jet-button>
        </template>
    </jet-form-section>

    <TaxSettingsModal :show="modal.taxSettings"
                      :default-tax-percentage="form.default_tax_percentage"
                      @close="modal.taxSettings = false"
                      @updated="form.default_tax_percentage = $event"/>
</template>

<script>
    import {defineComponent} from 'vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputVat from '@/Jetstream/InputVat.vue'
    import JetTextarea from '@/Jetstream/Textarea.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import JetDropdownSelectFilter from '@/Jetstream/DropdownSelectFilter.vue'
    import JetCompanyAutocomplete from '@/Jetstream/CompanyAutocomplete.vue'
    import {InformationCircleIcon} from '@heroicons/vue/outline'
    import {handleError} from "@/Services/RequestService";
    import TaxSettingsModal from "@/Pages/Settings/Partials/Modals/TaxSettingsModal";

    export default defineComponent({
        name: "ContactAndInvoice",

        props: ['data'],

        components: {
            TaxSettingsModal,
            JetInput,
            JetInputVat,
            JetInputError,
            JetTextarea,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetSecondaryButton,
            JetDropdownSelect,
            JetDropdownSelectFilter,
            JetCompanyAutocomplete,
            InformationCircleIcon,
        },

        data() {
            return {
                canEdit: this.can('settings:contact_and_invoice:edit'),

                form: this.$inertia.form({
                    _method: 'POST',
                    name: '',
                    type: null,
                    country: 'SK',
                    address: '',
                    postal_code: '',
                    city: '',
                    phone: '',
                    fax: '',
                    web: '',
                    register: '',
                    organization_id: '',  // IČO - Business ID
                    tax: '',              // DIČ -
                    value_added_tax: '',  // IČ DPH
                    tax_profile: '',
                    default_tax_percentage: 0,
                }),

                types: [
                    {label: this.__('Trade / simple accounting'), value: 'trade'},
                    {label: this.__('Company / double entry accounting'), value: 'ltd'},
                ],

                modal: {
                    taxSettings: false,
                },
            }
        },

        computed: {
            companyDetails() {
                return this.data.companyDetails;
            },

            countryDropdownItems() {
                return this.data.countryDropdownItems;
            },
        },

        mounted() {
            if (this.companyDetails !== null) {
                this.form.name = this.companyDetails.name;
                this.form.type = this.companyDetails.type;
                this.form.country = this.companyDetails.country;
                this.form.address = this.companyDetails.address;
                this.form.postal_code = this.companyDetails.postal_code;
                this.form.city = this.companyDetails.city;
                this.form.phone = this.companyDetails.phone;
                this.form.fax = this.companyDetails.fax;
                this.form.web = this.companyDetails.web;
                this.form.register = this.companyDetails.register;
                this.form.organization_id = this.companyDetails.organization_id;
                this.form.tax = this.companyDetails.tax;
                this.form.value_added_tax = this.companyDetails.value_added_tax;
                this.form.tax_profile = this.companyDetails.tax_profile;
                this.form.default_tax_percentage = Number(this.companyDetails.default_tax_percentage);
            }
        },

        methods: {
            saveCompanyDetails() {
                this.form.post(route('settings.contact_and_invoice.save'), {
                    preserveScroll: (page) => Object.keys(page.props.errors).length,
                    onError: handleError,
                })
            },

            populateFields(data) {
                this.form.name = data.name;
                this.form.address = data.address;
                this.form.postal_code = data.postal_code;
                this.form.city = data.city;
                this.form.organization_id = data.organization_id;
                this.form.tax = data.tax;
                this.form.value_added_tax = data.value_added_tax;
                this.form.register = data.register;
                this.form.tax_profile = data.tax_profile;
                this.form.type = data.is_company ? 'ltd' : 'trade';
            },
        },
    })
</script>
