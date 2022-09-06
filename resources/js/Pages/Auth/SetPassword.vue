<template>
    <Head :title="__('You can set a new password here')"/>

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo/>
        </template>

        <form @submit.prevent="submit">
            <div class="mb-5">
                <jet-label for="password" :value="__('You can set a new password here')" class="mt-6 mb-4 text-center text-base"/>
                <jet-input id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autofocus/>
                <jet-input-error :message="form.errors.password" class="mt-2"/>
            </div>

            <div class="flex items-center justify-end mb-4">
                <jet-button block :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    {{ __('Save password') }}
                </jet-button>
            </div>
        </form>

        <Link :href="route('documents', 'invoices')" class="block text-center text-gray-400 hover:text-gray-600 text-sm transition">
            {{ __('I will keep the current password') }}
        </Link>
    </jet-authentication-card>
</template>

<script>
    import {defineComponent} from 'vue';
    import {Head} from '@inertiajs/inertia-vue3';
    import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
    import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetInputError from '@/Jetstream/InputError.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import {handleError} from "@/Services/RequestService"
    import {Link} from '@inertiajs/inertia-vue3'

    export default defineComponent({
        components: {
            Head,
            JetAuthenticationCard,
            JetAuthenticationCardLogo,
            JetButton,
            JetInput,
            JetInputError,
            JetLabel,
            Link
        },

        data() {
            return {
                form: this.$inertia.form({
                    password: '',
                })
            }
        },

        methods: {
            submit() {
                this.form.put(this.route('user.password.update'), {
                    onFinish: () => this.form.reset('password'),
                    onError: handleError,
                })
            },
        },
    })
</script>
