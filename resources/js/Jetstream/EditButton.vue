<template>
    <button class="flex px-2 h-8 justify-center items-center bg-indigo-600 border border-transparent rounded-md text-sm text-white tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-25 transition"
            :class="{ 'opacity-25': disabled }"
            :disabled="disabled">
        <pencil-alt-icon class="w-3 h-3 mr-2"/> {{ __('Edit') }}
    </button>
</template>

<script>
    import {defineComponent} from 'vue'
    import {PencilAltIcon} from '@heroicons/vue/solid'

    export default defineComponent({
        components: {PencilAltIcon},

        props: {
            disabled: {
                type: Boolean,
                default: false,
            },
            block: {
                type: Boolean,
                default: false,
            },
        },
    })
</script>
