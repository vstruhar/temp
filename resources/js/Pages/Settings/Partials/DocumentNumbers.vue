<template>
    <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-tl-md rounded-lg">
        <div class="col-span-6 bg-gray-50 -mx-4 -my-5 sm:-mx-6 sm:-my-6 p-5 flex justify-between rounded-t-lg">
            <div class="font-semibold text-2xl text-gray-800 mt-1">{{ __('Document numbers') }}</div>

            <jet-button v-if="can('settings:document_numbers:create')" type="button" class="cursor-pointer flex px-4 items-center bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition" @click="createDocumentNumberModal = !createDocumentNumberModal">
                <plus-icon class="h-3 w-3 mr-2 -ml-1"/>
                {{ __('Add') }}
            </jet-button>
        </div>

        <div class="mt-5 p-6 lg:p-0 -mx-6 border-t">
            <jet-table :pagination="data" class="rounded-none border-none">
                <template #thead>
                    <jet-header-cell class="bg-gray-100">{{ __('Title') }}</jet-header-cell>
                    <jet-header-cell class="bg-gray-100">{{ __('Document type') }}</jet-header-cell>
                    <jet-header-cell class="bg-gray-100">{{ __('Format') }}</jet-header-cell>
                    <jet-header-cell class="bg-gray-100">{{ __('Period') }}</jet-header-cell>
                    <jet-header-cell class="bg-gray-100" v-if="can('settings:document_numbers:edit')">{{ __('Default') }}</jet-header-cell>
                    <th scope="col" v-if="can('settings:document_numbers:edit')" class="bg-gray-100"></th>
                </template>

                <template #tbody>
                    <jet-row v-for="item in data" :key="`table-row-${item.id}`" :pointer="false" class="border border-gray-200">
                        <jet-data-cell :name="__('Title')" slot-class="whitespace-nowrap font-bold" :data-id="item.id">{{ item.name }}</jet-data-cell>
                        <jet-data-cell :name="__('Document type')" slot-class="whitespace-nowrap">{{ getLabel('type', item.type) }}</jet-data-cell>
                        <jet-data-cell :name="__('Format')" slot-class="whitespace-nowrap">{{ item.format }}</jet-data-cell>
                        <jet-data-cell :name="__('Period')" slot-class="whitespace-nowrap">{{ getLabel('period', item.period) }}</jet-data-cell>
                        <jet-data-cell :name="__('Default')" slot-class="whitespace-nowrap" v-if="can('settings:document_numbers:edit')">
                            <check-circle-icon v-if="item.is_default" class="w-8 h-8 text-green-400"/>
                            <minus-circle-icon v-else class="w-8 h-8 text-gray-200"/>
                        </jet-data-cell>
                        <jet-action-cell class="self-end" v-if="can('settings:document_numbers:edit')">
                            <div class="flex justify-end px-2 py-3 whitespace-nowrap">
                                <jet-edit-button v-if="can('settings:document_numbers:edit')" @click="edit(item)"/>
                            </div>
                        </jet-action-cell>
                    </jet-row>

                    <tr v-if="data.length === 0">
                        <td class="py-10 whitespace-nowrap text-lg text-gray-400 text-center" colspan="6">{{ __('No items were found') }}</td>
                    </tr>
                </template>
            </jet-table>
        </div>

        <CreateDocumentNumberModal :show="createDocumentNumberModal" @close="createDocumentNumberModal = false"/>
        <EditDocumentNumberModal :show="editDocumentNumberModal" @close="editDocumentNumberModal = false" :item="editItem"/>
    </div>
</template>

<script>
    import CreateDocumentNumberModal from './Modals/CreateDocumentNumberModal.vue'
    import EditDocumentNumberModal from './Modals/EditDocumentNumberModal.vue'
    import JetActionCell from "@/Jetstream/Table/ActionCell";
    import JetButton from '@/Jetstream/Button.vue'
    import JetDataCell from "@/Jetstream/Table/DataCell";
    import JetDropdownSelect from '@/Jetstream/DropdownSelect.vue'
    import JetEditButton from '@/Jetstream/EditButton.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetHeaderCell from "@/Jetstream/Table/HeaderCell";
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetModal from '@/Jetstream/Modal.vue'
    import JetRow from "@/Jetstream/Table/Row";
    import JetTable from '@/Jetstream/Table.vue'
    import {PlusIcon, CheckCircleIcon, MinusCircleIcon} from '@heroicons/vue/solid'
    import {defineComponent} from 'vue'
    import {getLabel} from '@/Data/DropdownOptions/DocumentNumber';

    export default defineComponent({
        name: 'DocumentNumbers',

        props: ['data'],

        components: {
            CreateDocumentNumberModal,
            EditDocumentNumberModal,
            JetActionCell,
            JetButton,
            JetDataCell,
            JetDropdownSelect,
            JetEditButton,
            JetFormSection,
            JetHeaderCell,
            JetInput,
            JetInputError,
            JetLabel,
            JetModal,
            JetRow,
            JetTable,
            CheckCircleIcon,
            MinusCircleIcon,
            PlusIcon,
        },

        data() {
            return {
                createDocumentNumberModal: false,
                editDocumentNumberModal: false,
                editItem: {},
            }
        },

        methods: {
            edit(item) {
                this.editItem = item;
                this.editDocumentNumberModal = true;
            },

            getLabel,
        },
    })
</script>
