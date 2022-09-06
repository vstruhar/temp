<template>
    <JetModal :show="show" :max-width="maxWidth" :closeable="closeable" @close="close">
        <jet-form-section @submitted="create" :with-title="false">
            <template #form>
                <div class="col-span-6 bg-gray-50 -m-6 mb-1 p-5 flex justify-between rounded-t-lg">
                    <div class="font-semibold text-2xl text-gray-800">{{ __('Add invoice number') }}</div>
                    <x-icon class="w-6 h-6 cursor-pointer opacity-50 hover:opacity-75" @click="close"/>
                </div>

                <div class="col-span-3 sm:col-span-3">
                    <jet-label for="input-document-number-name" :value="__('Title')" required/>
                    <jet-input id="input-document-number-name" type="text" class="mt-1 block w-full" v-model="form.name"/>
                    <jet-input-error :message="form.errors.name" class="mt-2"/>
                </div>

                <div class="col-span-3 sm:col-span-3">
                    <jet-label :value="__('Document type')"/>
                    <jet-dropdown-select class="mt-1 block w-full" :options="type" v-model:value="form.type"/>
                    <jet-input-error :message="form.errors.type" class="mt-2"/>
                </div>

                <div class="col-span-3 sm:col-span-3">
                    <jet-label :value="__('Period')" required/>
                    <jet-dropdown-select class="mt-1 block w-full" :options="period" v-model:value="form.period"/>
                    <jet-input-error :message="form.errors.period" class="mt-2"/>
                </div>

                <div class="col-span-3 sm:col-span-3">
                    <jet-label :value="__('Format')" required/>
                    <jet-dropdown-select class="mt-1 block w-full" :options="format" v-model:value="form.format"/>
                    <jet-input-error :message="form.errors.format" class="mt-2"/>
                </div>

                <template v-if="form.format == 'custom'">
                    <div class="col-span-3 sm:col-span-3">
                        <jet-label for="input-document-number-format" :value="__('Custom format')" required/>
                        <jet-input id="input-document-number-format" type="text" class="mt-1 block w-full uppercase" v-model="custom_format"/>
                        <jet-input-error :message="form.errors.custom_format" class="mt-2"/>
                    </div>

                    <div class="col-span-6 sm:col-span-6">
                        <div class="font-semibold text-gray-800 px-2 py-3">{{ __('These characters will be replaced with real values') }}:</div>
                        <dl>
                            <div class="bg-gray-50 px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-900">RRRR</dt>
                                <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">{{ __('Year with 4 numbers') }}</dd>
                            </div>
                            <div class="bg-white px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-900">RR</dt>
                                <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">{{ __('Year with 2 numbers') }}</dd>
                            </div>
                            <div class="bg-gray-50 px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-900">MM</dt>
                                <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">{{ __('Month') }}</dd>
                            </div>
                            <div class="bg-white px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-900">DD</dt>
                                <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">{{ __('Day') }}</dd>
                            </div>
                            <div class="bg-gray-50 px-2 py-3 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
                                <dt class="text-sm font-medium text-gray-900">C</dt>
                                <dd class="mt-1 text-sm text-gray-500 sm:mt-0 sm:col-span-2">{{ __('Documents number') }}</dd>
                            </div>
                        </dl>
                        <div class="font-semibold text-sm text-gray-800 px-2 pt-4">
                            {{ __("If you don't want a character to be replaced with a value use # symbol before it") }}
                        </div>
                    </div>
                </template>

                <div class="col-span-6 sm:col-span-6 flex justify-between">
                    <label class="flex items-center">
                        <jet-checkbox v-model:checked="form.is_default" :disabled="itemsCount === 0 && form.type"/>
                        <span class="ml-2 text-sm text-gray-600" :class="{'opacity-50': itemsCount === 0 && form.type}">{{ __('Default') }}</span>
                    </label>

                    <div>
                        <jet-secondary-button @click="close" class="h-10">{{ __('Cancel') }}</jet-secondary-button>

                        <jet-button :class="{ 'opacity-25': form.processing }" :disabled="form.processing" class="justify-center px-6 h-10 ml-4">
                            {{ __('Save') }}
                        </jet-button>
                    </div>
                </div>
            </template>
        </jet-form-section>
    </JetModal>
</template>

<script>
import {defineComponent} from 'vue'
import JetButton from '@/Jetstream/Button.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
import JetModal from '@/Jetstream/Modal.vue'
import JetFormSection from '@/Jetstream/FormSection.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
import JetInputError from '@/Jetstream/InputError.vue'
import {PlusIcon, XIcon} from '@heroicons/vue/solid'
import {type, period, format, getLabel} from '@/Data/DropdownOptions/DocumentNumber';
import {handleError} from "@/Services/RequestService";

export default defineComponent({
    name: "CreateDocumentNumberModal",

    props: {
        show: {
            default: false,
        },
        maxWidth: {
            default: 'lg',
        },
        closeable: {
            default: true,
        },
    },

    components: {
        JetButton,
        JetSecondaryButton,
        JetModal,
        JetFormSection,
        JetInput,
        JetLabel,
        JetCheckbox,
        JetDropdownSelect,
        JetInputError,
        PlusIcon,
        XIcon,
    },

    data() {
        return {
            form: this.$inertia.form({
                _method: 'POST',
                name: null,
                type: null,
                period: null,
                format: null,
                current_counter: 0,
                is_default: false,
            }),
            custom_format: null,

            type,
            period,
            format,
        }
    },

    computed: {
        itemsCount() {
            return this.$page.props.data.filter(i => i.type === this.form.type).length;
        },
    },

    watch: {
        'form.type'() {
            this.form.is_default = this.itemsCount === 0 && this.form.type !== null;
        },
    },

    methods: {
        create() {
            this.form.format == 'custom' ? this.form.format = this.custom_format : '';

            this.form.post(route('settings.document_number.store'), {
                preserveScroll: false,
                onSuccess: () => {
                    this.form.reset();
                    this.custom_format = null;
                    this.close();
                },
                onError: handleError,
            });
        },

        getLabel,

        close() {
            this.form.reset();
            this.$emit('close');
        },
    },
})
</script>
