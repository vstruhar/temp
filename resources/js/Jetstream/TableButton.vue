<template>
    <button
          class="flex px-2 h-9 justify-center items-center border border-transparent rounded-md text-sm text-white tracking-widest focus:outline-none focus:ring disabled:opacity-25 transition"
          :class="[colorClasses, textColor]"
          :disabled="disabled">
        <slot/>
    </button>
</template>

<script>
    import {defineComponent} from 'vue'

    export default defineComponent({
        props: {
            textColor: {
                type: String,
                default: 'text-white',
            },
            color: {
                type: String,
                default: 'gray',
            },
            disabled: {
                type: Boolean,
                default: false,
            },
        },

        computed: {
            colorClasses() {
                switch (this.color) {
                    case 'gray': return 'bg-gray-500 border-gray-500 hover:bg-gray-600 active:bg-gray-700 focus:border-gray-600 focus:ring-gray-200';
                    case 'green': return 'bg-green-500 border-green-500 hover:bg-green-600 active:bg-green-700 focus:border-green-600 focus:ring-green-200';
                    case 'blue': return 'bg-blue-500 border-blue-500 hover:bg-blue-600 active:bg-blue-700 focus:border-blue-600 focus:ring-blue-200';
                    default: return 'bg-indigo-500 border-indigo-500 hover:bg-indigo-600 active:bg-indigo-700 focus:border-indigo-600 focus:ring-indigo-200';
                }
            },
        },
    })
</script>
