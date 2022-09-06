<template>
    <div>
        <button type="submit" :class="`block w-full text-sm leading-5 text-${textColor}-700 text-left hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition ${classes}`" v-if="as == 'button'">
            <slot></slot>
        </button>

        <a :href="href" :class="`block text-sm leading-5 text-${textColor}-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition ${classes}`" v-else-if="as =='a'">
            <slot></slot>
        </a>

        <Link :href="href" :class="`block text-sm leading-5 text-${textColor}-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition ${classes}`" v-else>
            <slot></slot>
        </Link>
    </div>
</template>

<script>
    import { defineComponent } from 'vue';
    import { Link } from '@inertiajs/inertia-vue3';

    export default defineComponent({
        components: {
            Link,
        },

        props: {
            href: String,
            as: String,
            textColor: {
                default: 'gray',
                type: String,
            },
            classes: {
                default: 'px-4 py-2',
            },
        },
    })
</script>
