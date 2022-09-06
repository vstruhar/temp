<template>
    <jet-dropdown-select-filter v-model:value="selected"
                                :options="companies"
                                :placeholder="__('Select company')"
                                :allow-empty="false"
                                :max-height="maxHeight"/>
</template>

<script>
import DropdownSelectFilter from "@/Jetstream/DropdownSelectFilter";

export default {
    name: "CompanyDropdown",

    emits: ['changed'],

    components: {
        'jet-dropdown-select-filter': DropdownSelectFilter,
    },

    props: {
        companyId: {
            type: Number,
            default: null,
        },
        maxHeight: {
            type: Number,
            default: 180,
        },
    },

    data() {
        return {
            selected: this.companyId,
        };
    },

    computed: {
        companies() {
            return Object.values(this.$page.props.user.all_teams)
                .map(company => ({value: company.id, label: company.name}));
        },
    },

    watch: {
        selected(value) {
            this.$emit('changed', value);
        },
    },
};
</script>
