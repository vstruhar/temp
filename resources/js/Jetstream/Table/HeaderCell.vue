<template>
    <th @click="sortable && $parent.sort(name)"
        class="px-4 py-3 text-left text-xs font-medium font-semibold text-gray-500 uppercase tracking-wider"
        :class="sortable ? 'cursor-pointer hover:text-gray-700' : ''">

        <slot/>

        <template v-if="sortable && $parent.isOrderedBy(name)">
            <SortAscendingIcon v-if="$parent.isAscendingOrder(name)" class="inline-block w-3 h-3 ml-3"/>
            <SortDescendingIcon v-else class="inline-block w-3 h-3 ml-3"/>
        </template>
    </th>
</template>

<script>
import {SortAscendingIcon} from '@heroicons/vue/solid';
import {SortDescendingIcon} from '@heroicons/vue/solid';

export default {
    name: "HeaderCell",

    components: {
        SortAscendingIcon,
        SortDescendingIcon,
    },

    props: {
        name: {
            type: String,
            default: '',
        },
        sortable: {
            type: Boolean,
            default: false,
        },
    },
}
</script>
