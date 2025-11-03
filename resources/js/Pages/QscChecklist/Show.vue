<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const route = window.route;

const props = defineProps({
    checklist: Object,
    complianceData: Object
});

// Helper function to get status badge color
const getStatusColor = (status) => {
    switch (status) {
        case 'Draft':
            return 'bg-gray-100 text-gray-800';
        case 'Submitted':
            return 'bg-blue-100 text-blue-800';
        case 'Reviewed':
            return 'bg-yellow-100 text-yellow-800';
        case 'Approved':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

// Helper function to get answer badge
const getAnswerBadge = (answer) => {
    if (answer === 'Yes' || answer === 'yes') {
        return { class: 'bg-green-100 text-green-800 border border-green-200', icon: '✓' };
    } else if (answer === 'No' || answer === 'no') {
        return { class: 'bg-red-100 text-red-800 border border-red-200', icon: '✗' };
    } else {
        return { class: 'bg-gray-100 text-gray-800 border border-gray-200', icon: '-' };
    }
};

// Helper function to get section score color
const getSectionScoreColor = (score) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 80) return 'text-lime-600';
    if (score >= 70) return 'text-yellow-600';
    if (score >= 60) return 'text-orange-600';
    return 'text-red-600';
};

// Format date with time
const formatDateTime = (datetime) => {
    if (!datetime) return 'N/A';
    return new Date(datetime).toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        hour12: true
    });
};

// Format date only
const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
};

// Helper functions for section calculations
const getSectionScore = (section) => {
    if (!props.complianceData || !props.complianceData.section_scores) {
        // Fallback calculation
        let total = 0;
        let passed = 0;
        section.items.forEach(item => {
            const value = props.checklist[item.key];
            if (value === 'Yes' || value === 'No' || value === 'yes' || value === 'no') {
                total++;
                if (value === 'Yes' || value === 'yes') passed++;
            }
        });
        return total > 0 ? Math.round((passed / total) * 100) : 0;
    }
    
    // Get section key from title
    const sectionKey = section.title.toLowerCase().replace(/[^a-z]/g, '_').replace(/_+/g, '_').replace(/^_|_$/g, '');
    const sectionData = props.complianceData.section_scores[sectionKey];
    return sectionData ? sectionData.score : 0;
};

const getSectionPassedCount = (section) => {
    let passed = 0;
    section.items.forEach(item => {
        const value = props.checklist[item.key];
        if (value === 'Yes' || value === 'yes') passed++;
    });
    return passed;
};

// Calculate compliance score from backend data or fallback calculation
const complianceScore = computed(() => {
    if (props.complianceData && props.complianceData.overall_score !== undefined) {
        return props.complianceData.overall_score;
    }
    
    // Fallback calculation
    if (!props.checklist) return 0;
    
    let totalQuestions = 0;
    let yesAnswers = 0;
    
    // Count all Yes/No answers
    Object.keys(props.checklist).forEach(key => {
        if (key.includes('_') && !key.includes('_comment') && !key.includes('_id') && !key.includes('_at')) {
            const value = props.checklist[key];
            if (value === 'Yes' || value === 'No' || value === 'yes' || value === 'no') {
                totalQuestions++;
                if (value === 'Yes' || value === 'yes') yesAnswers++;
            }
        }
    });
    
    return totalQuestions > 0 ? Math.round((yesAnswers / totalQuestions) * 100) : 0;
});

