<template>
    <Link :href="href" class="cursor-pointer flex h-9 px-4 items-center bg-gray-800 whitespace-nowrap border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition">
        <plus-icon class="w-4 h-4 mr-2 -ml-1"/>
        <slot/>
    </Link>
</template>

<script>
    import {defineComponent} from 'vue'
    import {Link} from '@inertiajs/inertia-vue3'
    import {PlusIcon} from '@heroicons/vue/solid'

    export default defineComponent({
        name: "CreateLinkButton",

        components: {
            Link,
            PlusIcon,
        },

        props: {
            href: String,
        },
    })
</script>
