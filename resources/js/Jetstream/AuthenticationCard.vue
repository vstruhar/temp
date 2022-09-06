<template>
    <div class="bg-gray-200 w-full h-full min-h-screen pt-20">
        <div class="w-full max-w-sm p-6 m-auto bg-white rounded-md shadow-md dark:bg-gray-800">
            <div>
                <slot name="logo" />
            </div>

            <div>
                <slot />
            </div>
        </div>
    </div>
</template>
