<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    errors: Object,
    restaurants: {
        type: Array,
        default: () => []
    }
});

// Form data using Inertia's useForm
const form = useForm({
    restaurant_name: '',
    mic: '',
    visit_date: new Date().toISOString().split('T')[0],
    purpose_of_visit: [],
    
    // Section 2: Customer / QSC
    oca_board_followed: null,
    oca_board_comments: '',
    staff_know_duty: null,
    staff_duty_comments: '',
    coaching_directing: null,
    coaching_comments: '',
    
    // Section 3: Cashier
    smile_greetings: null,
    smile_comments: '',
    suggestive_selling: null,
    suggestive_comments: '',
    offer_promotion: null,
    promotion_comments: '',
    thank_direction: null,
    thank_comments: '',
    
    // Section 4: Service Standards
    team_work_hustle: null,
    teamwork_comments: '',
    order_accuracy: null,
    accuracy_comments: '',
    service_time: null,
    service_comments: '',
    dine_in: null,
    dine_comments: '',
    take_out: null,
    takeout_comments: '',
    family: null,
    family_comments: '',
    delivery: null,
    delivery_comments: '',
    drive_thru: null,
    drive_comments: '',
    
    // Section 5: Financials
    weekly_schedule: null,
    schedule_comments: '',
    mod_financial_goal: null,
    financial_comments: '',
    sales_objectives: null,
    sales_comments: '',
    cash_policies: null,
    cash_comments: '',
    daily_waste: null,
    waste_comments: '',
    
    // Section 6: Quality / Pathing
    ttf_followed: null,
    ttf_comments: '',
    sandwich_assembly: null,
    assembly_comments: '',
    qsc_completed: null,
    qsc_comments: '',
    oil_standards: null,
    oil_comments: '',
    day_labels: null,
    labels_comments: '',
    equipment_working: null,
    equipment_comments: '',
    fryer_condition: null,
    fryer_comments: '',
    vegetable_prep: null,
    vegetable_comments: '',
    employee_appearance: null,
    appearance_comments: '',
    
    // Section 7: Cleanliness
    equipment_wrapped: null,
    wrapped_comments: '',
    sink_setup: null,
    sink_comments: '',
    sanitizer_standard: null,
    sanitizer_comments: '',
    dining_area_clean: null,
    dining_comments: '',
    restroom_clean: null,
    restroom_comments: '',
    
    // Section 8: Follow-Up
    last_visit_date: '',
    last_visit_summary: '',
    last_visit_update: '',
    other_follow_up: '',
    
    // Section 9: Observation Summary
    what_did_you_see: '',
    why_had_issue: '',
    how_to_improve: '',
    who_when_responsible: '',
    
    general_comments: '',
    status: 'Submitted'
});

// Options
const micOptions = ['Morning', 'Evening'];
const purposeOptions = [
    'Quality Audit',
    'Operational Assessment', 
    'Training Audit',
    'Measurement & Coaching'
];

// Computed score
const totalScore = computed(() => {
    let totalQuestions = 0;
    let yesAnswers = 0;
    
    const booleanFields = [
        'oca_board_followed', 'staff_know_duty', 'coaching_directing',
        'smile_greetings', 'suggestive_selling', 'offer_promotion', 'thank_direction',
        'team_work_hustle', 'order_accuracy', 'service_time', 'dine_in', 'take_out', 'family', 'delivery', 'drive_thru',
        'weekly_schedule', 'mod_financial_goal', 'sales_objectives', 'cash_policies', 'daily_waste',
        'ttf_followed', 'sandwich_assembly', 'qsc_completed', 'oil_standards', 'day_labels', 'equipment_working', 'fryer_condition', 'vegetable_prep', 'employee_appearance',
        'equipment_wrapped', 'sink_setup', 'sanitizer_standard', 'dining_area_clean', 'restroom_clean'
    ];
    
    booleanFields.forEach(field => {
        if (form[field] === true || form[field] === false) {
            totalQuestions++;
            if (form[field] === true) yesAnswers++;
        }
    });
    
    return totalQuestions > 0 ? Math.round((yesAnswers / totalQuestions) * 100) : 0;
});

// Submit form
const submitForm = () => {
    form.post(route('store-visits.store'), {
        onSuccess: () => {
            // Success handled by redirect from controller
        }
    });
};

// Save as draft
const saveDraft = () => {
    form.status = 'Draft';
    form.post(route('store-visits.store'));
};
</script>

