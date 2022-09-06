<template>
    <jet-dropdown :align="openDirection === 'up' ? 'top-right' : 'right'" width="44">
        <template #trigger>
            <span class="inline-flex rounded-md">
                <button type="button" class="h-9 inline-flex items-center px-3 py-2 text-sm border border-gray-300 lg:border-gray-200 shadow-sm rounded-md text-gray-500 bg-gray-200 lg:bg-gray-100 hover:text-gray-700 focus:outline-none transition">
                    <cog-icon class="w-5 h-5 -ml-1"/>
                    <chevron-down-icon class="w-4 h-4 -mr-1"/>
                </button>
            </span>
        </template>

        <template #content class="text-left">
            <div class="block px-4 py-2 text-xs text-gray-400">{{ __('Options') }}</div>

            <template v-if="can('documents:invoice:print') && !without.includes('print')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link as="button" @click="printPdf(invoice)">
                    <printer-icon class="inline-block -mt-1 text-gray-500 w-4 h-4 mr-2"/>
                    <span class="font-normal">{{ __('Print') }}</span>
                </jet-dropdown-link>
            </template>

            <template v-if="can('documents:invoice:download') && !without.includes('download')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link as="button" @click="downloadPdf(invoice)">
                    <document-download-icon class="inline-block -mt-1 text-gray-500 w-4 h-4 mr-2"/>
                    <span class="font-normal">{{ __('Download') }}</span>
                </jet-dropdown-link>
            </template>

            <template v-if="can('documents:invoice:send') && !without.includes('send')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link :href="route('documents.send_form', [type, invoice.id])">
                    <mail-icon class="inline-block -mt-1 text-gray-500 w-4 h-4 mr-2"/>
                    <span class="font-normal">{{ __('Send') }}</span>
                </jet-dropdown-link>
            </template>

            <template v-if="can('documents:credit_note:create') && invoice.credit_note_id === null && !without.includes('credit_note.create')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link :href="route('documents.issue.credit_note.create', invoice.id)">
                    <archive-icon class="inline-block -mt-1 text-gray-500 w-4 h-4 mr-2"/>
                    <span class="font-normal">{{ __('Credit note') }}</span>
                </jet-dropdown-link>
            </template>

            <template v-if="can('documents:invoice:duplicate') && !without.includes('duplicate')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link as="button" @click="duplicate(invoice)">
                    <document-duplicate-icon class="inline-block -mt-1 text-gray-500 w-4 h-4 mr-2"/>
                    <span class="font-normal">{{ __('Duplicate') }}</span>
                </jet-dropdown-link>
            </template>

            <template v-if="can('documents:invoice:delete') && !without.includes('delete')">
                <div class="border-t border-gray-100"></div>

                <jet-dropdown-link as="button" @click="$emit('delete', invoice.id)">
                    <trash-icon class="inline-block -mt-1 text-red-500 w-4 h-4 mr-2"/>
                    <span class="text-red-500">{{ __('Delete') }}</span>
                </jet-dropdown-link>
            </template>
        </template>
    </jet-dropdown>
</template>

<script>
    import axios from 'axios'
    import fileDownload from 'js-file-download'
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import {CogIcon, ChevronDownIcon, MailIcon, DocumentDuplicateIcon, DocumentDownloadIcon, TrashIcon, PrinterIcon, ArchiveIcon} from '@heroicons/vue/solid'
    import Print from '@/Services/Print'
    import EventBus from "@/Services/EventBus";

    export default {
        name: 'DocumentOptions',

        emits: ['delete'],

        components: {
            ChevronDownIcon,
            CogIcon,
            DocumentDownloadIcon,
            DocumentDuplicateIcon,
            JetDropdown,
            JetDropdownLink,
            MailIcon,
            TrashIcon,
            PrinterIcon,
            ArchiveIcon,
        },

        props: {
            invoice: Object,
            companyName: String,
            openDirection: String,
            without: {
                type: Array,
                default: () => [],
            },
        },

        data() {
            return {
                type: route().params.type,
            };
        },

        methods: {
            printPdf(invoice) {
                Print.documentPdf(invoice.id);
            },

            downloadPdf(invoice) {
                axios.get(route('documents.download', [this.type, invoice.id]), {responseType: 'blob'})
                    .then(response => {
                        fileDownload(response.data, `${this.companyName}_${invoice.number}_${invoice.lang_code}.pdf`, 'application/pdf');
                    });
            },

            duplicate(invoice) {
                EventBus.emit('document:duplicate:open', invoice);
            },
        },
    }
</script>
