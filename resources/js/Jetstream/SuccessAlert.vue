<template>
    <div v-if="message" class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-4 rounded-md">
        {{ message }}
    </div>
</template>

<script>
    import { defineComponent } from 'vue'

    export default defineComponent({
        name: "SuccessAlert",

        props: {
            message: String,
        },
    });
</script>
