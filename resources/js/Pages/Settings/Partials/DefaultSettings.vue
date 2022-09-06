<template>
    <jet-form-section @submitted="saveCompanyDetails" :with-title="false">
        <template #form>
            <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
                <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('Default settings') }}</div>
            </div>

            <div class="col-span-6 mt-5 text-xl font-semibold border-b pb-3">{{ __('Bank accounts') }}</div>

            <div class="col-span-6 md:col-span-3">
                <jet-label :value="__('Bank account')"/>
                <jet-dropdown-select-filter :options="data.bankAccountsDropdownItems" v-model:value="form.bank_account"/>
            </div>

            <div class="col-span-6 mt-3 text-xl font-semibold border-b pb-3">{{ __('Document numbers') }}</div>

            <div v-for="(dropdownItems, type) in data.documentNumbersDropdownItems" class="col-span-6 md:col-span-3 last:mb-5" :key="`document-number-${type}`">
                <jet-label :value="documentNumberNames[type]"/>
                <jet-dropdown-select-filter :options="dropdownItems" v-model:value="form.document_numbers[type]"/>
            </div>

            <div class="col-span-6 mt-3 text-xl font-semibold border-b pb-3">{{ __('Invoice issued by') }}</div>

            <div class="col-span-6 grid grid-cols-6 gap-6">
                <div class="col-span-2">
                    <jet-label :value="__('Name')"/>
                    <jet-input type="text" class="mt-1 w-full" v-model="form.issued_by.name"/>
                </div>

                <div class="col-span-2">
                    <jet-label :value="__('Phone')"/>
                    <jet-input type="text" class="mt-1 w-full" v-model="form.issued_by.phone"/>
                </div>

                <div class="col-span-2">
                    <jet-label :value="__('Email')"/>
                    <jet-input type="text" class="mt-1 w-full" v-model="form.issued_by.email"/>
                </div>
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="w-full mr-2">
                {{ __('Saved successfully') }}
            </jet-action-message>

            <jet-button :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                {{ __('Save') }}
            </jet-button>
        </template>
    </jet-form-section>
</template>

<script>
    import {defineComponent} from 'vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetDropdownSelectFilter from '@/Jetstream/DropdownSelectFilter.vue'
    import {handleError} from "@/Services/RequestService";

    export default defineComponent({
        name: "DefaultSettings",

        props: ['data'],

        components: {
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetSecondaryButton,
            JetDropdownSelectFilter,
        },

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'PUT',
                    bank_account: this.data.defaultBankAccount?.default_id,
                    document_numbers: {
                        'invoice': null,
                        'proforma-invoice': null,
                        'credit-note': null,
                        'quotation': null,
                    },
                    issued_by: {
                        name: this.data.defaultIssuedBy?.name,
                        phone: this.data.defaultIssuedBy?.phone,
                        email: this.data.defaultIssuedBy?.email,
                    },
                }),
                documentNumberNames: {
                    'invoice': this.__('Invoice'),
                    'proforma-invoice': this.__('Proforma invoice'),
                    'credit-note': this.__('Credit note'),
                    'quotation': this.__('Quotation'),
                },
            }
        },

        mounted() {
            Object.keys(this.form.document_numbers)
                .forEach(type => this.form.document_numbers[type] = this.data.defaultDocumentNumbers.find(i => i.type === type)?.id);
        },

        methods: {
            saveCompanyDetails() {
                this.form.post(route('settings.default_settings.update'), {
                    preserveScroll: (page) => Object.keys(page.props.errors).length,
                    onError: handleError,
                })
            },
        },
    })
</script>
