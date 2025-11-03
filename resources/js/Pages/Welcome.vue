<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: Boolean,
    canRegister: Boolean,
    laravelVersion: String,
    phpVersion: String,
});
</script>

<template>
    <Head title="QMS - Quality Management System" />
    <div 
        class="relative min-h-screen bg-cover bg-center bg-no-repeat"
        style="background-image: url('/bg1.jpg')"
    >
        <div class="absolute inset-0 bg-black bg-opacity-50"></div>
        
        <div class="relative flex min-h-screen flex-col items-center justify-center text-white">
            <div class="text-center mb-12">
                <img 
                    src="/logo.png" 
                    alt="QMS Logo" 
                    class="mx-auto mb-6 h-24 w-auto drop-shadow-2xl"
                />
                <h1 class="text-5xl font-bold mb-4 drop-shadow-lg">
                    Quality Management System
                </h1>
                <p class="text-xl text-gray-200 drop-shadow-md">
                    Professional Restaurant Quality Control & Store Visits Management
                </p>
            </div>

            <div v-if="canLogin" class="flex flex-col sm:flex-row gap-4 mb-8">
                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="px-8 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white text-lg font-semibold rounded-lg shadow-xl hover:from-blue-700 hover:to-blue-800 transform hover:scale-105 transition-all duration-200"
                >
                    Go to Dashboard
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="px-8 py-4 bg-gradient-to-r from-green-600 to-green-700 text-white text-lg font-semibold rounded-lg shadow-xl hover:from-green-700 hover:to-green-800 transform hover:scale-105 transition-all duration-200"
                    >
                        Login
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="px-8 py-4 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-lg font-semibold rounded-lg shadow-xl hover:from-purple-700 hover:to-purple-800 transform hover:scale-105 transition-all duration-200"
                    >
                        Register
                    </Link>
                </template>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto px-6">
                <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-lg p-6 text-center shadow-xl">
                    <div class="text-3xl text-blue-600 mb-4"></div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Store Visits</h3>
                    <p class="text-gray-600">Comprehensive restaurant quality audits</p>
                </div>
                
                <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-lg p-6 text-center shadow-xl">
                    <div class="text-3xl text-green-600 mb-4"></div>
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Action Plans</h3>
                    <p class="text-gray-600">Track improvements and corrections</p>
                </div>
                
                <div class="bg-white bg-opacity-90 backdrop-blur-sm rounded-lg p-6 text-center shadow-xl">
                    <div class="text-3xl text-purple-600 mb-4"></div>
                    <h3 class="text-lg font-semibent text-gray-800 mb-2">Reports</h3>
                    <p class="text-gray-600">Professional PDF and Excel reports</p>
                </div>
            </div>
        </div>
    </div>
</template>
