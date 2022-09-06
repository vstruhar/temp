import {DocumentStatus, readableStatus} from "@/Data/DocumentStatuses"
import moment from 'moment'

const DocumentModel = {
    document: null,

    set(document) {
        this.document = document;
        return this;
    },

    getTotal() {
        return this.document.taxable
            ? this.document.amount_with_tax
            : this.document.amount;
    },

    remainsToBePaid(documentPayments, documentTotal) {
        const payments = documentPayments || this.document.payments;
        const total = documentTotal || this.getTotal();

        const paymentsSum = payments.reduce((accumulator, payment) => accumulator + payment.amount, 0);

        return total - paymentsSum;
    },

    overPaid(documentPayments, documentTotal) {
        return Math.abs(this.remainsToBePaid(documentPayments, documentTotal));
    },

    billingExpired(billingDate, status = null) {
        if (status === DocumentStatus.PAYMENT_RECEIVED) {
            return false;
        }

        return moment(billingDate || this.document.billing_date).add(1, 'day').isBefore();
    },

    billingExpiredDays(billingDate) {
        const fromDate = moment(billingDate || this.document.billing_date);
        const today = moment().startOf('day');

        return moment.duration(fromDate.diff(today)).humanize();
    },

    status: {
        getClass(documentStatus, type = 'bg', shade = 500) {
            const status = documentStatus || DocumentModel.document.status;
            const color = this.getColor(status);

            if (color === 'black') {
                return `${type}-black`;
            }

            return `${type}-${color}-${shade}`;
        },

        getColor(documentStatus) {
            const status = documentStatus || DocumentModel.document.status;

            // quotation
            const quotationStatuses = [DocumentStatus.WAITING_FOR_APPROVAL, DocumentStatus.APPROVED, DocumentStatus.REJECTED];

            if (quotationStatuses.includes(status) && DocumentModel.billingExpired()) return 'gray';
            if (status === DocumentStatus.WAITING_FOR_APPROVAL) return 'yellow';
            if (status === DocumentStatus.APPROVED) return 'green';
            if (status === DocumentStatus.REJECTED) return 'red';

            // other
            if (status === DocumentStatus.PAYMENT_RECEIVED) return 'green';
            if (status === DocumentStatus.CANCELLED) return 'blue';
            if (status === DocumentStatus.OVER_PAYMENT) return 'black';
            if (status !== DocumentStatus.PAYMENT_RECEIVED && DocumentModel.billingExpired()) return 'red';
            if (status === DocumentStatus.PARTIAL_PAYMENT || status === DocumentStatus.WAITING_FOR_PAYMENT) return 'yellow';
        },

        paymentReceived(documentStatus) {
            const status = documentStatus || DocumentModel.document.status;

            return DocumentStatus.PAYMENT_RECEIVED === status;
        },

        cancelled(documentStatus) {
            const status = documentStatus || DocumentModel.document.status;

            return DocumentStatus.CANCELLED === status;
        },

        getReadableStatus() {
            return readableStatus(DocumentModel.document.status);
        },
    },
};

export default DocumentModel;
