<template>
    <Head :title="__('Login')"/>

    <jet-authentication-card>
        <template #logo>
            <jet-authentication-card-logo :full="true"/>
        </template>

        <jet-validation-errors class="mb-4"/>

        <jet-success-alert :message="status"/>

        <form @submit.prevent="submit">
            <div>
                <jet-label for="email" :value="__('Email')"/>
                <jet-input id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autofocus tabindex="1"/>
            </div>

            <div class="mt-4">
                <div class="flex items-center justify-between">
                    <jet-label for="password" :value="__('Password')"/>
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-gray-600 dark:text-gray-400 hover:underline">
                        {{ __('Forgot password?') }}
                    </Link>
                </div>
                <div class="relative">
                    <div class="absolute top-[9px] right-[14px]">
                        <EyeIcon v-if="!showPassword" class="w-6 h-6 p-1 cursor-pointer text-gray-400/75 hover:text-gray-400 transition-colors" @click="showPassword = true"/>
                        <EyeOffIcon v-if="showPassword" class="w-6 h-6 p-1 cursor-pointer text-gray-400/75 hover:text-gray-400 transition-colors" @click="showPassword = false"/>
                    </div>
                    <jet-input id="password" :type="showPassword ? 'text' : 'password'" class="mt-1 block w-full" v-model="form.password" required autocomplete="current-password" tabindex="2"/>
                </div>
            </div>

            <div class="block mt-4">
                <label class="flex items-center">
                    <jet-checkbox name="remember" v-model:checked="form.remember" tabindex="3"/>
                    <span class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                <jet-button block :class="{ 'opacity-25': form.processing }" :disabled="form.processing" tabindex="4">
                    {{ __('Login') }}
                </jet-button>
            </div>
        </form>

        <div class="flex items-center justify-between mt-4">
            <span class="w-1/5 border-b dark:border-gray-600 lg:w-1/5"></span>
            <p class="text-xs text-center text-gray-500 uppercase dark:text-gray-400">{{ __('or login using') }}</p>
            <span class="w-1/5 border-b dark:border-gray-400 lg:w-1/5"></span>
        </div>

        <div class="flex items-center mt-5 -mx-2">
            <button disabled type="button" class="pointer-events-none opacity-50 w-1/2 flex items-center justify-center w-full px-6 py-3 mx-2 text-sm font-medium text-white transition-colors duration-200 transform bg-red-500 rounded-md hover:bg-red-400 focus:bg-red-400 focus:outline-none">
                <svg class="w-4 h-4 mx-2 fill-current" viewBox="0 0 24 24">
                    <path d="M12.24 10.285V14.4h6.806c-.275 1.765-2.056 5.174-6.806 5.174-4.095 0-7.439-3.389-7.439-7.574s3.345-7.574 7.439-7.574c2.33 0 3.891.989 4.785 1.849l3.254-3.138C18.189 1.186 15.479 0 12.24 0c-6.635 0-12 5.365-12 12s5.365 12 12 12c6.926 0 11.52-4.869 11.52-11.726 0-.788-.085-1.39-.189-1.989H12.24z"></path>
                </svg>
                <span class="hidden mx-2 sm:inline">Google</span>
            </button>

            <a href="#" disabled class="pointer-events-none opacity-50 w-1/2 flex items-center justify-center w-full px-6 py-3 mx-2 text-sm font-medium text-white transition-colors duration-200 transform bg-blue-500 rounded-md hover:bg-blue-400 focus:bg-blue-400 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mx-2 fill-current" viewBox="0 0 24 24">
                    <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                </svg>
                <span class="hidden mx-2 sm:inline">Facebook</span>
            </a>
        </div>

        <div class="mt-8 text-xs font-light text-center text-gray-500 bg-gray-100 p-4 -m-6 rounded-b-md hidden">
            {{ __("Don't have account yet?") }}
            <Link :href="route('register')" class="font-medium text-gray-900 dark:text-gray-200 hover:underline">{{ __('Register here') }}</Link>
        </div>

    </jet-authentication-card>
</template>

<script>
import {defineComponent} from 'vue'
import JetAuthenticationCard from '@/Jetstream/AuthenticationCard.vue'
import JetAuthenticationCardLogo from '@/Jetstream/AuthenticationCardLogo.vue'
import JetButton from '@/Jetstream/Button.vue'
import JetInput from '@/Jetstream/Input.vue'
import JetCheckbox from '@/Jetstream/Checkbox.vue'
import JetLabel from '@/Jetstream/Label.vue'
import JetValidationErrors from '@/Jetstream/ValidationErrors.vue'
import JetSuccessAlert from '@/Jetstream/SuccessAlert.vue'
import {Head, Link} from '@inertiajs/inertia-vue3';
import {EyeIcon, EyeOffIcon} from '@heroicons/vue/solid';

export default defineComponent({
    components: {
        Head,
        JetAuthenticationCard,
        JetAuthenticationCardLogo,
        JetButton,
        JetInput,
        JetCheckbox,
        JetLabel,
        JetValidationErrors,
        JetSuccessAlert,
        Link,
        EyeIcon,
        EyeOffIcon,
    },

    props: {
        canResetPassword: Boolean,
        status: String
    },

    data() {
        return {
            showPassword: false,
            form: this.$inertia.form({
                email: '',
                password: '',
                remember: false
            })
        }
    },

    mounted() {
        const language = window.localStorage.getItem('language');

        if (language !== null && this.$page.props.locale.current !== language && !window.location.href.includes('?lang=')) {
            window.location.href = window.location.href + '?lang=' + language;
        }
    },

    methods: {
        submit() {
            this.form
                .transform(data => ({
                    ...data,
                    remember: this.form.remember ? 'on' : ''
                }))
                .post(this.route('login'), {
                    onFinish: () => this.form.reset('password'),
                })
        }
    },
})
</script>
