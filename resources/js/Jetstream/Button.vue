<template>
    <button :type="type"
            :class="[
                block ? 'w-full block p-3' : 'inline-flex px-4 py-2',
                `bg-${color}-${shade} border-${color}-${shade-100} hover:bg-${color}-${shade-100} active:bg-${color}-${shade+200} focus:border-${color}-${shade+200}`,
                textColor,
            ]"
            class="items-center border rounded-md font-semibold text-xs uppercase tracking-widest whitespace-nowrap focus:outline-none focus:ring focus:ring-gray-300 disabled:opacity-25 disabled:cursor-default transition">
        <slot></slot>
    </button>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        props: {
            type: {
                type: String,
                default: 'submit',
            },
            textColor: {
                type: String,
                default: 'text-white',
            },
            color: {
                type: String,
                default: 'gray',
            },
            shade: {
                type: Number,
                default: 800,
            },
            block: {
                type: Boolean,
                default: false,
            },
        }
    })
</script>
