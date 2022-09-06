<template>
    <app-layout :title="__('Clients')">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Clients') }}
            </h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-0">
            <div class="flex justify-between mb-6">
                <jet-filter-table :url="clients.path" :placeholder="__('Find clients')"/>
                <jet-create-link-button v-if="can('client:create')" :href="route('clients.create')">{{ __('Add client') }}</jet-create-link-button>
            </div>

            <jet-table :pagination="clients" ordered-by="name" direction="asc">
                <template #thead>
                    <jet-header-cell sortable name="name">{{ __('Name') }}</jet-header-cell>
                    <jet-header-cell sortable name="organization_id">{{ __('Business ID') }}</jet-header-cell>
                    <jet-header-cell sortable name="address">{{ __('Address') }}</jet-header-cell>
                    <th scope="col" class="no-stretch"></th>
                </template>

                <template #tbody>
                    <jet-row v-for="client in clients.data" :key="`table-row-${client.id}`" class="cursor-auto">
                        <jet-data-cell :name="__('Name')" class="text-gray-600 font-semibold" events-none>{{ client.name }}</jet-data-cell>
                        <jet-data-cell :name="__('Business ID')" class="whitespace-nowrap" events-none>{{ client.organization_id }}</jet-data-cell>
                        <jet-data-cell :name="__('Address')" class="col-span-2 self-stretch" events-none>{{ client.address +', '+ client.city }}</jet-data-cell>
                        <jet-action-cell class="self-end">
                            <div class="flex justify-end px-2 py-3 whitespace-nowrap">
                                <jet-edit-button v-if="can('client:edit')" :url="route('clients.edit', client.id)"/>
                                <jet-open-button v-else-if="can('client:show')" :href="route('clients.show', client.id)"/>

                                <jet-danger-button v-if="can('client:delete')" class="h-9 w-9 px-1 py-1 ml-2" @click="deleteClientId = client.id">
                                    <trash-icon class="h-4 w-4"/>
                                </jet-danger-button>
                            </div>
                        </jet-action-cell>
                    </jet-row>

                    <tr v-if="clients.total == 0">
                        <td class="px-6 py-12 whitespace-nowrap text-lg text-gray-400 text-center" colspan="4">{{ __('No items were found') }}</td>
                    </tr>
                </template>
            </jet-table>
        </div>

        <jet-delete-confirmation-modal :title="__('Delete client')"
                                       :message="__('This action will delete your client. Are you sure?')"
                                       :confirm-button-text="__('Delete client')"
                                       :processing="form.processing"
                                       :show="deleteClientId !== null"
                                       @confirmed="deleteItem"
                                       @close="deleteClientId = null"/>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import JetActionCell from "@/Jetstream/Table/ActionCell";
    import JetCreateLinkButton from '@/Jetstream/CreateLinkButton.vue'
    import JetDangerButton from '@/Jetstream/DangerButton.vue'
    import JetDataCell from "@/Jetstream/Table/DataCell";
    import JetDeleteConfirmationModal from '@/Jetstream/DeleteConfirmationModal.vue'
    import JetEditButton from '@/Jetstream/EditButtonLink.vue'
    import JetFilterTable from '@/Jetstream/FilterTable.vue'
    import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
    import JetOpenButton from '@/Jetstream/OpenButtonLink.vue'
    import JetRow from "@/Jetstream/Table/Row";
    import JetTable from '@/Jetstream/Table.vue'
    import {Link} from '@inertiajs/inertia-vue3'
    import {TrashIcon} from '@heroicons/vue/outline'
    import {defineComponent} from 'vue'
    import {handleError} from '@/Services/RequestService'

    export default defineComponent({
        components: {
            AppLayout,
            JetActionCell,
            JetCreateLinkButton,
            JetDangerButton,
            JetDataCell,
            JetDeleteConfirmationModal,
            JetEditButton,
            JetFilterTable,
            JetHeaderCell,
            JetOpenButton,
            JetRow,
            JetTable,
            Link,
            TrashIcon,
        },

        props: ['clients'],

        data() {
            return {
                form: this.$inertia.form(),
                deleteClientId: null,
            };
        },

        methods: {
            deleteItem() {
                this.form.delete(route('clients.delete', this.deleteClientId), {
                    onSuccess: () => this.deleteClientId = null,
                    onError: handleError,
                });
            },
        },
    });
</script>
