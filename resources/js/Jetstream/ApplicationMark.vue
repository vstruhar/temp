<template>
    <div class="flex flex-row flex-nowrap grow">
        <div class="shrink-0 block w-13 h-13 text-white p-1.5 bg-indigo-500 rounded-full">
            <light-bulb-icon class="w-auto h-7 text-white"/>
        </div>
        <span class="grow ml-4 mt-1 text-2xl text-white font-bold xs:block sm:hidden lg:block">Ponfys</span>
    </div>
</template>

<script>
    import {LightBulbIcon} from '@heroicons/vue/outline'

    export default {
        components: {
            LightBulbIcon,
        },
    }
</script>
