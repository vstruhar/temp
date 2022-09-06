<template>
    <div class="spinner" :style="{
        width: `${size}px`,
        height: `${size}px`,
        border: `${width}px solid transparent`,
        'border-color': color,
        'border-top-color': bgColor,
        }"></div>
</template>

<script>
export default {
    name: "Spinner",

    props: {
        size: {
            default: 30,
        },
        width: {
            default: 5,
        },
        color: {
            type: String,
            default: '#d9d9d9',
        },
        bgColor: {
            type: String,
            default: '#fff',
        },
    },
}
</script>

<style scoped>
.spinner {
    pointer-events: none;
    border-radius: 50%;
    animation: loadingspin 0.7s linear infinite;
}
@keyframes loadingspin {
    100% {
        transform: rotate(360deg)
    }
}
</style>
