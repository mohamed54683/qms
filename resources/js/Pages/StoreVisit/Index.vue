<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { usePermissions } from '@/Composables/usePermissions';
import Toast from '@/Components/Toast.vue';
import SuccessModal from '@/Components/SuccessModal.vue';

// Import route helper
const route = window.route;

// Get page props for flash messages
const page = usePage();

// Use permissions composable
const { can, hasRestaurantAccess, isAdmin, accessibleRestaurants } = usePermissions();

// Receive data from backend
const props = defineProps({
    visits: Object, // Changed to Object for pagination support
    restaurants: Array,
    areaManagers: Array,
    assignedRestaurants: Array,
    filters: Object,
    isAdmin: Boolean,
    canViewAreaManagerFilter: Boolean,
    canExportAll: Boolean,
    canViewAllRestaurants: Boolean,
    auth: Object
});

// Computed property for visits data (pagination support)
const visitsData = computed(() => props.visits?.data || []);

// Filter form data
const filters = ref({
    restaurant: props.filters?.restaurant || '',
    restaurants: props.filters?.restaurants || [],
    area_manager: props.filters?.area_manager || '',
    mic: props.filters?.mic || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    status: props.filters?.status || '',
    score_min: props.filters?.score_min || '',
    score_max: props.filters?.score_max || '',
    search: props.filters?.search || '',
    purpose: props.filters?.purpose || '',
    has_action_items: props.filters?.has_action_items || ''
});

// UI state
const showAllRestaurants = ref(false);
const selectedVisits = ref([]);
const bulkAction = ref('');
const showFilters = ref(false);
const searchQuery = ref(props.filters?.search || '');
const loading = ref(false);
const exportLoading = ref(false);
const hoveredCard = ref(null);
const isSearching = ref(false);
const showSearchSuggestions = ref(false);
const selectedSuggestionIndex = ref(-1);
const searchInput = ref(null);
const tableView = ref('list');
const showExportDropdown = ref(false);
const showPrintModal = ref(false);
const confirmingAll = ref(false);

// Toast notification state
const toast = ref({
    show: false,
    type: 'success',
    title: '',
    message: ''
});

// Success Modal state
const successModal = ref({
    show: false,
    title: 'Action Plans Created Successfully!',
    message: '',
    actionPlansCount: 0
});

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        // Check if it's an action plans success message
        const message = flash.success;
        const actionPlanMatch = message.match(/(\d+)\s+action\s+plans?\s+created/i);
        
        if (actionPlanMatch) {
            // Show success modal for action plans
            const count = parseInt(actionPlanMatch[1]);
            successModal.value = {
                show: true,
                title: 'Action Plans Created Successfully!',
                message: message,
                actionPlansCount: count
            };
        } else {
            // Show toast for other success messages
            toast.value = {
                show: true,
                type: 'success',
                title: '✨ Success!',
                message: message
            };
        }
    } else if (flash?.error) {
        toast.value = {
            show: true,
            type: 'error',
            title: '❌ Error',
            message: flash.error
        };
    }
}, { deep: true, immediate: true });

// Filter options
const micOptions = ['Morning', 'Evening'];
const statusOptions = ['Draft', 'Submitted', 'Reviewed', 'Approved'];

// Apply filters
const applyFilters = () => {
    router.get(route('store-visits.index'), filters.value, {
        preserveState: true,
        replace: true
    });
};

// Clear filters
const clearFilters = () => {
    filters.value = {
        restaurant: '',
        mic: '',
        date_from: '',
        date_to: '',
        status: '',
        score_min: '',
        score_max: '',
        search: '',
        purpose: '',
        has_action_items: ''
    };
    applyFilters();
};

// Quick filter functions
const quickFilterToday = () => {
    const today = new Date().toISOString().split('T')[0];
    filters.value.date_from = today;
    filters.value.date_to = today;
};

const quickFilterWeek = () => {
    const today = new Date();
    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    filters.value.date_from = weekAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
};

const quickFilterMonth = () => {
    const today = new Date();
    const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    filters.value.date_from = monthAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
};

// Debounced filter application to improve performance
let filterTimeout;
watch(filters, () => {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        applyFilters();
    }, 500); // Wait 500ms before applying filters for better performance
}, { deep: true });

// Statistics calculations
const statistics = computed(() => {
    const total = visitsData.value.length;
    const completed = visitsData.value.filter(v => v.status === 'Submitted' || v.status === 'Approved').length;
    const pending = visitsData.value.filter(v => v.status === 'Draft' || v.status === 'Pending Review').length;
    const withActionItems = visitsData.value.filter(v => v.actionItems > 0).length;
    const averageScore = total > 0 ? Math.round(visitsData.value.reduce((acc, v) => acc + (v.score || 0), 0) / total) : 0;
    const highPerformers = visitsData.value.filter(v => (v.score || 0) >= 90).length;
    const lowPerformers = visitsData.value.filter(v => (v.score || 0) < 70).length;
    
    return {
        total,
        completed,
        pending,
        withActionItems,
        averageScore,
        highPerformers,
        lowPerformers,
        completionRate: total > 0 ? Math.round((completed / total) * 100) : 0
    };
});

// Removed duplicate isAdmin - already imported from usePermissions composable

const canViewAllRestaurants = computed(() => {
    return isAdmin.value;
});

const userRestaurantCount = computed(() => {
    if (isAdmin.value) return props.restaurants?.length || 0;
    // For non-admin users, restaurants are already filtered by backend
    return props.restaurants?.length || 0;
});

const pendingConfirmationCount = computed(() => {
    return visitsData.value.filter(visit => visit.needsActionPlans).length;
});

// Export functions with loading state
const exportPDF = () => {
    exportLoading.value = true;
    
    // Show loading message
    const loadingToast = document.createElement('div');
    loadingToast.id = 'pdf-loading-toast';
    loadingToast.className = 'fixed top-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-fade-in';
    loadingToast.innerHTML = `
        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="font-medium">Generating PDF Report...</span>
    `;
    document.body.appendChild(loadingToast);
    
    // Open PDF in new tab
    window.open(route('store-visits.export-pdf', filters.value), '_blank');
    
    // Remove loading message and show success
    setTimeout(() => {
        loadingToast.remove();
        exportLoading.value = false;
        
        const successToast = document.createElement('div');
        successToast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 animate-fade-in';
        successToast.innerHTML = `
            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span class="font-medium">PDF Report Ready! Check your downloads.</span>
        `;
        document.body.appendChild(successToast);
        
        setTimeout(() => successToast.remove(), 3000);
    }, 1500);
};

const exportExcel = () => {
    exportLoading.value = true;
    window.open(route('store-visits.export-excel', filters.value), '_blank');
    setTimeout(() => exportLoading.value = false, 1500);
};

const exportCSV = () => {
    exportLoading.value = true;
    window.open(route('store-visits.export-csv', filters.value), '_blank');
    setTimeout(() => exportLoading.value = false, 1500);
};

const printReports = () => {
    showPrintModal.value = true;
};

// Bulk actions
const selectAll = ref(false);

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedVisits.value = visitsData.value.map(v => v.id);
    } else {
        selectedVisits.value = [];
    }
};

const bulkUpdateStatus = (status) => {
    if (selectedVisits.value.length === 0) {
        alert('Please select visits to update');
        return;
    }
    
    router.post(route('store-visits.bulk-update'), {
        visits: selectedVisits.value,
        status: status
    });
};

const exportSinglePDF = (visitId) => {
    window.open(route('store-visits.export-single-pdf', visitId), '_blank');
};

// Print Visit Report in Modern Format
const printVisitReport = (visit) => {
    // Open print page in new window
    const printUrl = route('store-visits.print-report', visit.id);
    const printWindow = window.open(printUrl, '_blank', 'width=800,height=600');
    
    // Focus the print window
    if (printWindow) {
        printWindow.focus();
    }
};

// Action Plan Confirmation
const confirmingVisit = ref(null);
const submittingVisit = ref(null);