// Section items - Complete structure matching the form
const sections = [
    {
        title: 'Exterior',
        items: [
            { key: 'exterior_lights_open', label: 'Lights Open' },
            { key: 'exterior_logo_clean', label: 'Logo Clean' },
            { key: 'exterior_parking_clean', label: 'Parking Clean' },
            { key: 'exterior_no_graffiti', label: 'No Graffiti' }
        ]
    },
    {
        title: 'Doors and Glass',
        items: [
            { key: 'doors_glass_clean', label: 'Glass Clean' },
            { key: 'doors_door_handle', label: 'Door Handle Clean' },
            { key: 'doors_entrance_clean', label: 'Entrance Clean' }
        ]
    },
    {
        title: 'Frontline',
        items: [
            { key: 'frontline_areas_organized', label: 'Areas Organized' },
            { key: 'frontline_customers_greeted', label: 'Customers Greeted' },
            { key: 'frontline_menu_available', label: 'Menu Available & Readable' },
            { key: 'frontline_seven_steps', label: 'Seven Steps Service' },
            { key: 'frontline_tables_clean', label: 'Tables Clean' },
            { key: 'frontline_high_chairs', label: 'High Chairs Available & Clean' },
            { key: 'frontline_no_damaged_tables', label: 'No Damaged Tables/Chairs' }
        ]
    },
    {
        title: 'Restrooms',
        items: [
            { key: 'restroom_no_full_trash', label: 'No Full Trash Bins' },
            { key: 'restroom_soap_available', label: 'Soap Available' },
            { key: 'restroom_hand_dryer', label: 'Hand Dryer Working' }
        ]
    },
    {
        title: 'Holding & Heating',
        items: [
            { key: 'holding_product_temp', label: 'Product Temperature Correct' },
            { key: 'holding_product_age', label: 'Product Age Within Limits' },
            { key: 'holding_check_product', label: 'Check Product Quality' },
            { key: 'holding_products_fresh', label: 'Products Fresh' },
            { key: 'holding_internal_temp', label: 'Internal Temperature Monitored' },
            { key: 'holding_shortening_level', label: 'Shortening Level Correct' },
            { key: 'holding_check_shortening', label: 'Check Shortening Quality' },
            { key: 'holding_fryer_maintenance', label: 'Fryer Maintenance Done' },
            { key: 'holding_use_tray', label: 'Use Proper Trays' },
            { key: 'holding_fry_basket', label: 'Fry Basket Clean' },
            { key: 'holding_fries_salted', label: 'Fries Properly Salted' },
            { key: 'holding_fries_cooking', label: 'Fries Cooking Time Correct' },
            { key: 'holding_buns_quality', label: 'Buns Quality Good' },
            { key: 'holding_teflon_sheet', label: 'Teflon Sheet Used' },
            { key: 'holding_bread_standard', label: 'Bread Standard Met' }
        ]
    },
    {
        title: 'Thawing',
        items: [
            { key: 'thawing_day_labels', label: 'Day Labels Applied' },
            { key: 'thawing_no_tampering', label: 'No Tampering with Products' },
            { key: 'thawing_temp_range', label: 'Temperature Range Correct' },
            { key: 'thawing_no_overstock', label: 'No Overstocking' },
            { key: 'thawing_utensils_clean', label: 'Utensils Clean' },
            { key: 'thawing_sink_setup', label: 'Sink Setup Proper' },
            { key: 'thawing_portion_standards', label: 'Portion Standards Met' },
            { key: 'thawing_sultan_sauce', label: 'Sultan Sauce Quality' },
            { key: 'thawing_vegetables_date', label: 'Vegetables Date Labeled' },
            { key: 'thawing_follow_fifo', label: 'Follow FIFO (First In, First Out)' }
        ]
    },
    {
        title: 'Burger Assembly',
        items: [
            { key: 'burger_standard_setup', label: 'Standard Setup Followed' },
            { key: 'burger_sauce_spiral', label: 'Sauce Applied in Spiral' },
            { key: 'burger_ingredients_order', label: 'Ingredients in Correct Order' }
        ]
    }
];
</script>

