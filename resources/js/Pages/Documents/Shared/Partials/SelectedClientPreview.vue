<template>
    <dl>
        <div class="odd:bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="client.name && show('name')">
            <dt class="text-sm font-medium text-gray-500">{{ __('Name') }}</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ client.name }}</dd>
        </div>
        <div class="odd:bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="show('location')">
            <dt class="text-sm font-medium text-gray-500">{{ __('Address') }}</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ client.address +', '+ client.city +', '+ client.country }}</dd>
        </div>
        <div class="odd:bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="client.organization_id  && show('organization_id')">
            <dt class="text-sm font-medium text-gray-500">{{ __('Business ID') }}</dt> <!-- IČO -->
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ client.organization_id }}</dd>
        </div>
        <div class="odd:bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="client.tax  && show('tax')">
            <dt class="text-sm font-medium text-gray-500">{{ __('Tax ID') }}</dt>
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ client.tax }}</dd>
        </div>
        <div class="odd:bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6" v-if="client.value_added_tax  && show('value_added_tax')">
            <dt class="text-sm font-medium text-gray-500">{{ __('VAT') }}</dt> <!-- IČ DPH -->
            <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ client.value_added_tax }}</dd>
        </div>
    </dl>
</template>

<script>
    export default {
        name: "SelectedClientPreview",

        props: {
            client: Object,
            except: {
                default: [],
                type: Array,
            },
        },

        methods: {
            show(property) {
                return !this.except.includes(property);
            },
        },
    }
</script>