const submitVisit = async (visitId) => {
    if (submittingVisit.value) {
        return;
    }
    
    if (!confirm('Submit this draft report? Status will change to "Submitted".')) {
        return;
    }
    
    submittingVisit.value = visitId;
    
    try {
        router.post(route('store-visits.submit-visit', visitId), {}, {
            preserveScroll: true,
            onSuccess: () => {
                submittingVisit.value = null;
            },
            onError: (errors) => {
                submittingVisit.value = null;
                console.error('Error submitting visit:', errors);
                alert('Error: ' + (errors.message || 'Failed to submit visit'));
            }
        });
    } catch (error) {
        submittingVisit.value = null;
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
};

const confirmActionPlans = async (visitId) => {
    if (confirmingVisit.value) {
        return;
    }
    
    // Show immediate feedback
    confirmingVisit.value = visitId;
    
    try {
        // Use Inertia router.post for proper CSRF handling
        router.post(route('store-visits.confirm-action-plans'), {
            visit_id: visitId
        }, {
            preserveScroll: true,
            onSuccess: () => {
                confirmingVisit.value = null;
                // Success message will be shown via flash message
            },
            onError: (errors) => {
                confirmingVisit.value = null;
                console.error('Error creating action plans:', errors);
                alert('Error: ' + (errors.message || 'Failed to create action plans'));
            }
        });
    } catch (error) {
        confirmingVisit.value = null;
        console.error('Error:', error);
        alert('Error: ' + error.message);
    }
};

// Confirm All Pending Visits
const confirmAllPendingVisits = async () => {
    if (confirmingAll.value) return;
    
    const pendingVisits = visitsData.value.filter(visit => visit.needsActionPlans);
    
    if (pendingVisits.length === 0) {
        toast.value = {
            show: true,
            type: 'info',
            title: '✨ All Set!',
            message: 'No visits need action plan confirmation. Everything is up to date!'
        };
        return;
    }
    
    // Show confirmation dialog with details
    const confirmMessage = `🚀 CONFIRM ALL ACTION PLANS\n\n` +
        `This will automatically:\n` +
        `• Process ${pendingVisits.length} pending visit(s)\n` +
        `• Create action plans for all "NO" answers\n` +
        `• Include comments from inspection forms\n` +
        `• Mark visits as "Approved"\n\n` +
        `Proceed with bulk confirmation?`;
    
    if (!confirm(confirmMessage)) {
        return;
    }
    
    confirmingAll.value = true;
    
    try {
        const visitIds = pendingVisits.map(visit => visit.id);
        
        router.post(route('store-visits.bulk-confirm-action-plans'), {
            visit_ids: visitIds
        }, {
            preserveScroll: true,
            onSuccess: () => {
                confirmingAll.value = false;
                // Show success modal instead of toast for better impact
                successModal.value = {
                    show: true,
                    title: '🎉 Bulk Confirmation Successful!',
                    message: `Successfully processed ${pendingVisits.length} visit(s) and created action plans with comments.`,
                    actionPlansCount: pendingVisits.length
                };
            },
            onError: (errors) => {
                confirmingAll.value = false;
                console.error('Error creating bulk action plans:', errors);
                toast.value = {
                    show: true,
                    type: 'error',
                    title: '❌ Bulk Confirmation Failed',
                    message: errors.message || 'Failed to confirm action plans. Please try again.'
                };
            }
        });
    } catch (error) {
        confirmingAll.value = false;
        console.error('Error:', error);
        toast.value = {
            show: true,
            type: 'error',
            title: '❌ System Error',
            message: error.message || 'An unexpected error occurred during bulk confirmation.'
        };
    }
};

// Simple CountUp component
const CountUp = {
    props: {
        end: {
            type: Number,
            required: true
        },
        suffix: {
            type: String,
            default: ''
        },
        duration: {
            type: Number,
            default: 2000
        }
    },
    setup(props) {
        const current = ref(0);
        
        const animate = () => {
            const start = performance.now();
            const step = (timestamp) => {
                const progress = Math.min((timestamp - start) / props.duration, 1);
                current.value = Math.floor(progress * props.end);
                
                if (progress < 1) {
                    requestAnimationFrame(step);
                }
            };
            requestAnimationFrame(step);
        };
        
        // Start animation when component mounts
        setTimeout(() => animate(), 100);
        
        return { current };
    },
    template: `<span>{{ current }}{{ suffix }}</span>`
};

// Enhanced search functionality
const searchSuggestions = computed(() => {
    if (!searchQuery.value || searchQuery.value.length < 2) return [];
    
    const suggestions = [];
    const searchLower = searchQuery.value.toLowerCase();
    
    // Restaurant suggestions
    const restaurantMatches = props.restaurants.filter(r => 
        r.name.toLowerCase().includes(searchLower) || 
        r.code.toLowerCase().includes(searchLower)
    ).slice(0, 3);
    
    restaurantMatches.forEach(restaurant => {
        const count = visitsData.value.filter(v => v.restaurant === restaurant.name).length;
        suggestions.push({
            text: restaurant.name,
            category: 'Restaurant',
            count,
            action: () => {
                filters.value.restaurant = restaurant.name;
                applyFilters();
            }
        });
    });
    
    // Purpose suggestions
    const purposes = ['Quality Audit', 'Operational Assessment', 'Training Audit', 'Measurement & Coaching'];
    const purposeMatches = purposes.filter(p => p.toLowerCase().includes(searchLower)).slice(0, 2);
    
    purposeMatches.forEach(purpose => {
        const count = visitsData.value.filter(v => 
            Array.isArray(v.purpose) ? v.purpose.includes(purpose) : v.purpose === purpose
        ).length;
        suggestions.push({
            text: purpose,
            category: 'Purpose',
            count,
            action: () => {
                filters.value.purpose = purpose;
                applyFilters();
            }
        });
    });
    
    // Status suggestions
    const statuses = ['Draft', 'Submitted', 'Reviewed', 'Approved'];
    const statusMatches = statuses.filter(s => s.toLowerCase().includes(searchLower)).slice(0, 2);
    
    statusMatches.forEach(status => {
        const count = visitsData.value.filter(v => v.status === status).length;
        suggestions.push({
            text: status,
            category: 'Status',
            count,
            action: () => {
                filters.value.status = status;
                applyFilters();
            }
        });
    });
    
    return suggestions.slice(0, 6);
});

const filteredVisitsCount = computed(() => {
    if (!searchQuery.value) return visitsData.value.length;
    
    const searchLower = searchQuery.value.toLowerCase();
    return visitsData.value.filter(visit => 
        visit.restaurant?.toLowerCase().includes(searchLower) ||
        visit.purpose?.toString().toLowerCase().includes(searchLower) ||
        visit.status?.toLowerCase().includes(searchLower) ||
        visit.mic?.toLowerCase().includes(searchLower)
    ).length;
});

const handleSearchInput = () => {
    isSearching.value = true;
    
    // Debounce search
    clearTimeout(window.searchTimeout);
    window.searchTimeout = setTimeout(() => {
        filters.value.search = searchQuery.value;
        applyFilters();
        isSearching.value = false;
    }, 300);
};

const handleSearchKeydown = (event) => {
    if (!showSearchSuggestions.value || searchSuggestions.value.length === 0) return;
    
    switch (event.key) {
        case 'ArrowDown':
            event.preventDefault();
            selectedSuggestionIndex.value = Math.min(
                selectedSuggestionIndex.value + 1,
                searchSuggestions.value.length - 1
            );
            break;
        case 'ArrowUp':
            event.preventDefault();
            selectedSuggestionIndex.value = Math.max(selectedSuggestionIndex.value - 1, -1);
            break;
        case 'Enter':
            event.preventDefault();
            if (selectedSuggestionIndex.value >= 0) {
                selectSuggestion(searchSuggestions.value[selectedSuggestionIndex.value]);
            }
            break;
        case 'Escape':
            showSearchSuggestions.value = false;
            selectedSuggestionIndex.value = -1;
            searchInput.value?.blur();
            break;
    }
};

const selectSuggestion = (suggestion) => {
    searchQuery.value = suggestion.text;
    showSearchSuggestions.value = false;
    selectedSuggestionIndex.value = -1;
    suggestion.action();
};

const hideSearchSuggestions = () => {
    setTimeout(() => {
        showSearchSuggestions.value = false;
        selectedSuggestionIndex.value = -1;
    }, 150);
};

const clearSearch = () => {
    searchQuery.value = '';
    filters.value.search = '';
    applyFilters();
};
</script>

<template>
    <Head title="Store Visit Reports" />
    
    <!-- Success Modal for Action Plans -->
    <SuccessModal
        v-if="successModal.show"
        :title="successModal.title"
        :message="successModal.message"
        :action-plans-count="successModal.actionPlansCount"
        @close="successModal.show = false"
    />
    
    <!-- Toast Notification -->
    <Toast
        v-if="toast.show"
        :type="toast.type"
        :title="toast.title"
        :message="toast.message"
        @close="toast.show = false"
    />

    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-slate-50 to-blue-50 border-b border-gray-200 -mx-6 -mt-6 px-6 pt-6 pb-6 mb-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-blue-100 rounded-xl shadow-sm">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Store Visit Reports</h1>
                                <div class="h-0.5 w-20 bg-blue-500 rounded-full mt-1"></div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-base flex items-center gap-2">
                            <span>Monitor store quality assessments and performance metrics</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ statistics.total }} Reports
                            </span>
                        </p>
                    </div>
                    
                    <!-- Enhanced Organized Actions Toolbar -->
                    <div class="flex flex-col gap-3 w-full md:w-auto">
                        <!-- Primary Actions Row -->
                        <div class="flex flex-wrap gap-2 items-center justify-end">
                            <!-- Print PDF Button (Most Prominent) - HIDDEN -->
                            <!-- <button @click="exportPDF" 
                                    class="group flex items-center justify-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 relative overflow-hidden">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                <svg class="w-5 h-5 group-hover:animate-pulse relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                <span class="relative z-10">Print PDF</span>
                                <span class="absolute top-0 right-0 w-2 h-2 bg-yellow-300 rounded-full animate-ping"></span>
                            </button> -->

                            <!-- Confirm All Button -->
                            <button @click="confirmAllPendingVisits" 
                                    v-if="pendingConfirmationCount > 0"
                                    :disabled="confirmingAll"
                                    class="group flex items-center justify-center gap-2 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 disabled:from-green-400 disabled:to-green-500 text-white px-6 py-3 rounded-xl font-bold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 relative overflow-hidden">
                                <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                <svg v-if="confirmingAll" class="w-5 h-5 animate-spin relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <svg v-else class="w-5 h-5 group-hover:animate-pulse relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="relative z-10 font-bold">
                                    {{ confirmingAll ? 'Processing...' : `✅ Confirm All (${pendingConfirmationCount})` }}
                                </span>
                                <span v-if="!confirmingAll" class="absolute top-1 right-1 w-3 h-3 bg-yellow-300 rounded-full animate-pulse"></span>
                            </button>
                            
                            <!-- Export Dropdown (Secondary Action) -->
                            <div class="relative">
                                <button @click="showExportDropdown = !showExportDropdown" 
                                        class="flex items-center justify-center gap-2 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium transition-all duration-200 shadow-md hover:shadow-lg"
                                        :class="{ 'ring-2 ring-emerald-300': showExportDropdown }">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                    </svg>
                                    <span>More Exports</span>
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showExportDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                            
                            <!-- Export Dropdown Menu -->
                            <Transition
                                enter-active-class="transition duration-200 ease-out"
                                enter-from-class="transform scale-95 opacity-0 translate-y-2"
                                enter-to-class="transform scale-100 opacity-100 translate-y-0"
                                leave-active-class="transition duration-150 ease-in"
                                leave-from-class="transform scale-100 opacity-100 translate-y-0"
                                leave-to-class="transform scale-95 opacity-0 translate-y-2"
                            >
                                <div v-if="showExportDropdown" 
                                     @click.away="showExportDropdown = false"
                                     class="absolute right-0 top-full mt-2 w-72 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
                                    <!-- Header -->
                                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3">
                                        <div class="flex items-center justify-between text-white">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                                                </svg>
                                                <span class="font-semibold">Export Options</span>
                                            </div>
                                            <span class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ statistics.total }} reports</span>
                                        </div>
                                    </div>
                                    
                                    <div class="p-3 space-y-1.5">
                                        <!-- Excel Export -->
                                        <button @click="exportExcel(); showExportDropdown = false" 
                                                class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-gray-700 hover:bg-green-50 rounded-xl transition-all duration-200 group border-2 border-transparent hover:border-green-200">
                                            <div class="p-2.5 bg-gradient-to-br from-green-100 to-green-200 group-hover:from-green-200 group-hover:to-green-300 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">Excel Spreadsheet</div>
                                                <div class="text-xs text-gray-500">Full data analysis (.xlsx)</div>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                        
                                        <!-- PDF Export (Alternative) - HIDDEN -->
                                        <!-- <button @click="exportPDF(); showExportDropdown = false" 
                                                class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-gray-700 hover:bg-red-50 rounded-xl transition-all duration-200 group border-2 border-transparent hover:border-red-200">
                                            <div class="p-2.5 bg-gradient-to-br from-red-100 to-red-200 group-hover:from-red-200 group-hover:to-red-300 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5 text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">PDF Document</div>
                                                <div class="text-xs text-gray-500">Detailed report (.pdf)</div>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button> -->
                                        
                                        <!-- CSV Export -->
                                        <button @click="exportCSV(); showExportDropdown = false" 
                                                class="w-full flex items-center gap-3 px-4 py-3 text-left text-sm text-gray-700 hover:bg-blue-50 rounded-xl transition-all duration-200 group border-2 border-transparent hover:border-blue-200">
                                            <div class="p-2.5 bg-gradient-to-br from-blue-100 to-blue-200 group-hover:from-blue-200 group-hover:to-blue-300 rounded-xl transition-all shadow-sm">
                                                <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <div class="font-semibold text-gray-900">CSV File</div>
                                                <div class="text-xs text-gray-500">Plain text data (.csv)</div>
                                            </div>
                                            <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Footer Info -->
                                    <div class="px-4 py-3 bg-gradient-to-r from-gray-50 to-gray-100 border-t border-gray-200">
                                        <div v-if="isAdmin" class="flex items-center gap-2 text-xs text-gray-600">
                                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            <span><strong>Admin:</strong> Exporting all reports</span>
                                        </div>
                                        <div v-else class="flex items-center gap-2 text-xs text-gray-600">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span>Exporting your filtered reports</span>
                                        </div>
                                    </div>
                                </div>
                            </Transition>
                        </div>

                        <!-- New Visit Report Button -->
                        <Link 
                            v-if="can('store_visits', 'create')"
                            :href="route('store-visits.create')" 
                            class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 relative overflow-hidden group"
                        >
                            <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            <span class="relative z-10">New Visit</span>
                        </Link>
                    </div>

                        <!-- Search Bar Row -->
                        <div class="relative flex-1 md:w-96 group">
                            <!-- Search Icon with Animation -->
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-200" 
                                     :class="{ 'animate-pulse': isSearching }" 
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            
                            <!-- Enhanced Search Input -->
                            <input
                                ref="searchInput"
                                v-model="searchQuery"
                                @input="handleSearchInput"
                                @focus="showSearchSuggestions = true"
                                @blur="hideSearchSuggestions"
                                @keydown="handleSearchKeydown"
                                type="text"
                                placeholder="🔍 Search restaurants, purposes, comments, or any text..."
                                class="block w-full pl-10 pr-12 py-3 border-2 border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 shadow-sm focus:shadow-md group-hover:shadow-md"
                                :class="{ 'bg-blue-50 border-blue-400': showSearchSuggestions }"
                            />
                            
                            <!-- Clear Search Button -->
                            <button 
                                v-if="searchQuery"
                                @click="clearSearch"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                            
                            <!-- Search Results Count -->
                            <div v-if="searchQuery && !showSearchSuggestions" 
                                 class="absolute -bottom-6 left-0 text-xs text-gray-500">
                                {{ filteredVisitsCount }} results found
                            </div>
                            
                            <!-- Search Suggestions Dropdown -->
                            <div v-if="showSearchSuggestions && searchSuggestions.length > 0" 
                                 class="absolute top-full left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-30 max-h-64 overflow-y-auto">
                                <div class="p-2">
                                    <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide px-3 py-2">Quick Suggestions</div>
                                    <div 
                                        v-for="(suggestion, index) in searchSuggestions" 
                                        :key="index"
                                        @mousedown.prevent="selectSuggestion(suggestion)"
                                        class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-blue-50 cursor-pointer transition-colors group"
                                        :class="{ 'bg-blue-100': selectedSuggestionIndex === index }"
                                    >
                                        <div class="p-1.5 rounded-md bg-gray-100 group-hover:bg-blue-100">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900">{{ suggestion.text }}</div>
                                            <div class="text-xs text-gray-500">{{ suggestion.category }}</div>
                                        </div>
                                        <div class="text-xs text-blue-500 opacity-0 group-hover:opacity-100 transition-opacity">
                                            {{ suggestion.count }} results
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <Link 
                            v-if="can('store_visits', 'create')"
                            :href="route('store-visits.create')" 
                            class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            📋 New Visit Report
                        </Link>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-4 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- User Access Information (for non-admin) -->
                <div v-if="!isAdmin" class="mb-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-blue-900">
                                <strong>Your Access:</strong> 
                                You can view {{ visits.length }} reports from {{ userRestaurantCount }} restaurant(s) assigned to you.
                            </p>
                            <div class="flex gap-2 mt-1">
                                <span v-if="canViewAllRestaurants" class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">Full Access</span>
                                <span v-else class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">User Access</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Interactive Statistics Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Visits Card -->
                    <div 
                        @click="clearFilters"
                        @mouseenter="hoveredCard = 'total'"
                        @mouseleave="hoveredCard = null"
                        class="group cursor-pointer bg-gradient-to-br from-white to-blue-50 hover:from-blue-50 hover:to-blue-100 border border-gray-200 hover:border-blue-400 p-5 rounded-xl shadow-sm hover:shadow-xl transform hover:scale-110 hover:-translate-y-2 transition-all duration-500 relative overflow-hidden"
                        :class="{ 'ring-2 ring-blue-300 ring-opacity-50': hoveredCard === 'total' }"
                    >
                        <!-- Animated background gradient -->
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-400/10 to-purple-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Floating particles effect -->
                        <div class="absolute top-2 right-2 w-2 h-2 bg-blue-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-all duration-700"></div>
                        <div class="absolute bottom-3 left-3 w-1 h-1 bg-purple-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-pulse transition-all duration-1000"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-blue-100 to-blue-200 group-hover:from-blue-200 group-hover:to-blue-300 rounded-xl transition-all duration-300 group-hover:rotate-6 group-hover:scale-110 shadow-lg group-hover:shadow-xl">
                                    <svg class="w-7 h-7 text-blue-600 group-hover:text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors duration-300 font-mono">
                                        {{ statistics.total }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">Total Visits</p>
                                    <!-- Tooltip -->
                                    <div v-if="hoveredCard === 'total'" class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-xs whitespace-nowrap z-20 opacity-0 animate-fadeInUp">
                                        Click to view all visits
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-3 w-16 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full w-full animate-pulse group-hover:animate-none transition-all duration-500"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block group-hover:text-blue-600 transition-colors">100%</span>
                            </div>
                        </div>
                        
                        <!-- Bottom accent line -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-blue-500 to-purple-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                    </div>
                    
                    <!-- Completed Card -->
                    <div 
                        @click="filters.status = 'Submitted'; applyFilters()"
                        @mouseenter="hoveredCard = 'completed'"
                        @mouseleave="hoveredCard = null"
                        class="group cursor-pointer bg-gradient-to-br from-white to-green-50 hover:from-green-50 hover:to-green-100 border border-gray-200 hover:border-green-400 p-5 rounded-xl shadow-sm hover:shadow-xl transform hover:scale-110 hover:-translate-y-2 transition-all duration-500 relative overflow-hidden"
                        :class="{ 'ring-2 ring-green-300 ring-opacity-50': hoveredCard === 'completed' }"
                    >
                        <!-- Success celebration particles -->
                        <div class="absolute inset-0 bg-gradient-to-br from-green-400/10 to-emerald-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Checkmark animation elements -->
                        <div class="absolute top-2 right-2 w-2 h-2 bg-green-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce transition-all duration-700"></div>
                        <div class="absolute bottom-3 left-3 w-1 h-1 bg-emerald-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-all duration-1000"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-green-100 to-green-200 group-hover:from-green-200 group-hover:to-green-300 rounded-xl transition-all duration-300 group-hover:rotate-12 group-hover:scale-110 shadow-lg group-hover:shadow-xl">
                                    <svg class="w-7 h-7 text-green-600 group-hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-green-700 transition-colors duration-300 font-mono">
                                        {{ statistics.completed }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">✅ Completed</p>
                                    <!-- Tooltip -->
                                    <div v-if="hoveredCard === 'completed'" class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-xs whitespace-nowrap z-20 opacity-0 animate-fadeInUp">
                                        Click to filter completed visits
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-3 w-16 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-green-500 to-emerald-500 rounded-full transition-all duration-1000 animate-pulse group-hover:animate-none" 
                                         :style="`width: ${statistics.total ? (statistics.completed / statistics.total * 100) : 0}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block group-hover:text-green-600 transition-colors">{{ statistics.completionRate }}%</span>
                            </div>
                        </div>
                        
                        <!-- Completion progress indicator -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-green-500 to-emerald-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700"></div>
                    </div>
                    
                    <!-- Pending Card -->
                    <div 
                        @click="filters.status = 'Draft'; applyFilters()"
                        @mouseenter="hoveredCard = 'pending'"
                        @mouseleave="hoveredCard = null"
                        class="group cursor-pointer bg-gradient-to-br from-white to-orange-50 hover:from-orange-50 hover:to-orange-100 border border-gray-200 hover:border-orange-400 p-5 rounded-xl shadow-sm hover:shadow-xl transform hover:scale-110 hover:-translate-y-2 transition-all duration-500 relative overflow-hidden"
                        :class="{ 'ring-2 ring-orange-300 ring-opacity-50': hoveredCard === 'pending' }"
                    >
                        <!-- Pending animation elements -->
                        <div class="absolute inset-0 bg-gradient-to-br from-orange-400/10 to-yellow-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Clock ticking animation -->
                        <div class="absolute top-2 right-2 w-2 h-2 bg-orange-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-pulse transition-all duration-700"></div>
                        <div class="absolute bottom-3 left-3 w-1 h-1 bg-yellow-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce transition-all duration-1000"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-orange-100 to-orange-200 group-hover:from-orange-200 group-hover:to-orange-300 rounded-xl transition-all duration-300 group-hover:rotate-6 group-hover:scale-110 shadow-lg group-hover:shadow-xl">
                                    <svg class="w-7 h-7 text-orange-600 group-hover:text-orange-700 group-hover:animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-orange-700 transition-colors duration-300 font-mono">
                                        {{ statistics.pending }}
                                    </p>
                                    <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">⏳ Pending</p>
                                    <!-- Tooltip -->
                                    <div v-if="hoveredCard === 'pending'" class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-xs whitespace-nowrap z-20 opacity-0 animate-fadeInUp">
                                        Click to filter pending visits
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-3 w-16 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-orange-500 to-yellow-500 rounded-full transition-all duration-1000 animate-pulse group-hover:animate-none" 
                                         :style="`width: ${statistics.total ? (statistics.pending / statistics.total * 100) : 0}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block group-hover:text-orange-600 transition-colors">{{ statistics.total ? Math.round(statistics.pending / statistics.total * 100) : 0 }}%</span>
                            </div>
                        </div>
                        
                        <!-- Urgency indicator -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-orange-500 to-yellow-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700 animate-pulse"></div>
                    </div>
                    
                    <!-- Average Score Card -->
                    <div 
                        @mouseenter="hoveredCard = 'score'"
                        @mouseleave="hoveredCard = null"
                        class="group cursor-pointer bg-gradient-to-br from-white to-purple-50 hover:from-purple-50 hover:to-purple-100 border border-gray-200 hover:border-purple-400 p-5 rounded-xl shadow-sm hover:shadow-xl transform hover:scale-110 hover:-translate-y-2 transition-all duration-500 relative overflow-hidden"
                        :class="{ 'ring-2 ring-purple-300 ring-opacity-50': hoveredCard === 'score' }"
                    >
                        <!-- Chart animation elements -->
                        <div class="absolute inset-0 bg-gradient-to-br from-purple-400/10 to-indigo-400/10 opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                        
                        <!-- Analytics particles -->
                        <div class="absolute top-2 right-2 w-2 h-2 bg-purple-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-ping transition-all duration-700"></div>
                        <div class="absolute bottom-3 left-3 w-1 h-1 bg-indigo-400 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-pulse transition-all duration-1000"></div>
                        <div class="absolute top-1/2 right-4 w-1.5 h-1.5 bg-purple-300 rounded-full opacity-0 group-hover:opacity-100 group-hover:animate-bounce transition-all duration-800"></div>
                        
                        <div class="flex items-center justify-between relative z-10">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-gradient-to-br from-purple-100 to-purple-200 group-hover:from-purple-200 group-hover:to-purple-300 rounded-xl transition-all duration-300 group-hover:rotate-12 group-hover:scale-110 shadow-lg group-hover:shadow-xl">
                                    <svg class="w-7 h-7 text-purple-600 group-hover:text-purple-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-purple-700 transition-colors duration-300 font-mono">
                                        {{ statistics.averageScore }}%
                                    </p>
                                    <p class="text-sm font-medium text-gray-500 group-hover:text-gray-700">📊 Avg Score</p>
                                    <!-- Performance indicator -->
                                    <div class="mt-1">
                                        <span class="text-xs px-2 py-1 rounded-full" 
                                              :class="statistics.averageScore >= 90 ? 'bg-green-100 text-green-700' : 
                                                     statistics.averageScore >= 70 ? 'bg-yellow-100 text-yellow-700' : 
                                                     'bg-red-100 text-red-700'">
                                            {{ statistics.averageScore >= 90 ? 'Excellent' : 
                                               statistics.averageScore >= 70 ? 'Good' : 'Needs Improvement' }}
                                        </span>
                                    </div>
                                    <!-- Tooltip -->
                                    <div v-if="hoveredCard === 'score'" class="absolute -top-12 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white px-3 py-1 rounded-lg text-xs whitespace-nowrap z-20 opacity-0 animate-fadeInUp">
                                        Average performance score
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-3 w-16 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-purple-500 to-indigo-500 rounded-full transition-all duration-1500 animate-pulse group-hover:animate-none" 
                                         :style="`width: ${statistics.averageScore}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block group-hover:text-purple-600 transition-colors">{{ statistics.averageScore }}%</span>
                                
                                <!-- Mini chart visualization -->
                                <div class="mt-2 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                    <div class="w-1 h-2 bg-purple-300 rounded-full animate-pulse" style="animation-delay: 0ms"></div>
                                    <div class="w-1 h-3 bg-purple-400 rounded-full animate-pulse" style="animation-delay: 100ms"></div>
                                    <div class="w-1 h-2.5 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 200ms"></div>
                                    <div class="w-1 h-4 bg-purple-600 rounded-full animate-pulse" style="animation-delay: 300ms"></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance gradient bar -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gradient-to-r from-purple-500 via-indigo-500 to-blue-500 transform scale-x-0 group-hover:scale-x-100 transition-transform duration-700"></div>
                    </div>
                </div>

                <!-- Always Visible Quick Filters -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Restaurant Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-56">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <select 
                                v-model="filters.restaurant" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">🏪 All Restaurants</option>
                                <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.name">
                                    {{ restaurant.code }} - {{ restaurant.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-40">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <select 
                                v-model="filters.status" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">All Status</option>
                                <option value="Draft">✏️ Draft</option>
                                <option value="Submitted">� Submitted</option>
                                <option value="Reviewed">👁️ Reviewed</option>
                                <option value="Approved">✅ Approved</option>
                            </select>
                        </div>

                        <!-- MIC Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-36">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <select 
                                v-model="filters.mic" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">All Shifts</option>
                                <option value="Morning">☀️ Morning</option>
                                <option value="Evening">🌙 Evening</option>
                            </select>
                        </div>

                        <!-- Quick Date Filters -->
                        <div class="flex items-center gap-2">
                            <button 
                                @click="quickFilterToday"
                                class="px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                            >
                                Today
                            </button>
                            <button 
                                @click="quickFilterWeek"
                                class="px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                            >
                                Week
                            </button>
                            <button 
                                @click="quickFilterMonth"
                                class="px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                            >
                                Month
                            </button>
                        </div>

                        <!-- Clear Filters Button -->
                        <button 
                            @click="clearFilters"
                            v-if="Object.values(filters).some(f => f)"
                            class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200 ml-auto"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </button>

                        <!-- Advanced Filters Toggle -->
                        <button 
                            @click="showFilters = !showFilters"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            {{ showFilters ? 'Less Filters' : 'More Filters' }}
                        </button>
                    </div>
                </div>

                <!-- Enhanced Advanced Filters Panel -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <div v-if="showFilters" class="bg-white border border-gray-200 rounded-2xl shadow-lg mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                Advanced Filters
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Multi-Restaurant Filter -->
                                <div class="space-y-2 col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Restaurants (Multi-Select)
                                    </label>
                                    <select 
                                        v-model="filters.restaurants" 
                                        multiple
                                        size="4"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                        <option 
                                            v-for="restaurant in restaurants" 
                                            :key="restaurant.id" 
                                            :value="restaurant.name"
                                        >
                                            {{ restaurant.code }} - {{ restaurant.name }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500">Hold Ctrl/Cmd to select multiple</p>
                                </div>

                                <!-- Area Manager Filter -->
                                <div v-if="props.canViewAreaManagerFilter" class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Area Manager
                                    </label>
                                    <select 
                                        v-model="filters.area_manager" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                        <option value="">👥 All Managers</option>
                                        <option 
                                            v-for="manager in areaManagers" 
                                            :key="manager.id" 
                                            :value="manager.id"
                                        >
                                            {{ manager.name }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date From -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date From
                                    </label>
                                    <input 
                                        v-model="filters.date_from" 
                                        type="date" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <!-- Date To -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date To
                                    </label>
                                    <input 
                                        v-model="filters.date_to" 
                                        type="date" 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <!-- Purpose -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        Purpose
                                    </label>
                                    <select v-model="filters.purpose" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="">🎯 All Purposes</option>
                                        <option value="Quality Audit">🔍 Quality Audit</option>
                                        <option value="Operational Assessment">📊 Operational Assessment</option>
                                        <option value="Training Audit">🎓 Training Audit</option>
                                        <option value="Measurement & Coaching">� Measurement & Coaching</option>
                                    </select>
                                </div>

                                <!-- Min Score -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path>
                                        </svg>
                                        Min Score
                                    </label>
                                    <input 
                                        v-model="filters.score_min" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        placeholder="0"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <!-- Max Score -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10"></path>
                                        </svg>
                                        Max Score
                                    </label>
                                    <input 
                                        v-model="filters.score_max" 
                                        type="number" 
                                        min="0" 
                                        max="100" 
                                        placeholder="100"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                    >
                                </div>

                                <!-- Action Items -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Action Items
                                    </label>
                                    <select v-model="filters.has_action_items" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="">📊 All Reports</option>
                                        <option value="yes">⚠️ Has Action Items</option>
                                        <option value="no">✅ No Action Items</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Export Actions -->
                            <div class="mt-8 pt-6 border-t border-gray-200 flex flex-wrap gap-4 justify-between items-center">
                                <div class="flex gap-3">
                                    <button 
                                        @click="exportExcel" 
                                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                        📊 Export Excel
                                    </button>
                                    <!-- <button 
                                        @click="exportPDF" 
                                        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-all duration-200 shadow-sm hover:shadow-md"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                        </svg>
                                        📄 Export PDF
                                    </button> -->
                                </div>
                                <div class="text-sm text-gray-600">
                                    <span v-if="isAdmin" class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"></path>
                                        </svg>
                                        👑 Admin View - All Data
                                    </span>
                                    <span class="ml-4">Showing {{ statistics.total }} visits</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Modern Visits Table -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-slate-50 border-b border-gray-200">
                        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    Store Visit Reports
                                    <span v-if="selectedVisits.length > 0" class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full animate-pulse">
                                        {{ selectedVisits.length }} selected
                                    </span>
                                </h3>
                                <p class="text-sm text-gray-600 mt-1">
                                    Showing {{ visits.length }} of {{ visits.length }} visits
                                    <span v-if="Object.values(filters).some(f => f)" class="ml-2 text-blue-600 font-medium">(filtered)</span>
                                </p>
                            </div>
                            
                            <!-- Table Actions -->
                            <div class="flex gap-2 items-center">
                                <!-- Bulk Actions -->
                                <Transition
                                    enter-active-class="transition duration-200 ease-out"
                                    enter-from-class="transform scale-95 opacity-0"
                                    enter-to-class="transform scale-100 opacity-100"
                                    leave-active-class="transition duration-150 ease-in"
                                    leave-from-class="transform scale-100 opacity-100"
                                    leave-to-class="transform scale-95 opacity-0"
                                >
                                    <div v-if="selectedVisits.length > 0" class="flex items-center gap-2 mr-4 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                        <span class="text-xs font-medium text-blue-700">{{ selectedVisits.length }} selected</span>
                                        <button 
                                            v-if="can('store_visits', 'approve')"
                                            @click="bulkUpdateStatus('Pending Review')"
                                            class="px-2 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs rounded transition-colors"
                                            title="Mark for Review"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </button>
                                        <button 
                                            @click="selectedVisits = []"
                                            class="px-2 py-1 bg-gray-600 hover:bg-gray-700 text-white text-xs rounded transition-colors"
                                            title="Clear Selection"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </Transition>
                                
                                <!-- View Options -->
                                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                                    <button 
                                        @click="tableView = 'grid'"
                                        :class="tableView === 'grid' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'"
                                        class="p-1.5 rounded transition-all duration-200"
                                        title="Grid View"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                                        </svg>
                                    </button>
                                    <button 
                                        @click="tableView = 'list'"
                                        :class="tableView === 'list' ? 'bg-white shadow-sm text-gray-900' : 'text-gray-500'"
                                        class="p-1.5 rounded transition-all duration-200"
                                        title="List View"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Performance Indicators -->
                        <div v-if="visits.length > 0" class="mt-4 flex items-center gap-4 text-xs text-gray-500">
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span>{{ statistics.completed }} Completed</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                                <span>{{ statistics.pending }} Pending</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 bg-purple-400 rounded-full"></div>
                                <span>{{ statistics.averageScore }}% Avg Score</span>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th v-if="can('store_visits', 'approve')" class="w-12 px-3 py-3">
                                        <input 
                                            type="checkbox" 
                                            @change="toggleSelectAll"
                                            :checked="selectedVisits.length === visits.length && visits.length > 0"
                                            class="w-4 h-4 text-blue-600 bg-white border border-gray-300 rounded focus:ring-blue-500 focus:ring-1"
                                        >
                                    </th>
                                    <th class="w-48 px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700 uppercase">Restaurant & Details</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700 uppercase">Visit Info</span>
                                        </div>
                                    </th>
                                    <th class="w-24 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Score</span>
                                        </div>
                                    </th>
                                    <th class="w-28 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Status</span>
                                        </div>
                                    </th>
                                    <th class="w-32 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Visit Date</span>
                                        </div>
                                    </th>
                                    <th class="w-32 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Action Plans</span>
                                        </div>
                                    </th>
                                    <th class="w-20 px-3 py-3 text-center">
                                        <span class="text-xs font-semibold text-gray-700">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="visit in visitsData" :key="visit.id" 
                                    class="group hover:bg-blue-50/30 transition-all duration-200 hover:shadow-sm">
                                    <td v-if="can('store_visits', 'approve')" class="px-3 py-4 text-center">
                                        <input 
                                            type="checkbox" 
                                            :value="visit.id" 
                                            v-model="selectedVisits"
                                            class="w-4 h-4 text-blue-600 bg-white border border-gray-300 rounded focus:ring-blue-500 focus:ring-1"
                                        >
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-2">
                                            <div class="font-semibold text-gray-900 text-sm">
                                                {{ visit.restaurant }}
                                            </div>
                                            <div class="flex items-center gap-1 text-xs text-blue-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ visit.mic }} Shift
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ new Date(visit.created_at).toLocaleDateString() }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="space-y-2">
                                            <div class="text-xs text-gray-700">
                                                <div v-if="visit.purpose" class="space-y-1">
                                                    <span v-for="purpose in (Array.isArray(visit.purpose) ? visit.purpose : [visit.purpose])" :key="purpose" 
                                                          class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mr-1">
                                                        {{ purpose }}
                                                    </span>
                                                </div>
                                                <div v-else class="text-gray-400 italic">No purpose specified</div>
                                            </div>
                                            
                                            <!-- Issue Status Indicators -->
                                            <div class="space-y-1">
                                                <div v-if="visit.hasIssues" class="flex items-center gap-2">
                                                    <div class="flex items-center gap-1 text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                        </svg>
                                                        {{ visit.actionItems }} NO Issues
                                                    </div>
                                                    <div v-if="visit.issuesSummary" class="text-xs text-gray-500 truncate" :title="visit.issuesSummary">
                                                        {{ visit.issuesSummary.length > 30 ? visit.issuesSummary.substring(0, 30) + '...' : visit.issuesSummary }}
                                                    </div>
                                                </div>
                                                <div v-else class="flex items-center gap-1 text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    No Issues Found
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="w-12 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div 
                                                    class="h-full rounded-full transition-all duration-500"
                                                    :class="visit.score >= 90 ? 'bg-green-500' : visit.score >= 70 ? 'bg-yellow-500' : 'bg-red-500'"
                                                    :style="`width: ${visit.score || 0}%`"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-medium" 
                                                  :class="visit.score >= 90 ? 'text-green-700' : visit.score >= 70 ? 'text-yellow-700' : 'text-red-700'">
                                                {{ visit.score || 0 }}%
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                              :class="visit.status === 'Draft' ? 'bg-gray-100 text-gray-800' :
                                                     visit.status === 'Submitted' ? 'bg-blue-100 text-blue-800' :
                                                     visit.status === 'Reviewed' ? 'bg-yellow-100 text-yellow-800' :
                                                     visit.status === 'Approved' ? 'bg-green-100 text-green-800' :
                                                     'bg-gray-100 text-gray-800'">
                                            {{ visit.status === 'Draft' ? '✏️ Draft' :
                                               visit.status === 'Submitted' ? '📤 Submitted' :
                                               visit.status === 'Reviewed' ? '👁️ Reviewed' :
                                               visit.status === 'Approved' ? '✅ Approved' :
                                               visit.status }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div class="text-xs text-gray-500">
                                            {{ new Date(visit.date || visit.created_at).toLocaleDateString() }}
                                        </div>
                                        <div v-if="isAdmin && visit.created_by" class="text-xs text-gray-400 mt-1">
                                            by {{ visit.created_by }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div class="space-y-2">
                                            <!-- Action Plan Status -->
                                            <div v-if="!visit.hasIssues" class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                No Action Needed
                                            </div>
                                            <div v-else-if="visit.needsActionPlans" class="space-y-2">
                                                <!-- Quick Confirm Link Only -->
                                                <Link 
                                                    :href="route('store-visits.quick-confirm', visit.id)"
                                                    class="inline-flex items-center gap-1 px-3 py-1 text-xs font-medium text-white bg-orange-600 hover:bg-orange-700 rounded-full transition-colors duration-200"
                                                    title="Quick Confirm and Create Action Plans"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Quick Confirm
                                                </Link>
                                            </div>
                                            <div v-else class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded-full inline-flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                </svg>
                                                Plans Created
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4">
                                        <div class="flex items-center justify-center gap-1">
                                            <Link 
                                                :href="route('store-visits.show', visit.id)" 
                                                class="p-1.5 text-blue-600 hover:text-blue-800 hover:bg-blue-100 rounded-md transition-all duration-200"
                                                title="View Details"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            <button 
                                                @click="printVisitReport(visit)"
                                                class="p-1.5 text-purple-600 hover:text-purple-800 hover:bg-purple-100 rounded-md transition-all duration-200"
                                                title="Print Report"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                            </button>
                                            <Link 
                                                v-if="can('store_visits', 'edit')"
                                                :href="route('store-visits.edit', visit.id)" 
                                                class="p-1.5 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-md transition-all duration-200"
                                                title="Edit Report"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </Link>
                                            <button 
                                                v-if="visit.status === 'draft'"
                                                @click="submitVisit(visit.id)"
                                                :disabled="submittingVisit === visit.id"
                                                class="p-1.5 text-green-600 hover:text-green-800 hover:bg-green-100 rounded-md transition-all duration-200 disabled:opacity-50"
                                                title="Submit Report"
                                            >
                                                <svg v-if="submittingVisit === visit.id" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                                </svg>
                                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Enhanced Pagination -->
                    <div class="bg-gradient-to-r from-slate-100 via-gray-100 to-blue-50 px-6 py-5 border-t-2 border-blue-200">
                        <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                            <!-- Results Info -->
                            <div class="flex items-center gap-3 text-sm">
                                <div class="p-2 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl shadow-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2-2V7a2 2 0 012-2h2a2 2 0 002 2v2a2 2 0 002 2h2a2 2 0 012-2V7a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 00-2 2h-2a2 2 0 00-2 2v6a2 2 0 01-2 2H9a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="bg-white px-4 py-2 rounded-xl shadow-sm border border-gray-200">
                                    <span class="font-bold text-gray-700">
                                        Showing <span class="text-blue-600 font-extrabold text-base">{{ visits.from || 0 }}</span> 
                                        to <span class="text-blue-600 font-extrabold text-base">{{ visits.to || 0 }}</span> 
                                        of <span class="text-green-600 font-extrabold text-base">{{ visits.total || 0 }}</span> results
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Desktop Pagination -->
                            <div class="hidden md:flex items-center gap-1 flex-wrap justify-center">
                                <component 
                                    v-for="(link, index) in visits.links" 
                                    :key="index"
                                    :is="link.url ? Link : 'span'"
                                    :href="link.url"
                                    preserve-scroll
                                    class="min-w-[40px] px-4 py-2.5 text-sm font-semibold rounded-lg transition-all duration-300 border flex items-center justify-center"
                                    :class="{
                                        'bg-gradient-to-r from-blue-600 to-blue-700 text-white border-blue-600 shadow-lg scale-105 ring-2 ring-blue-300': link.active,
                                        'bg-gray-200 text-gray-400 cursor-not-allowed border-gray-200': !link.url,
                                        'bg-white text-gray-700 hover:bg-gradient-to-r hover:from-blue-500 hover:to-blue-600 hover:text-white border-gray-300 hover:border-blue-400 hover:shadow-md hover:scale-105': link.url && !link.active
                                    }"
                                    v-html="link.label"
                                ></component>
                            </div>
                            
                            <!-- Mobile Pagination -->
                            <div class="flex md:hidden items-center gap-3 w-full">
                                <Link
                                    v-if="visits.prev_page_url"
                                    :href="visits.prev_page_url"
                                    preserve-scroll
                                    class="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-blue-50 text-gray-700 hover:text-blue-600 px-4 py-3 rounded-xl font-semibold border border-gray-300 hover:border-blue-400 transition-all shadow-sm hover:shadow-md"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </Link>
                                <span v-else class="flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-400 px-4 py-3 rounded-xl font-semibold border border-gray-200 cursor-not-allowed">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Previous
                                </span>
                                
                                <div class="bg-white px-4 py-3 rounded-xl border border-gray-300 shadow-sm">
                                    <span class="font-bold text-gray-700">
                                        Page <span class="text-blue-600">{{ visits.current_page }}</span> of <span class="text-green-600">{{ visits.last_page }}</span>
                                    </span>
                                </div>
                                
                                <Link
                                    v-if="visits.next_page_url"
                                    :href="visits.next_page_url"
                                    preserve-scroll
                                    class="flex-1 flex items-center justify-center gap-2 bg-white hover:bg-blue-50 text-gray-700 hover:text-blue-600 px-4 py-3 rounded-xl font-semibold border border-gray-300 hover:border-blue-400 transition-all shadow-sm hover:shadow-md"
                                >
                                    Next
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </Link>
                                <span v-else class="flex-1 flex items-center justify-center gap-2 bg-gray-100 text-gray-400 px-4 py-3 rounded-xl font-semibold border border-gray-200 cursor-not-allowed">
                                    Next
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Enhanced Print Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showPrintModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300"
                     :class="showPrintModal ? 'scale-100 opacity-100' : 'scale-95 opacity-0'">
                    
                    <!-- Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-purple-50 to-blue-50 rounded-t-2xl">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-purple-100 rounded-lg">
                                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900">🖨️ Print Store Visit Reports</h3>
                                    <p class="text-sm text-gray-600">Choose your print options</p>
                                </div>
                            </div>
                            <button @click="showPrintModal = false" 
                                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Modal Body -->
                    <div class="p-6 space-y-4">
                        <div class="space-y-3">
                            <button @click="window.print(); showPrintModal = false" 
                                    class="w-full flex items-center gap-3 p-3 text-left bg-gradient-to-r from-blue-50 to-indigo-50 hover:from-blue-100 hover:to-indigo-100 border border-blue-200 rounded-xl transition-all duration-200 group">
                                <div class="p-2 bg-blue-100 group-hover:bg-blue-200 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Print Current View</div>
                                    <div class="text-sm text-gray-600">Print the reports currently displayed</div>
                                </div>
                                <svg class="w-5 h-5 text-blue-400 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            
                            <!-- <button @click="exportPDF(); showPrintModal = false" 
                                    class="w-full flex items-center gap-3 p-3 text-left bg-gradient-to-r from-red-50 to-pink-50 hover:from-red-100 hover:to-pink-100 border border-red-200 rounded-xl transition-all duration-200 group">
                                <div class="p-2 bg-red-100 group-hover:bg-red-200 rounded-lg transition-colors">
                                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M9 17V7a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">Download as PDF</div>
                                    <div class="text-sm text-gray-600">Save reports as PDF file for printing</div>
                                </div>
                                <svg class="w-5 h-5 text-red-400 group-hover:text-red-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button> -->
                        </div>
                        
                        <!-- Print Info -->
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-gray-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="text-sm text-gray-600">
                                    <p class="font-medium">Print Tips:</p>
                                    <ul class="mt-1 space-y-1 text-xs">
                                        <li>• Current filters will be applied to printed reports</li>
                                        <li>• For best results, use landscape orientation</li>
                                        <li>• Consider adjusting page margins in print settings</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl">
                        <div class="flex justify-end gap-3">
                            <button @click="showPrintModal = false" 
                                    class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Transition>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
/* Modern table styling that matches TailwindCSS classes */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Transition animations */
.transition-all {
    transition: all 0.3s ease;
}

.hover\:scale-105:hover {
    transform: scale(1.05);
}

.hover\:shadow-md:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Animation for progress bars */
@keyframes progressFill {
    from { width: 0%; }
    to { width: var(--progress-width); }
}

/* Enhanced hover effects */
tr.group:hover {
    background-color: rgba(59, 130, 246, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

/* Status badge animations */
.inline-flex.items-center {
    transition: all 0.2s ease;
}

.inline-flex.items-center:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

/* Custom animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse-subtle {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.8;
    }
}

@keyframes shimmer {
    0% {
        background-position: -200px 0;
    }
    100% {
        background-position: calc(200px + 100%) 0;
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.4s ease-out forwards;
}

.animate-slideInRight {
    animation: slideInRight 0.3s ease-out forwards;
}

.animate-pulse-subtle {
    animation: pulse-subtle 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Skeleton loading animations */
.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

/* Smooth transitions for all interactive elements */
* {
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}

/* Loading states */
.loading-overlay {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(2px);
}

/* Enhanced focus states */
input:focus, select:focus, button:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Improved button interactions */
button:active {
    transform: translateY(1px);
}

/* Table row stagger animation */
tbody tr {
    animation-fill-mode: both;
}

tbody tr:nth-child(1) { animation-delay: 0.05s; }
tbody tr:nth-child(2) { animation-delay: 0.1s; }
tbody tr:nth-child(3) { animation-delay: 0.15s; }
tbody tr:nth-child(4) { animation-delay: 0.2s; }
tbody tr:nth-child(5) { animation-delay: 0.25s; }

/* Progressive loading effect */
.progressive-load {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Enhanced card shadows */
.card-hover-shadow {
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.card-hover-shadow:hover {
    box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
}

/* Mobile optimizations */
@media (max-width: 768px) {
    /* Enhanced touch targets */
    button, .cursor-pointer {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Better spacing for mobile */
    .statistics-card {
        padding: 1rem;
    }
    
    /* Optimized table for mobile */
    .mobile-table-row {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Stack filter controls vertically on mobile */
    .mobile-filter-stack {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    /* Enhanced mobile search */
    .mobile-search {
        width: 100%;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    /* Better mobile navigation */
    .mobile-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 1rem;
        z-index: 50;
    }
    
    /* Swipe gestures for table rows */
    .swipeable-row {
        position: relative;
        overflow: hidden;
    }
    
    .swipe-actions {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, transparent, #ef4444);
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .swipeable-row.swiping .swipe-actions {
        transform: translateX(0);
    }
    
    /* Improved mobile tooltips */
    .mobile-tooltip {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        z-index: 1000;
        animation: slideUpMobile 0.3s ease-out;
    }
    
    @keyframes slideUpMobile {
        from {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    
    /* Better mobile statistics cards */
    .mobile-stat-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .mobile-stat-card {
        padding: 0.75rem;
        min-height: 80px;
    }
    
    /* Optimized mobile text sizes */
    .mobile-text-lg { font-size: 1.125rem; }
    .mobile-text-base { font-size: 1rem; }
    .mobile-text-sm { font-size: 0.875rem; }
    .mobile-text-xs { font-size: 0.75rem; }
}

    /* Print Styles */
    @media print {
        /* Hide non-essential elements when printing */
        .no-print,
        button:not(.print-visible),
        .bg-gradient-to-r,
        .shadow-sm,
        .shadow-md,
        .shadow-lg,
        .shadow-xl {
            display: none !important;
        }
    
        /* Optimize page layout for printing */
        body {
            background: white !important;
            font-size: 12pt;
            line-height: 1.4;
        }
    
        /* Ensure proper page breaks */
        .print-page-break {
            page-break-before: always;
        }
    
        .print-avoid-break {
            page-break-inside: avoid;
        }
    
        /* Style headers for print */
        h1, h2, h3 {
            color: black !important;
            page-break-after: avoid;
        }
    
        /* Optimize table printing */
        table {
            width: 100% !important;
            border-collapse: collapse;
            font-size: 10pt;
        }
    
        th, td {
            border: 1px solid #000 !important;
            padding: 4pt 6pt !important;
            text-align: left;
            background: white !important;
            color: black !important;
        }
    
        th {
            background: #f0f0f0 !important;
            font-weight: bold;
        }
    
        /* Ensure proper spacing */
        .print-section {
            margin-bottom: 20pt;
        }
    
        /* Remove background colors and shadows */
        * {
            background: white !important;
            color: black !important;
            box-shadow: none !important;
        }
    
        /* Show important information clearly */
        .print-header {
            border-bottom: 2pt solid black;
            margin-bottom: 10pt;
            padding-bottom: 10pt;
        }
    
        /* Optimize statistics for print */
        .statistics-card {
            border: 1pt solid black !important;
            margin-bottom: 10pt;
            padding: 8pt;
            display: inline-block;
            width: 48%;
            margin-right: 2%;
        }
    
        /* Page margins */
        @page {
            margin: 0.5in;
            size: landscape;
        }
    
        /* Ensure URLs are visible in print */
        a[href]:after {
            content: " (" attr(href) ")";
            font-size: 9pt;
            color: #555;
        }
    
        /* Hide decorative elements */
        .animate-pulse,
        .animate-spin,
        .animate-bounce {
            animation: none !important;
        }
    }
    </style>

<style scoped>
.restaurant-select {
    background: white !important;
    border: none !important;
    border-radius: 10px !important;
    padding: 12px 45px 12px 15px !important;
    font-weight: 500;
    color: #333;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    font-size: 14px;
    line-height: 1.5;
    transition: all 0.3s ease;
    background-image: none !important;
}

.restaurant-select:focus {
    outline: none !important;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2) !important;
    border: none !important;
}

.restaurant-select option {
    padding: 12px 15px;
    font-weight: 500;
    color: #333;
    background: white;
    border: none;
}

.restaurant-select option:first-child {
    background: linear-gradient(135deg, #f8f9ff 0%, #e8f2ff 100%);
    font-weight: 600;
    color: #667eea;
}

.restaurant-select option:hover,
.restaurant-select option:selected {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.select-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #667eea;
    font-size: 12px;
    pointer-events: none;
    z-index: 1;
}

.restaurant-select-wrapper:hover .select-icon {
    color: #5a6fd8;
    animation: bounce 0.6s ease;
}

@keyframes bounce {
    0%, 20%, 60%, 100% { transform: translateY(-50%); }
    40% { transform: translateY(-60%); }
    80% { transform: translateY(-55%); }
}

/* Enhanced form labels */
.form-label.fw-bold {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 13px;
    letter-spacing: 0.5px;
    margin-bottom: 8px;
    display: inline-block;
    position: relative;
}

.form-label.fw-bold::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 30px;
    height: 2px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 1px;
}

/* Success indicator styling */
.text-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
    font-size: 12px;
}

.text-success i {
    color: #28a745;
    margin-right: 5px;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.6; }
    100% { opacity: 1; }
}

/* Restaurant count badge */
.text-muted {
    background: rgba(102, 126, 234, 0.1);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 11px;
    color: #667eea !important;
    font-weight: 600;
    margin-left: 5px;
}

/* Restaurant Statistics Card */
.restaurant-stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    transform: scale(1.02);
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
}

.restaurant-stat-card:hover {
    transform: scale(1.05) translateY(-5px);
    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.4);
}

.restaurant-stat-card .card-body {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 15px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.bg-gradient-restaurant {
    background: linear-gradient(135deg, #ff9a56 0%, #ffad56 100%);
    box-shadow: 0 8px 25px rgba(255, 154, 86, 0.3);
}

.text-restaurant {
    background: linear-gradient(135deg, #ff9a56 0%, #ffad56 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 24px;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.restaurant-stat-card .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
    background: rgba(255, 255, 255, 0.1);
    font-size: 12px;
}

.restaurant-stat-card .text-success {
    color: #4ade80 !important;
    background: rgba(74, 222, 128, 0.2);
    padding: 2px 6px;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 600;
}

/* Enhanced statistics cards */
.visit-stat-card {
    border-radius: 15px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.visit-stat-card .icon-circle {
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
}

/* Restaurant Showcase Styles */
.restaurant-showcase-card {
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border-radius: 15px;
    overflow: hidden;
}

.restaurant-showcase-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 1.5rem;
}

.restaurant-counter {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(255, 255, 255, 0.2);
    padding: 1rem;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}

.counter-number {
    font-size: 2rem;
    font-weight: bold;
    line-height: 1;
}

.counter-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.restaurant-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 1rem;
}

.restaurant-card {
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    border-radius: 12px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.restaurant-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s;
}

.restaurant-card:hover::before {
    left: 100%;
}

.restaurant-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    border-color: #667eea;
}

.restaurant-card.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: #764ba2;
    transform: translateY(-3px);
}

.restaurant-code {
    font-weight: bold;
    font-size: 1.2rem;
    color: #667eea;
    margin-bottom: 0.5rem;
}

.restaurant-card.active .restaurant-code {
    color: white;
}

.restaurant-name {
    font-weight: 600;
    margin-bottom: 1rem;
    font-size: 1.1rem;
}



.visit-indicator {
    text-align: center;
}

.visited-badge {
    background: #28a745;
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.not-visited-badge {
    background: #ffc107;
    color: #333;
    padding: 0.3rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
}

.more-restaurants {
    background: linear-gradient(135deg, #fd746c 0%, #ff9068 100%);
    color: white;
    text-align: center;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.more-count {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.more-text {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.more-action {
    font-size: 0.9rem;
    opacity: 0.9;
}

.all-restaurants-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1rem;
    border-top: 2px solid #eee;
    background: #f8f9fa;
    border-radius: 10px;
}

.restaurant-card.small {
    padding: 1rem;
    font-size: 0.9rem;
}

.restaurant-card.small .restaurant-code {
    font-size: 1rem;
}

.restaurant-card.small .restaurant-name {
    font-size: 1rem;
}

@media (max-width: 768px) {
    .restaurant-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .restaurant-counter {
        padding: 0.8rem;
    }
    
    .counter-number {
        font-size: 1.5rem;
    }
}

/* Enhanced Filter Styles */
.enhanced-label {
    font-weight: 600;
    font-size: 0.95rem;
    color: #495057;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
}

.enhanced-label i {
    font-size: 0.9rem;
}

.input-wrapper {
    position: relative;
}

.enhanced-select,
.enhanced-input {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 2.5rem 0.75rem 1rem;
    font-size: 0.9rem;
    background: #fff;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04);
}

.enhanced-select:focus,
.enhanced-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    outline: none;
}

.input-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    font-size: 0.85rem;
    pointer-events: none;
}

.score-input {
    padding-right: 2.5rem;
}

.selection-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.enhanced-clear-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(220, 53, 69, 0.2);
}

.enhanced-clear-btn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    color: white;
}

.text-purple {
    color: #6f42c1 !important;
}

.text-orange {
    color: #fd7e14 !important;
}

/* Enhanced restaurant select styling */
.restaurant-select.enhanced-select {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: 2px solid transparent;
}

.restaurant-select.enhanced-select option {
    background: #fff;
    color: #333;
    padding: 0.5rem;
}

/* Filter section improvements */
.card-body {
    padding: 1.5rem;
}

.row {
    margin-bottom: 0.5rem;
}

/* Responsive improvements */
@media (max-width: 992px) {
    .enhanced-label {
        font-size: 0.9rem;
    }
    
    .enhanced-select,
    .enhanced-input {
        font-size: 0.85rem;
        padding: 0.6rem 2rem 0.6rem 0.8rem;
    }
}

/* Mobile Experience Optimizations */
@media (max-width: 768px) {
    /* Enhanced touch targets */
    button, .cursor-pointer {
        min-height: 44px;
        min-width: 44px;
    }
    
    /* Better spacing for mobile */
    .statistics-card {
        padding: 1rem;
    }
    
    /* Optimized table for mobile */
    .mobile-table-row {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        background: white;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }
    
    /* Stack filter controls vertically on mobile */
    .mobile-filter-stack {
        flex-direction: column;
        gap: 0.75rem;
    }
    
    /* Enhanced mobile search */
    .mobile-search {
        width: 100%;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    /* Better mobile navigation */
    .mobile-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        padding: 1rem;
        z-index: 50;
    }
    
    /* Swipe gestures for table rows */
    .swipeable-row {
        position: relative;
        overflow: hidden;
    }
    
    .swipe-actions {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        background: linear-gradient(90deg, transparent, #ef4444);
        width: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        transform: translateX(100%);
        transition: transform 0.3s ease;
    }
    
    .swipeable-row.swiping .swipe-actions {
        transform: translateX(0);
    }
    
    /* Improved mobile tooltips */
    .mobile-tooltip {
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        z-index: 1000;
        animation: slideUpMobile 0.3s ease-out;
    }
    
    @keyframes slideUpMobile {
        from {
            opacity: 0;
            transform: translate(-50%, 20px);
        }
        to {
            opacity: 1;
            transform: translate(-50%, 0);
        }
    }
    
    /* Better mobile statistics cards */
    .mobile-stat-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }
    
    .mobile-stat-card {
        padding: 0.75rem;
        min-height: 80px;
    }
    
    /* Optimized mobile text sizes */
    .mobile-text-lg { font-size: 1.125rem; }
    .mobile-text-base { font-size: 1rem; }
    .mobile-text-sm { font-size: 0.875rem; }
    .mobile-text-xs { font-size: 0.75rem; }
}

/* Toast notification animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}
</style>
