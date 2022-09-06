import axios from "axios";
import {debounce} from "lodash";

export default {
    watch: {
        'form.invoice_number_id'(value, oldValue) {
            const selected     = this.documentNumberOptions.find(i => i.value == value);
            const prevSelected = this.documentNumberOptions.find(i => i.value == oldValue);

            if (value === 0) {
                this.form.variable_symbol = '';
                this.form.name            = '';
                if (prevSelected && prevSelected.next_number === this.form.number) this.form.number = '';
            } else {
                if (this.form.number == this.form.variable_symbol || this.form.variable_symbol == '') {
                    this.form.variable_symbol = selected?.next_number;
                }
                switch (this.$options.name) {
                    case 'CreateInvoice':
                        if (this.form.name == (this.__('Invoice') + ': ' + this.form.number) || this.form.name == '') this.form.name = this.__('Invoice') + ': ' + selected?.next_number;
                        break;
                    case 'CreateCreditNote':
                        if (this.form.name == (this.__('Credit note for invoice') +' '+this.form.number) || this.form.name == '') this.form.name = this.__('Credit note for invoice') +' '+this.document.number;
                        break;
                    case 'CreateProformaInvoice':
                        if (this.form.name == (this.__('Proforma invoice') +': '+this.form.number) || this.form.name == '') this.form.name = this.__('Proforma invoice') +': '+selected?.next_number;
                        break;
                }
                this.form.number = selected?.next_number;
            }
        },

        'form.number'(value) {
            const documentNumber = this.documentNumberOptions.find(i => i?.next_number === value);
            this.form.invoice_number_id = documentNumber ? documentNumber.value : 0;
        },

        'form.date_created': debounce(function (date) {
            axios.get(route('documents.company.document_numbers', {type: this.type, company: this.companyDetails.company_id, date}))
                .then(({data}) => {
                    this.documentNumberOptions.forEach(item => {
                        item.next_number = data.items.find(i => i.value === item.value)?.next_number;
                    });

                    if (this.form.invoice_number_id !== null) {
                        const selected = this.documentNumberOptions.find(i => i.value === this.form.invoice_number_id);

                        let documentName = this.__('Invoice');

                        if (this.$options.name === 'CreateCreditNote') documentName = this.__('Credit note');
                        if (this.$options.name === 'CreateProformaInvoice') documentName = this.__('Proforma invoice');
                        if (this.$options.name === 'CreateQuotation') documentName = this.__('Quotation');

                        this.form.number = selected?.next_number;
                        this.form.name = documentName +': '+selected?.next_number;
                        this.form.variable_symbol = selected?.next_number;
                    }
                });
        }, 400),

        'form.client_id'() {
            this.form.generate_qr_code = this.canGenerateQrCode;
        },
    },
};
