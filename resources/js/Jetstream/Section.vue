<template>
    <div class="mt-5 md:mt-0">
        <div class="bg-white shadow rounded-lg" :class="noPadding ? null : classes">
            <slot></slot>
        </div>
    </div>
</template>

<script>
import {defineComponent} from 'vue'

export default defineComponent({
    props: {
        classes: {
            type: String,
            default: 'px-4 py-5 sm:px-6 sm:py-5',
        },
        noPadding: {
            type: Boolean,
            default: false,
        },
    },
})
</script>
