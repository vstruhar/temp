<template>
    <jet-form-section :with-title="false" @submitted="submitFiles">
        <template #form>
            <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
                <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('Invoice') }}</div>
            </div>

            <div class="col-span-6 mt-5 flex flex-col items-start">
                <jet-label for="inputColor" :value="__('Color')" class="text-lg leading-6 font-medium text-gray-900 mb-2 inline-block"/>
                <div class="flex gap-5 mt-1">
                    <input v-if="can('settings:invoice:edit')" v-model="form.invoice_color" type="color" id="inputColor" class="w-16 h-10"/>
                    <jet-input type="text" v-model="form.invoice_color" class="w-[120px]" readonly/>
                </div>
                <jet-input-error :message="form.errors.invoice_color" class="mt-2"/>
            </div>

            <div class="col-span-6 sm:col-span-3 lg:pr-6 mt-4">
                <jet-label for="inputLogo" :value="__('Company logo')" class="text-lg leading-6 font-medium text-gray-900 mb-2"/>
                <input v-if="can('settings:invoice:edit')" class="mt-1" type="file" id="inputLogo" @input="form.logo = $event.target.files[0]"/>
                <jet-input-error :message="form.errors.logo" class="mt-2"/>

                <div v-if="data && data.logo !== null" class="relative rounded-md overflow-hidden border-4 border-indigo-400 mt-6 inline-block">
                    <jet-danger-button v-if="data && data.logo !== null && can('settings:invoice:delete')" class="px-2 py-2 absolute top-3 right-3" @click="confirmFileDeletion('logo')">
                        <trash-icon class="w-5 h-5"/>
                    </jet-danger-button>

                    <img :src="$inertia.page.props.appUrl+'/storage/'+data.logo+cacheBuster" class="max-w-full">
                </div>
            </div>

            <div class="col-span-6 sm:col-span-3 mt-4">
                <jet-label for="InputSignature" :value="__('Signature')" class="text-lg leading-6 font-medium text-gray-900 mb-2"/>
                <input v-if="can('settings:invoice:edit')" class="mt-1" type="file" id="InputSignature" @input="form.signature = $event.target.files[0]"/>
                <jet-input-error :message="form.errors.signature" class="mt-2"/>

                <div v-if="data && data.signature !== null" class="relative rounded-md overflow-hidden border-4 border-indigo-400 mt-6 inline-block">
                    <jet-danger-button v-if="data && data.signature !== null && can('settings:invoice:delete')" class="px-2 py-2 absolute top-3 right-3" @click="confirmFileDeletion('signature')">
                        <trash-icon class="w-5 h-5"/>
                    </jet-danger-button>

                    <img :src="$inertia.page.props.appUrl+'/storage/'+data.signature+cacheBuster" class="max-w-full">
                </div>
            </div>

            <div v-if="can('settings:invoice:edit')" class="flex col-span-6 sm:col-span-6 bg-gray-100 p-3 my-4">
                <information-circle-icon class="w-6 h-6 mr-3"/>
                <div class="w-full text-center border-l-2 px-3">
                    {{ __('Supported formats are') }} <span class="px-2 py-1 inline-flex leading-5 font-semibold bg-blue-100 text-blue-800 rounded-md">.jpg</span>
                    a <span class="px-2 py-1 inline-flex leading-5 font-semibold bg-blue-100 text-blue-800 rounded-md">.png</span>.
                    {{ __('Maximum image size is 4MB') }}.
                </div>
            </div>
        </template>

        <template #actions>
            <jet-action-message v-if="form.progress" :on="form.progress" class="mr-3">
                {{ form.progress.percentage }}%
            </jet-action-message>

            <jet-action-message :on="form.recentlySuccessful" class="mr-3">
                {{ __('Saved successfully') }}
            </jet-action-message>

            <jet-button v-if="can('settings:invoice:edit')" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="justify-center px-6 h-10">
                {{ __('Save') }}
            </jet-button>
        </template>
    </jet-form-section>

    <jet-delete-confirmation-modal :title="__('Delete')"
                                   :message="__('Are you sure you want to delete this?')"
                                   :confirm-button-text="__('Delete')"
                                   :processing="form.processing"
                                   :show="confirmingFileDeletion"
                                   @confirmed="deleteFile"
                                   @close="confirmingFileDeletion = false"></jet-delete-confirmation-modal>
</template>

<script>
    import {defineComponent} from 'vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetActionMessage from '@/Jetstream/ActionMessage.vue'
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import {InformationCircleIcon, TrashIcon} from '@heroicons/vue/outline'
    import {handleError} from "@/Services/RequestService";

    export default defineComponent({
        name: "InvoiceSettings",

        props: ['data'],

        components: {
            JetInput,
            JetInputError,
            JetActionMessage,
            JetButton,
            JetFormSection,
            JetLabel,
            JetDropdownSelect,
            JetDangerButton,
            JetDeleteConfirmationModal,
            InformationCircleIcon,
            TrashIcon,
        },

        data() {
            return {
                form: this.$inertia.form({
                    invoice_color: this.data.invoice_color,
                    logo: null,
                    signature: null,
                }),
                confirmingFileDeletion: false,
                deleting: false,
                type: '',
                cacheBuster: null,
            }
        },

        mounted() {
            this.updateChangeBusting();
        },

        methods: {
            submitFiles() {
                this.form.post(route('settings.invoice.store'), {
                    onSuccess: () => this.updateChangeBusting(),
                    onError: handleError,
                })
            },

            deleteFile() {
                this.form.delete(route('settings.invoice.delete', this.type), {
                    onSuccess: () => this.confirmingFileDeletion = false,
                    onError: () => alert('Stala sa chyba'),
                });
            },

            confirmFileDeletion(value) {
                this.type = value;
                this.confirmingFileDeletion = true;
            },

            updateChangeBusting() {
                this.cacheBuster = '?v='+Date.now()
            },
        },
    })
</script>
