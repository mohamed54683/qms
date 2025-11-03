<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div 
        class="relative min-h-screen bg-cover bg-center bg-no-repeat"
        style="background-image: url('/bg1.jpg')"
    >
        <!-- Overlay for better text visibility -->
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        
        <div class="relative flex min-h-screen flex-col items-center pt-6 sm:justify-center sm:pt-0">
            <div class="mt-6 w-full overflow-hidden bg-white bg-opacity-95 backdrop-blur-sm px-8 py-6 shadow-2xl sm:max-w-md sm:rounded-xl border border-gray-200">
                <slot />
            </div>
        </div>
    </div>
</template>

<!-- Login/Register Buttons Enhancement -->
<style scoped>
.btn-login, .btn-register {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
    transition: all 0.3s ease;
}
.btn-login:hover, .btn-register:hover {
    transform: translateY(-2px);
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
}
.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    border: none !important;
    padding: 12px 24px !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
}
.btn-primary:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3) !important;
}
</style>
