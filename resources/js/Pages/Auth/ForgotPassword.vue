<template>
    <Head title="Zabudnuté heslo" />

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo />
        </template>

        <div class="mb-5 text-sm text-gray-600">
            {{ __('Enter you email. Then you will receive instructions on how to reset your password') }}
        </div>

        <jet-success-alert :message="status"/>

        <jet-validation-errors class="mb-4" />

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" :value="__('Email')" />
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus />
            </div>

            <div class="flex items-center justify-end mt-5 mb-14">
                <jet-button block :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ __('Send email') }}
                </jet-button>
            </div>

            <div class="flex items-center justify-center bg-gray-100 p-4 -m-6 rounded-b-md">
                <Link :href="route('login')" class="text-sm text-center text-gray-800 dark:text-gray-200 hover:underline">&lt; {{ __('Back') }}</Link>
            </div>
        </form>
    </jet-authentication-card>
</template>

<script>
    import { defineComponent } from 'vue'
    import { Head, Link } from '@inertiajs/inertia-vue3';
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
    import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
    import JetSuccessAlert from '@/Jetstream/SuccessAlert.vue'
    import {handleError} from "@/Services/RequestService";

    export default defineComponent({
        components: {
            Head,
            Link,
            JetAuthenticationCard,
            JetAuthenticationCardLogo,
            JetButton,
            JetInput,
            JetLabel,
            JetSuccessAlert,
            JetValidationErrors
        },

        props: {
            status: String,
        },

        data() {
            return {
                form: this.$inertia.form({
                    email: ''
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('password.email'), {
                    onError: handleError,
                })
            }
        }
    })
</script>
