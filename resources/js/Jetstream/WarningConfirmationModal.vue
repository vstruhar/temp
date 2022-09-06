<template>
    <modal :show="show" :max-width="maxWidth" :closeable="closeable && !processing" @close="close">
        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                    <svg class="h-6 w-6 text-yellow-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>

                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                    <h3 class="text-lg">{{ title }}</h3>

                    <div class="mt-2">{{ message }}</div>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-gray-100 text-right">
            <jet-secondary-button @click="close" :disabled="processing">
                {{ __('Cancel') }}
            </jet-secondary-button>

            <jet-button @click="confirm"
                        color="yellow"
                        type="button"
                        :class="{'opacity-25': processing}"
                        :disabled="processing"
                        class="ml-2 bg-yellow-500 hover:bg-yellow-600 active:bg-yellow-700 focus:border-yellow-700">
                {{ confirmButtonText }}
            </jet-button>
        </div>
    </modal>
</template>

<script>
    import { defineComponent } from 'vue'
    import Modal from './Modal.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

    export default defineComponent({
        emits: ['close', 'confirmed'],

        components: {
            Modal,
            JetButton,
            JetSecondaryButton,
        },

        props: {
            title: String,
            message: String,
            confirmButtonText: {
                type: String,
                default: 'Odstrániť položku',
            },
            processing: {
                default: false,
            },
            show: {
                default: false
            },
            maxWidth: {
                default: '2xl'
            },
            closeable: {
                default: true
            },
        },

        methods: {
            confirm() {
                this.$emit('confirmed');
            },

            close() {
                this.$emit('close');
            },
        },
    })
</script>
