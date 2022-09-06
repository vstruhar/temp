<template>
    <jet-dropdown ref="jetDropdown" align="right" width="64" :close-on-content-click="false">
        <template #trigger>
            <button type="button" class="inline-flex justify-center w-full rounded-lg shadow-sm px-2 lg:px-4 py-2 bg-white text-sm font-medium text-gray-700 border border-gray-300 hover:bg-gray-50 focus:border-litepie-primary-300 focus:ring focus:ring-litepie-primary-500 focus:ring-opacity-10 focus:outline-none">
                {{ selection }}
                <svg class="hidden lg:block -mr-1 ml-2 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </template>

        <template #content>
            <div class="px-4 py-3">
                <div class="text-sm mb-4">
                    <litepie-datepicker
                        ref="datepicker"
                        v-model="datepicker.value"
                        :placeholder="__('Time period')"
                        separator=" - "
                        as-single
                        use-range
                        :formatter="datepicker.formatter"
                        :shortcuts="false"
                        :i18n="$page.props.locale.current"
                    />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div v-for="(option, index) in options"
                         :key="`filter-date-option-${option.value}`"
                         class="cursor-pointer text-center text-sm text-gray-600 p-2 bg-gray-100 rounded-md border border-gray-200 hover:bg-indigo-500 hover:text-white transition"
                         :class="{'col-span-2': options.length === (index+1), 'bg-indigo-500 text-white': option.value === period}"
                         @click="filterByPeriod(option.value)">
                        {{ option.label }}
                    </div>
                </div>
            </div>
        </template>
    </jet-dropdown>
</template>

<script>
import {defineComponent} from 'vue'
import UrlQueryService from '@/Services/UrlQueryService'
import LitepieDatepicker from 'litepie-datepicker'
import JetDropdown from '@/Jetstream/Dropdown'
import moment from 'moment'

export default defineComponent({
    name: 'DateFilter',

    emits: ['changed'],

    components: {
        LitepieDatepicker,
        JetDropdown,
    },

    data() {
        return {
            period: 'this-year',

            options: [
                {label: this.__('Today'), value: 'today'},
                {label: this.__('Yesterday'), value: 'yesterday'},
                {label: this.__('This month'), value: 'this-month'},
                {label: this.__('Last month'), value: 'last-month'},
                {label: this.__('This quarter'), value: 'this-quarter'},
                {label: this.__('Last quarter'), value: 'last-quarter'},
                {label: this.__('This year'), value: 'this-year'},
                {label: this.__('Last year'), value: 'last-year'},
                {label: this.__('All'), value: 'all'},
            ],

            datepicker: {
                value: {
                    startDate: '',
                    endDate: '',
                },
                formatter: {
                    date: 'DD.MM.YYYY',
                    month: 'MMM',
                },
            },
        };
    },

    computed: {
        selection() {
            const period = this.options.find(o => o.value === this.period)?.label;
            if (period) return period;

            if (this.datepicker.value.startDate && this.datepicker.value.endDate) {
                return this.datepicker.value.startDate + ' - ' + this.datepicker.value.endDate;
            }

            this.period = 'this-year';
            return this.__('This year');
        },
    },

    watch: {
        'datepicker.value': {
            handler(value) {
                this.filterByDate(value);
            },
            deep: true,
        },
    },

    mounted() {
        const startDate = UrlQueryService.get('startDate', '');
        const endDate = UrlQueryService.get('endDate', '');

        if (startDate) this.datepicker.value.startDate = this.convertDate(startDate);
        if (endDate) this.datepicker.value.endDate = this.convertDate(endDate);

        this.period = UrlQueryService.get('period', '');
    },

    methods: {
        convertDate(date) {
            if (date.includes('-')) return moment(date, 'YYYY-MM-DD').format('DD.MM.YYYY');
            if (date.includes('.')) return moment(date, 'DD.MM.YYYY').format('YYYY-MM-DD');
        },

        filterByDate(value) {
            if (value.startDate) UrlQueryService.set('startDate', this.convertDate(value.startDate));
            if (value.endDate) UrlQueryService.set('endDate', this.convertDate(value.endDate));

            if (value.startDate && value.endDate) {
                UrlQueryService.remove('period');
                this.period = null;

                this.$refs.jetDropdown.close();
            } else {
                // don't focus datepicker and don't show it
                this.$refs.datepicker.LitepieRef.classList.add('hidden');
                setTimeout(() => {this.$refs.datepicker.hide(); this.$refs.datepicker.LitepieInputRef.blur()}, 0);
                setTimeout(() => this.$refs.datepicker.LitepieRef.classList.remove('hidden'), 300);

                UrlQueryService.remove('startDate');
                UrlQueryService.remove('endDate');
            }
            this.$emit('changed', this.getSelection());
        },

        filterByPeriod(period) {
            this.period = period;
            UrlQueryService.set('period', period);

            this.$refs.datepicker.clearPicker();

            this.datepicker.value.startDate = '';
            this.datepicker.value.endDate = '';
            UrlQueryService.remove('startDate');
            UrlQueryService.remove('endDate');

            this.$refs.jetDropdown.close();
        },

        getSelection() {
            if (this.datepicker.value.startDate && this.datepicker.value.endDate) {
                return {
                    startDate: this.convertDate(this.datepicker.value.startDate),
                    endDate: this.convertDate(this.datepicker.value.endDate),
                };
            }

            let date;

            switch (this.period) {
                case 'today': date = {startDate: moment(), endDate: moment()}; break;
                case 'yesterday': date = {startDate: moment().subtract(1, 'day'), endDate: moment().subtract(1, 'day')}; break;
                case 'this-month': date = {startDate: moment().startOf('month'), endDate: moment().endOf('month')}; break;
                case 'last-month': date = {startDate: moment().subtract(1, 'month').startOf('month'), endDate: moment().subtract(1, 'month').endOf('month')}; break;
                case 'this-quarter': date = {startDate: moment().startOf('quarter'), endDate: moment().endOf('quarter')}; break;
                case 'last-quarter': date = {startDate: moment().subtract(3, 'months').startOf('quarter'), endDate: moment().subtract(3, 'months').endOf('quarter')}; break;
                case 'this-year': date = {startDate: moment().startOf('year'), endDate: moment().endOf('year')}; break;
                case 'last-year': date = {startDate: moment().subtract(1, 'year').startOf('year'), endDate: moment().subtract(1, 'year').endOf('year')}; break;
            }

            return {
                startDate: date ? date.startDate.format('YYYY-MM-DD') : null,
                endDate: date ? date.endDate.format('YYYY-MM-DD') : null,
            };
        },
    },
});
</script>

<style scoped>
::v-deep(#litepie .litepie-datepicker) {
    box-shadow: rgba(99, 99, 99, 0.2) 0 2px 8px 0;
}
</style>
