<template>
    <div class="relative">
        <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
               :class="{'bg-gray-50 text-gray-600': disabled || readonly}"
               v-bind="$attrs"
               :disabled="disabled || readonly"
               :value="modelValue"
               @input="$emit('update:modelValue', $event.target.value)"
               ref="input"
               :readonly="disabled || readonly">

        <jet-spinner v-if="requestPending" class="absolute top-3 right-4" size="18" width="2"/>

        <div class="absolute top-12 w-full text-xs text-center select-none">
            <div v-if="status === 'valid'" class="bg-green-50 text-green-600 p-2 rounded">{{ __('VAT ID valid for VAT purposes in the EU') }}</div>
            <div v-if="status === 'invalid'" class="bg-red-50 text-red-600 p-2 rounded">{{ __('VAT ID invalid for VAT purposes in the EU') }}</div>
            <div v-if="status === 'failed'" class="bg-yellow-50 text-yellow-600 p-2 rounded">{{ __('Failed to validate VAT ID') }}</div>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue';
import JetSpinner from '@/Jetstream/Spinner';
import {debounce} from "lodash";

export default defineComponent({
    inheritAttrs: false,

    components: {
        JetSpinner,
    },

    props: {
        modelValue: null,
        readonly: {
            default: false,
            type: Boolean,
        },
        disabled: {
            default: false,
            type: Boolean,
        },
    },

    emits: ['update:modelValue'],

    data() {
        return {
            status: null,
            requestPending: false,
        };
    },

    watch: {
        modelValue: debounce(function () {
            this.validate();
        }, 600),
    },

    mounted() {
        this.validate();
    },

    methods: {
        validate() {
            if (!this.modelValue || this.modelValue.length < 6) return;

            this.requestPending = true;
            this.status = null;

            axios.get(route('company.validate.vat'), {params: {vat: this.modelValue}})
                .then(response => this.status = response.data.status)
                .catch(error => (error.response && error.response.status === 422) && (this.status = 'invalid'))
                .finally(() => this.requestPending = false);
        },

        focus() {
            this.$refs.input.focus()
        },
    },
})
</script>
