<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Link,
        },
        props: ['href', 'active'],

        computed: {
            classes() {
                return this.active
                    ? 'inline-flex items-center px-3 lg:px-5 pt-1 -mb-1 tracking-wide bg-indigo-900 text-sm font-bold leading-5 text-white focus:outline-none transition shadow-md rounded-b-lg'
                    : 'inline-flex items-center px-3 lg:px-5 pt-1 tracking-wide text-sm font-bold leading-5 text-gray-200 hover:text-white focus:outline-none focus:text-white transition'
            }
        }
    })
</script>
