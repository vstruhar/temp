import HelperMixin from '@/Mixins/HelperMixin'

const __ = HelperMixin.methods.__;

const DocumentStatus = {
    WAITING_FOR_PAYMENT: 'waiting-for-payment',
    PARTIAL_PAYMENT: 'partial-payment',
    PAYMENT_RECEIVED: 'payment-received',
    OVER_PAYMENT: 'over-payment',
    CANCELLED: 'cancelled',

    WAITING_FOR_APPROVAL: 'waiting-for-approval',
    APPROVED: 'approved',
    REJECTED: 'rejected',
};

const statusItems = [
    {value: DocumentStatus.WAITING_FOR_PAYMENT, label: __('Waiting for payment')},
    {value: DocumentStatus.PARTIAL_PAYMENT, label: __('Partially paid')},
    {value: DocumentStatus.PAYMENT_RECEIVED, label: __('Paid')},
    {value: DocumentStatus.OVER_PAYMENT, label: __('Overpayment')},
    {value: DocumentStatus.CANCELLED, label: __('Credit note')},

    {value: DocumentStatus.WAITING_FOR_APPROVAL, label: __('Waiting for approval')},
    {value: DocumentStatus.APPROVED, label: __('Approved')},
    {value: DocumentStatus.REJECTED, label: __('Rejected')},
];

const readableStatus = (status) => {
    return statusItems.find(i => i.value === status)?.label;
};

export {DocumentStatus, readableStatus, statusItems};
