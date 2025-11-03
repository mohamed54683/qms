<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// Import route helper
const route = window.route;

// Receive data from backend
const props = defineProps({
    checklists: Object,
    restaurants: Array,
    filters: Object,
    isAdmin: Boolean,
    canExportAll: Boolean,
    canViewAllRestaurants: Boolean,
    auth: Object
});

// Filter form data
const filters = ref({
    restaurant: props.filters?.restaurant || '',
    time_option: props.filters?.time_option || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    search: props.filters?.search || '',
    status: props.filters?.status || ''
});

// UI state
const showExportDropdown = ref(false);
const exportDropdown = ref(null);
const showFilters = ref(false);

// Filter options
const timeOptions = ['12PM', '5PM', '8PM', '11PM'];
const statusOptions = ['Draft', 'Submitted', 'Reviewed', 'Approved'];

// Export functions
const exportToExcel = async () => {
    try {
        const query = new URLSearchParams(filters.value).toString();
        const response = await fetch(route('qsc-checklist.export-csv') + '?' + query);
        const blob = await response.blob();
        downloadFile(blob, 'qsc-checklist-export.csv');
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const exportToPDF = async () => {
    try {
        const query = new URLSearchParams(filters.value).toString();
        const response = await fetch(route('qsc-checklist.export-pdf') + '?' + query);
        const blob = await response.blob();
        downloadFile(blob, 'qsc-checklist-report.pdf');
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const exportToCSV = async () => {
    try {
        const data = props.checklists.data.map(checklist => ({
            'Date': formatDate(checklist.date),
            'Store Name': checklist.store_name,
            'MOD': checklist.mod,
            'Time Option': checklist.time_option,
            'Status': checklist.status,
            'Compliance Score': checklist.compliance_score || 'N/A',
            'Manager Signature': checklist.manager_signature,
            'Created By': checklist.user?.name || 'N/A',
            'Created At': formatDate(checklist.created_at)
        }));
        
        const csv = convertToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        downloadFile(blob, 'qsc-checklist-data.csv');
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const convertToCSV = (data) => {
    if (!data.length) return '';
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => 
            headers.map(header => {
                const value = row[header];
                return typeof value === 'string' && value.includes(',') 
                    ? `"${value}"` 
                    : value;
            }).join(',')
        )
    ].join('\n');
    
    return csvContent;
};

const downloadFile = (blob, filename) => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
};

// Print functions
const printPage = () => {
    window.print();
};

const printForm = (checklistId) => {
    const printWindow = window.open('', '_blank');
    const checklist = props.checklists.data.find(c => c.id === checklistId);
    
    if (checklist) {
        printWindow.document.write(`
            <html>
                <head>
                    <title>QSC Checklist - ${checklist.store_name}</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 20px; }
                        .header { text-align: center; margin-bottom: 30px; }
                        .details { margin-bottom: 20px; }
                        .detail-row { margin: 10px 0; }
                        .label { font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h1>Q.S.C Checklist</h1>
                        <h2>${checklist.store_name}</h2>
                    </div>
                    <div class="details">
                        <div class="detail-row"><span class="label">Date:</span> ${formatDate(checklist.date)}</div>
                        <div class="detail-row"><span class="label">MOD:</span> ${checklist.mod}</div>
                        <div class="detail-row"><span class="label">Time Option:</span> ${checklist.time_option}</div>
                        <div class="detail-row"><span class="label">Status:</span> ${checklist.status}</div>
                        <div class="detail-row"><span class="label">Compliance Score:</span> ${checklist.compliance_score || 'N/A'}%</div>
                        <div class="detail-row"><span class="label">Manager Signature:</span> ${checklist.manager_signature}</div>
                        <div class="detail-row"><span class="label">Created By:</span> ${checklist.user?.name || 'N/A'}</div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
};

// Filter functions
const applyFilters = () => {
    router.get(route('qsc-checklist.report'), filters.value, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    filters.value = {
        restaurant: '',
        time_option: '',
        date_from: '',
        date_to: '',
        search: '',
        status: ''
    };
    applyFilters();
};

const quickFilterToday = () => {
    const today = new Date().toISOString().split('T')[0];
    filters.value.date_from = today;
    filters.value.date_to = today;
    applyFilters();
};

const quickFilterWeek = () => {
    const today = new Date();
    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    filters.value.date_from = weekAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
    applyFilters();
};

const quickFilterMonth = () => {
    const today = new Date();
    const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    filters.value.date_from = monthAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
    applyFilters();
};

// Utility functions
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusBadgeClass = (status) => {
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

const getScoreColor = (score) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 80) return 'text-yellow-600';
    if (score >= 70) return 'text-orange-600';
    return 'text-red-600';
};

const confirmChecklist = (id) => {
    if (!confirm('Confirm this checklist status?')) return;
    router.post(route('qsc-checklist.confirm', id), {}, { preserveScroll: true });
};

// Statistics
const statistics = computed(() => {
    const data = props.checklists?.data || [];
    const total = data.length;
    const avgScore = total > 0 ? 
        data.reduce((sum, checklist) => sum + (checklist.compliance_score || 0), 0) / total : 0;
    
    return {
        total,
        draft: data.filter(c => c.status === 'Draft').length,
        submitted: data.filter(c => c.status === 'Submitted').length,
        reviewed: data.filter(c => c.status === 'Reviewed').length,
        approved: data.filter(c => c.status === 'Approved').length,
        avgScore: Math.round(avgScore),
        highPerformance: data.filter(c => (c.compliance_score || 0) >= 90).length,
        needsAttention: data.filter(c => (c.compliance_score || 0) < 70).length
    };
});

// Handle clicks outside dropdown
const handleClickOutside = (event) => {
    if (exportDropdown.value && !exportDropdown.value.contains(event.target)) {
        showExportDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Q.S.C Checklist Reports" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Q.S.C Checklist Reports</h2>
                    <p class="text-sm text-gray-600 mt-1">Manage and track quality, service, and cleanliness checklists</p>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Print Buttons -->
                    <button @click="printPage" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Page
                    </button>

                    <!-- Export Dropdown -->
                    <div class="relative" ref="exportDropdown">
                        <button @click="showExportDropdown = !showExportDropdown" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div v-show="showExportDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <button @click="exportToExcel" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export as Excel
                                </button>
                                <button @click="exportToPDF" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Export as PDF
                                </button>
                                <button @click="exportToCSV" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export as CSV
                                </button>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('qsc-checklist.form')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Checklist
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Checklists</dt>
                                        <dd class="text-2xl font-bold text-gray-900">{{ statistics.total }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m-7 6v-2a3 3 0 013-3h4a3 3 0 013 3v2m-8-4h8m-8-8V5a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Draft</dt>
                                        <dd class="text-2xl font-bold text-yellow-600">{{ statistics.draft }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Submitted</dt>
                                        <dd class="text-2xl font-bold text-blue-600">{{ statistics.submitted }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Reviewed</dt>
                                        <dd class="text-2xl font-bold text-green-600">{{ statistics.reviewed }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Filters & Search</h3>
                            <button @click="showFilters = !showFilters" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                {{ showFilters ? 'Hide' : 'Show' }} Filters
                            </button>
                        </div>
                    </div>
                    
                    <div v-show="showFilters" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant</label>
                                <select v-model="filters.restaurant" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Restaurants</option>
                                    <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.name">
                                        {{ restaurant.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time Option</label>
                                <select v-model="filters.time_option" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Times</option>
                                    <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select v-model="filters.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input v-model="filters.search" type="text" placeholder="Search checklists..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                                <input v-model="filters.date_from" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                                <input v-model="filters.date_to" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button @click="quickFilterToday" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Today
                            </button>
                            <button @click="quickFilterWeek" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Last 7 Days
                            </button>
                            <button @click="quickFilterMonth" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Last 30 Days
                            </button>
                            <button @click="clearFilters" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Q.S.C Checklists</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="checklist in props.checklists.data" :key="checklist.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(checklist.date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ checklist.store_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.mod }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.time_option }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusBadgeClass(checklist.status)">
                                            {{ checklist.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="getScoreColor(checklist.compliance_score)">
                                        {{ checklist.compliance_score ? checklist.compliance_score + '%' : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.user?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <Link :href="route('qsc-checklist.show', checklist.id)" class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            
                                            <button @click="confirmChecklist(checklist.id)" class="text-green-600 hover:text-green-900 px-2 py-1 rounded hover:bg-green-50 transition-colors" title="Confirm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </button>
                                            
                                            <button @click="printForm(checklist.id)" class="text-gray-600 hover:text-gray-900 px-2 py-1 rounded hover:bg-gray-50 transition-colors" title="Print">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Empty State -->
                        <div v-if="!props.checklists.data.length" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No checklists found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new Q.S.C checklist.</p>
                            <div class="mt-6">
                                <Link :href="route('qsc-checklist.form')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    New Checklist
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div v-if="props.checklists.links && props.checklists.links.length > 3" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex justify-between flex-1 sm:hidden">
                                <Link v-if="props.checklists.prev_page_url" :href="props.checklists.prev_page_url" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </Link>
                                <Link v-if="props.checklists.next_page_url" :href="props.checklists.next_page_url" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Showing <span class="font-medium">{{ props.checklists.from }}</span> to <span class="font-medium">{{ props.checklists.to }}</span> of <span class="font-medium">{{ props.checklists.total }}</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link v-for="link in props.checklists.links" :key="link.label" :href="link.url" v-html="link.label" :class="[
                                            'relative inline-flex items-center px-2 py-2 border text-sm font-medium',
                                            link.active 
                                                ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                            link.url ? 'hover:bg-gray-50' : 'cursor-not-allowed opacity-50'
                                        ]"></Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
    
    .border-gray-200 {
        border-color: #e5e7eb !important;
    }
    
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    thead {
        display: table-header-group;
    }
    
    tfoot {
        display: table-footer-group;
    }
}

.export-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 50;
}
</style>