<template>
    <modal :show="show" max-width="sm" :closeable="true" @close="show = false">
        <div class="max-w-7xl mx-auto">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <jet-form-section @submitted="duplicate" :with-title="false">
                    <template #form>
                        <div class="col-span-6 bg-gray-50 -m-6 mb-0 p-5 flex justify-between rounded-t-lg">
                            <div class="font-semibold text-2xl text-gray-800">{{ __('Duplicate') }}</div>
                        </div>

                        <div class="col-span-6 mb-1">
                            <div class="my-2">
                                <div class="mb-6">
                                    <jet-label :value="__('Company')" required/>
                                    <company-dropdown :company-id="form.company_id" @changed="form.company_id = $event" :max-height="130"/>
                                    <jet-input-error :message="form.errors.company_id" class="mt-2"/>
                                </div>

                                <label class="flex items-center">
                                    <jet-checkbox :value="true" v-model:checked="form.copy_client"/>
                                    <span class="ml-2 text-sm text-gray-600">{{ __('Copy buyer') }}</span>
                                </label>

                                <div v-if="!form.copy_client" class="mt-5 mb-3">
                                    <jet-label :value="__('Buyer')" required/>
                                    <jet-dropdown-select-filter ref="clientDropdown"
                                                                class="mt-1 w-full"
                                                                :options="clientsDropdownItems"
                                                                v-model:value="form.client_id"
                                                                :allow-empty="false"
                                                                :max-height="90"/>

                                    <jet-input-error :message="form.errors.client_id" v-if="form.client_id === null" class="mt-2"/>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template #actions>
                        <jet-secondary-button @click="show = false" :disabled="form.processing" class="justify-center w-24 h-10 mr-4">
                            {{ __('Cancel') }}
                        </jet-secondary-button>

                        <jet-button :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                            {{ __('Duplicate') }}
                        </jet-button>
                    </template>
                </jet-form-section>
            </div>
        </div>
    </modal>
</template>

<script>
import CompanyDropdown from "@/Pages/Documents/Shared/CompanyDropdown";
import EventBus from "@/Services/EventBus";
import JetButton from '@/Jetstream/Button.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetDropdownSelect from "@/Jetstream/DropdownSelect";
import JetDropdownSelectFilter from "@/Jetstream/DropdownSelectFilter";
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import Modal from '@/Jetstream/Modal.vue'
import axios from 'axios';
import {defineComponent} from 'vue'
import {handleError} from "@/Services/RequestService";

export default defineComponent({
    name: 'DuplicateDocumentModal',

    emits: ['close'],

    components: {
        CompanyDropdown,
        JetButton,
        JetCheckbox,
        JetDropdownSelect,
        JetDropdownSelectFilter,
        JetFormSection,
        JetInput,
        JetInputError,
        JetLabel,
        JetSecondaryButton,
        Modal,
    },

    props: {
        type: {
            type: String,
        },
    },

    data() {
        return {
            show: false,
            clientsDropdownItems: [],
            form: this.$inertia.form({
                company_id: this.$page.props.user.current_team_id,
                client_id: null,
                document_id: null,
                copy_client: true,
            }),
        };
    },

    watch: {
        show(value) {
            if (value) {
                this.getClientDropdownItems();
            }
        },

        'form.company_id'() {
            this.getClientDropdownItems();
        },
    },

    mounted() {
        EventBus.on('document:duplicate:open', (document) => {
            this.form.document_id = document.id;
            this.form.client_id   = document.client_id;
            this.form.copy_client = true;
            this.show = true;
        });

        EventBus.on('document:duplicate:close', () => this.show = false);
    },

    beforeUnmount() {
        EventBus.off('document:duplicate:open');
        EventBus.off('document:duplicate:close');
    },

    methods: {
        getClientDropdownItems() {
            axios.get(route('company.clients.dropdown_items', this.form.company_id))
                .then(({data}) => {
                    this.clientsDropdownItems = data.items;

                    if (!this.form.copy_client) {
                        this.$refs.clientDropdown.selection = this.clientsDropdownItems.length ? this.clientsDropdownItems[0] : null;
                    }
                })
                .catch(error => handleError(error));
        },

        duplicate() {
            this.form.post(route('documents.duplicate', [this.type, this.form.document_id]), {
                onSuccess: () => {
                    this.show = false;
                },
                onError: handleError,
            });
        },
    },
})
</script>
