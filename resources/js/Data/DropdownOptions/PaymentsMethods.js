import HelperMixin from '@/Mixins/HelperMixin'

const __ = HelperMixin.methods.__;

export default [
    {
        label: __('Bank transfer'),
        value: 'bank_transfer'
    },
    {
        label: __('Cash'),
        value: 'cash'
    },
    {
        label: __('Card'),
        value: 'card'
    },
    {
        label: __('Cash on delivery'),
        value: 'cash_on_delivery'
    },
    {
        label: __('Mutual credit'),
        value: 'mutual_credit'
    },
    {
        label: __('Custom'),
        value: 'custom'
    }
];
