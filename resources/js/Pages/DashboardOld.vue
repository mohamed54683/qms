<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Line, Bar, Doughnut } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend } from 'chart.js';
import { usePermissions } from '@/Composables/usePermissions';

// Register Chart.js components
ChartJS.register(CategoryScale, LinearScale, PointElement, LineElement, BarElement, ArcElement, Title, Tooltip, Legend);

const props = defineProps({
    stats: Object,
    recentVisits: Array,
    recentActionPlans: Array,
    activityLog: Array,
    chartData: Object,
});

const { can, showInDashboard, isAdmin } = usePermissions();

// Animation states
const cardsLoaded = ref(false);

// Animate cards on mount
onMounted(() => {
    setTimeout(() => {
        cardsLoaded.value = true;
    }, 200);
});

// Chart configurations
const visitTrendChartData = computed(() => ({
    labels: props.chartData?.visitsTrend?.labels || [],
    datasets: [{
        label: 'Store Visits',
        data: props.chartData?.visitsTrend?.data || [],
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        tension: 0.4,
        fill: true,
    }]
}));

const visitTrendOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { display: false },
        title: { display: false }
    },
    scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
    }
};

const performanceChartData = computed(() => ({
    labels: props.chartData?.performance?.labels || [],
    datasets: [{
        label: 'Average Score',
        data: props.chartData?.performance?.data || [],
        backgroundColor: [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(236, 72, 153, 0.8)',
        ],
    }]
}));

const performanceOptions = {
    responsive: true,
    maintainAspectRatio: false,
    indexAxis: 'y',
    plugins: {
        legend: { display: false },
    },
    scales: {
        x: { beginAtZero: true, max: 100 }
    }
};

const actionPlanChartData = computed(() => ({
    labels: ['Pending', 'In Progress', 'Completed'],
    datasets: [{
        data: [
            props.stats?.pendingActionPlans || 0,
            props.stats?.inProgressActionPlans || 0,
            props.stats?.completedActionPlans || 0
        ],
        backgroundColor: ['#f59e0b', '#3b82f6', '#10b981'],
    }]
}));

const actionPlanOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' }
    }
};
</script>

