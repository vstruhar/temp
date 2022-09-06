require('./bootstrap');

import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/inertia-vue3'
import {InertiaProgress} from '@inertiajs/progress'
import VTooltipPlugin from 'v-tooltip'
import HelperMixin from '@/Mixins/HelperMixin'
import InputRestrict from '@/Directives/InputRestrict'
import {VueClipboard} from '@soerenmartius/vue3-clipboard'

import './Plugins/axios'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        const page = require(`./Pages/${name}`).default;
        // page.layout = page.layout || AppLayout; // disabled persistent layouts
        return page;
    },
    setup({el, app, props, plugin}) {
        return createApp({render: () => h(app, props)})
            .use(plugin)
            .use(VTooltipPlugin)
            .use(VueClipboard)
            .mixin({methods: {route}})
            .mixin(HelperMixin)
            .directive('allow', InputRestrict)
            .mount(el)
    },
});

InertiaProgress.init({color: '#ffffff'});
