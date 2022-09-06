<template>
    <div class="flex flex-wrap gap-2 lg:gap-3 w-full lg:w-auto">
        <text-filter v-if="!without.includes('text')" @changed="filter" :placeholder="placeholder"/>
        <status-filter v-if="!without.includes('status')" @changed="filter"/>
        <date-filter v-if="!without.includes('date')" @changed="filter"/>
        <more-filters v-if="!without.includes('more')" @changed="filter" :without="without"/>
    </div>
</template>

<script>
import DateFilter from "@/Pages/Documents/Shared/Filters/DateFilter";
import MoreFilters from "@/Pages/Documents/Shared/Filters/MoreFilters";
import StatusFilter from "@/Pages/Documents/Shared/Filters/StatusFilter";
import TextFilter from '@/Pages/Documents/Shared/Filters/TextFilter';
import UrlQueryService from "@/Services/UrlQueryService";
import EventBus from "@/Services/EventBus";

export default {
    name: "DocumentsFilters",

    components: {
        DateFilter,
        MoreFilters,
        StatusFilter,
        TextFilter,
    },

    props: {
        without: {
            default: [],
            type: Array,
        },
        placeholder: String,
    },

    methods: {
        filter() {
            UrlQueryService.set('page', 1);

            this.$inertia.get(
                route('documents', route().params.type),
                UrlQueryService.all(),
                {replace: true, preserveState: true}
            );
        },
    },

    mounted() {
        EventBus.on('filter', this.filter);
    },

    beforeUnmount() {
        EventBus.off('filter', this.filter);
    },
}
</script>