<template>
    <Head title="QMS Dashboard" />
    
    <DefaultAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                QMS Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" :class="{ 'opacity-100 translate-y-0': cardsLoaded, 'opacity-0 translate-y-4': !cardsLoaded }" style="transition: all 0.6s ease;">
                    <!-- Total Visits Card -->
                    <div v-if="showInDashboard('store_visits')" class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white transform hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold opacity-90">Total Store Visits</h3>
                                <p class="text-3xl font-bold mt-2">{{ stats.totalVisits }}</p>
                                <p class="text-sm opacity-80">This Month: {{ stats.visitsThisMonth }}</p>
                            </div>
                            <div class="text-4xl opacity-20">
                                🏪
                            </div>
                        </div>
                    </div>

                    <!-- Completed Visits Card -->
                    <div v-if="showInDashboard('store_visits')" class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-6 text-white transform hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold opacity-90">Completed Visits</h3>
                                <p class="text-3xl font-bold mt-2">{{ stats.completedVisits }}</p>
                                <p class="text-sm opacity-80">{{ stats.completionRate }}% Completion Rate</p>
                            </div>
                            <div class="text-4xl opacity-20">
                                ✅
                            </div>
                        </div>
                    </div>

                    <!-- Pending Visits Card -->
                    <div v-if="showInDashboard('store_visits')" class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-lg p-6 text-white transform hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold opacity-90">Pending Visits</h3>
                                <p class="text-3xl font-bold mt-2">{{ stats.pendingVisits }}</p>
                                <p class="text-sm opacity-80">Requires Attention</p>
                            </div>
                            <div class="text-4xl opacity-20">
                                ⏳
                            </div>
                        </div>
                    </div>

                    <!-- Active Restaurants Card -->
                    <div v-if="showInDashboard('restaurants') || isAdmin" class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-6 text-white transform hover:-translate-y-1 transition-all duration-300 shadow-lg hover:shadow-xl">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold opacity-90">{{ isAdmin ? 'All Restaurants' : 'My Restaurants' }}</h3>
                                <p class="text-3xl font-bold mt-2">{{ stats.totalRestaurants }}</p>
                                <p class="text-sm opacity-80">{{ isAdmin ? 'System Wide' : 'Assigned to You' }}</p>
                            </div>
                            <div class="text-4xl opacity-20">
                                🏢
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Row -->
                <div v-if="showInDashboard('store_visits')" class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                    <!-- Visit Trends Chart -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Visit Trends (Last 7 Days)</h3>
                        <div class="h-64">
                            <Line :data="visitTrendChartData" :options="visitTrendOptions" />
                        </div>
                    </div>

                    <!-- Action Plans Distribution -->
                    <div v-if="showInDashboard('action_plans')" class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Action Plans Status</h3>
                        <div class="h-64">
                            <Doughnut :data="actionPlanChartData" :options="actionPlanOptions" />
                        </div>
                    </div>

                    <!-- Performance by Restaurant -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Top Restaurants by Score</h3>
                        <div class="h-64">
                            <Bar :data="performanceChartData" :options="performanceOptions" />
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        <Link v-if="can('store_visits', 'create')" href="/qms/store-visits/create" class="flex flex-col items-center justify-center p-4 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">➕</span>
                            <span class="text-sm font-medium text-gray-700">New Visit</span>
                        </Link>
                        <Link v-if="can('store_visits', 'view')" href="/qms/store-visits" class="flex flex-col items-center justify-center p-4 bg-green-50 hover:bg-green-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">📋</span>
                            <span class="text-sm font-medium text-gray-700">All Visits</span>
                        </Link>
                        <Link v-if="can('action_plans', 'view')" href="/qms/action-plans" class="flex flex-col items-center justify-center p-4 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">📝</span>
                            <span class="text-sm font-medium text-gray-700">Action Plans</span>
                        </Link>
                        <Link v-if="can('qsc_checklist', 'create')" href="/qms/qsc-checklist/create" class="flex flex-col items-center justify-center p-4 bg-purple-50 hover:bg-purple-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">✓</span>
                            <span class="text-sm font-medium text-gray-700">QSC Form</span>
                        </Link>
                        <Link v-if="can('temperature_tracking', 'view')" href="/qms/temperature-tracking" class="flex flex-col items-center justify-center p-4 bg-red-50 hover:bg-red-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">�️</span>
                            <span class="text-sm font-medium text-gray-700">Temperature</span>
                        </Link>
                        <Link v-if="can('restaurants', 'view') || isAdmin" href="/qms/restaurants" class="flex flex-col items-center justify-center p-4 bg-indigo-50 hover:bg-indigo-100 rounded-lg transition-colors">
                            <span class="text-2xl mb-2">🏢</span>
                            <span class="text-sm font-medium text-gray-700">Restaurants</span>
                        </Link>
                    </div>
                </div>

                <!-- Content Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Recent Store Visits -->
                    <div v-if="showInDashboard('store_visits')" class="lg:col-span-2">
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Store Visits</h3>
                                <Link v-if="can('store_visits', 'view')" href="/qms/store-visits" class="text-sm text-blue-600 hover:text-blue-800">View All →</Link>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visited By</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="visit in recentVisits" :key="visit.id" class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ new Date(visit.visit_date).toLocaleDateString() }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ visit.restaurant_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.user_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full" :class="{
                                                    'bg-green-100 text-green-800': visit.status === 'Completed' || visit.status === 'Approved',
                                                    'bg-blue-100 text-blue-800': visit.status === 'In Progress' || visit.status === 'Under Review',
                                                    'bg-yellow-100 text-yellow-800': visit.status === 'Pending' || visit.status === 'Draft',
                                                    'bg-gray-100 text-gray-800': visit.status === 'Cancelled'
                                                }">{{ visit.status }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ visit.score ? visit.score + '%' : '-' }}</td>
                                        </tr>
                                        <tr v-if="!recentVisits || recentVisits.length === 0">
                                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">No recent visits found</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Action Plans Summary -->
                        <div v-if="showInDashboard('action_plans')" class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Action Plans</h3>
                                <Link v-if="can('action_plans', 'view')" href="/qms/action-plans" class="text-sm text-blue-600 hover:text-blue-800">View All →</Link>
                            </div>
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Pending</p>
                                        <p class="text-2xl font-bold text-yellow-600">{{ stats.pendingActionPlans }}</p>
                                    </div>
                                    <span class="text-3xl">⏳</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">In Progress</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ stats.inProgressActionPlans }}</p>
                                    </div>
                                    <span class="text-3xl">🔄</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">Completed</p>
                                        <p class="text-2xl font-bold text-green-600">{{ stats.completedActionPlans }}</p>
                                    </div>
                                    <span class="text-3xl">✅</span>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-lg shadow-sm">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Activity</h3>
                            </div>
                            <div class="p-6">
                                <div v-if="activityLog && activityLog.length > 0" class="space-y-4">
                                    <div v-for="activity in activityLog" :key="activity.id" class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-500">
                                                <span class="text-sm font-medium text-white">✓</span>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm text-gray-900">{{ activity.description }}</p>
                                            <p class="text-xs text-gray-400">{{ new Date(activity.created_at).toLocaleString() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div v-else class="text-center py-4 text-sm text-gray-500">
                                    No recent activity
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
/* Custom animations with CSS only - no external libraries */
.hover\:translate-y-1:hover {
    --tw-translate-y: -0.25rem;
    transform: translate(var(--tw-translate-x), var(--tw-translate-y)) rotate(var(--tw-rotate)) skewX(var(--tw-skew-x)) skewY(var(--tw-skew-y)) scaleX(var(--tw-scale-x)) scaleY(var(--tw-scale-y));
}

.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}
</style>
