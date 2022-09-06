<template>
    <multiselect v-model="selection"
                 :options="filteredOptions"
                 trackBy="value"
                 label="label"
                 :multiple="false"
                 :internal-search="false"
                 @search-change="searchChange"
                 :allowEmpty="allowEmpty"
                 :searchable="searchable"
                 :maxHeight="maxHeight"
                 :placeholder="localPlaceholder"
                 :openDirection="openDirection"
                 selectLabel=""
                 selectedLabel=""
                 deselectLabel=""
                 :class="{'no-empty': allowEmpty}">
        <template #noOptions>{{ __('List is empty') }}</template>
    </multiselect>
</template>

<script>
import {defineComponent} from 'vue'
import Multiselect from 'vue-multiselect';

export default defineComponent({
    name: "DropdownSelectFilter",

    components: {
        Multiselect,
    },

    props: {
        value: {
            default: null,
        },
        openDirection: {
            default: 'bottom',
        },
        options: {
            type: Array,
            default: [],
        },
        maxHeight: {
            type: Number,
            default: 180,
        },
        allowEmpty: {
            default: true,
            type: Boolean,
        },
        searchable: {
            default: true,
            type: Boolean,
        },
        placeholder: String,
    },

    data() {
        return {
            selection: null,
            filteredOptions: [],
            localPlaceholder: '',
        };
    },

    watch: {
        selection(selection) {
            this.$emit('update:value', selection ? selection.value : null);
        },

        options: {
            immediate: true,
            handler(value) {
                this.filteredOptions = value;
            },
        },

        value: {
            immediate: true,
            handler(value) {
                if (value) {
                    this.selection = this.options.find(i => i.value == value);
                }
            },
        },
    },

    mounted() {
        this.localPlaceholder = this.placeholder || this.__('Select item');
    },

    methods: {
        searchChange(search) {
            this.filteredOptions = search
                ? this.options.filter(option => this.normalizedContains(search, option))
                : this.options;
        },

        normalizedContains(needle, haystack) {
            const regExp = new RegExp(this.removeDiacritics(needle), 'gi');
            return regExp.test(this.removeDiacritics(haystack.label));
        },

        removeDiacritics(text) {
            return text.normalize('NFD').replace(/[\u0300-\u036f]/g, '');
        },
    },
})
</script>
