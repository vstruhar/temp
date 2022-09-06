<template>
    <Listbox v-model="localValue" :disabled="isDisabled || disabled">
        <div class="relative mt-1">
            <ListboxButton :style="{height: `${height}px`}" :class="{'bg-gray-50': isDisabled || disabled}" class="headlessui-listbox-button w-full relative h-10 py-2 pl-3 pr-10 text-left bg-white border border-gray-300 rounded-md shadow-sm cursor-default focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 text-sm">
                <span class="block truncate flex" :class="{'text-gray-600': isDisabled || disabled}">
                    <img v-if="selectedOption && selectedOption.icon" :src="selectedOption.icon" class="w-4 h-4 mr-3"/>
                    {{ selectedOptionLabel }}
                </span>
                <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                    <SelectorIcon class="w-5 h-5 text-gray-400" aria-hidden="true"/>
                </span>
            </ListboxButton>

            <transition
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <ListboxOptions class="absolute w-full py-1 mt-1 overflow-auto bg-white rounded-md shadow-lg max-h-60 ring-1 ring-black ring-opacity-5 focus:outline-none text-sm z-50">
                    <ListboxOption
                        v-slot="{active, selected}"
                        v-for="option in options"
                        :key="option.value"
                        :value="option.value"
                        as="template"
                    >
                        <li :class="[active ? 'text-gray-900 bg-indigo-50' : 'text-gray-900', 'cursor-default select-none relative py-2 pl-9 pr-4', selected ? 'bg-gray-50' : '']">
                            <span :class="[selected ? 'font-medium' : 'font-normal', 'block truncate flex']">
                                <img v-if="option.icon" :src="option.icon" class="w-4 h-4 mr-3"/>{{ option.label }}
                            </span>
                            <span v-if="selected" class="absolute inset-y-0 left-0 flex items-center pl-2 text-gray-600">
                                <CheckIcon class="w-4 h-4" aria-hidden="true"/>
                            </span>
                        </li>
                    </ListboxOption>
                </ListboxOptions>
            </transition>
        </div>
    </Listbox>
</template>

<script>
    import {defineComponent} from 'vue'
    import {CheckIcon, SelectorIcon} from '@heroicons/vue/solid'
    import {Listbox, ListboxButton, ListboxOptions, ListboxOption} from '@headlessui/vue'

    export default defineComponent({
        name: "DropdownSelect",

        components: {
            Listbox,
            ListboxButton,
            ListboxOptions,
            ListboxOption,
            CheckIcon,
            SelectorIcon,
        },

        props: {
            value: {
                default: null,
            },
            options: {
                type: Array,
                default: [],
            },
            placeholder: String,
            height: {
                default: '42',
                type: String,
            },
            disabled: {
                default: false,
                type: Boolean,
            },
        },

        data() {
            return {
                localValue: this.value,
            };
        },

        watch: {
            localValue(selection) {
                this.$emit('update:value', selection);
            },
        },

        computed: {
            selectedOption() {
                return this.options.find(o => o.value === this.value);
            },

            selectedOptionLabel() {
                this.localValue = this.value;

                return this.selectedOption
                    ? this.selectedOption.label
                    : this.placeholder || this.__('Select item');
            },

            isDisabled() {
                return this.options.length === 0 || (this.options.length === 1 && !this.options[0].label);
            },
        },
    })
</script>
