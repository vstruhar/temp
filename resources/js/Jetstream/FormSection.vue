<template>
    <div :class="[withTitle ? 'md:grid md:grid-cols-3 md:gap-6' : 'md:grid md:grid-cols-1 md:gap-6']">
        <jet-section-title v-if="withTitle">
            <template #title><slot name="title"></slot></template>
            <template #description><slot name="description"></slot></template>
        </jet-section-title>

        <div class="mt-5 mt-0 md:col-span-2">
            <form @submit.prevent="$emit('submitted')">
                <div :class="[(withCard ? 'px-4 py-5 bg-white sm:p-6 shadow' : ''), (hasActions ? 'rounded-tl-lg rounded-tr-lg' : 'rounded-lg')]">
                    <div class="grid grid-cols-6 auto-rows-auto gap-6 items-start">
                        <slot name="form"></slot>
                    </div>
                </div>

                <div class="flex items-center justify-end px-4 py-4 bg-gray-50 text-right sm:px-6 shadow rounded-bl-md rounded-br-md relative z-10" v-if="hasActions">
                    <slot name="actions"></slot>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import { defineComponent } from 'vue'
    import JetSectionTitle from './SectionTitle.vue'

    export default defineComponent({
        emits: ['submitted'],

        props: {
            withTitle: {
                type: Boolean,
                default: true,
            },
            withCard: {
                type: Boolean,
                default: true,
            },
        },

        components: {
            JetSectionTitle,
        },

        computed: {
            hasActions() {
                return !! this.$slots.actions
            }
        }
    })
</script>
