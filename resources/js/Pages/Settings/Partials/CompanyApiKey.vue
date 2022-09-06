<template>
    <jet-form-section :with-title="false">
        <template #form>
            <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
                <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('API key') }}</div>
            </div>

            <p class="col-span-6 mt-5">{{ __('Using this API key you can connect to your company on ponfys24 and generate invoices.') }}</p>

            <div class="col-span-6 lg:col-span-4">
                <jet-label for="apiUrl" :value="__('API url')"/>
                <jet-input id="apiUrl" type="text" class="mt-1 block w-full" v-model="apiUrl" :disabled="true"/>
            </div>

            <div class="col-span-6 lg:col-span-4 mb-5">
                <jet-label for="apiKey" :value="__('API key')"/>

                <div class="relative">
                    <jet-input id="apiKey" type="text" class="mt-1 block w-full pr-10" v-model="data.key" :disabled="true"/>

                    <div v-if="!copiedApiKey"
                         v-clipboard:copy="data.key"
                         v-clipboard:success="clipboardCopySuccess"
                         v-clipboard:error="clipboardCopyFailed">
                        <ClipboardCopyIcon class="w-5 h-5 absolute top-[11px] right-3 cursor-pointer transition-colors text-gray-400 hover:text-gray-600"/>
                    </div>

                    <CheckIcon v-else class="w-5 h-5 absolute top-[11px] right-3 text-green-600"/>
                </div>
            </div>
        </template>

        <template #actions>
            <jet-button @click="showConfirm = true" :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center px-6 h-10">
                {{ __('Reset API key') }}
            </jet-button>
        </template>
    </jet-form-section>

    <reset-company-api-key-modal :show="showConfirm"
                                 @close="showConfirm = false"
                                 @confirmed="resetCompanyApiKey"/>
</template>

<script>
import {defineComponent} from 'vue'
import JetButton from '@/Jetstream/Button.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import {InformationCircleIcon, ClipboardCopyIcon, CheckIcon} from '@heroicons/vue/outline'
import {handleError} from "@/Services/RequestService";
import ResetCompanyApiKeyModal from "@/Pages/Settings/Partials/Modals/ResetCompanyApiKeyModal";

export default defineComponent({
    name: "ContactAndInvoice",

    props: ['data'],

    components: {
        ResetCompanyApiKeyModal,
        JetInput,
        JetButton,
        JetFormSection,
        JetLabel,
        InformationCircleIcon,
        ClipboardCopyIcon,
        CheckIcon,
    },

    data() {
        return {
            showConfirm: false,
            copiedApiKey: false,
            apiUrl: 'https://api.ponfys24.sk',
            form: this.$inertia.form(),
        };
    },

    methods: {
        resetCompanyApiKey() {
            this.form.put(route('settings.company_api_key.reset'), {
                onError: handleError,
            });
        },

        clipboardCopySuccess() {
            this.copiedApiKey = true;
            setTimeout(() => this.copiedApiKey = false, 1500);
        },

        clipboardCopyFailed() {
            alert('Failed to copy API key');
        },
    },
})
</script>
