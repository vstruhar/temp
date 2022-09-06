<template>
    <div>
        <div :class="{'bg-green-500': style == 'success', 'bg-red-600': style == 'danger'}" v-if="show && message">
            <div class="max-w-screen-xl mx-auto py-2 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center min-w-0">
                        <span class="flex p-2">
                            <check-circle-icon class="h-7 w-7 text-white" v-if="style == 'success'"/>
                            <x-circle-icon class="h-7 w-7 text-white" v-if="style == 'danger'"/>
                        </span>

                        <p class="ml-3 font-bold text text-white truncate">
                            {{ message }}
                        </p>
                    </div>

                    <div class="flex-shrink-0 sm:ml-3">
                        <button
                            type="button"
                            class="-mr-1 flex p-2 sm:-mr-2 transition"
                            aria-label="Dismiss"
                            @click.prevent="show = false">
                            <x-icon class="w-6 h-6 text-white"/>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="false && show && message" class="fixed top-2 left-0 right-0 mx-auto pb-4 max-w-screen-md px-2 md:px-0 z-50">
            <div :class="{'bg-green-500': style == 'success', 'bg-red-600': style == 'danger'}"
                 class="md:w-3/4 mx-auto rounded-lg md:rounded-full px-4 md:pl-8 md:pr-2 py-2 md:flex items-center justify-between shadow-lg">

                <div class="text-white font-semibold md:max-w-xl flex items-center">
                    <span class="flex p-2">
                        <check-circle-icon class="h-7 w-7 text-white" v-if="style == 'success'"/>
                        <x-circle-icon class="h-7 w-7 text-white" v-if="style == 'danger'"/>
                    </span>

                    <div class="ml-3">{{ message }}</div>
                </div>

                <div class="py-2 md:py-0 text-center">
                    <button @click.prevent="show = false" class="block md:inline-block mx-auto my-2 md:my-0 md:mx-2 px-4 py-2 text-sm text-gray-400 focus:outline-none hover:underline">
                        <x-icon class="w-6 h-6 text-white"/>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {defineComponent} from 'vue'
    import {CheckCircleIcon, XCircleIcon, XIcon} from '@heroicons/vue/solid'

    export default defineComponent({
        components: {
            CheckCircleIcon,
            XCircleIcon,
            XIcon,
        },

        data() {
            return {
                show: true,
            }
        },

        watch: {
            '$page.props.jetstream.flash'() {
                this.show = true;
            },
        },

        computed: {
            style() {
                return this.$page.props.jetstream.flash?.bannerStyle || 'success'
            },

            message() {
                return this.$page.props.jetstream.flash?.banner || ''
            },
        },
    })
</script>
