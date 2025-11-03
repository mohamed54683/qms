<template>
    <Head title="Edit Store Visit" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Store Visit Report
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <!-- Header Section -->
                        <div class="mb-6">
                            <h1 class="text-2xl font-bold text-gray-900">Edit Store Visit Report</h1>
                            <p class="text-gray-600">Modify the store visit report details</p>
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="submit">
                            <!-- Basic Information -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Basic Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="restaurant_name" class="block text-sm font-medium text-gray-700">Restaurant Name *</label>
                                        <input
                                            id="restaurant_name"
                                            v-model="form.restaurant_name"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <div v-if="form.errors.restaurant_name" class="text-red-600 text-sm mt-1">{{ form.errors.restaurant_name }}</div>
                                    </div>

                                    <div>
                                        <label for="mic" class="block text-sm font-medium text-gray-700">MIC *</label>
                                        <input
                                            id="mic"
                                            v-model="form.mic"
                                            type="text"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <div v-if="form.errors.mic" class="text-red-600 text-sm mt-1">{{ form.errors.mic }}</div>
                                    </div>

                                    <div>
                                        <label for="visit_date" class="block text-sm font-medium text-gray-700">Visit Date *</label>
                                        <input
                                            id="visit_date"
                                            v-model="form.visit_date"
                                            type="date"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        />
                                        <div v-if="form.errors.visit_date" class="text-red-600 text-sm mt-1">{{ form.errors.visit_date }}</div>
                                    </div>

                                    <div>
                                        <label for="purpose_of_visit" class="block text-sm font-medium text-gray-700">Purpose of Visit *</label>
                                        <textarea
                                            id="purpose_of_visit"
                                            v-model="form.purpose_of_visit"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        ></textarea>
                                        <div v-if="form.errors.purpose_of_visit" class="text-red-600 text-sm mt-1">{{ form.errors.purpose_of_visit }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 2: Customer / QSC -->
                            <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">👥 Section 2 – Customer / QSC</h3>
                                <div class="space-y-6">
                                    <!-- Question 1 -->
                                    <div class="bg-white p-4 rounded-lg border border-green-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">OCA Board is Completely Followed/Communicated</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.oca_board_followed" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.oca_board_followed" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea
                                            v-model="form.oca_board_comments"
                                            placeholder="Comments (optional)"
                                            rows="2"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>

                                    <!-- Question 2 -->
                                    <div class="bg-white p-4 rounded-lg border border-green-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Staff Know their Side Duty</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.staff_know_duty" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.staff_know_duty" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea
                                            v-model="form.staff_duty_comments"
                                            placeholder="Comments (optional)"
                                            rows="2"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>

                                    <!-- Question 3 -->
                                    <div class="bg-white p-4 rounded-lg border border-green-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Coaching and Directing</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.coaching_directing" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.coaching_directing" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea
                                            v-model="form.coaching_comments"
                                            placeholder="Comments (optional)"
                                            rows="2"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 3: Cashier -->
                            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">💰 Section 3 – Cashier</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Smile and Greetings -->
                                    <div class="bg-white p-4 rounded-lg border border-yellow-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Smile and Friendly Greetings</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.smile_greetings" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.smile_greetings" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.smile_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- Suggestive Selling -->
                                    <div class="bg-white p-4 rounded-lg border border-yellow-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Suggestive Selling</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.suggestive_selling" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.suggestive_selling" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.suggestive_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 4: Service Standards -->
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">⚡ Section 4 – Service Standards</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Team Work and Hustle -->
                                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Team Work and Hustle</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.team_work_hustle" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.team_work_hustle" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.teamwork_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- Order Accuracy -->
                                    <div class="bg-white p-4 rounded-lg border border-blue-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Order Accuracy</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.order_accuracy" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.order_accuracy" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.accuracy_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 5: Financials -->
                            <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">💼 Section 5 – Financials</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Weekly Schedule -->
                                    <div class="bg-white p-4 rounded-lg border border-purple-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Weekly Schedule</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.weekly_schedule" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.weekly_schedule" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.schedule_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- MOD Financial Goal -->
                                    <div class="bg-white p-4 rounded-lg border border-purple-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">MOD Financial Goal</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.mod_financial_goal" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.mod_financial_goal" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.financial_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 6: Quality / Pathing -->
                            <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">🔧 Section 6 – Quality / Pathing</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- TTF Followed -->
                                    <div class="bg-white p-4 rounded-lg border border-orange-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">TTF Followed</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.ttf_followed" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.ttf_followed" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.ttf_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- Equipment Working -->
                                    <div class="bg-white p-4 rounded-lg border border-orange-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Equipment Working</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.equipment_working" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.equipment_working" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.equipment_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 7: Cleanliness -->
                            <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">🧽 Section 7 – Cleanliness</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Dining Area Clean -->
                                    <div class="bg-white p-4 rounded-lg border border-teal-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Dining Area Clean</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.dining_area_clean" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.dining_area_clean" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.dining_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>

                                    <!-- Restroom Clean -->
                                    <div class="bg-white p-4 rounded-lg border border-teal-100">
                                        <label class="block text-sm font-medium text-gray-700 mb-3">Restroom Clean</label>
                                        <div class="flex space-x-4 mb-3">
                                            <label class="flex items-center">
                                                <input type="radio" :value="true" v-model="form.restroom_clean" class="mr-2">
                                                <span class="text-green-600">Yes</span>
                                            </label>
                                            <label class="flex items-center">
                                                <input type="radio" :value="false" v-model="form.restroom_clean" class="mr-2">
                                                <span class="text-red-600">No</span>
                                            </label>
                                        </div>
                                        <textarea v-model="form.restroom_comments" placeholder="Comments" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 8: Follow-Up -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📅 Section 8 – Follow-Up</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="last_visit_date" class="block text-sm font-medium text-gray-700">Last Visit Date</label>
                                        <input
                                            id="last_visit_date"
                                            v-model="form.last_visit_date"
                                            type="date"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        />
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="last_visit_summary" class="block text-sm font-medium text-gray-700">Last Visit Summary</label>
                                        <textarea
                                            id="last_visit_summary"
                                            v-model="form.last_visit_summary"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="last_visit_update" class="block text-sm font-medium text-gray-700">Updates on Previous Issues</label>
                                        <textarea
                                            id="last_visit_update"
                                            v-model="form.last_visit_update"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                    
                                    <div class="md:col-span-2">
                                        <label for="other_follow_up" class="block text-sm font-medium text-gray-700">Other Follow-up Items</label>
                                        <textarea
                                            id="other_follow_up"
                                            v-model="form.other_follow_up"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Section 9: Observation Summary -->
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📊 Section 9 – Observation Summary</h3>
                                <div class="space-y-6">
                                    <div>
                                        <label for="what_did_you_see" class="block text-sm font-medium text-gray-700">What did you see?</label>
                                        <textarea
                                            id="what_did_you_see"
                                            v-model="form.what_did_you_see"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="why_had_issue" class="block text-sm font-medium text-gray-700">Why did it happen?</label>
                                        <textarea
                                            id="why_had_issue"
                                            v-model="form.why_had_issue"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="how_to_improve" class="block text-sm font-medium text-gray-700">How to improve?</label>
                                        <textarea
                                            id="how_to_improve"
                                            v-model="form.how_to_improve"
                                            rows="4"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                    
                                    <div>
                                        <label for="who_when_responsible" class="block text-sm font-medium text-gray-700">Who & When?</label>
                                        <textarea
                                            id="who_when_responsible"
                                            v-model="form.who_when_responsible"
                                            rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- General Comments -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-8">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">💬 General Comments</h3>
                                <textarea
                                    v-model="form.general_comments"
                                    rows="4"
                                    placeholder="Any additional comments or observations..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between">
                                <Link 
                                    :href="route('store-visits.index')"
                                    class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded"
                                >
                                    Cancel
                                </Link>
                                
                                <div class="space-x-2">
                                    <Link 
                                        :href="route('store-visits.show', visit.id)"
                                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    >
                                        View Report
                                    </Link>
                                    
                                    <button
                                        type="submit"
                                        :disabled="form.processing"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                    >
                                        <span v-if="form.processing">Updating...</span>
                                        <span v-else>Update Report</span>
                                    </button>
                                    
                                    <!-- Confirm Button for Restaurant Managers and Admins -->
                                    <button
                                        v-if="canConfirm"
                                        type="button"
                                        @click="confirm"
                                        :disabled="form.processing"
                                        class="bg-orange-500 hover:bg-orange-700 text-white font-bold py-2 px-4 rounded disabled:opacity-50"
                                    >
                                        <span v-if="form.processing">Confirming...</span>
                                        <span v-else>✓ Confirm & Create Action Plans</span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import { computed } from 'vue'
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue'

const props = defineProps({
    visit: {
        type: Object,
        required: true
    },
    restaurants: {
        type: Array,
        default: () => []
    },
    auth: {
        type: Object,
        required: true
    }
})

// Check if user can confirm (Restaurant Manager or Admin)
const canConfirm = computed(() => {
    return props.auth?.user?.roles && 
           props.auth.user.roles.some(role => 
               role.name === 'admin' || role.name === 'restaurant_manager'
           );
});

// Initialize form with existing visit data
const form = useForm({
    restaurant_name: props.visit.restaurant_name || '',
    mic: props.visit.mic || '',
    visit_date: props.visit.visit_date || '',
    purpose_of_visit: props.visit.purpose_of_visit || '',
    
    // Section 2: Customer / QSC
    oca_board_followed: props.visit.oca_board_followed,
    oca_board_comments: props.visit.oca_board_comments || '',
    staff_know_duty: props.visit.staff_know_duty,
    staff_duty_comments: props.visit.staff_duty_comments || '',
    coaching_directing: props.visit.coaching_directing,
    coaching_comments: props.visit.coaching_comments || '',
    
    // Section 3: Cashier
    smile_greetings: props.visit.smile_greetings,
    smile_comments: props.visit.smile_comments || '',
    suggestive_selling: props.visit.suggestive_selling,
    suggestive_comments: props.visit.suggestive_comments || '',
    offer_promotion: props.visit.offer_promotion,
    promotion_comments: props.visit.promotion_comments || '',
    thank_direction: props.visit.thank_direction,
    thank_comments: props.visit.thank_comments || '',
    
    // Section 4: Service Standards
    team_work_hustle: props.visit.team_work_hustle,
    teamwork_comments: props.visit.teamwork_comments || '',
    order_accuracy: props.visit.order_accuracy,
    accuracy_comments: props.visit.accuracy_comments || '',
    service_time: props.visit.service_time,
    service_comments: props.visit.service_comments || '',
    dine_in: props.visit.dine_in,
    dine_comments: props.visit.dine_comments || '',
    take_out: props.visit.take_out,
    takeout_comments: props.visit.takeout_comments || '',
    family: props.visit.family,
    family_comments: props.visit.family_comments || '',
    delivery: props.visit.delivery,
    delivery_comments: props.visit.delivery_comments || '',
    drive_thru: props.visit.drive_thru,
    drive_comments: props.visit.drive_comments || '',
    
    // Section 5: Financials
    weekly_schedule: props.visit.weekly_schedule,
    schedule_comments: props.visit.schedule_comments || '',
    mod_financial_goal: props.visit.mod_financial_goal,
    financial_comments: props.visit.financial_comments || '',
    sales_objectives: props.visit.sales_objectives,
    sales_comments: props.visit.sales_comments || '',
    cash_policies: props.visit.cash_policies,
    cash_comments: props.visit.cash_comments || '',
    daily_waste: props.visit.daily_waste,
    waste_comments: props.visit.waste_comments || '',
    
    // Section 6: Quality / Pathing
    ttf_followed: props.visit.ttf_followed,
    ttf_comments: props.visit.ttf_comments || '',
    sandwich_assembly: props.visit.sandwich_assembly,
    assembly_comments: props.visit.assembly_comments || '',
    qsc_completed: props.visit.qsc_completed,
    qsc_comments: props.visit.qsc_comments || '',
    oil_standards: props.visit.oil_standards,
    oil_comments: props.visit.oil_comments || '',
    day_labels: props.visit.day_labels,
    labels_comments: props.visit.labels_comments || '',
    equipment_working: props.visit.equipment_working,
    equipment_comments: props.visit.equipment_comments || '',
    fryer_condition: props.visit.fryer_condition,
    fryer_comments: props.visit.fryer_comments || '',
    vegetable_prep: props.visit.vegetable_prep,
    vegetable_comments: props.visit.vegetable_comments || '',
    employee_appearance: props.visit.employee_appearance,
    appearance_comments: props.visit.appearance_comments || '',
    
    // Section 7: Cleanliness
    equipment_wrapped: props.visit.equipment_wrapped,
    wrapped_comments: props.visit.wrapped_comments || '',
    sink_setup: props.visit.sink_setup,
    sink_comments: props.visit.sink_comments || '',
    sanitizer_standard: props.visit.sanitizer_standard,
    sanitizer_comments: props.visit.sanitizer_comments || '',
    dining_area_clean: props.visit.dining_area_clean,
    dining_comments: props.visit.dining_comments || '',
    restroom_clean: props.visit.restroom_clean,
    restroom_comments: props.visit.restroom_comments || '',
    
    // Section 8: Follow-Up
    last_visit_date: props.visit.last_visit_date || '',
    last_visit_summary: props.visit.last_visit_summary || '',
    last_visit_update: props.visit.last_visit_update || '',
    other_follow_up: props.visit.other_follow_up || '',
    
    // Section 9: Observation Summary
    what_did_you_see: props.visit.what_did_you_see || '',
    why_had_issue: props.visit.why_had_issue || '',
    how_to_improve: props.visit.how_to_improve || '',
    who_when_responsible: props.visit.who_when_responsible || '',
    
    general_comments: props.visit.general_comments || '',
    status: props.visit.status || 'Draft'
})

const submit = () => {
    form.put(route('store-visits.update', props.visit.id), {
        onSuccess: () => {
            // Handle success - redirect will happen automatically
        }
    })
}

const confirm = () => {
    // Create a copy of form data and add confirm flag
    const confirmForm = useForm({
        ...form.data(),
        confirmed: true,
        create_action_plans: true
    });
    
    confirmForm.put(route('store-visits.update', props.visit.id), {
        onSuccess: () => {
            // Handle success - redirect will happen automatically
        }
    })
}
</script>