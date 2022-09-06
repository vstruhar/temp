import HelperMixin from '@/Mixins/HelperMixin'

const __ = HelperMixin.methods.__;

const type = [
    {label: __('Invoice'), value: 'invoice'},
    {label: __('Proforma invoice'), value: 'proforma-invoice'},
    {label: __('Quotation'), value: 'quotation'},
    {label: __('Credit note'), value: 'credit-note'},
];

const period = [
    {label: __('Yearly'), value: 'yearly'},
    {label: __('Monthly'), value: 'monthly'},
    {label: __('Daily'), value: 'daily'},
    {label: __('Do not reset'), value: 'none'},
];

const format = [
    {label: 'CCCRRRR', value: 'CCCRRRR'},
    {label: 'RRCCCC', value: 'RRCCCC'},
    {label: __('Other'), value: 'custom'},
];

const options = {type, period, format};

const getLabel = (field, value) => {
    const item = options[field].find(i => i.value === value);
    return item ? item.label : '/';
};

export {type, period, format, getLabel};
