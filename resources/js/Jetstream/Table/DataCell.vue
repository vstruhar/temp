<template>
    <td class="text-sm text-gray-600 flex lg:table-cell flex-col flex-nowrap items-start" :class="padding">
        <!-- Show column name on mobile -->
        <div class="text-xs text-gray-400 lg:hidden mb-1 pointer-events-none" v-if="name">{{ name }}</div>

        <div class="text-xl sm:text-lg lg:text-sm" :class="[slotClasses, eventsNone ? 'pointer-events-none' : null, inline ? 'inline-block' : 'w-full']">
            <slot/>
        </div>
    </td>
</template>

<script>
export default {
    name: "DataCell",

    props: {
        name: {
            type: String,
            default: null,
        },
        slotClasses: {
            type: String,
            default: null,
        },
        padding: {
            type: String,
            default: 'px-0 py-0 lg:px-4 lg:py-3',
        },
        eventsNone: {
            type: Boolean,
            default: false,
        },
        inline: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
