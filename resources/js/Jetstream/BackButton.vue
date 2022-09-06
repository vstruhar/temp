<template>
    <Link :href="url"
          class="flex h-10 px-5 justify-center items-center bg-gray-300 border border-transparent rounded-md font-bold text-xs text-gray-600 uppercase tracking-widest hover:bg-gray-300/70 active:bg-gray-300/70 focus:outline-none focus:border-gray-200 focus:ring focus:ring-gray-100 disabled:opacity-25 transition"
          :class="{'opacity-25': disabled}"
          :disabled="disabled">
        <chevron-left-icon class="w-4 h-4 mr-2 -ml-2"/> {{ __('Back') }}
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import {Link} from '@inertiajs/inertia-vue3'
    import {ChevronLeftIcon} from '@heroicons/vue/solid'

    export default defineComponent({
        components : {
            Link,
            ChevronLeftIcon,
        },

        props: {
            url: {
                type: String,
            },
            disabled: {
                type: Boolean,
                default: false,
            },
            block: {
                type: Boolean,
                default: false,
            },
        }
    })
</script>
