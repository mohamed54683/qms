<template>
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed top-4 right-4 z-50 max-w-md w-full animate-bounce-in"
        >
            <div
                :class="[
                    'rounded-xl shadow-2xl p-5 backdrop-blur-sm border-l-4',
                    typeClasses[type]
                ]"
            >
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <!-- Success Icon -->
                        <svg v-if="type === 'success'" class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <!-- Error Icon -->
                        <svg v-else-if="type === 'error'" class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <!-- Warning Icon -->
                        <svg v-else-if="type === 'warning'" class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <!-- Info Icon -->
                        <svg v-else class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p :class="['text-sm font-bold', titleColorClasses[type]]">
                            {{ title }}
                        </p>
                        <p v-if="message" :class="['mt-1 text-sm', messageColorClasses[type]]">
                            {{ message }}
                        </p>
                    </div>
                    <button
                        @click="close"
                        :class="['rounded-md inline-flex p-1 hover:bg-opacity-20 hover:bg-gray-500 focus:outline-none transition-colors', buttonColorClasses[type]]"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <!-- Progress bar -->
                <div v-if="autoClose" class="mt-3 h-1 bg-gray-200 rounded-full overflow-hidden">
                    <div 
                        :class="['h-full transition-all', progressColorClasses[type]]"
                        :style="{ width: `${progress}%`, transitionDuration: `${duration}ms` }"
                    ></div>
                </div>
            </div>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'warning', 'info'].includes(value)
    },
    title: {
        type: String,
        required: true
    },
    message: {
        type: String,
        default: ''
    },
    duration: {
        type: Number,
        default: 5000
    },
    autoClose: {
        type: Boolean,
        default: true
    }
});

const emit = defineEmits(['close']);

const show = ref(false);
const progress = ref(100);
let timer = null;
let progressTimer = null;

const typeClasses = {
    success: 'bg-white border-green-500 shadow-green-100',
    error: 'bg-white border-red-500 shadow-red-100',
    warning: 'bg-white border-yellow-500 shadow-yellow-100',
    info: 'bg-white border-blue-500 shadow-blue-100'
};

const titleColorClasses = {
    success: 'text-green-900',
    error: 'text-red-900',
    warning: 'text-yellow-900',
    info: 'text-blue-900'
};

const messageColorClasses = {
    success: 'text-green-700',
    error: 'text-red-700',
    warning: 'text-yellow-700',
    info: 'text-blue-700'
};

const buttonColorClasses = {
    success: 'text-green-600',
    error: 'text-red-600',
    warning: 'text-yellow-600',
    info: 'text-blue-600'
};

const progressColorClasses = {
    success: 'bg-green-500',
    error: 'bg-red-500',
    warning: 'bg-yellow-500',
    info: 'bg-blue-500'
};

const close = () => {
    show.value = false;
    if (timer) clearTimeout(timer);
    if (progressTimer) clearInterval(progressTimer);
    emit('close');
};

onMounted(() => {
    show.value = true;
    if (props.autoClose && props.duration > 0) {
        // Animate progress bar
        progress.value = 0;
        
        timer = setTimeout(() => {
            close();
        }, props.duration);
    }
});
</script>

<style scoped>
@keyframes bounce-in {
    0% {
        transform: scale(0.95);
        opacity: 0;
    }
    50% {
        transform: scale(1.02);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-bounce-in {
    animation: bounce-in 0.3s ease-out;
}
</style>
