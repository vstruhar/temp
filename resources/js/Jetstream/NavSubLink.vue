<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {Link},

        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                    ? 'inline-flex items-center px-6 lg:px-8 tracking-wider text-xs font-bold leading-5 text-white bg-indigo-800/75 focus:outline-none shadow-md transition'
                    : 'inline-flex items-center px-6 lg:px-8 tracking-wide text-xs font-bold leading-5 text-white opacity-60 hover:opacity-100 focus:outline-none focus:text-white transition'
            },
        },
    })
</script>
