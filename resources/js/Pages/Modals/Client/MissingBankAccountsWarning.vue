<template>
    <jet-confirmation-modal :show="show" :closeable="!redirecting">
        <template #title>
            {{ __('Missing bank account') }}
        </template>

        <template #content>
            {{ __(`You don't have a bank account, do you want to create one?`) }}
        </template>

        <template #footer>
            <jet-danger-button @click="close" class="w-16 justify-center" :class="{'opacity-25': redirecting}" :disabled="redirecting">
                {{ __('No') }}
            </jet-danger-button>

            <jet-secondary-button class="ml-3 w-24 justify-center" @click="confirm" :class="{'opacity-25': redirecting}" :disabled="redirecting">
                {{ __('Yes') }}
            </jet-secondary-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import {defineComponent} from 'vue'
import Modal from '@/Jetstream/Modal.vue'
import JetConfirmationModal from '@/Jetstream/ConfirmationModal.vue'
import JetDangerButton from '@/Jetstream/DangerButton.vue'
import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'

export default defineComponent({
    emits: ['close'],

    components: {
        Modal,
        JetConfirmationModal,
        JetDangerButton,
        JetSecondaryButton,
    },

    props: {
        show: {
            default: false,
        },
    },

    data() {
        return {
            redirecting: false,
        };
    },

    methods: {
        confirm() {
            this.redirecting = true;

            this.$inertia.get(route('settings.bank_accounts'));
        },

        close() {
            this.$emit('close');
        },
    },
});
</script>
