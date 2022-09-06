<template>
    <jet-modal :show="show" max-width="lg" :closeable="true" @close="show = false">
        <div class="max-w-7xl mx-auto mt-20">
            <div class="bg-white shadow-xl rounded-xl">
                <global-search/>
            </div>
        </div>
    </jet-modal>
</template>

<script>
import JetModal from '@/Jetstream/Modal.vue'
import EventBus from "@/Services/EventBus";
import GlobalSearch from "@/Jetstream/GlobalSearch";

export default {
    name: "GlobalSearchModal",

    components: {
        GlobalSearch,
        JetModal,
    },

    data() {
        return {
            show: false,
        };
    },

    mounted() {
        EventBus.on('global:search:open', () => {
            this.open();
        });

        EventBus.on('global:search:close', () => {
            this.close();
        });

        // open on Cmd + K / Ctrl + K
        document.addEventListener('keydown', event => {
            if ((event.ctrlKey || event.metaKey) && (event.key.toLowerCase() === 'k') && !this.show) {
                this.open();
            }
        });
    },

    methods: {
        open() {
            this.show = true;
            this.$nextTick(() => document.querySelector('.autocomplete.global-search input').focus());
        },

        close() {
            this.show = false;
        },
    },
}
</script>
