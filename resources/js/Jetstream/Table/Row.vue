<template>
    <tr class="hover:bg-gray-50 grid grid-cols-2 gap-x-4 gap-y-2 lg:table-row bg-white rounded-lg shadow-lg overflow-hidden p-5 lg:p-0"
        :class="{'cursor-pointer': pointer}">
        <slot/>
    </tr>
</template>

<script>
export default {
    name: "Row",

    props: {
        pointer: {
            type: Boolean,
            default: true,
        },
    },
}
</script>
