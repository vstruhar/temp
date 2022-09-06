<template>
    <Link :href="'/'">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="mx-auto w-20 h-20 mb-2 text-white p-2 bg-indigo-500 rounded-full" viewBox="0 0 24 24">
            <light-bulb-icon class="w-10 h-10 text-white"/>
        </svg>
        <div class="w-full text-center text-2xl font-bold mb-4">Ponfys</div>
    </Link>
</template>

<script>
    import {defineComponent} from 'vue'
    import {Link} from '@inertiajs/inertia-vue3';
    import {LightBulbIcon} from '@heroicons/vue/outline'

    export default defineComponent({
        components: {
            Link,
            LightBulbIcon,
        },
    })
</script>
