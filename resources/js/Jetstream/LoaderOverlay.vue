<template>
    <vue-element-loading
        :active="active"
        spinner="bar-fade-scale"
        color="#4338ca"
        :text="__('Please wait')"
        :text-style="{color: '#fff', 'font-size': '1rem', 'font-weight': '600'}"
        size="64"
        duration="1"
        background-color="rgba(0,0,0, 0.45)"
        is-full-screen/>
</template>

<script>
    import VueElementLoading from 'vue-element-loading'
    import EventBus from '@/Services/EventBus'

    export default {
        components: {
            VueElementLoading,
        },

        data() {
            return {
                active: false,
            };
        },

        mounted() {
            EventBus.on('loader:show', () => {
                this.active = true;
            });

            EventBus.on('loader:hide', () => {
                this.active = false;
            });
        },
    }
</script>
