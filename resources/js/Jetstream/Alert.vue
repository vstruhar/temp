<template>
    <div class="px-6 py-4 my-4 shadow rounded-lg text-lg flex items-center mx-auto w-3/4 xl:w-2/4"
         :class="{'bg-yellow-100': type === 'warning', 'bg-red-100': type === 'danger', 'bg-blue-100': type === 'info', 'bg-green-100': type === 'success'}">

        <exclamation-icon v-if="type === 'warning'" class="text-yellow-500 w-8 h-8 sm:w-8 sm:h-8 mr-5"/>
        <exclamation-icon v-if="type === 'danger'" class="text-red-500 w-8 h-8 sm:w-8 sm:h-8 mr-5"/>
        <exclamation-icon v-if="type === 'info'" class="text-blue-500 w-8 h-8 sm:w-8 sm:h-8 mr-5"/>
        <exclamation-icon v-if="type === 'success'" class="text-green-500 w-8 h-8 sm:w-8 sm:h-8 mr-5"/>

        <span :class="{'text-yellow-700': type === 'warning', 'text-red-700': type === 'danger', 'text-blue-700': type === 'info', 'text-green-700': type === 'success'}">
            <slot/>
        </span>
    </div>
</template>

<script>
    import {ExclamationIcon, ExclamationCircleIcon, CheckCircleIcon, InformationCircleIcon} from '@heroicons/vue/solid'

    export default {
        name: "Alert",

        components: {
            ExclamationIcon,
            ExclamationCircleIcon,
            CheckCircleIcon,
            InformationCircleIcon,
        },

        props: {
            message: String,
            type: {
                type: String,
                default: 'info',
            },
        },
    }
</script>
