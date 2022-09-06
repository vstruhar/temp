<template>
    <div>
        <Head :title="title"/>

        <jet-loader-overlay/>

        <global-search-modal/>

        <div v-if="$page.props.admin_impersonating_client" class="bg-red-500 text-center text-white font-bold p-3 tracking-wide">Administrátor prihlásený na účet klienta</div>

        <div class="min-h-screen bg-gray-100 flex flex-col">
            <nav class="bg-indigo-600 shadow-md">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto px-4 sm:px-2 lg:px-8">
                    <div class="flex justify-between h-16">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex-shrink-0 flex items-center">
                                <Link :href="route('dashboard')">
                                    <jet-application-mark class="h-10 w-auto"/>
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div class="hidden ml-2 md:ml-5 sm:flex z-10">
                                <jet-nav-link v-if="can('dashboard:show')" :href="route('dashboard')" :active="route().current('dashboard')">
                                    {{ __('Dashboard') }}
                                </jet-nav-link>

                                <jet-nav-link v-if="can('documents:invoice:list')" :href="route('documents', 'invoices')" :active="route().current('documents*')">
                                    {{ __('Invoices') }}
                                </jet-nav-link>

                                <jet-nav-link v-if="can('client:list')" :href="route('clients')" :active="route().current('clients*')">
                                    {{ __('Clients') }}
                                </jet-nav-link>

                                <jet-nav-link :href="route('tools.print.index')" :active="route().current('tools*')">
                                    {{ __('Tools') }}
                                </jet-nav-link>

                                <div class="flex items-center">
                                    <button @click="openGlobalSearch" class="text-white rounded-full p-2 bg-indigo-700 hover:bg-indigo-800/75 active:bg-indigo-900 focus:outline-none transition">
                                        <svg class="w-4 h-4" fill="currentColor" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M23.822 20.88l-6.353-6.354c.93-1.465 1.467-3.2 1.467-5.059.001-5.219-4.247-9.467-9.468-9.467s-9.468 4.248-9.468 9.468c0 5.221 4.247 9.469 9.468 9.469 1.768 0 3.421-.487 4.839-1.333l6.396 6.396 3.119-3.12zm-20.294-11.412c0-3.273 2.665-5.938 5.939-5.938 3.275 0 5.94 2.664 5.94 5.938 0 3.275-2.665 5.939-5.94 5.939-3.274 0-5.939-2.664-5.939-5.939z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="hidden sm:flex space-x-2 xl:space-x-3 sm:items-center sm:ml-3">
                            <div class="relative" v-if="can('settings:show')">
                                <Link :href="route('settings.contact_and_invoice')"
                                      :class="route().current('settings*') ? 'bg-indigo-800' : 'bg-indigo-700'"
                                      class="inline-flex items-center h-9 pl-1.5 pr-2.5 border border-transparent rounded-lg font-bold text-sm text-gray-200 tracking-wide hover:bg-indigo-800/75 active:bg-indigo-900 focus:outline-none transition">
                                    <CogIcon class="w-4 h-4 -mr-1 xl:mr-2"/>
                                    <div class="hidden xl:block">{{ __('Settings') }}</div>
                                </Link>
                            </div>

                            <div class="relative">
                                <!-- Teams Dropdown -->
                                <jet-dropdown align="right" width="60" v-if="$page.props.jetstream.hasTeamFeatures">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center pl-3 pr-2 py-2 max-w-[220px] border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:bg-gray-50 hover:text-gray-700 focus:outline-none focus:bg-gray-50 active:bg-gray-50 transition">
                                                <span class="truncate">{{ $page.props.user.current_team.name }}</span>

                                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <!-- Team Management -->
                                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                                <template v-if="false">
                                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                                        {{ __('Manage company') }}
                                                    </div>

                                                    <!-- Team Settings -->
                                                    <jet-dropdown-link :href="route('company.show', $page.props.user.current_team)">
                                                        {{ __('Settings') }}
                                                    </jet-dropdown-link>

                                                    <jet-dropdown-link :href="route('company.create')" v-if="false && $page.props.jetstream.canCreateTeams && $page.props.admin_impersonating_client">
                                                        {{ __('Create new company') }}
                                                    </jet-dropdown-link>

                                                    <div class="border-t border-gray-100"></div>
                                                </template>

                                                <!-- Company Switcher -->
                                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <jet-dropdown-link as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </jet-dropdown-link>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative hidden md:block">
                                <jet-dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.user.profile_photo_url" :alt="$page.props.user.name" />
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center pl-3 pr-2 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition whitespace-nowrap max-w-[120px]">
                                                <span class="truncate">{{ $page.props.user.name }}</span>

                                                <svg class="ml-1 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <jet-dropdown-link :href="route('profile.show')">
                                            {{ __('My profile') }}
                                        </jet-dropdown-link>

                                        <jet-dropdown-link :href="route('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                            API Tokens
                                        </jet-dropdown-link>

                                        <div class="border-t border-gray-100"></div>

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <jet-dropdown-link as="button" text-color="red">
                                                {{ __('Logout') }}
                                            </jet-dropdown-link>
                                        </form>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <!-- Languages Dropdown -->
                            <div class="relative">
                                <jet-dropdown align="right" width="24" class="mt-0.5">
                                    <template #trigger>
                                        <button type="button" class="inline-flex items-center px-2 py-1 h-9 border border-transparent rounded-md bg-indigo-700 hover:bg-indigo-800 active:bg-indigo-900 focus:outline-none transition">
                                            <jet-flag :code="$page.props.locale.current" class="block w-7 h-7"/>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="divide-y divide-gray-400 divide-opacity-25">
                                            <jet-dropdown-link v-for="langCode in $page.props.locale.languages"
                                                               as="button"
                                                               @click="setLanguage(langCode)"
                                                               classes="px-3 py-2">
                                                <jet-flag :code="langCode" class="block w-7 h-7"/>
                                            </jet-dropdown-link>
                                        </div>
                                    </template>
                                </jet-dropdown>
                            </div>

                            <form @submit.prevent="logout">
                                <button type="submit" v-tooltip="{content: __('Logout'), delay: {show: 1000}}" class="font-bold inline-flex items-center pl-3 pr-2.5 py-2 h-9 tracking-wider border border-red-600 rounded-md text-white text-xs bg-red-500 hover:bg-red-600 active:bg-red-600 focus:outline-none transition">
                                    <svg class="w-4 h-4 fill-current" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                        <path d="M16 9v-4l8 7-8 7v-4h-8v-6h8zm-16-7v20h14v-2h-12v-16h12v-2h-14z"/>
                                    </svg>
                                </button>
                            </form>
                        </div>

                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = ! showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md hover:text-gray-500 hover:bg-gray-100 focus:outline-none transition"
                                    :class="{'bg-gray-100 text-gray-500': showingNavigationDropdown, 'text-white': !showingNavigationDropdown}">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{'hidden': showingNavigationDropdown, 'inline-flex': ! showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{'hidden': ! showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div :class="{'block': showingNavigationDropdown, 'hidden': ! showingNavigationDropdown}" class="sm:hidden bg-white">
                    <div class="pt-2 pb-3 space-y-1">
                        <jet-responsive-nav-link v-if="can('dashboard:show')" :href="route('dashboard')" :active="route().current('dashboard')">
                            {{ __('Dashboard') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('documents:invoice:list')" :href="route('documents', 'invoices')" :active="route().current('documents*', 'invoices')" font-weight="bold">
                            {{ __('Invoices') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('documents:proforma_invoice:list')" :href="route('documents', 'proforma-invoices')" :active="route().current('documents*', 'proforma-invoices')" font-weight="bold">
                            {{ __('Proforma invoices') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('documents:quotation:list')" :href="route('documents', 'quotations')" :active="route().current('documents*', 'quotations')" font-weight="bold">
                            {{ __('Quotations') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('documents:credit_note:list')" :href="route('documents', 'credit-notes')" :active="route().current('documents*', 'credit-notes')" font-weight="bold">
                            {{ __('Credit notes') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('client:list')" :href="route('clients')" :active="route().current('clients*')">
                            {{ __('Clients') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link :href="route('tools.print.index')" :active="route().current('tools*')">
                            {{ __('Tools') }}
                        </jet-responsive-nav-link>

                        <jet-responsive-nav-link v-if="can('settings:show')" :href="route('settings.contact_and_invoice')" :active="route().current('settings*')" text-color="indigo">
                            {{ __('Settings') }}
                        </jet-responsive-nav-link>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div class="py-1">
                        <div class="space-y-1">
                            <div class="border-t-4 border-gray-200 block px-4 py-2 text-sm text-gray-400 bg-gray-100">
                                {{ __('Profile setting') }}
                            </div>

                            <jet-responsive-nav-link :href="route('profile.show')" :active="route().current('profile.show')">
                                {{ __('My profile') }}
                            </jet-responsive-nav-link>

                            <jet-responsive-nav-link :href="route('api-tokens.index')" :active="route().current('api-tokens.index')" v-if="$page.props.jetstream.hasApiFeatures">
                                API Tokens
                            </jet-responsive-nav-link>

                            <!-- Authentication -->
                            <form method="POST" @submit.prevent="logout">
                                <jet-responsive-nav-link as="button" text-color="red">
                                    {{ __('Logout') }}
                                </jet-responsive-nav-link>
                            </form>

                            <!-- Team Management -->
                            <template v-if="$page.props.jetstream.hasTeamFeatures">
                                <template v-if="false">
                                    <div class="border-t border-gray-200 block px-4 py-2 text-sm text-gray-400 bg-gray-100">
                                        {{ __('Manage company') }}
                                    </div>

                                    <!-- Team Settings -->
                                    <jet-responsive-nav-link :href="route('company.show', $page.props.user.current_team)" :active="route().current('company.show')">
                                        {{ __('Settings') }}
                                    </jet-responsive-nav-link>

                                    <jet-responsive-nav-link :href="route('company.create')" :active="route().current('company.create')" v-if="$page.props.jetstream.canCreateTeams">
                                        {{ __('Create new company') }}
                                    </jet-responsive-nav-link>

                                    <div class="border-t border-gray-200"></div>
                                </template>

                                <!-- Team Switcher -->
                                <template v-for="team in $page.props.user.all_teams" :key="team.id">
                                    <form @submit.prevent="switchToTeam(team)">
                                        <jet-responsive-nav-link as="button">
                                            <div class="flex items-center">
                                                <svg v-if="team.id == $page.props.user.current_team_id" class="mr-2 h-5 w-5 text-green-400" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <div>{{ team.name }}</div>
                                            </div>
                                        </jet-responsive-nav-link>
                                    </form>
                                </template>

                                <div class="border-t border-gray-200 block px-4 py-2 text-sm text-gray-400 bg-gray-100">
                                    {{ __('Languages') }}
                                </div>

                                <jet-responsive-nav-link v-for="langCode in $page.props.locale.languages"
                                                         :href="route('language.set_locale', langCode)"
                                                         :active="$page.props.locale.current === langCode"
                                                         as="a"
                                                         v-if="$page.props.jetstream.canCreateTeams"
                                                         class="inline-block">
                                    <jet-flag :code="langCode" class="block w-8 h-8"/>
                                </jet-responsive-nav-link>
                            </template>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Invoice menu items -->
            <nav class="bg-indigo-700 shadow-md hidden sm:block" v-if="route().current('documents*')">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-11">
                        <div class="flex">
                            <jet-nav-sub-link v-if="can('documents:invoice:list')" :href="route('documents', 'invoices')" :active="route().current('documents*', 'invoices')">
                                {{ __('Invoices') }}
                            </jet-nav-sub-link>

                            <jet-nav-sub-link v-if="can('documents:proforma_invoice:list')" :href="route('documents', 'proforma-invoices')" :active="route().current('documents*', 'proforma-invoices')">
                                {{ __('Proforma invoices') }}
                            </jet-nav-sub-link>

                            <jet-nav-sub-link v-if="can('documents:quotation:list')" :href="route('documents', 'quotations')" :active="route().current('documents*', 'quotations')">
                                {{ __('Quotations') }}
                            </jet-nav-sub-link>

                            <jet-nav-sub-link v-if="can('documents:credit_note:list')" :href="route('documents', 'credit-notes')" :active="route().current('documents*', 'credit-notes')">
                                {{ __('Credit notes') }}
                            </jet-nav-sub-link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Tools menu items -->
            <nav class="bg-indigo-700 shadow-md hidden sm:block" v-if="route().current('tools*')">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between h-10">
                        <div class="flex">
                            <jet-nav-sub-link :href="route('tools.print.index')" :active="route().current('tools.print*')">
                                {{ __('Print') }}
                            </jet-nav-sub-link>

                            <jet-nav-sub-link v-if="can('tools:export:list')" :href="route('tools.export.index')" :active="route().current('tools.export*')">
                                {{ __('Export') }}
                            </jet-nav-sub-link>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header class="bg-white shadow" v-if="$slots.header" :class="{'mt-12 sm:mt-0': showingNavigationDropdown}">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header"></slot>
                </div>
            </header>

            <jet-banner/>

            <!-- Page Content -->
            <main class="max-w-7xl mx-auto py-6 lg:py-10 px-3 sm:px-6 lg:px-8 flex-grow w-full">
                <slot></slot>
            </main>

            <Footer bg-white/>
        </div>
    </div>
</template>

<script>
    import Footer from '@/Pages/Home/Footer.vue'
    import JetApplicationMark from '@/Jetstream/ApplicationMark.vue'
    import JetBanner from '@/Jetstream/Banner.vue'
    import JetDropdown from '@/Jetstream/Dropdown.vue'
    import JetDropdownLink from '@/Jetstream/DropdownLink.vue'
    import JetFlag from '@/Jetstream/Flag.vue'
    import JetLoaderOverlay from '@/Jetstream/LoaderOverlay.vue'
    import JetNavLink from '@/Jetstream/NavLink.vue'
    import JetNavSubLink from '@/Jetstream/NavSubLink.vue'
    import JetResponsiveNavLink from '@/Jetstream/ResponsiveNavLink.vue'
    import { defineComponent } from 'vue'
    import { Head, Link } from '@inertiajs/inertia-vue3'
    import {CogIcon, SearchIcon} from '@heroicons/vue/solid'
    import {LogoutIcon} from '@heroicons/vue/outline'
    import moment from "moment";
    import EventBus from "@/Services/EventBus";
    import GlobalSearchModal from "@/Pages/Modals/GlobalSearchModal";

    export default defineComponent({
        props: {
            title: String,
        },

        components: {
            GlobalSearchModal,
            CogIcon,
            SearchIcon,
            LogoutIcon,
            Footer,
            Head,
            JetApplicationMark,
            JetBanner,
            JetDropdown,
            JetDropdownLink,
            JetFlag,
            JetLoaderOverlay,
            JetNavLink,
            JetNavSubLink,
            JetResponsiveNavLink,
            Link,
        },

        data() {
            return {
                showingNavigationDropdown: false,
            }
        },

        beforeCreate() {
            let languageCode = this.$page.props.locale.current;

            switch (languageCode) {
                case 'rs': languageCode = 'sr'; break;
            }

            moment.locale(languageCode);
        },

        methods: {
            switchToTeam(team) {
                this.$inertia.put(route('current-team.update'), {
                    'team_id': team.id
                }, {
                    preserveState: false
                })
            },

            setLanguage(code) {
                window.localStorage.setItem('language', code);
                window.location.href = route('language.set_locale', code);
            },

            openGlobalSearch() {
                EventBus.emit('global:search:open');
            },

            logout() {
                this.$inertia.post(route('logout'));
            },
        }
    })
</script>
