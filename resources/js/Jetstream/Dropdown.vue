<template>
    <div class="relative">
        <div ref="trigger" @click="toggle()">
            <slot name="trigger"></slot>
        </div>

        <!-- Full Screen Dropdown Overlay -->
        <div v-show="open" class="fixed inset-0 z-40 cursor-default" @click="close"></div>

        <teleport to="body">
            <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="transform opacity-0 scale-95"
                enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-75"
                leave-from-class="transform opacity-100 scale-100"
                leave-to-class="transform opacity-0 scale-95">

                <div v-show="open"
                     ref="dropdown"
                     class="absolute z-50 mt-2 rounded-md shadow-lg"
                     :class="[widthClass, alignmentClasses]"
                     style="display: none;"
                     :style="positionStyle"
                     @click="closeOnContentClick && close()">

                    <div class="rounded-md ring-1 ring-black ring-opacity-5" :class="contentClasses">
                        <slot name="content"></slot>
                    </div>
                </div>
            </transition>
        </teleport>
    </div>
</template>

<script>
import { defineComponent, onMounted, onUnmounted, ref } from "vue";

export default defineComponent({
    props: {
        align: {
            default: 'right',
        },
        width: {
            default: '48',
        },
        contentClasses: {
            default: () => ['py-1', 'bg-white'],
        },
        closeOnContentClick: {
            default: true,
        },
    },

    data() {
        return {
            positionStyle: '',
        };
    },

    setup() {
        let open = ref(false);

        const closeOnEscape = (e) => {
            if (open.value && e.keyCode === 27) {
                open.value = false
            }
        };

        onMounted(() => document.addEventListener('keydown', closeOnEscape));
        onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

        return {
            open,
        }
    },

    computed: {
        widthClass() {
            return {
                '96': 'w-96',
                '64': 'w-64',
                '56': 'w-56',
                '52': 'w-52',
                '48': 'w-48',
                '44': 'w-44',
            }[this.width.toString()]
        },

        alignmentClasses() {
            if (this.align === 'left') {
                return 'origin-top-left left-0'
            } else if (this.align === 'right') {
                return 'origin-top-right right-0'
            } else {
                return 'origin-bottom bottom-0'
            }
        },
    },

    methods: {
        toggle() {
            this.open = ! this.open;

            this.$nextTick(() => {
                const button = this.$refs.trigger.getBoundingClientRect();
                const dropdown = this.$refs.dropdown.getBoundingClientRect();

                const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

                const left = (button.left + button.width) - dropdown.width - 9 + scrollLeft;

                this.positionStyle = `left: ${left}px; top: ${button.bottom + scrollTop}px; right: auto;`;
            });
        },

        close() {
            this.open = false;
        },
    },
})
</script>
