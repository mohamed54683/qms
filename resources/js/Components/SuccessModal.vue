<template>
    <!-- Backdrop -->
    <Transition
        enter-active-class="transition-opacity ease-out duration-300"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
        leave-active-class="transition-opacity ease-in duration-200"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div
            v-if="show"
            class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click="close"
        >
            <!-- Success Modal -->
            <Transition
                enter-active-class="transition-all ease-out duration-300"
                enter-from-class="opacity-0 scale-95 translate-y-4"
                enter-to-class="opacity-100 scale-100 translate-y-0"
                leave-active-class="transition-all ease-in duration-200"
                leave-from-class="opacity-100 scale-100"
                leave-to-class="opacity-0 scale-95"
            >
                <div
                    v-if="show"
                    @click.stop
                    class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden transform"
                >
                    <!-- Animated Success Icon -->
                    <div class="bg-gradient-to-br from-green-400 via-emerald-500 to-teal-600 p-8 relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 opacity-10">
                            <div class="absolute top-0 left-0 w-32 h-32 bg-white rounded-full -translate-x-16 -translate-y-16"></div>
                            <div class="absolute bottom-0 right-0 w-40 h-40 bg-white rounded-full translate-x-20 translate-y-20"></div>
                        </div>
                        
                        <!-- Success Checkmark with Animation -->
                        <div class="relative z-10 flex justify-center">
                            <div class="success-checkmark">
                                <div class="check-icon">
                                    <span class="icon-line line-tip"></span>
                                    <span class="icon-line line-long"></span>
                                    <div class="icon-circle"></div>
                                    <div class="icon-fix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-8 text-center">
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">
                            🎉 {{ title }}
                        </h2>
                        <p class="text-gray-600 text-base leading-relaxed mb-6">
                            {{ message }}
                        </p>
                        
                        <!-- Action Plans Count Badge -->
                        <div v-if="actionPlansCount" class="inline-flex items-center gap-2 bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-200 rounded-full px-6 py-3 mb-6">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span class="font-semibold text-green-800">{{ actionPlansCount }} Action Plans Created</span>
                        </div>
                        
                        <!-- Close Button -->
                        <button
                            @click="close"
                            class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl"
                        >
                            Awesome! 👍
                        </button>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<script setup>
import { ref, onMounted } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Success!'
    },
    message: {
        type: String,
        required: true
    },
    actionPlansCount: {
        type: Number,
        default: 0
    },
    duration: {
        type: Number,
        default: 0 // 0 means manual close only
    }
});

const emit = defineEmits(['close']);

const show = ref(false);
let timer = null;

const close = () => {
    show.value = false;
    if (timer) clearTimeout(timer);
    setTimeout(() => {
        emit('close');
    }, 300);
};

onMounted(() => {
    show.value = true;
    if (props.duration > 0) {
        timer = setTimeout(() => {
            close();
        }, props.duration);
    }
});
</script>

<style scoped>
/* Success Checkmark Animation */
.success-checkmark {
    width: 120px;
    height: 120px;
    margin: 0 auto;
}

.check-icon {
    width: 120px;
    height: 120px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid rgba(255, 255, 255, 0.3);
    background: rgba(255, 255, 255, 0.2);
    animation: scale-in 0.5s ease-out;
}

.check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}

.check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 1s ease-in;
}

.check-icon::before, .check-icon::after {
    content: '';
    height: 120px;
    position: absolute;
    background: transparent;
    transform: rotate(-45deg);
}

.icon-line {
    height: 5px;
    background-color: white;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}

.icon-line.line-tip {
    top: 56px;
    left: 21px;
    width: 30px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s 0.3s ease-out forwards;
    opacity: 0;
}

.icon-line.line-long {
    top: 50px;
    right: 12px;
    width: 60px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s 0.5s ease-out forwards;
    opacity: 0;
}

.icon-circle {
    top: -4px;
    left: -4px;
    z-index: 10;
    width: 120px;
    height: 120px;
    border-radius: 50%;
    position: absolute;
    box-sizing: content-box;
    border: 4px solid rgba(255, 255, 255, 0.5);
}

.icon-fix {
    top: 8px;
    width: 5px;
    left: 26px;
    z-index: 1;
    height: 85px;
    position: absolute;
    transform: rotate(-45deg);
    background-color: transparent;
}

@keyframes scale-in {
    0% {
        transform: scale(0);
        opacity: 0;
    }
    50% {
        transform: scale(1.1);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@keyframes rotate-circle {
    0% {
        transform: rotate(-45deg);
    }
    5% {
        transform: rotate(-45deg);
    }
    12% {
        transform: rotate(-405deg);
    }
    100% {
        transform: rotate(-405deg);
    }
}

@keyframes icon-line-tip {
    0% {
        width: 0;
        left: 1px;
        top: 19px;
        opacity: 0;
    }
    54% {
        width: 0;
        left: 1px;
        top: 19px;
        opacity: 1;
    }
    70% {
        width: 30px;
        left: 21px;
        top: 56px;
        opacity: 1;
    }
    84% {
        width: 34px;
        left: 19px;
        top: 58px;
        opacity: 1;
    }
    100% {
        width: 30px;
        left: 21px;
        top: 56px;
        opacity: 1;
    }
}

@keyframes icon-line-long {
    0% {
        width: 0;
        right: 46px;
        top: 54px;
        opacity: 0;
    }
    65% {
        width: 0;
        right: 46px;
        top: 54px;
        opacity: 1;
    }
    84% {
        width: 60px;
        right: 12px;
        top: 50px;
        opacity: 1;
    }
    100% {
        width: 60px;
        right: 12px;
        top: 50px;
        opacity: 1;
    }
}
</style>
