<template>
    <jet-confirmation-modal :show="show" @close="$emit('close')">
        <template #title>{{ __('Reset API key') }}?</template>

        <template #content>{{ __('After resetting, your current API key will stop working. Are you sure?') }}</template>

        <template #footer>
            <jet-secondary-button @click="$emit('close')">{{ __('Cancel') }}</jet-secondary-button>
            <jet-danger-button class="ml-2" @click="$emit('confirmed');$emit('close')">{{ __('Reset API key') }}</jet-danger-button>
        </template>
    </jet-confirmation-modal>
</template>

<script>
import JetConfirmationModal from "@/Jetstream/ConfirmationModal";
import JetSecondaryButton from "@/Jetstream/SecondaryButton";
import JetDangerButton from "@/Jetstream/DangerButton";
import {defineComponent} from "vue";

export default defineComponent({
    name: "ResetCompanyApiKeyModal",

    emits: ['confirmed', 'close'],

    components: {
        JetDangerButton,
        JetSecondaryButton,
        JetConfirmationModal,
    },

    props: {
        show: {
            default: false,
            type: Boolean,
        },
    },
})
</script>