<template>
    <Head title="View QSC Checklist" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    QSC Checklist Details
                </h2>
                <Link 
                    :href="route('qsc-checklist.report')" 
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Reports
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header Information Card -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
                    <div class="p-6 bg-gradient-to-r from-blue-50 to-indigo-50">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Store Name</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ checklist.store_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Date</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ formatDate(checklist.date) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Time Option</p>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ checklist.time_option }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Status</p>
                                <span class="mt-1 inline-flex px-3 py-1 text-sm font-semibold rounded-full" :class="getStatusColor(checklist.status)">
                                    {{ checklist.status }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mt-6 pt-6 border-t border-gray-200">
                            <div>
                                <p class="text-sm font-medium text-gray-500">MOD</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ checklist.mod || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Day</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ checklist.day || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Manager Signature</p>
                                <p class="mt-1 text-base font-semibold text-gray-900">{{ checklist.manager_signature || 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Compliance Score</p>
                                <div class="mt-1 flex items-center">
                                    <div class="text-2xl font-bold" :class="complianceScore >= 80 ? 'text-green-600' : complianceScore >= 60 ? 'text-yellow-600' : 'text-red-600'">
                                        {{ complianceScore }}%
                                    </div>
                                    <div class="ml-3 w-20 bg-gray-200 rounded-full h-2">
                                        <div 
                                            class="h-2 rounded-full transition-all duration-300"
                                            :class="complianceScore >= 80 ? 'bg-green-600' : complianceScore >= 60 ? 'bg-yellow-600' : 'bg-red-600'"
                                            :style="{ width: complianceScore + '%' }">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="complianceData">
                                <p class="text-sm font-medium text-gray-500">Issues Found</p>
                                <div class="mt-1 flex items-center space-x-3">
                                    <span class="text-lg font-bold text-red-600">{{ complianceData.issues_count || 0 }}</span>
                                    <span class="text-xs text-gray-500">/ {{ complianceData.total_questions || 0 }} items</span>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div v-if="complianceData" class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6 pt-6 border-t border-gray-200">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600">{{ complianceData.yes_answers || 0 }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Passed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-600">{{ complianceData.no_answers || 0 }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Failed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ complianceData.comments_count || 0 }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Comments</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-gray-600">{{ complianceData.total_questions || 0 }}</div>
                                <div class="text-xs text-gray-500 uppercase tracking-wide">Total Items</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checklist Sections -->
                <div class="space-y-6">
                    <div v-for="(section, index) in sections" :key="section.title" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-indigo-600">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-semibold text-white">{{ section.title }}</h3>
                                <div v-if="complianceData && complianceData.section_scores" class="flex items-center space-x-3">
                                    <div class="text-white text-sm">
                                        {{ getSectionPassedCount(section) }}/{{ section.items.length }} Passed
                                    </div>
                                    <div class="bg-white bg-opacity-20 rounded-full px-3 py-1">
                                        <span class="text-white font-semibold text-sm">
                                            {{ getSectionScore(section) }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="item in section.items" :key="item.key" class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center space-x-3">
                                                <span 
                                                    v-if="checklist[item.key]"
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold"
                                                    :class="getAnswerBadge(checklist[item.key]).class">
                                                    {{ getAnswerBadge(checklist[item.key]).icon }}
                                                </span>
                                                <span v-else class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                                    -
                                                </span>
                                                <p class="text-sm font-medium text-gray-700">{{ item.label }}</p>
                                            </div>
                                            <div v-if="checklist[item.key + '_comment']" class="mt-3 ml-9 p-3 bg-amber-50 border-l-4 border-amber-400 rounded-r">
                                                <div class="flex items-start">
                                                    <svg class="w-4 h-4 text-amber-600 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                    </svg>
                                                    <div>
                                                        <p class="text-sm font-semibold text-amber-800">Comment:</p>
                                                        <p class="text-sm text-amber-700 mt-1">{{ checklist[item.key + '_comment'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <span 
                                                v-if="checklist[item.key]"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold"
                                                :class="getAnswerBadge(checklist[item.key]).class">
                                                {{ checklist[item.key] }}
                                            </span>
                                            <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                                Not Answered
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Information -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
                    <div class="p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Audit Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created By</p>
                                <p class="mt-1 text-base text-gray-900">{{ checklist.user?.name || 'N/A' }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ checklist.user?.roles?.[0]?.name || 'Role not specified' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Created At</p>
                                <p class="mt-1 text-base text-gray-900">{{ formatDateTime(checklist.created_at) }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Last Updated</p>
                                <p class="mt-1 text-base text-gray-900">{{ formatDateTime(checklist.updated_at) }}</p>
                            </div>
                        </div>
                        
                        <!-- Summary Statistics -->
                        <div v-if="complianceData" class="mt-6 pt-6 border-t border-gray-200">
                            <h5 class="text-md font-semibold text-gray-900 mb-3">Audit Summary</h5>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-center">
                                    <div>
                                        <div class="text-lg font-bold text-gray-900">{{ complianceData.total_questions }}</div>
                                        <div class="text-xs text-gray-500">Total Items Checked</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-green-600">{{ complianceData.yes_answers }}</div>
                                        <div class="text-xs text-gray-500">Items Passed</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-red-600">{{ complianceData.no_answers }}</div>
                                        <div class="text-xs text-gray-500">Items Failed</div>
                                    </div>
                                    <div>
                                        <div class="text-lg font-bold text-blue-600">{{ complianceData.comments_count }}</div>
                                        <div class="text-xs text-gray-500">Comments Added</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 flex items-center space-x-4">
                    <Link 
                        :href="route('qsc-checklist.report')" 
                        class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </Link>
                    
                    <Link 
                        :href="route('qsc-checklist.edit', checklist.id)" 
                        class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:border-yellow-900 focus:ring focus:ring-yellow-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Checklist
                    </Link>

                    <button
                        @click="window.print()"
                        class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print
                    </button>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none;
    }
}
</style>
