<template>
    <textarea class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm w-full"
              :class="{'bg-gray-50 text-gray-600': disabled}"
              :disabled="disabled"
              :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              ref="input"
              :rows="rows"></textarea>
</template>

<script>
    import {defineComponent} from 'vue'

    export default defineComponent({
        props: {
            modelValue: String,
            rows: Number,
            disabled: {
                default: false,
                type: Boolean,
            },
        },

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            },
        },
    })
</script>
