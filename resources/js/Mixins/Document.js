import omit from 'lodash/omit'
import round from 'lodash/round'
import Currencies from "@/Data/Currencies";

export default {
    computed: {
        selectedCurrency() {
            return Currencies.find(i => i.code === this.form.invoice_currency);
        },

        clientsDropdownItems() {
            return this.clients.map(client => {
                return {
                    value: client.id,
                    label: client.name,
                };
            });
        },

        taxable() {
            if (this.document === null || this.issuingRealInvoice) {
                return this.isTaxPayer;
            }
            return this.document.taxable;
        },
    },

    watch: {
        'form.client_id'() {
            if (!this.form.client_id) return null;

            const client = this.clients.find(c => c.id === this.form.client_id);

            // update client in form, it will be saved as json
            this.form.client = omit(client, ['contacts', 'updated_at', 'created_at']);
        },
    },

    methods: {
        addItem() {
            this.form.items.push({
                name: null,
                price: 0,
                subtotal_without_tax: 0,
                price_with_tax: 0,
                tax: this.taxable ? this.companyDetails.default_tax_percentage : 0,
                quantity: 1,
                unit: 'ks',
                image: null,
                discount_amount: 0,
                discount_percent: 0,
                description: null,
                descriptionToggled: false,
                discountToggled: false,
            });
        },

        toggleItemDiscount(item) {
            item.discountToggled = !item.discountToggled;

            if (!item.discountToggled) {
                item.discount_amount = 0;
                item.discount_percent = 0;
                this.calculateItem(item);
            }
        },

        deleteItem() {
            this.form.items.splice(this.documentItemDeleteIndex, 1);
            this.documentItemDeleteIndex = null;
            this.calculateSummary();
        },

        calculateAll() {
            this.form.items.forEach(item => {
                this.calculateItem(item, item.calculated_from_subtotal)
            });
            this.calculateSummary();
        },

        calculateItem(item, calculateFromSubtotal = false) {
            if (calculateFromSubtotal) {
                let result = 0;

                if (this.taxable) {
                    const tax = item.tax / 100 + 1;

                    result = Number(item.price_with_tax) / tax;
                    result = result / item.quantity;
                } else {
                    if (!item.subtotal_without_tax) {
                        item.subtotal_without_tax = item.price * item.quantity;
                    }
                    result = Number(item.subtotal_without_tax) / item.quantity;
                }
                result += item.discount_amount;

                item.price = result;
            } else {
                let subtotal = Number(item.price) * Number(item.quantity);

                if (item.discount_amount) {
                    subtotal -= Number(item.discount_amount);
                }
                if (this.taxable) {
                    let tax = subtotal * item.tax / 100;

                    item.price_with_tax = subtotal + tax;
                } else {
                    item.price_with_tax = 0;
                }
                item.subtotal_without_tax = subtotal;
            }

            item.calculated_from_subtotal = calculateFromSubtotal;

            this.calculateSummary();
        },

        calculateSummary() {
            this.form.amount_with_tax = 0;
            this.form.amount = 0;
            this.form.discount = 0;
            this.form.tax = 0;
            this.form.subtotal_without_tax = 0;

            this.form.items.forEach(item => {
                const priceWithoutTax = Number(item.price) * Number(item.quantity) - Number(item.discount_amount);
                const priceWithTax = Number(item.price_with_tax);

                if (item.discount_amount) {
                    this.form.discount += Number(item.discount_amount);
                }

                if (this.taxable) {
                    this.form.tax += round(priceWithTax - priceWithoutTax, 2);
                    this.form.amount_with_tax += round(priceWithTax, 2);
                }

                if (this.taxable && item.tax === 0) {
                    // show subtotal without tax when 0 is used as tax
                    this.form.subtotal_without_tax += round(priceWithoutTax, 2);
                } else {
                    this.form.amount += round(priceWithoutTax, 2);
                }
            });
        },

        // calculate percent to number or reverse
        calculateDiscount(value) {
            this.form.items.forEach(item => {
                let subtotal = Number(item.price) * Number(item.quantity);

                if (value == 'percentage') {
                    const result = Number(subtotal * (item.discount_percent / 100));
                    item.discount_amount = round(result, 2);
                } else {
                    const result = Number((item.discount_amount / subtotal) * 100);
                    item.discount_percent = round(result, 2);
                }
            });
        },

        removeItem(index) {
            this.form.items.splice(index, 1);
            this.calculateSummary();
        },

        populateItem(item, data) {
            item.name = data.name;
            item.unit = data.unit;

            item.price = this.type === 'credit-notes'
                ? (data.price * -1)
                : data.price;

            this.calculateItem(item);
        },
    },
}
