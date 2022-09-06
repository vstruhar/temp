<template>
    <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-1 focus:outline-none divide-y">
        <li v-if="can('settings:contact_and_invoice:show')">
            <Link :href="route('settings.contact_and_invoice')"
                  class="flex items-center flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.contact_and_invoice')}">
                <UserIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('Contact and invoice data') }}
            </Link>
        </li>

        <li v-if="can('settings:bank_accounts:show')">
            <Link :href="route('settings.bank_accounts')"
                  class="flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.bank_accounts')}">
                <CreditCardIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('Bank accounts') }}
            </Link>
        </li>

        <li v-if="$page.props.user.owns_current_company">
            <Link :href="route('settings.invoice')"
                  class="flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.invoice')}">
                <DocumentTextIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('Invoice') }}
            </Link>
        </li>

        <li v-if="can('settings:document_numbers:show')">
            <Link :href="route('settings.document_number')"
                  class="flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.document_number')}">
                <ColorSwatchIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('Document numbers') }}
            </Link>
        </li>

        <li>
            <Link :href="route('settings.default_settings')"
                  class="flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.default_settings')}">
                <AdjustmentsIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('Default settings') }}
            </Link>
        </li>

        <li v-if="$page.props.role.name === 'Owner' && can('settings:company_api_key:show')">
            <Link :href="route('settings.company_api_key')"
                  class="flex items-center text-gray-700 block px-4 py-4 text-sm hover:bg-gray-100 hover:text-gray-900"
                  :class="{'bg-gray-100 font-bold border-t-0 border-indigo-400 border-l-4': route().current('settings.company_api_key')}">
                <KeyIcon class="w-4 h-4 inline flex-shrink-0 -mt-1 mr-3 opacity-50"/>
                {{ __('API key') }}
            </Link>
        </li>
    </ul>
</template>

<script>
import {defineComponent} from 'vue'
import {Link} from '@inertiajs/inertia-vue3'
import {CreditCardIcon, UserIcon, DocumentTextIcon, ColorSwatchIcon, KeyIcon, AdjustmentsIcon} from '@heroicons/vue/solid'

export default defineComponent({
    name: "Aside",

    components: {
        Link,
        AdjustmentsIcon,
        CreditCardIcon,
        UserIcon,
        DocumentTextIcon,
        ColorSwatchIcon,
        KeyIcon,
    },
})
</script>
