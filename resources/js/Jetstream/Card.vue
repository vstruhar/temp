<template>
    <div class="bg-white shadow rounded-lg" :class="{'px-4 py-5 sm:p-6': padding}">
        <div v-if="title || $slots.title" class="col-span-6 bg-gray-50 mb-4 px-6 py-2 rounded-t-lg" :class="{'-mx-4 -mt-5 sm:-mt-6 sm:-mx-6': padding}">
            <div v-if="title" class="font-semibold text-lg text-gray-500 mt-1">{{ title }}</div>
            <div v-if="$slots.title" class="font-semibold text-lg text-gray-500 mt-1">
                <slot name="title"/>
            </div>
        </div>

        <slot/>
    </div>
</template>

<script>
export default {
    name: "Card",

    props: {
        title: {
            default: null,
            type: String,
        },
        padding: {
            default: true,
            type: Boolean,
        },
    },
}
</script>

