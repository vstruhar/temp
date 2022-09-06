<template>
    <div class="relative" :class="containerClass">
        <input class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
               :class="{'bg-gray-50 text-gray-600': disabled || readonly}"
               v-bind="$attrs"
               :disabled="disabled || readonly"
               :value="modelValue"
               @input="$emit('update:modelValue', $event.target.value)"
               ref="input"
               :readonly="disabled || readonly">

        <jet-spinner v-if="loading" class="absolute top-3 right-4" size="18" width="2"/>
    </div>
</template>

<script>
import {defineComponent} from 'vue';
import JetSpinner from '@/Jetstream/Spinner';

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
        loading: {
            default: false,
            type: Boolean,
        },
        containerClass: {
            default: null,
            type: String,
        },
    },

    emits: ['update:modelValue'],

    mounted() {
        //this.$refs.input.focus()
    },

    methods: {
        focus() {
            this.$refs.input.focus()
        },
    },
})
</script>
