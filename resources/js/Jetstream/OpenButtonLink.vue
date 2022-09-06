<template>
    <Link :href="url"
          class="flex px-2 h-9 justify-center items-center bg-indigo-600 border border-transparent rounded-md text-sm text-white tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-300 disabled:opacity-25 transition"
          :class="{ 'opacity-25': disabled }"
          :disabled="disabled">
        <folder-open-icon class="h-3 h-3 mr-2"/>
        {{ __('Open') }}
    </Link>
</template>

<script>
    import {defineComponent} from 'vue'
    import {Link} from '@inertiajs/inertia-vue3'
    import {FolderOpenIcon} from '@heroicons/vue/solid'

    export default defineComponent({
        components: {
            Link,
            FolderOpenIcon,
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
