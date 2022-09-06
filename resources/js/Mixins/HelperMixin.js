import moment from "moment";
import {formatMoney} from 'accounting-js';
import {get} from 'lodash';
import Currencies from "@/Data/Currencies";

export default {
    methods: {
        __(key, replacements = {}) {
            let translation = window._translations[key] || key;

            Object.keys(replacements).forEach(r => {
                translation = translation.replace(`:${r}`, replacements[r]);
            });

            return translation;
        },

        can(permission) {
            const permissions = this.$page.props.role.permissions;

            if (permissions.length === 1 && permissions[0] === '*') {
                return true;
            }

            return permissions.findIndex(i => i === permission) !== -1;
        },

        role(role) {
            return this.$page.props.role.key === role;
        },

        date(date) {
            return moment(date).format('DD.MM.YYYY');
        },

        datetime(date, separator = ' ') {
            const momentDate = moment(date);
            const formattedDate = momentDate.format('DD.MM.YYYY');
            const formattedTime = momentDate.format('H:mm\\h');

            return formattedDate + separator + formattedTime;
        },

        money(value, config = {}) {
            return formatMoney(value, {
                symbol: config.symbol
                    ? config.symbol
                    : get(this.$.ctx, 'selectedCurrency.symbol', get(this.$.ctx, '_.props.selectedCurrency.symbol', '')), // check context, if not found try props
                format: '%v %s',
                precision: Number(
                    config.hasOwnProperty('precision')
                        ? config.precision
                        : get(this.$.ctx.selectedCurrency, 'precision', get(this.$.ctx, '_.props.selectedCurrency.precision', 2)) // check context, if not found try props
                ),
                decimal: config.decimalSeparator || ',',
                thousand: config.thousandSeparator || '.',
            })
        },

        moneyDocument(value, document) {
            const currency = Currencies.find(i => i.code === document.invoice_currency);

            return this.money(value, {
                symbol: currency?.symbol,
                precision: currency?.precision,
            });
        },
    },
};
