<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Line, Bar, Doughnut, Pie } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend, Filler } from 'chart.js';
import { usePermissions } from '@/Composables/usePermissions';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend, Filler);

const props = defineProps({
    auth: Object,
    currentFilter: String,
    kpis: Object,
    stats: Object,
    branchPerformance: Object,
    charts: Object,
    userAnalytics: Object,
    recentVisits: Array,
    recentActionPlans: Array,
    recentActivities: Array,
    isAreaManager: Boolean,
    areaManagers: Array,
    filters: Object,
    canViewAreaManagerFilter: Boolean,
});

const { can, showInDashboard, isAdmin } = usePermissions();

// Animation states
const cardsLoaded = ref(false);

// Date filter state - initialize from props or default to 'week'
const dateFilter = ref(props.currentFilter || 'week'); // 'today', 'week', 'month'
const areaManagerFilter = ref(props.filters?.area_manager || '');

onMounted(() => {
    setTimeout(() => {
        cardsLoaded.value = true;
    }, 300);
});

// Filter methods
const filterByDate = (filter) => {
    dateFilter.value = filter;
    // Reload dashboard with selected filter, preserving area manager filter
    const params = { filter: filter };
    if (areaManagerFilter.value) {
        params.area_manager = areaManagerFilter.value;
    }
    router.get(route('dashboard'), params, { 
        preserveState: true,
        preserveScroll: true,
        only: ['kpis', 'stats', 'charts', 'recentVisits', 'recentActionPlans']
    });
};

const filterByAreaManager = () => {
    // Reload dashboard with selected area manager filter, preserving date filter
    const params = { filter: dateFilter.value };
    if (areaManagerFilter.value) {
        params.area_manager = areaManagerFilter.value;
    }
    router.get(route('dashboard'), params, { 
        preserveState: true,
        preserveScroll: true,
        only: ['kpis', 'stats', 'charts', 'recentVisits', 'recentActionPlans']
    });
};

// Chart configurations with dark theme
const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                color: '#94a3b8',
                font: { size: 11 },
                padding: 15,
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleColor: '#f1f5f9',
            bodyColor: '#cbd5e1',
            borderColor: '#334155',
            borderWidth: 1,
            padding: 12,
            boxPadding: 6,
        }
    },
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8', font: { size: 11 } }
        },
        x: {
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8', font: { size: 11 } }
        }
    }
};

// Area Manager Bar Chart Options (specific for better label handling)
const areaManagerChartOptions = {
    ...chartOptions,
    scales: {
        y: {
            beginAtZero: true,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8', font: { size: 11 } }
        },
        x: {
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { 
                color: '#94a3b8', 
                font: { size: 10 },
                maxRotation: 45,
                minRotation: 0
            }
        }
    },
    plugins: {
        ...chartOptions.plugins,
        tooltip: {
            ...chartOptions.plugins.tooltip,
            callbacks: {
                title: (context) => {
                    return `Area Manager: ${context[0].label}`;
                },
                label: (context) => {
                    return `Store Visits: ${context.parsed.y}`;
                }
            }
        }
    }
};

// QSC Score Trend Chart
const qscScoreTrendData = computed(() => ({
    labels: props.charts?.qscScoreTrend?.labels || [],
    datasets: [{
        label: 'Average QSC Score',
        data: props.charts?.qscScoreTrend?.data || [],
        borderColor: '#10b981',
        backgroundColor: 'rgba(16, 185, 129, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#10b981',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
    }]
}));

// Visits Trend Chart
const visitsTrendData = computed(() => ({
    labels: props.charts?.visitTrends?.labels || [],
    datasets: [{
        label: 'Store Visits',
        data: props.charts?.visitTrends?.data || [],
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 4,
    }]
}));

