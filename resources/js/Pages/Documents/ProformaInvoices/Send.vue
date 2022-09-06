<template>
    <app-layout :title="__('Send proforma invoice')+`: ${number}`">
        <template #header>
            <h2 class="font-semibold text-2xl text-gray-800 leading-tight text-center md:text-left">
                {{ __('Send proforma invoice') }}: {{ number }}
            </h2>
        </template>

        <jet-form-section @submitted="send" :with-title="false" :with-card="false">
            <template #form>
                <div class="col-span-6 md:col-span-2">
                    <jet-card :title="__('Client')" class="md:pb-8 mb-6">
                        <!-- Client email -->
                        <div class="col-span-6 sm:col-span-6 mb-8">
                            <jet-label for="email" :value="__('Email')" required/>
                            <jet-input id="email" type="text" class="mt-1 block w-full" v-model="form.email"/>
                            <jet-input-error :message="form.errors.email" class="mt-2"/>
                        </div>

                        <selected-client-preview :client="document.client" :except="['organization_id', 'value_added_tax', 'tax']" class="-m-6 mt-6"/>
                    </jet-card>

                    <jet-card :title="__('Sender')" class="sm:pb-8">
                        <div class="grid grid-cols-2 gap-6">
                            <!-- Sender name -->
                            <div class="col-span-1 md:col-span-2">
                                <jet-label for="inputSenderName" :value="__('Name')"/>
                                <jet-input id="inputSenderName" type="text" disabled class="mt-1 block w-full disabled:bg-gray-100" v-model="sender.name"/>
                            </div>

                            <!-- Sender email -->
                            <div class="col-span-1 md:col-span-2">
                                <jet-label for="inputSenderEmail" :value="__('Email')"/>
                                <jet-input id="inputSenderEmail" type="text" disabled class="mt-1 block w-full disabled:bg-gray-100" v-model="sender.email"/>
                            </div>
                        </div>
                    </jet-card>
                </div>

                <jet-card title="Email" class="col-span-6 md:col-span-4 sm:pb-8">
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Name -->
                        <div>
                            <jet-label for="address" :value="__('Subject')" required/>
                            <jet-input id="address" type="text" class="mt-1 block w-full" v-model="form.name"/>
                            <jet-input-error :message="form.errors.name" class="mt-2"/>
                        </div>

                        <!-- Message -->
                        <div class="mt-2">
                            <jet-label for="msg" :value="__('Message')" required/>
                            <jet-textarea id="msg" class="mt-1 block w-full" v-model="form.message" :rows="11"/>
                            <jet-input-error :message="form.errors.message" class="mt-2"/>
                        </div>

                        <div class="flex justify-between mt-5">
                            <jet-back-button :url="route('documents', 'proforma-invoices')"></jet-back-button>

                            <jet-button :class="{'opacity-25': form.processing}" :disabled="form.processing" class="justify-center h-10">
                                <mail-icon class="w-4 h-4 mr-2"/>
                                {{ __('Send email') }}
                            </jet-button>
                        </div>
                    </div>
                </jet-card>
            </template>
        </jet-form-section>
    </app-layout>
</template>

<script>
    import AppLayout from '@/Layouts/AppLayout.vue'
    import DocumentMixin from '@/Mixins/Document'
    import JetBackButton from '@/Jetstream/BackButton.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetCard from '@/Jetstream/Card.vue'
    import JetFormSection from '@/Jetstream/FormSection.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetSecondaryButton from '@/Jetstream/SecondaryButton.vue'
    import JetTextarea from '@/Jetstream/Textarea.vue'
    import SelectedClientPreview from "../Shared/Partials/SelectedClientPreview"
    import {defineComponent} from 'vue'
    import {Link} from '@inertiajs/inertia-vue3'
    import {MailIcon} from '@heroicons/vue/solid'
    import {handleError} from "@/Services/RequestService";
    import EventBus from "@/Services/EventBus";

    export default defineComponent({
        mixins: [DocumentMixin],

        components: {
            AppLayout,
            JetBackButton,
            JetButton,
            JetCard,
            JetFormSection,
            JetInput,
            JetInputError,
            JetLabel,
            JetSecondaryButton,
            JetTextarea,
            Link,
            SelectedClientPreview,
            MailIcon,
        },

        props: ['document', 'sender', 'bankAccountNumber', 'emailSubject'],

        data() {
            return {
                form: this.$inertia.form({
                    _method: 'POST',
                    name: '',
                    email: '',
                    message: '',
                }),
                number: '',
                clientName: '',
                amount: 0,
                variableSymbol: '',
                clientAddress: '',
            }
        },

        mounted() {
            this.number = this.document.number;
            this.amount = this.document.amount_with_tax ? this.document.amount_with_tax : this.document.amount;

            this.form.name = this.emailSubject;
            this.form.variableSymbol = this.document.variable_symbol ? this.document.variable_symbol : '';
            this.form.email = this.document.client.email;

            this.form.message = this.__('Hello, we are sending an invoice as attachment')+': ' + this.document.number
                + '\n\n'+this.__('The amount to be paid')+': ' + this.amount + ' ' + this.document.invoice_currency
                + '\n\n'+this.__('Variable symbol')+': ' + this.form.variableSymbol
                + '\n\n'+this.__('Account number')+': ' + this.bankAccountNumber
                + '\n\n'+this.__('Thank you for your payment and have a nice day')+'.';
        },

        methods: {
            send() {
                EventBus.emit('loader:show');

                this.form.post(route('documents.send', ['proforma-invoices', this.document.id]), {
                    onError: handleError,
                    onFinish: () => EventBus.emit('loader:hide'),
                });
            },
        },
    })
</script>
