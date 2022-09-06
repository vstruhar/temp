<template>
    <JetFormSection :with-title="false" @submitted="createAccounts">
        <template #form>
            <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
                <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('Bank accounts') }}</div>

                <jet-button v-if="can('settings:bank_accounts:create')" type="button" class="bg-indigo-700 hover:bg-indigo-800 h-10" @click="addAccount()" :disabled="form.processing">
                    <plus-icon class="h-3 w-3 mr-2 -ml-1"/> {{ __('Add') }}
                </jet-button>
            </div>

            <div class="col-span-6 grid grid-cols-7 gap-x-4 gap-y-1 border-gray-100 mt-4"
                 :class="form.accounts.length === (index+1) ? 'border-b-0 pb-5' : 'border-b-8 pb-10 -mb-5 lg:mb-0'"
                 v-for="(account, index) in form.accounts"
                 :key="`account-${index}`">

                <!-- Default -->
                <div class="order-1 col-span-3 lg:col-span-1 mt-3 flex pt-4 lg:pt-8">
                    <input type="radio"
                           :value="index"
                           v-model="defaultBankAccount"
                           :checked="defaultBankAccount === index"
                           :disabled="!canEdit"
                           :id="`inputDefault${index}`"
                           class="mr-2 focus:ring-indigo-300 h-4 w-4 text-indigo-600 border-gray-300">
                    <jet-label :value="__('Default')" :for="`inputDefault${index}`"/>
                </div>

                <!-- IBAN -->
                <div class="order-3 lg:order-2 mt-3" :class="can('settings:bank_accounts:delete') ? 'col-span-7 lg:col-span-3' : 'col-span-7 lg:col-span-3'">
                    <jet-label :value="__('IBAN')"/>
                    <jet-input v-allow.number.alpha.space type="text" class="mt-1 block w-full text-sm" v-model="account.iban" :disabled="!canEdit" @input="autoPopulateFields(account)" @paste="autoPopulateFields(account)" @blur="formatIban(account)"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.iban`]" class="mt-2"/>
                </div>

                <!-- Name -->
                <div class="order-4 lg:order-3 col-span-4 md:col-span-3 lg:col-span-2 mt-3">
                    <jet-label :value="__('Bank name')"/>
                    <jet-input type="text" class="mt-1 block w-full text-sm" v-model="account.name" :disabled="!canEdit"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.name`]" class="mt-2"/>
                </div>

                <!-- Currency -->
                <div class="order-5 lg:order-4 md:order-6 col-span-3 md:col-span-1 mt-3">
                    <jet-label for="currency" :value="__('Currency')"/>
                    <jet-dropdown-select :options="currencies" v-model:value="account.currency" class="text-sm" height="38" :disabled="!canEdit"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.currency`]" class="mt-2"/>
                </div>

                <!-- Show on documents -->
                <div class="order-2 lg:order-5 col-span-4 lg:col-span-2 mt-3 flex pt-4 lg:pt-8">
                    <input type="checkbox"
                           true-value="1"
                           false-value="0"
                           v-model="account.show_on_documents"
                           :disabled="!canEdit"
                           :checked="account.show_on_documents === 1"
                           :id="`inputShowOnDocuments${index}`"
                           class="mr-2 focus:ring-indigo-300 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    <jet-label :value="__('Show on documents')" :for="`inputShowOnDocuments${index}`"/>
                </div>

                <!-- Number account -->
                <div class="order-6 md:order-5 col-span-4 lg:col-span-2 mt-3">
                    <jet-label :value="__('Account number')"/>
                    <jet-input v-allow.number.minus type="text" class="mt-1 block w-full text-sm" v-model="account.number_account" :disabled="!canEdit" @input="updateIban(account, 'number')"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.number_account`]" class="mt-2"/>
                </div>

                <!-- Bank code -->
                <div class="order-7 col-span-3 md:col-span-2 lg:col-span-1 mt-3">
                    <jet-label :value="__('Bank code')"/>
                    <jet-input v-allow.number.max-4 type="text" class="mt-1 block w-full text-sm" v-model="account.bank_code" :disabled="!canEdit" @input="updateIban(account, 'code')"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.bank_code`]" class="mt-2"/>
                </div>

                <!-- SWIFT -->
                <div class="order-8 col-span-4 md:col-span-2 lg:col-span-1 mt-3">
                    <jet-label :value="__('SWIFT')"/>
                    <jet-input v-allow.number.alpha type="text" class="mt-1 block w-full text-sm" v-model="account.swift" :disabled="!canEdit"/>
                    <jet-input-error :message="form.errors[`accounts.${index}.swift`]" class="mt-2"/>
                </div>

                <jet-danger-button v-if="can('settings:bank_accounts:delete')"
                                   class="order-9 col-span-3 md:col-span-2 lg:col-span-1 mt-9 h-9 px-3 tracking-normal"
                                   @click="confirmAccountDeletion(index)"
                                   :class="{'opacity-25': form.processing}"
                                   :disabled="form.processing || form.accounts.length === 1">
                    <trash-icon class="h-4 w-4"/>
                </jet-danger-button>
            </div>
        </template>

        <template #actions>
            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                {{ __('Saved successfully') }}
            </jet-action-message>

            <jet-button v-if="can('settings:bank_accounts:edit')" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="justify-center px-6 h-10">
                {{ __('Save') }}
            </jet-button>
        </template>
    </JetFormSection>

    <jet-delete-confirmation-modal :title="__('Delete bank account')"
                                   :message="__('This action will delete bank account. Are you sure?')"
                                   :confirm-button-text="__('Delete bank account')"
                                   :processing="form.processing"
                                   :show="confirmingAccountDeletion"
                                   @confirmed="deleteAccount"
                                   @close="confirmingAccountDeletion = false"/>
</template>

<script>
    import {defineComponent} from 'vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import Button from '../../../Jetstream/Button.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import Currencies from "@/Data/DropdownOptions/Currencies"
    import {PlusIcon, TrashIcon} from '@heroicons/vue/solid'
    import {handleError} from "@/Services/RequestService"
    import Banks from '@/Data/Banks'
    import {padEnd} from 'lodash'

    export default defineComponent({
        name: "BankAccounts",

        components: {
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetDeleteConfirmationModal,
            JetDangerButton,
            JetDropdownSelect,
            Button,
            PlusIcon,
            TrashIcon,
        },

        props: ['section', 'data'],

        data() {
            return {
                canEdit: this.can('settings:bank_accounts:edit'),

                form: this.$inertia.form({
                    accounts: [],
                    errors: {},
                }),
                deleteAccountIndex: null,
                confirmingAccountDeletion: false,
                deleting: false,
                currencies: Currencies,
                defaultBankAccount: null,
            };
        },

        watch: {
            defaultBankAccount(index) {
                this.form.accounts.forEach(account => account.default = 0);
                this.form.accounts[index].default = 1;
            },
        },

        mounted() {
            this.form.accounts = this.data;

            if (this.form.accounts.length === 0) {
                this.addAccount();
            }

            this.defaultBankAccount = this.form.accounts.findIndex(i => i.default === 1);
        },

        methods: {
            autoPopulateFields(account, except = []) {
                this.$nextTick(() => {
                    // clean IBAN by keeping only numbers and letters
                    const iban = account.iban.replace(/[^A-Za-z0-9]/g, '');

                    if (iban.length < 2) return;

                    // auto populate only Slovak banks
                    if (iban.length >= 8 && iban.substring(0, 2).toLowerCase() === 'sk') {
                        const bankCode = iban.substring(4, 8);

                        const bank = Banks.find(b => b.code === bankCode);

                        if (bank) {
                            if (!except.includes('name')) account.name = bank.name;
                            if (!except.includes('bank_code')) account.bank_code = bank.code;
                            if (!except.includes('swift')) account.swift = bank.swift;
                        }
                    }

                    if (iban.length > 14) {
                        account.number_account = iban.substring(14);
                    }
                });
            },

            formatIban(account) {
                if (account.iban.length) {
                    account.iban = account.iban.replace(/[^A-Za-z0-9]/g, '').match(/.{1,4}/g).join(' ');
                }
            },

            updateIban(account, type) {
                account.iban = account.iban.replace(/[^A-Za-z0-9]/g, '');

                if (account.iban.length >= 8) {
                    if (type === 'number') {
                        const number = account.number_account.replace(/\s+/g, '');

                        if (number.length <= 10) {
                            account.iban = account.iban.substring(0, 14) + number;
                        }
                    } else if (type === 'code') {
                        let code = account.bank_code.replace(/[\s+]/g, '');
                        code = padEnd(code, 4, '0');
                        account.iban = account.iban.substring(0, 4) + code  + account.iban.substring(8);

                        this.autoPopulateFields(account, ['bank_code'])
                    }
                }
                this.formatIban(account);
            },

            addAccount() {
                this.form.accounts.push({
                    id: null,
                    name: '',
                    currency: 'EUR',
                    number_account: '',
                    bank_code: '',
                    iban: '',
                    swift: '',
                    default: this.form.accounts.length === 0 ? 1 : 0,
                    show_on_documents: 1,
                });
            },

            createAccounts() {
                this.form.post(route('settings.bank_account.store'), {
                    preserveScroll: false,
                    onSuccess: () => {
                        this.form.accounts = this.data;
                    },
                    onError: handleError,
                });
            },

            confirmAccountDeletion(index) {
                this.deleteAccountIndex = index;
                this.confirmingAccountDeletion = true;
            },

            deleteAccount() {
                const bankAccount = this.form.accounts[this.deleteAccountIndex];

                if (bankAccount.id === null) {
                    this.form.accounts.splice(this.deleteAccountIndex, 1);
                    this.confirmingAccountDeletion = false;

                    if (bankAccount.default) {
                        this.defaultBankAccount = 0;
                    }
                } else {
                    this.form.delete(route('settings.bank_account.delete', bankAccount.id), {
                        onSuccess: () => {
                            this.form.accounts.splice(this.deleteAccountIndex, 1);
                            this.confirmingAccountDeletion = false;

                            if (bankAccount.default) {
                                this.defaultBankAccount = 0;
                            }
                        },
                        onError: handleError,
                    });
                }
            },
        },
    });
</script>