// Visits by Area Manager Chart
const visitsByAreaManagerData = computed(() => ({
    labels: props.charts?.visitsByAreaManager?.labels || [],
    datasets: [{
        label: 'Store Visits',
        data: props.charts?.visitsByAreaManager?.data || [],
        backgroundColor: props.charts?.visitsByAreaManager?.colors || [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
            '#06b6d4', '#84cc16', '#f97316', '#ec4899', '#6b7280'
        ],
        borderColor: 'rgba(59, 130, 246, 1)',
        borderWidth: 1,
        borderRadius: 8,
    }]
}));

// Action Plan Status Chart
const actionPlanStatusData = computed(() => ({
    labels: props.charts?.actionPlanStatus?.labels || [],
    datasets: [{
        data: props.charts?.actionPlanStatus?.data || [],
        backgroundColor: props.charts?.actionPlanStatus?.colors || ['#f59e0b', '#3b82f6', '#10b981'],
        borderWidth: 0,
    }]
}));

// Issue Categories Chart
const issueCategoriesData = computed(() => ({
    labels: props.charts?.issueCategories?.map(c => c.category) || [],
    datasets: [{
        data: props.charts?.issueCategories?.map(c => c.count) || [],
        backgroundColor: ['#ef4444', '#f59e0b', '#10b981', '#3b82f6', '#8b5cf6', '#6b7280'],
        borderWidth: 0,
    }]
}));

// Restaurant Performance Chart
const performanceData = computed(() => ({
    labels: props.charts?.performance?.labels || [],
    datasets: [{
        label: 'Average Score',
        data: props.charts?.performance?.data || [],
        backgroundColor: 'rgba(16, 185, 129, 0.8)',
    }]
}));

const performanceOptions = {
    ...chartOptions,
    indexAxis: 'y',
    scales: {
        x: { 
            beginAtZero: true, 
            max: 100,
            grid: { color: 'rgba(148, 163, 184, 0.1)' },
            ticks: { color: '#94a3b8', font: { size: 11 } }
        },
        y: {
            grid: { display: false },
            ticks: { color: '#94a3b8', font: { size: 11 } }
        }
    }
};

const doughnutOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            display: true,
            position: 'bottom',
            labels: {
                color: '#94a3b8',
                font: { size: 11 },
                padding: 15,
            }
        },
        tooltip: {
            backgroundColor: 'rgba(15, 23, 42, 0.95)',
            titleColor: '#f1f5f9',
            bodyColor: '#cbd5e1',
            borderColor: '#334155',
            borderWidth: 1,
            padding: 12,
        }
    }
};
</script>