<template>
    <Head title="New Store Visit Report" />
    
    <DefaultAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📝 New Store Visit Report
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Navigation Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm text-gray-500">
                            <li><Link href="/dashboard" class="hover:text-gray-700">Home</Link></li>
                            <li><span>›</span></li>
                            <li><Link href="/store-visits" class="hover:text-gray-700">Store Visits</Link></li>
                            <li><span>›</span></li>
                            <li class="text-gray-900 font-medium">New Report</li>
                        </ol>
                    </nav>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <!-- Section 1: General Information -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                📋 Section 1 – General Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant *</label>
                                    <select v-model="form.restaurant_name" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                           required>
                                        <option value="">Select Restaurant</option>
                                        <option v-for="restaurant in props.restaurants" 
                                               :key="restaurant.id" 
                                               :value="restaurant.name">
                                            {{ restaurant.name }} - {{ restaurant.location }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.restaurant_name" class="mt-1 text-sm text-red-600">{{ form.errors.restaurant_name }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">MIC *</label>
                                    <select v-model="form.mic" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                           required>
                                        <option value="">Select Shift</option>
                                        <option v-for="mic in micOptions" :key="mic" :value="mic">{{ mic }}</option>
                                    </select>
                                    <p v-if="form.errors.mic" class="mt-1 text-sm text-red-600">{{ form.errors.mic }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Visit Date *</label>
                                    <input v-model="form.visit_date" 
                                          type="date" 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                          required>
                                    <p v-if="form.errors.visit_date" class="mt-1 text-sm text-red-600">{{ form.errors.visit_date }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto Score</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                                        <span class="text-lg font-bold" :class="totalScore >= 80 ? 'text-green-600' : totalScore >= 60 ? 'text-yellow-600' : 'text-red-600'">
                                            {{ totalScore }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Purpose of Visit *</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label v-for="purpose in purposeOptions" :key="purpose" class="flex items-center">
                                        <input type="checkbox" 
                                              :value="purpose" 
                                              v-model="form.purpose_of_visit"
                                              class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700">{{ purpose }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.purpose_of_visit" class="mt-1 text-sm text-red-600">{{ form.errors.purpose_of_visit }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Customer / QSC -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                👥 Section 2 – Customer / QSC
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- OCA Board Communication -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">OCA Board is Completely Followed/Communicated</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.oca_board_followed = true"
                                           :class="form.oca_board_followed === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.oca_board_followed = false"
                                           :class="form.oca_board_followed === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.oca_board_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Staff Know their Side Duty -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Staff Know their Side Duty</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.staff_know_duty = true"
                                           :class="form.staff_know_duty === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.staff_know_duty = false"
                                           :class="form.staff_know_duty === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.staff_duty_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Coaching and Directing -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Coaching and Directing</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.coaching_directing = true"
                                           :class="form.coaching_directing === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.coaching_directing = false"
                                           :class="form.coaching_directing === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.coaching_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Cashier -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-yellow-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                💰 Section 3 – Cashier
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <!-- Smile and Friendly Greetings -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Smile and Friendly Greetings</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.smile_greetings = true"
                                           :class="form.smile_greetings === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.smile_greetings = false"
                                           :class="form.smile_greetings === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.smile_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Suggestive Selling -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Suggestive Selling</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.suggestive_selling = true"
                                           :class="form.suggestive_selling === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.suggestive_selling = false"
                                           :class="form.suggestive_selling === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.suggestive_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- New Offers -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Offer new Promotion</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.offer_promotion = true"
                                           :class="form.offer_promotion === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.offer_promotion = false"
                                           :class="form.offer_promotion === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.promotion_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Thank You -->
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Saying Thank You and Provides Direction</p>
                                </div>
                                <div class="flex space-x-4">
                                    <button type="button" 
                                           @click="form.thank_direction = true"
                                           :class="form.thank_direction === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        Yes
                                    </button>
                                    <button type="button" 
                                           @click="form.thank_direction = false"
                                           :class="form.thank_direction === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700'"
                                           class="px-4 py-2 rounded-md font-medium hover:opacity-80">
                                        No
                                    </button>
                                </div>
                                <div>
                                    <input v-model="form.thank_comments" 
                                          type="text" 
                                          placeholder="Comments..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Continue with other sections... -->
                    <!-- For brevity, I'll add a placeholder for the remaining sections -->
                    
                    <!-- Action Buttons -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm text-gray-600">Auto Score: <span class="font-bold text-lg" :class="totalScore >= 80 ? 'text-green-600' : totalScore >= 60 ? 'text-yellow-600' : 'text-red-600'">{{ totalScore }}%</span></p>
                            </div>
                            <div class="flex space-x-4">
                                <button type="button" 
                                       @click="saveDraft"
                                       :disabled="form.processing"
                                       class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 disabled:opacity-50">
                                    Save Draft
                                </button>
                                <button type="submit" 
                                       :disabled="form.processing"
                                       class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                    {{ form.processing ? 'Submitting...' : 'Submit Report' }}
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
/* Custom transitions */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>