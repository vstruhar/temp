<template>
    <footer :class="bgWhite ? 'bg-white shadow' : 'bg-gray-100'">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="lg:flex justify-between">
                <div class="w-full lg:w-2/5">
                    <div>
                        <div class="text-3xl font-bold text-gray-800 mb-4">Ponfys24</div>

                        <p class="max-w-md mt-2 text-gray-500 text-sm pr-20">
                            {{ __('Issue invoices easily, in a modern design, directly via a web browser on a computer or tablet') }}
                        </p>
                    </div>
                </div>

                <div class="hidden lg:block">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-gray-500 uppercase font-bold text-l mb-6">{{ __('Contact') }}</h3>
                            <span class="block mt-2 text-sm text-gray-600 hover:underline mb-3">+421 940 633 333</span>
                            <span class="block mt-2 text-sm text-gray-600 hover:underline">ponfysacc@gmail.com</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-indigo-600">
            <div class="max-w-7xl mx-auto py-4 sm:px-6 lg:px-8 flex items-center sm:flex-row flex-col min-h-20">
                <p class="text-sm text-gray-300 sm:mt-0 mt-4">© 2021 Ponfys24.sk</p>
                <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                    <a class="text-white">
                      <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                      </svg>
                    </a>
                    <a class="ml-3 text-white">
                      <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                      </svg>
                    </a>
                    <a class="ml-3 text-white">
                      <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <rect width="20" height="20" x="2" y="2" rx="5" ry="5"></rect>
                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37zm1.5-4.87h.01"></path>
                      </svg>
                    </a>
                    <a class="ml-3 text-white">
                      <svg fill="currentColor" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="0" class="w-5 h-5" viewBox="0 0 24 24">
                        <path stroke="none" d="M16 8a6 6 0 016 6v7h-4v-7a2 2 0 00-2-2 2 2 0 00-2 2v7h-4v-7a6 6 0 016-6zM2 9h4v12H2z"></path>
                        <circle cx="4" cy="4" r="2" stroke="none"></circle>
                      </svg>
                    </a>
                </span>
            </div>
        </div>
    </footer>
</template>

<script>
    export default {
        name: "Footer",

        props: {
            bgWhite: {
                type: Boolean,
            },
        },
    }
</script>
