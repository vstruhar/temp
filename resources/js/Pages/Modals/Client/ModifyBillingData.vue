<template>
    <modal :show="show" max-width="3xl" :closeable="true" @close="$emit('close')">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-0">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <jet-form-section @submitted="saveChanges" :with-title="false">
                    <template #form>
                        <div class="col-span-6 bg-gray-50 -m-6 p-5 flex justify-between rounded-t-lg">
                            <div class="font-semibold text-2xl text-gray-800">{{ __('Modify billing data') }}</div>
                        </div>

                        <div class="col-span-6 sm:col-span-3 grid auto-rows-auto gap-3 border-r-4 border-gray-100 pr-5 mt-4">
                            <!-- Name -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingName" :value="__('Company name')"/>
                                <jet-input id="inputModifyBillingName" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.name"/>
                            </div>

                            <!-- Address -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingAddress" :value="__('Address')"/>
                                <jet-input id="inputModifyBillingAddress" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.address"/>
                            </div>

                            <!-- City -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingCity" :value="__('City')"/>
                                <jet-input id="inputModifyBillingCity" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.city"/>
                            </div>

                            <!-- ZIP -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingPostalCode" :value="__('Zip')"/>
                                <jet-input id="inputModifyBillingPostalCode" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.postal_code"/>
                            </div>

                            <!-- Country -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label :value="__('Country')"/>
                                <jet-dropdown-select-filter :options="countryDropdownItems" v-model:value="companyDetailsLocal.country"/>
                            </div>

                            <!-- Company ID -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingOrganizationId" :value="__('Business ID')"/>
                                <jet-input id="inputModifyBillingOrganizationId" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.organization_id"/>
                            </div>

                            <!-- Tax ID -->
                            <div class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingTax" :value="__('Tax ID')"/>
                                <jet-input id="inputModifyBillingTax" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.tax"/>
                            </div>

                            <!-- Value Added Tax ID -->
                            <div v-if="companyDetails.tax_profile != 0" class="col-span-6 sm:col-span-3">
                                <jet-label for="inputModifyBillingValueAddedTax" :value="__('VAT')"/>
                                <jet-input id="inputModifyBillingValueAddedTax" type="text" class="mt-1 block w-full text-sm" v-model="companyDetailsLocal.value_added_tax"/>
                            </div>
                        </div>

                        <div class="col-span-6 sm:col-span-3 gap-3 mt-4">
                            <div class="text-xl font-semibold text-gray-900 mb-4">{{ __('Logo') }}</div>

                            <div class="flex items-center mb-2">
                                <input type="radio" :value="false" v-model="withLogoLocal" :checked="withLogoLocal === false" id="inputModifyBillingWithoutLogo" class="mr-3 focus:ring-indigo-300 h-4 w-4 text-indigo-600 border-gray-300">
                                <jet-label :value="__(`Without logo`)" for="inputModifyBillingWithoutLogo" class="cursor-pointer text-base"/>
                            </div>

                            <div class="flex items-center mb-2" v-if="companyDetailsLocal.logo !== null">
                                <input type="radio" :value="true" v-model="withLogoLocal" :checked="withLogoLocal === true" id="inputModifyBillingWithLogo" class="mr-3 focus:ring-indigo-300 h-4 w-4 text-indigo-600 border-gray-300">
                                <jet-label for="inputModifyBillingWithLogo" class="cursor-pointer">
                                    <img :src="companyDetailsLocal.logo_url" class="max-h-20 w-full">
                                </jet-label>
                            </div>

                            <hr class="border-2 border-gray-100 my-6">

                            <div class="text-xl font-semibold text-gray-900 mb-3">{{ __('Bank accounts') }}</div>

                            <div v-for="(account, index) in bankAccountsSorted" class="flex items-center mb-2" :class="[account.default ? 'bg-gray-50 -mx-3 px-3 py-2' : '']">
                                <input type="checkbox" :true-value="1" :false-value="0" v-model="account.show_on_documents" :checked="account.show_on_documents === 1" :id="`inputModifyBillingShowOnDocuments${index}`" class="mr-3 focus:ring-indigo-300 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                <jet-label :for="`inputModifyBillingShowOnDocuments${index}`" class="text-base cursor-pointer">
                                    <p class="font-semibold text-lg" :class="[account.default ? 'font-bold text-indigo-600 text-2xl' : '']">{{ account.name }}</p>
                                    <p :class="[account.default ? 'font-bold text-base' : 'text-gray-400 text-sm']">
                                        {{ account.number_account }} /{{ account.bank_code }}
                                    </p>
                                </jet-label>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <jet-secondary-button @click="$emit('close')" class="justify-center w-24 h-10 mr-4">{{ __('Cancel') }}
                        </jet-secondary-button>

                        <jet-button class="justify-center px-6 h-10">
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
    import orderBy from 'lodash/orderBy'
    import cloneDeep from 'lodash/cloneDeep'

    export default defineComponent({
        emits: ['close', 'updateBillingData'],

        components: {
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
        },

        props: {
            show: {
                default: false
            },
            companyDetails: {
                type: Object,
            },
            bankAccounts: {
                type: Array,
                default: () => [],
            },
            withLogo: {
                type: Boolean,
            },
            countryDropdownItems: {
                default: () => [],
            },
        },

        data() {
            return {
                companyDetailsLocal: {},
                bankAccountsLocal: [],
                withLogoLocal: true,
            }
        },

        computed: {
            bankAccountsSorted() {
                return orderBy(this.bankAccountsLocal, ['name', 'default'], ['asc', 'desc']);
            },
        },

        watch: {
            show: {
                immediate: true,
                handler(value) {
                    if (value) {
                        this.bankAccountsLocal = cloneDeep(this.bankAccounts);
                        this.companyDetailsLocal = cloneDeep(this.companyDetails);
                        this.withLogoLocal = cloneDeep(this.withLogo);
                    }
                }
            },
        },

        methods: {
            saveChanges() {
                this.$emit('updateBillingData', {
                    withLogo: this.withLogoLocal,
                    companyDetails: this.companyDetailsLocal,
                    bankAccounts: this.bankAccountsLocal,
                });
                this.$emit('close');
            },
        },
    })
</script>
