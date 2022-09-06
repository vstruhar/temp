<template>
    <app-layout :title="__('Dashboard')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Dashboard') }}
            </h2>
        </template>

        <select-company :companies="companies" :with-title="false" :with-container="false" bg-color-class="bg-gray-100"/>
    </app-layout>
</template>

<script>
    import {defineComponent} from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SelectCompany from '@/Pages/Companies/SelectCompany.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SelectCompany,
        },

        props: ['companies'],
    })
</script>
