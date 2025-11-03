<template>
    <img 
        src="/logo.png" 
        alt="QMS Logo" 
        class="application-logo"
        :style="{
            height: height || 'auto',
            width: width || 'auto',
            maxHeight: maxHeight || '50px',
            maxWidth: maxWidth || '200px'
        }"
    />
</template>

<script setup>
defineProps({
    height: {
        type: String,
        default: null
    },
    width: {
        type: String,
        default: null
    },
    maxHeight: {
        type: String,
        default: '50px'
    },
    maxWidth: {
        type: String,
        default: '200px'
    }
})
</script>

<style scoped>
.application-logo {
    object-fit: contain;
    transition: all 0.3s ease;
}

.application-logo:hover {
    transform: scale(1.05);
}
</style>
