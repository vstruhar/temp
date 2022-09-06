<template>
    <div class="divide-y divide-gray-200">
        <div class="text-sm tracking-wider font-semibold text-gray-400 py-2 text-center bg-gray-50 rounded-t-lg">{{ __('History of changes') }}</div>

        <div v-for="revision in revisions" :key="`invoice-revision-${revision.id}`">
            <div v-if="can('documents:revision:restore')" @click="confirmRevisionRestore(revision)" class="group flex items-center px-5 py-3 w-full tracking-wide text-gray-500 hover:text-gray-700 hover:bg-indigo-50 cursor-pointer transition">
                <clipboard-copy-icon class="w-5 h-5 mr-4 text-gray-400 group-hover:text-gray-600"/>
                <div class="flex flex-col">
                    <div class="text-sm">{{ datetime(revision.created_at, ' / ') }}</div>
                    <div class="text-xs tracking-wide font-semibold text-gray-400 group-hover:text-indigo-400">{{ revision.user.name }}</div>
                </div>
            </div>
        </div>
    </div>

    <jet-warning-confirmation-modal :title="title"
                                    :message="message"
                                    :confirm-button-text="confirmButtonText"
                                    :processing="form.processing"
                                    :show="modal.confirmRevisionRestore"
                                    @confirmed="restoreRevision"
                                    @close="modal.confirmRevisionRestore = false"/>
</template>

<script>
    import {defineComponent} from 'vue'
    import {ClipboardCopyIcon} from '@heroicons/vue/outline'
    import JetWarningConfirmationModal from '@/Jetstream/WarningConfirmationModal.vue'
    import {handleError} from "@/Services/RequestService"

    export default defineComponent({
        props: {
            revisions: Array,
            invoiceId: Number,
            title: String,
            message: String,
            confirmButtonText: String,
        },

        components: {
            ClipboardCopyIcon,
            JetWarningConfirmationModal,
        },

        data() {
            return {
                form: this.$inertia.form(),
                selectedRevisionId: null,
                modal: {
                    confirmRevisionRestore: false,
                },
            }
        },

        methods: {
            confirmRevisionRestore(revision) {
                this.modal.confirmRevisionRestore = true;
                this.selectedRevisionId = revision.id;
            },

            restoreRevision() {
                this.form.put(route('documents.revision.restore', [route().params.type, this.invoiceId, this.selectedRevisionId]), {
                    onSuccess: () => {
                        this.modal.confirmRevisionRestore = false;
                        this.selectedRevisionId = null;
                    },
                    onError: handleError,
                });
            },
        },
    });
</script>
