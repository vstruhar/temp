<template>
    <app-layout :title="__('Settings')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Settings') }}
            </h2>
        </template>

        <div class="lg:grid lg:grid-cols-7 lg:gap-6">
            <div class="lg:col-span-2">
                <div class="px-4 py-5 sm:p-6 bg-white shadow rounded-lg">
                    <Aside/>
                </div>
            </div>

            <div class="mt-5 lg:mt-0 lg:col-span-5">
                <component :is="section" :data="data"/>
            </div>
        </div>
    </app-layout>
</template>

<script>
import {defineComponent} from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Aside from '@/Pages/Settings/Aside.vue'
import ContactAndInvoice from '@/Pages/Settings/Partials/ContactAndInvoice.vue'
import BankAccounts from '@/Pages/Settings/Partials/BankAccounts.vue'
import Invoice from '@/Pages/Settings/Partials/Invoice.vue'
import DocumentNumbers from '@/Pages/Settings/Partials/DocumentNumbers.vue'
import CompanyApiKey from '@/Pages/Settings/Partials/CompanyApiKey.vue'
import DefaultSettings from '@/Pages/Settings/Partials/DefaultSettings.vue'

export default defineComponent({
    components: {
        AppLayout,
        Aside,
        ContactAndInvoice,
        BankAccounts,
        Invoice,
        DocumentNumbers,
        CompanyApiKey,
        DefaultSettings,
    },

    props: ['section', 'data'],
})
</script>
