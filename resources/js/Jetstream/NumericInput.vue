<template>
    <input
        v-if="!readOnly"
        ref="numeric"
        :placeholder="placeholder"
        :disabled="disabled"
        v-model="amount"
        type="tel"
        @blur="onBlurHandler"
        @input="onInputHandler"
        @focus="onFocusHandler"
        @change="onChangeHandler"
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
    >
    <div v-else ref="readOnly" class="bg-gray-50 text-gray-600 p-2 border border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">{{ amount }}</div>
</template>

<script>
    import {formatMoney, unformat, toFixed} from 'accounting-js'
    import {isNumeric} from 'lodash-contrib'
    import {replace} from 'lodash'

    export default {
        emits: ['update:modelValue', 'change', 'input', 'blur', 'focus'],

        props: {
            /**
             * Maximum value allowed.
             */
            max: {
                type: Number,
                default: Number.MAX_SAFE_INTEGER || 9007199254740991,
                required: false,
            },

            /**
             * Minimum value allowed.
             */
            min: {
                type: Number,
                default: Number.MIN_SAFE_INTEGER || -9007199254740991,
                required: false,
            },

            /**
             * Enable/Disable minus value.
             */
            minus: {
                type: Boolean,
                default: false,
                required: false,
            },

            /**
             * Input placeholder.
             */
            placeholder: {
                type: String,
                default: '',
                required: false,
            },

            /**
             * Value when the input is empty
             */
            emptyValue: {
                type: [Number, String],
                default: '',
                required: false,
            },

            /**
             * Number of decimals.
             * Decimals symbol are the opposite of separator symbol.
             */
            precision: {
                type: Number,
                default: 0,
                required: false,
            },

            precisionWhenNeeded: {
                type: Boolean,
                default: false,
            },

            /**
             * Thousand separator type.
             * Separator props accept either . or , (default).
             */
            separator: {
                type: String,
                default: ',',
                required: false,
            },

            /**
             * Forced thousand separator.
             * Accepts any string.
             */
            thousandSeparator: {
                default: undefined,
                required: false,
                type: String,
            },

            /**
             * Forced decimal separator.
             * Accepts any string.
             */
            decimalSeparator: {
                default: undefined,
                required: false,
                type: String,
            },

            /**
             * The output type used for v-model.
             * It can either be String or Number (default).
             */
            outputType: {
                required: false,
                type: String,
                default: 'Number',
            },

            /**
             * v-model value.
             */
            value: {
                type: [Number, String],
                default: 0,
                required: true,
            },

            /**
             * Hide input and show value in text only.
             */
            readOnly: {
                type: Boolean,
                default: false,
                required: false,
            },

            /**
             * Class for the span tag when readOnly props is true.
             */
            readOnlyClass: {
                type: String,
                default: '',
                required: false,
            },

            disabled: {
                type: Boolean,
                default: false,
                required: false,
            },
        },

        data: () => ({
            amount: '',
        }),

        computed: {
            /**
             * Number type of formatted value.
             * @return {Number}
             */
            amountNumber () {
                return this.unformat(this.amount);
            },

            /**
             * Number type of value props.
             * @return {Number}
             */
            valueNumber () {
                return this.unformat(this.value);
            },

            /**
             * Define decimal separator based on separator props.
             * @return {String} '.' or ','
             */
            decimalSeparatorSymbol () {
                if (typeof this.decimalSeparator !== 'undefined') return this.decimalSeparator;
                if (this.separator === ',') return '.';
                return ',';
            },

            /**
             * Define thousand separator based on separator props.
             * @return {String} '.' or ','
             */
            thousandSeparatorSymbol () {
                if (typeof this.thousandSeparator !== 'undefined') return this.thousandSeparator;
                if (this.separator === '.') return '.';
                if (this.separator === 'space') return ' ';
                return ',';
            },
        },

        watch: {
            /**
             * Watch for value change from other input with same v-model.
             * @param {Number} newValue
             */
            valueNumber (newValue) {
                if (this.$refs.numeric !== document.activeElement) {
                    this.amount = this.format(newValue);
                }
            },

            /**
             * When readOnly is true, replace the span tag class.
             * @param {Boolean} newValue
             * @param {Boolean} oldValue
             */
            readOnly (newValue, oldValue) {
                if (oldValue === false && newValue === true) {
                    this.$nextTick(() => {
                        if (this.readOnlyClass) {
                            this.$refs.readOnly.className = this.readOnlyClass;
                        }
                    })
                }
            },

            /**
             * Immediately reflect separator changes
             */
            separator () {
                this.process(this.valueNumber);
                this.amount = this.format(this.valueNumber);
            },

            /**
             * Immediately reflect precision changes
             */
            precision () {
                this.process(this.valueNumber);
                this.amount = this.format(this.valueNumber);
            },
        },

        mounted () {
            // Set default value props when valueNumber has some value
            if (this.valueNumber || this.isDeliberatelyZero()) {
                this.process(this.valueNumber);
                this.amount = this.format(this.valueNumber);

                // In case of delayed props value.
                setTimeout(() => {
                    this.process(this.valueNumber);
                    this.amount = this.format(this.valueNumber);
                }, 500)
            }

            // Set read-only span element's class
            if (this.readOnly && this.readOnlyClass) this.$refs.readOnly.className = this.readOnlyClass
        },

        methods: {
            /**
             * Handle change event.
             * @param {Object} e
             */
            onChangeHandler (e) {
                this.$emit('change', e)
            },

            /**
             * Handle blur event.
             * @param {Object} e
             */
            onBlurHandler (e) {
                this.$emit('blur', e);

                if (this.amount === null || this.amount === '') {
                    this.amount = this.format(this.emptyValue || 0);
                    this.update(this.amountNumber);
                    this.$emit('input');
                } else {
                    this.amount = this.format(this.amountNumber);
                }
            },

            /**
             * Handle focus event.
             * @param {Object} e
             */
            onFocusHandler (e) {
                this.$emit('focus', e);

                if (this.emptyValue == this.amountNumber) {
                    this.amount = null;
                } else {
                    this.amount = formatMoney(this.amountNumber, {
                        format: '%v',
                        precision: this.getPrecision(this.amountNumber),
                        decimal: this.decimalSeparatorSymbol,
                        thousand: this.thousandSeparatorSymbol,
                    });
                }
            },

            getPrecision(value) {
                if (this.precisionWhenNeeded) {
                    return (value % 1 === 0) ? 0 : Number(this.precision);
                }
                return Number(this.precision);
            },

            /**
             * Handle input event.
             */
            onInputHandler () {
                if (this.amount.length === 0 || this.amount === '-') return;
                this.process(this.amountNumber);
                this.$emit('input');
            },

            /**
             * Validate value before update the component.
             * @param {Number} value
             */
            process (value) {
                if (value >= this.max) this.update(this.max);
                if (value <= this.min) this.update(this.min);
                if ((value >= this.min && value <= this.max) || this.minus) this.update(value);
                if (!this.minus && value < 0) this.min >= 0 ? this.update(this.min) : this.update(0);
            },

            /**
             * Update parent component model value.
             * @param {Number} value
             */
            update (value) {
                const precision = this.getPrecision(value);
                const fixedValue = toFixed(value, precision);
                const output = this.outputType.toLowerCase() === 'string' ? fixedValue : Number(fixedValue);

                if (this.amount !== null && !isNumeric(replace(this.amount.toString(), ',', '.'))) {
                    this.amount = this.format(
                        this.unformat(this.amount)
                    );
                }
                this.$emit('update:modelValue', output);
            },

            /**
             * Format value using symbol and separator.
             * @param {Number} value
             * @return {String}
             */
            format (value) {
                return formatMoney(value, {
                    format: '%v',
                    precision: this.getPrecision(value),
                    decimal: this.decimalSeparatorSymbol,
                    thousand: this.thousandSeparatorSymbol
                })
            },

            /**
             * Remove symbol and separator.
             * @param {Number} value
             * @return {Number}
             */
            unformat (value) {
                const toUnformat = typeof value === 'string' && value === '' ? this.emptyValue : value;
                return unformat(toUnformat, this.decimalSeparatorSymbol)
            },

            /**
             * Check if value was deliberately set to zero and not just evaluated
             * @return {boolean}
             */
            isDeliberatelyZero () {
                return this.valueNumber === 0 && this.value !== '';
            },
        },
    }
</script>