<template>
    <Head title="GHIDAS QMS — Dashboard" />
    
    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="flex flex-col gap-4">
                <div>
                    <h2 class="font-bold text-xl sm:text-2xl text-gray-800 leading-tight">
                        🎯 GHIDAS QMS — Operations Dashboard
                    </h2>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-xs sm:text-sm text-gray-500">Real-time overview of restaurant performance, compliance, and operational excellence</p>
                        <span v-if="isAreaManager && !isAdmin" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            📊 Area Manager View
                        </span>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <!-- Three Filter Buttons - Mobile Responsive -->
                    <button 
                        @click="filterByDate('today')"
                        class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                        :class="dateFilter === 'today' 
                            ? 'bg-blue-600 text-white shadow-md hover:bg-blue-700' 
                            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        <span>📅</span>
                        <span>TODAY</span>
                    </button>
                    
                    <button 
                        @click="filterByDate('week')"
                        class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                        :class="dateFilter === 'week' 
                            ? 'bg-blue-600 text-white shadow-md hover:bg-blue-700' 
                            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        <span>📅</span>
                        <span>THIS WEEK</span>
                    </button>
                    
                    <button 
                        @click="filterByDate('month')"
                        class="flex-1 sm:flex-none px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium transition-all duration-200 flex items-center justify-center gap-2"
                        :class="dateFilter === 'month' 
                            ? 'bg-blue-600 text-white shadow-md hover:bg-blue-700' 
                            : 'bg-white border border-gray-300 text-gray-700 hover:bg-gray-50'"
                    >
                        <span>📅</span>
                        <span>THIS MONTH</span>
                    </button>
                    
                    <!-- Area Manager Filter -->
                    <div v-if="props.canViewAreaManagerFilter && areaManagers.length > 0" class="flex-1 sm:flex-none">
                        <select 
                            v-model="areaManagerFilter" 
                            @change="filterByAreaManager"
                            class="w-full px-3 py-2 text-xs sm:text-sm border border-gray-300 rounded-lg bg-white text-gray-700 hover:bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">👥 All Area Managers</option>
                            <option v-for="manager in areaManagers" :key="manager.id" :value="manager.id">
                                {{ manager.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- 🧩 KPI Summary Cards (Improved Design) -->
                <div 
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6" 
                    :class="{ 'opacity-100 translate-y-0': cardsLoaded, 'opacity-0 translate-y-4': !cardsLoaded }" 
                    style="transition: all 0.6s ease;"
                >
                    <!-- Total Restaurants -->
                    <div class="group bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-5 text-white shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-8 -mt-8"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">🏪</span>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Total</span>
                            </div>
                            <p class="text-sm font-medium opacity-90 mb-1">Restaurants</p>
                            <p class="text-3xl font-bold">{{ kpis.totalRestaurants }}</p>
                        </div>
                    </div>

                    <!-- Total Users -->
                    <div class="group bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-5 text-white shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-8 -mt-8"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">👥</span>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Active</span>
                            </div>
                            <p class="text-sm font-medium opacity-90 mb-1">System Users</p>
                            <p class="text-3xl font-bold">{{ kpis.totalUsers }}</p>
                        </div>
                    </div>

                    <!-- Visits This Month -->
                    <div class="group bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-5 text-white shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-8 -mt-8"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">📋</span>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">+12%</span>
                            </div>
                            <p class="text-sm font-medium opacity-90 mb-1">Visits This Month</p>
                            <p class="text-3xl font-bold">{{ kpis.visitsThisMonth }}</p>
                        </div>
                    </div>

                    <!-- Average QSC Score -->
                    <div class="group bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl p-5 text-white shadow-md hover:shadow-xl transform hover:scale-105 transition-all duration-300 relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-white opacity-5 rounded-full -mr-8 -mt-8"></div>
                        <div class="relative">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-3xl">✅</span>
                                <span class="text-xs bg-white/20 px-2 py-1 rounded-full">Avg</span>
                            </div>
                            <p class="text-sm font-medium opacity-90 mb-1">QSC Score</p>
                            <p class="text-3xl font-bold">{{ kpis.avgQscScore }}%</p>
                        </div>
                    </div>
                </div>

                <!-- Secondary KPIs Row -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Pending Action Plans -->
                    <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">⏳</span>
                                <p class="text-sm font-semibold text-gray-700">Pending Plans</p>
                            </div>
                            <span class="text-sm text-orange-600 font-bold">{{ kpis.pendingActionPlans }}</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <p class="text-2xl font-bold text-gray-800">{{ kpis.pendingActionPlans }}</p>
                            <span class="text-xs text-gray-500 mb-1">awaiting</span>
                        </div>
                    </div>

                    <!-- Completed Plans -->
                    <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">✅</span>
                                <p class="text-sm font-semibold text-gray-700">Completed</p>
                            </div>
                            <span class="text-sm text-green-600 font-bold">{{ kpis.completedActionPlans }}</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <p class="text-2xl font-bold text-gray-800">{{ kpis.completedActionPlans }}</p>
                            <span class="text-xs text-gray-500 mb-1">closed</span>
                        </div>
                    </div>

                    <!-- TTF Compliance -->
                    <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🔥</span>
                                <p class="text-sm font-semibold text-gray-700">TTF Compliance</p>
                            </div>
                            <span class="text-sm text-indigo-600 font-bold">{{ kpis.ttfComplianceRate }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2 rounded-full" :style="{ width: kpis.ttfComplianceRate + '%' }"></div>
                        </div>
                    </div>

                    <!-- Temperature Compliance -->
                    <div class="bg-white rounded-xl p-5 shadow-md hover:shadow-lg transition-all duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl">🌡️</span>
                                <p class="text-sm font-semibold text-gray-700">Temperature</p>
                            </div>
                            <span class="text-sm text-red-600 font-bold">{{ kpis.temperatureCompliance }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full" :style="{ width: kpis.temperatureCompliance + '%' }"></div>
                        </div>
                    </div>
                </div>

                <!-- 📊 Performance Analytics Area (Improved Layout) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
                    <!-- QSC Score Trend -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>📈</span> QSC Score Trend
                            </h3>
                            <span class="text-xs text-gray-500">Last 7 days</span>
                        </div>
                        <div class="h-56">
                            <Line :data="qscScoreTrendData" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Store Visits Trend -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>📊</span> Visit Trends
                            </h3>
                            <span class="text-xs text-gray-500">Last 7 days</span>
                        </div>
                        <div class="h-56">
                            <Line :data="visitsTrendData" :options="chartOptions" />
                        </div>
                    </div>

                    <!-- Action Plan Status -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>⚙️</span> Action Plans
                            </h3>
                            <span class="text-xs text-gray-500">Status</span>
                        </div>
                        <div class="h-56">
                            <Doughnut :data="actionPlanStatusData" :options="doughnutOptions" />
                        </div>
                    </div>
                </div>

                <!-- 📊 Secondary Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                    <!-- Store Visits by Area Manager -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>�</span> Visits by Area Manager
                            </h3>
                            <span class="text-xs text-gray-500">Manager performance</span>
                        </div>
                        <div class="h-64">
                            <Bar :data="visitsByAreaManagerData" :options="areaManagerChartOptions" />
                        </div>
                    </div>

                    <!-- Issue Categories -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>🔍</span> Issue Categories
                            </h3>
                            <span class="text-xs text-gray-500">Distribution</span>
                        </div>
                        <div class="h-64">
                            <Pie :data="issueCategoriesData" :options="doughnutOptions" />
                        </div>
                    </div>
                </div>

                <!-- 🧮 Branch & Area Performance (Improved Design) -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
                    <!-- Top 5 Stores -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>🥇</span> Top 5 Stores
                        </h3>
                        <div class="space-y-2">
                            <div v-for="(branch, index) in branchPerformance.topBranches" :key="index" class="flex items-center justify-between p-3 bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg hover:shadow-sm transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm">
                                        <span class="text-lg">{{ index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : (index + 1) }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate">{{ branch.name }}</p>
                                        <p class="text-xs text-gray-500">{{ branch.visits }} visits</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <p class="text-lg font-bold text-green-600">{{ branch.score }}%</p>
                                </div>
                            </div>
                            <div v-if="!branchPerformance.topBranches || branchPerformance.topBranches.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                No data available
                            </div>
                        </div>
                    </div>

                    <!-- Lowest 5 Stores -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>⚠️</span> Needs Attention
                        </h3>
                        <div class="space-y-2">
                            <div v-for="(branch, index) in branchPerformance.lowBranches" :key="index" class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg hover:shadow-sm transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm">
                                        <span class="text-lg">⚠️</span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-gray-800 text-sm truncate">{{ branch.name }}</p>
                                        <p class="text-xs text-gray-500">{{ branch.visits }} visits</p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0 ml-2">
                                    <p class="text-lg font-bold text-red-600">{{ branch.score }}%</p>
                                </div>
                            </div>
                            <div v-if="!branchPerformance.lowBranches || branchPerformance.lowBranches.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                No data available
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Area Manager Summary & Top Performance (Improved) -->
                <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 mb-6">
                    <!-- Area Performance -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>🧭</span> Area Performance
                        </h3>
                        <div class="space-y-2">
                            <div v-for="area in branchPerformance.areaPerformance" :key="area.area" class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg hover:shadow-sm transition-all">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold text-gray-800 text-sm">{{ area.area }}</span>
                                    <span class="text-base font-bold text-blue-600">{{ area.score }}%</span>
                                </div>
                                <div class="flex justify-between text-xs text-gray-600">
                                    <span>{{ area.visits }} visits</span>
                                    <span>{{ area.plans }} plans</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Restaurants Performance -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>🏆</span> Top Restaurants
                        </h3>
                        <div class="h-64">
                            <Bar :data="performanceData" :options="performanceOptions" />
                        </div>
                    </div>
                </div>

                <!-- ⚙️ Action Plan Overview & Activities (Redesigned) -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Action Plan Metrics - Compact Version -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>⚙️</span> Action Plans
                        </h3>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">⏳</span>
                                    <span class="text-xs font-medium text-gray-600">Pending</span>
                                </div>
                                <span class="text-lg font-bold text-yellow-600">{{ stats.pendingActionPlans }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">🔄</span>
                                    <span class="text-xs font-medium text-gray-600">In Progress</span>
                                </div>
                                <span class="text-lg font-bold text-blue-600">{{ stats.inProgressActionPlans }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">⚠️</span>
                                    <span class="text-xs font-medium text-gray-600">Overdue</span>
                                </div>
                                <span class="text-lg font-bold text-red-600">{{ stats.overdueActionPlans }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xl">✅</span>
                                    <span class="text-xs font-medium text-gray-600">Completed</span>
                                </div>
                                <span class="text-lg font-bold text-green-600">{{ kpis.completedActionPlans }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Activities Feed -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2 sticky top-0 bg-white">
                            <span>🕓</span> Recent Activities
                        </h3>
                        <div class="space-y-2 max-h-80 overflow-y-auto pr-2">
                            <div v-for="activity in recentActivities" :key="activity.id" class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-shrink-0 text-xl">{{ activity.icon }}</div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ activity.title }}</p>
                                    <p class="text-xs text-gray-600 truncate">{{ activity.description }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ activity.time }}</p>
                                </div>
                            </div>
                            <div v-if="!recentActivities || recentActivities.length === 0" class="text-center py-8 text-gray-400 text-sm">
                                No recent activities
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overdue Action Plans Table (Compact) -->
                <div v-if="stats.overdueActionPlans > 0" class="bg-white rounded-xl shadow-md p-5 mb-6 hover:shadow-lg transition-shadow duration-300 border border-red-100">
                    <h3 class="text-base font-bold text-red-800 mb-4 flex items-center gap-2">
                        <span>⚠️</span> Overdue Action Plans ({{ stats.overdueActionPlans }})
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-red-50">
                                <tr>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Item</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Restaurant</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Priority</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Due Date</th>
                                    <th class="px-3 py-2 text-left text-xs font-medium text-gray-700 uppercase">Days Overdue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                <tr v-for="plan in recentActionPlans.filter(p => p.days_overdue > 0)" :key="plan.id" class="hover:bg-red-50 transition-colors">
                                    <td class="px-3 py-2 text-xs text-gray-900">{{ plan.item }}</td>
                                    <td class="px-3 py-2 text-xs text-gray-700">{{ plan.restaurant }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        <span class="px-2 py-0.5 text-xs font-semibold rounded-full" :class="{
                                            'bg-red-100 text-red-800': plan.priority === 'High',
                                            'bg-yellow-100 text-yellow-800': plan.priority === 'Medium',
                                            'bg-green-100 text-green-800': plan.priority === 'Low'
                                        }">{{ plan.priority }}</span>
                                    </td>
                                    <td class="px-3 py-2 text-xs text-gray-700">{{ plan.due_date }}</td>
                                    <td class="px-3 py-2 text-xs">
                                        <span class="px-2 py-0.5 bg-red-100 text-red-800 rounded-full text-xs font-bold">
                                            {{ plan.days_overdue }}d
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- 🌡️ Compliance & Forms (Redesigned) -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                    <!-- Compliance Overview -->
                    <div class="lg:col-span-2 bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>🌡️</span> Compliance Overview
                        </h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">Temperature Tracking</span>
                                    <span class="text-xs font-bold text-red-600">{{ kpis.temperatureCompliance }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-red-500 to-red-600 h-2 rounded-full transition-all duration-500" :style="{ width: kpis.temperatureCompliance + '%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">TTF Audit Pass Rate</span>
                                    <span class="text-xs font-bold text-indigo-600">{{ kpis.ttfComplianceRate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2 rounded-full transition-all duration-500" :style="{ width: kpis.ttfComplianceRate + '%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">Visit Completion Rate</span>
                                    <span class="text-xs font-bold text-green-600">{{ stats.completionRate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-2 rounded-full transition-all duration-500" :style="{ width: stats.completionRate + '%' }"></div>
                                </div>
                            </div>
                            <div>
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-xs font-medium text-gray-700">Action Plan Resolution</span>
                                    <span class="text-xs font-bold text-blue-600">{{ stats.actionPlanResolutionRate }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full transition-all duration-500" :style="{ width: stats.actionPlanResolutionRate + '%' }"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Forms Submission Stats -->
                    <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span>📋</span> Submissions
                        </h3>
                        <div class="space-y-3">
                            <div class="p-3 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg">
                                <p class="text-xs font-medium text-gray-700 mb-1">Today</p>
                                <p class="text-2xl font-bold text-blue-600">{{ stats.visitsToday }}</p>
                            </div>
                            <div class="p-3 bg-gradient-to-r from-green-50 to-green-100 rounded-lg">
                                <p class="text-xs font-medium text-gray-700 mb-1">This Week</p>
                                <p class="text-2xl font-bold text-green-600">{{ stats.visitsThisWeek }}</p>
                            </div>
                            <div class="p-3 bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg">
                                <p class="text-xs font-medium text-gray-700 mb-1">This Month</p>
                                <p class="text-2xl font-bold text-purple-600">{{ kpis.visitsThisMonth }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 👥 User & Role Analytics (Compact) -->
                <div class="bg-white rounded-xl shadow-md p-5 mb-6 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                    <h3 class="text-base font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <span>👥</span> User & Role Analytics
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
                        <div v-for="role in userAnalytics.usersByRole" :key="role.role" class="p-3 bg-gradient-to-br from-slate-50 to-slate-100 rounded-lg text-center hover:shadow-sm transition-shadow">
                            <p class="text-xs font-medium text-gray-600 mb-1">{{ role.role }}</p>
                            <p class="text-2xl font-bold text-slate-700">{{ role.count }}</p>
                        </div>
                        <div class="p-3 bg-gradient-to-br from-green-50 to-emerald-100 rounded-lg text-center hover:shadow-sm transition-shadow">
                            <p class="text-xs font-medium text-gray-600 mb-1">Active Today</p>
                            <p class="text-2xl font-bold text-green-600">{{ userAnalytics.activeUsersToday }}</p>
                        </div>
                    </div>
                </div>

                <!-- 🧾 Recent Visits & Quick Actions (Improved Layout) -->
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Recent Store Visits - Takes more space -->
                    <div class="lg:col-span-3 bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                        <div class="px-5 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 flex justify-between items-center border-b border-gray-100">
                            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                                <span>📋</span> Recent Store Visits
                            </h3>
                            <Link v-if="can('store_visits', 'view')" href="/qms/store-visits" class="text-xs text-blue-600 hover:text-blue-800 font-semibold">
                                View All →
                            </Link>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Restaurant</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Visited By</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-3 py-2 text-left text-xs font-medium text-gray-500 uppercase">Score</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="visit in recentVisits" :key="visit.id" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-3 py-2 text-xs text-gray-900">{{ new Date(visit.visit_date).toLocaleDateString() }}</td>
                                        <td class="px-3 py-2 text-xs font-medium text-gray-900">{{ visit.restaurant_name }}</td>
                                        <td class="px-3 py-2 text-xs text-gray-700">{{ visit.user_name }}</td>
                                        <td class="px-3 py-2 text-xs">
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full" :class="{
                                                'bg-green-100 text-green-800': visit.status === 'Completed' || visit.status === 'Approved',
                                                'bg-blue-100 text-blue-800': visit.status === 'In Progress' || visit.status === 'Under Review',
                                                'bg-yellow-100 text-yellow-800': visit.status === 'Pending' || visit.status === 'Draft',
                                                'bg-gray-100 text-gray-800': visit.status === 'Cancelled'
                                            }">{{ visit.status }}</span>
                                        </td>
                                        <td class="px-3 py-2 text-xs font-bold" :class="{
                                            'text-green-600': visit.score >= 90,
                                            'text-blue-600': visit.score >= 70 && visit.score < 90,
                                            'text-orange-600': visit.score >= 50 && visit.score < 70,
                                            'text-red-600': visit.score < 50 && visit.score > 0
                                        }">{{ visit.score ? visit.score + '%' : '-' }}</td>
                                    </tr>
                                    <tr v-if="!recentVisits || recentVisits.length === 0">
                                        <td colspan="5" class="px-3 py-8 text-center text-xs text-gray-400">No recent visits found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Quick Actions - Compact sidebar -->
                    <div class="space-y-4">
                        <!-- Quick Actions -->
                        <div class="bg-white rounded-xl shadow-md p-5 hover:shadow-lg transition-shadow duration-300 border border-gray-100">
                            <h3 class="text-base font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <span>⚡</span> Quick Actions
                            </h3>
                            <div class="space-y-2">
                                <Link v-if="can('store_visits', 'create')" href="/qms/store-visits/create" class="flex items-center justify-center px-3 py-2 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all font-medium text-xs shadow-sm hover:shadow-md">
                                    <span class="mr-2">➕</span> New Visit
                                </Link>
                                <Link v-if="can('action_plans', 'view')" href="/qms/action-plans" class="flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:border-blue-500 hover:text-blue-600 transition-all font-medium text-xs">
                                    <span class="mr-2">⚙️</span> Action Plans
                                </Link>
                                <Link v-if="can('qsc_checklist', 'view')" href="/qms/qsc-checklist" class="flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:border-green-500 hover:text-green-600 transition-all font-medium text-xs">
                                    <span class="mr-2">✅</span> Q.S.C
                                </Link>
                                <Link v-if="can('temperature_tracking', 'view')" href="/qms/temperature-tracking" class="flex items-center justify-center px-3 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:border-red-500 hover:text-red-600 transition-all font-medium text-xs">
                                    <span class="mr-2">🌡️</span> Temperature
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ⚙️ System Footer (Compact) -->
                <div class="bg-white rounded-xl shadow-md p-6 text-center border border-gray-200">
                    <div class="flex items-center justify-center gap-3 mb-3">
                        <img src="/logo.png" alt="GHIDAS Logo" class="h-10 w-auto">
                        <div class="h-8 w-px bg-blue-200"></div>
                        <div class="text-left">
                            <p class="text-blue-700 text-sm font-bold">
                                GHIDAS Company
                            </p>
                            <p class="text-blue-600 text-xs font-medium">
                                Quality Management System
                            </p>
                        </div>
                    </div>
                    <p class="text-blue-800 text-xs font-semibold mb-1">
                        © 2025 Ghidas
                    </p>
                    <p class="text-blue-600 text-xs">
                        Developed & Managed by GHIDAS | Version v2.5.3
                    </p>
                </div>

            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
/* Custom scrollbar for activity feed */
.overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Smooth transitions */
* {
    transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Hover effects */
.hover\:scale-105:hover {
    transform: scale(1.05);
}
</style>
