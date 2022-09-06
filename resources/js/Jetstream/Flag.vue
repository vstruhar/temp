<template>
    <img v-if="code" :src="`/images/flags/${code.toLowerCase()}.svg`"/>
</template>

<script>
    export default {
        name: "Flag",

        props: {
            code: {
                default: null,
                type: String,
            }
        },
    }
</script>
