<template>
    <Head title="View Store Visit" />
    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">View Store Visit Report</h2>
                <button @click="window.print()" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    Print Report
                </button>
            </div>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6 flex items-center justify-between">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">Store Visit Report</h1>
                                <p class="text-gray-600">View details - Restaurant: {{ visit.restaurant_name }}, Date: {{ visit.visit_date }}</p>
                            </div>
                            <Link :href="route('store-visits.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"> Back</Link>
                        </div>
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">📋 Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Restaurant</label><div class="p-3 bg-white rounded-md border">{{ visit.restaurant_name }}</div></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">MIC</label><div class="p-3 bg-white rounded-md border">{{ visit.mic }}</div></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Visit Date</label><div class="p-3 bg-white rounded-md border">{{ visit.visit_date }}</div></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Purpose</label><div class="p-3 bg-white rounded-md border whitespace-pre-wrap">{{ visit.purpose_of_visit }}</div></div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">👥 Customer / QSC</h3>
                            <div class="space-y-3">
                                <div class="bg-white p-3 rounded border"><strong>OCA Board:</strong> <span :class="visit.oca_board_followed ? 'text-green-600' : 'text-red-600'">{{ visit.oca_board_followed ? ' Yes' : ' No' }}</span><div v-if="visit.oca_board_comments" class="text-gray-600 mt-1 italic">{{ visit.oca_board_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Staff Duty:</strong> <span :class="visit.staff_know_duty ? 'text-green-600' : 'text-red-600'">{{ visit.staff_know_duty ? ' Yes' : ' No' }}</span><div v-if="visit.staff_duty_comments" class="text-gray-600 mt-1 italic">{{ visit.staff_duty_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Coaching:</strong> <span :class="visit.coaching_directing ? 'text-green-600' : 'text-red-600'">{{ visit.coaching_directing ? ' Yes' : ' No' }}</span><div v-if="visit.coaching_comments" class="text-gray-600 mt-1 italic">{{ visit.coaching_comments }}</div></div>
                            </div>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">💰 Cashier</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white p-3 rounded border"><strong>Smile/Greetings:</strong> <span :class="visit.smile_greetings ? 'text-green-600' : 'text-red-600'">{{ visit.smile_greetings ? ' Yes' : ' No' }}</span><div v-if="visit.smile_comments" class="text-gray-600 mt-1 italic">{{ visit.smile_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Suggestive Selling:</strong> <span :class="visit.suggestive_selling ? 'text-green-600' : 'text-red-600'">{{ visit.suggestive_selling ? ' Yes' : ' No' }}</span><div v-if="visit.selling_comments" class="text-gray-600 mt-1 italic">{{ visit.selling_comments }}</div></div>
                            </div>
                        </div>
                                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">⚡ Service Standards</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white p-3 rounded border"><strong>Team Work & Hustle:</strong> <span :class="visit.team_work_hustle ? 'text-green-600' : 'text-red-600'">{{ visit.team_work_hustle ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.hustle_comments" class="text-gray-600 mt-1 italic">{{ visit.hustle_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Fast Service:</strong> <span :class="visit.fast_service ? 'text-green-600' : 'text-red-600'">{{ visit.fast_service ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.service_comments" class="text-gray-600 mt-1 italic">{{ visit.service_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Product Quality:</strong> <span :class="visit.product_quality ? 'text-green-600' : 'text-red-600'">{{ visit.product_quality ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.quality_comments" class="text-gray-600 mt-1 italic">{{ visit.quality_comments }}</div></div>
                            </div>
                        </div>
                        
                        <!-- Section 5: Financials -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">💼 Financials</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white p-3 rounded border"><strong>Weekly Schedule:</strong> <span :class="visit.weekly_schedule ? 'text-green-600' : 'text-red-600'">{{ visit.weekly_schedule ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.schedule_comments" class="text-gray-600 mt-1 italic">{{ visit.schedule_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>MOD Financial Goal:</strong> <span :class="visit.mod_financial_goal ? 'text-green-600' : 'text-red-600'">{{ visit.mod_financial_goal ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.financial_comments" class="text-gray-600 mt-1 italic">{{ visit.financial_comments }}</div></div>
                            </div>
                        </div>

                        <!-- Section 6: Quality/Pathing -->
                        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">🔧 Quality / Pathing</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white p-3 rounded border"><strong>TTF Followed:</strong> <span :class="visit.ttf_followed ? 'text-green-600' : 'text-red-600'">{{ visit.ttf_followed ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.ttf_comments" class="text-gray-600 mt-1 italic">{{ visit.ttf_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Equipment Working:</strong> <span :class="visit.equipment_working ? 'text-green-600' : 'text-red-600'">{{ visit.equipment_working ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.equipment_comments" class="text-gray-600 mt-1 italic">{{ visit.equipment_comments }}</div></div>
                            </div>
                        </div>

                        <!-- Section 7: Cleanliness -->
                        <div class="bg-teal-50 border border-teal-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">🧽 Cleanliness</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="bg-white p-3 rounded border"><strong>Dining Area Clean:</strong> <span :class="visit.dining_area_clean ? 'text-green-600' : 'text-red-600'">{{ visit.dining_area_clean ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.dining_comments" class="text-gray-600 mt-1 italic">{{ visit.dining_comments }}</div></div>
                                <div class="bg-white p-3 rounded border"><strong>Restroom Clean:</strong> <span :class="visit.restroom_clean ? 'text-green-600' : 'text-red-600'">{{ visit.restroom_clean ? '✓ Yes' : '✗ No' }}</span><div v-if="visit.restroom_comments" class="text-gray-600 mt-1 italic">{{ visit.restroom_comments }}</div></div>
                            </div>
                        </div>

                        <!-- Section 8: Follow-Up -->
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">📅 Follow-Up from Last Visit</h3>
                            <div class="space-y-4">
                                <div v-if="visit.last_visit_date" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Last Visit Date</label>
                                    <div class="text-gray-900">{{ visit.last_visit_date }}</div>
                                </div>
                                <div v-if="visit.last_visit_summary" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Summary of Last Visit</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.last_visit_summary }}</div>
                                </div>
                                <div v-if="visit.last_visit_update" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Update on Last Visit</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.last_visit_update }}</div>
                                </div>
                                <div v-if="visit.other_follow_up" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Other Follow-Up</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.other_follow_up }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 9: Observation Summary -->
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">📊 Observation Summary</h3>
                            <div class="space-y-4">
                                <div v-if="visit.what_did_you_see" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">What did you see?</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.what_did_you_see }}</div>
                                </div>
                                <div v-if="visit.why_had_issue" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Why had an issue?</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.why_had_issue }}</div>
                                </div>
                                <div v-if="visit.how_to_improve" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">How to improve?</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.how_to_improve }}</div>
                                </div>
                                <div v-if="visit.who_when_responsible" class="bg-white p-3 rounded border">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Who & When Responsible?</label>
                                    <div class="text-gray-900 whitespace-pre-wrap">{{ visit.who_when_responsible }}</div>
                                </div>
                            </div>
                        </div>
                        <div v-if="visit.general_comments" class="bg-gray-50 border rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-2">💬 General Comments</h3>
                            <div class="whitespace-pre-wrap text-gray-700">{{ visit.general_comments }}</div>
                        </div>

                        <!-- Attached Images -->
                        <div v-if="visit.attachments && JSON.parse(visit.attachments).length > 0" class="bg-white border rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">📸 Attached Images</h3>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="(image, index) in JSON.parse(visit.attachments)" 
                                     :key="index"
                                     class="group relative cursor-pointer"
                                     @click="openImageModal(image)">
                                    <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition">
                                        <img :src="`/storage/${image}`" 
                                             :alt="`Image ${index + 1}`"
                                             class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                    </div>
                                    <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center">
                                        <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Plans -->
                        <div v-if="visit.action_plans && visit.action_plans.length > 0" class="bg-red-50 border border-red-200 rounded-lg p-6 mb-4">
                            <h3 class="text-lg font-semibold mb-4">⚠️ Action Plans</h3>
                            <div class="space-y-4">
                                <div v-for="plan in visit.action_plans" 
                                     :key="plan.id"
                                     class="bg-white rounded-lg border border-red-300 p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">{{ plan.item }}</h4>
                                            <p class="text-sm text-gray-600 mb-2">{{ plan.issue }}</p>
                                            <div class="flex items-center gap-4 text-sm">
                                                <span class="px-2 py-1 rounded text-xs font-medium"
                                                      :class="{
                                                          'bg-red-100 text-red-800': plan.priority === 'High',
                                                          'bg-yellow-100 text-yellow-800': plan.priority === 'Medium',
                                                          'bg-blue-100 text-blue-800': plan.priority === 'Low'
                                                      }">
                                                    {{ plan.priority }} Priority
                                                </span>
                                                <span class="px-2 py-1 rounded text-xs font-medium"
                                                      :class="{
                                                          'bg-gray-100 text-gray-800': plan.status === 'Pending',
                                                          'bg-blue-100 text-blue-800': plan.status === 'In Progress',
                                                          'bg-green-100 text-green-800': plan.status === 'Completed'
                                                      }">
                                                    {{ plan.status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Plan Images -->
                                    <div v-if="plan.images && plan.images.length > 0" class="mt-4 pt-4 border-t border-gray-200">
                                        <h5 class="text-sm font-semibold text-gray-700 mb-3">📷 Related Images ({{ plan.images.length }})</h5>
                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                            <div v-for="image in plan.images" 
                                                 :key="image.id"
                                                 class="group relative">
                                                <div class="aspect-square rounded-lg overflow-hidden border-2 border-gray-200 hover:border-blue-500 transition cursor-pointer"
                                                     @click="openActionPlanImageModal(image)">
                                                    <img :src="`/storage/${image.image_path}`" 
                                                         :alt="image.original_name"
                                                         class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                </div>
                                                <div class="mt-1 text-xs text-center text-gray-600 truncate">{{ image.original_name }}</div>
                                                <a :href="`/storage/${image.image_path}`" 
                                                   download
                                                   @click.stop
                                                   class="absolute top-2 right-2 bg-white bg-opacity-90 hover:bg-opacity-100 rounded-full p-1.5 shadow-md transition opacity-0 group-hover:opacity-100"
                                                   title="Download">
                                                    <svg class="w-4 h-4 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image Modal -->
                        <Teleport to="body">
                            <div v-if="selectedImage" 
                                 class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
                                 @click="closeImageModal">
                                <button @click="closeImageModal" 
                                        class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                                <img :src="`/storage/${selectedImage}`" 
                                     alt="Full size image" 
                                     class="max-w-full max-h-full object-contain"
                                     @click.stop>
                            </div>
                        </Teleport>

                        <div class="flex items-center justify-between pt-6 border-t mt-6">
                            <Link :href="route('store-visits.index')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-gray-700"> Back</Link>
                            <div class="flex gap-3">
                                <!-- <button @click="printReport" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-purple-700">Print</button> -->
                                <Link v-if="can('store_visits', 'edit')" :href="route('store-visits.edit', visit.id)" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-blue-700">Edit</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>
<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { usePermissions } from '@/Composables/usePermissions'
import { ref } from 'vue'

const route = window.route
const { can } = usePermissions()
const props = defineProps({ visit: Object })

const selectedImage = ref(null)

const openImageModal = (image) => {
    selectedImage.value = image
}

const openActionPlanImageModal = (image) => {
    selectedImage.value = image.image_path
}

const closeImageModal = () => {
    selectedImage.value = null
}

const printReport = () => {
    const printUrl = route('store-visits.print-report', props.visit.id)
    const printWindow = window.open(printUrl, '_blank', 'width=800,height=600')
    if (printWindow) printWindow.focus()
}
</script>
