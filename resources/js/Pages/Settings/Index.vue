<template>
    <Head title="Settings - QMS Dashboard" />
    
    <DefaultAuthenticatedLayout>
        <template #header>
            <!-- Modern Header Section -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        ⚙️
                        <span class="ml-3">QMS Settings</span>
                    </h1>
                    <p class="text-gray-600 mt-2">Manage system roles, restaurants, and configurations</p>
                </div>
                
                <!-- Quick Action Buttons -->
                <div class="flex space-x-4">
                    <Link :href="route('users.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New User
                    </Link>
                    <Link :href="route('restaurants.create')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add New Restaurant
                    </Link>
                </div>
            </div>
        </template>

        <!-- Dashboard Overview Section -->
        <div class="space-y-8">
            
            <!-- Permission Management Section (New) -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-2xl shadow-lg p-8 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold mb-2 flex items-center">
                            🔐 Permission & Role Management
                        </h2>
                        <p class="text-indigo-100 mb-4">
                            Manage page access permissions and operations (View, Create, Edit, Delete, Approve) for each user role
                        </p>
                        <ul class="text-indigo-100 text-sm space-y-1 mb-4">
                            <li>✓ Define permissions by user type</li>
                            <li>✓ Control dashboard and page visibility</li>
                            <li>✓ Link users to their assigned restaurants</li>
                        </ul>
                        <Link :href="route('permission-management.index')" class="inline-flex items-center px-6 py-3 bg-white text-indigo-600 rounded-lg font-medium hover:bg-indigo-50 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                            Manage Permissions
                        </Link>
                    </div>
                    <div class="hidden lg:block">
                        <svg class="w-40 h-40 opacity-20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </div>
            
            <!-- Interactive Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Users Card -->
                <div @click="navigateToUsers" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_users }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-blue-600 group-hover:text-blue-700">Click to manage users →</span>
                    </div>
                </div>

                <!-- Admin Users Card -->
                <div @click="filterUsers('admin')" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Admin Users</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.admin_users || 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-purple-600 group-hover:text-purple-700">View admin users →</span>
                    </div>
                </div>

                <!-- Area Managers Card -->
                <div @click="filterUsers('area_manager')" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-200 transition-colors">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Area Managers</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.area_managers || 0 }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-green-600 group-hover:text-green-700">View area managers →</span>
                    </div>
                </div>

                <!-- Restaurants Card -->
                <div @click="navigateToRestaurants" class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Restaurants</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total_restaurants }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <span class="text-sm text-orange-600 group-hover:text-orange-700">Manage restaurants →</span>
                    </div>
                </div>
            </div>

            <!-- User Management Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Users Overview -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                👥
                                <span class="ml-3">User Management</span>
                            </h2>
                            <p class="text-gray-600 mt-1">Manage system users, roles, and permissions</p>
                        </div>
                        <Link :href="route('users.index')" class="text-blue-600 hover:text-blue-700 font-medium flex items-center">
                            View All Users
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Search Bar -->
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input v-model="userSearch" type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Search users...">
                        </div>
                    </div>

                    <!-- Recent Users List -->
                    <div v-if="recentUsers.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
                        <p class="text-gray-600 mb-6">Start by adding your first user to the system</p>
                        <Link :href="route('users.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Add First User
                        </Link>
                    </div>
                    <div v-else class="space-y-4">
                        <div v-for="user in filteredUsers.slice(0, 5)" :key="user.id" class="flex items-center p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center text-white font-medium">
                                    {{ user.name.charAt(0).toUpperCase() }}
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <div class="font-medium text-gray-900">{{ user.name }}</div>
                                <div class="text-sm text-gray-600">{{ user.email }}</div>
                                <div class="flex space-x-1 mt-1">
                                    <span v-for="role in user.roles" :key="role" :class="getRoleBadgeClass(role)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                                        {{ role }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-sm text-gray-500">
                                Joined {{ user.created_at }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Restaurant Management Section -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 flex items-center">
                                🏪
                                <span class="ml-3">Restaurant Management</span>
                            </h2>
                            <p class="text-gray-600 mt-1">Manage restaurant profiles and assign managers</p>
                        </div>
                        <Link :href="route('restaurants.index')" class="text-green-600 hover:text-green-700 font-medium flex items-center">
                            View All Restaurants
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </Link>
                    </div>

                    <!-- Restaurant Status Overview -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">Active</p>
                                    <p class="text-lg font-bold text-green-900">{{ stats.active_restaurants }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-800">Inactive</p>
                                    <p class="text-lg font-bold text-red-900">{{ stats.inactive_restaurants }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-blue-800">Total</p>
                                    <p class="text-lg font-bold text-blue-900">{{ stats.total_restaurants }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Restaurants List -->
                    <div v-if="recentRestaurants.length === 0" class="text-center py-12">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No restaurants found</h3>
                        <p class="text-gray-600 mb-6">Start by adding your first restaurant to the system</p>
                        <Link :href="route('restaurants.create')" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                            Add First Restaurant
                        </Link>
                    </div>
                    <div v-else>
                        <div class="overflow-hidden">
                            <table class="min-w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Restaurant</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Added</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr v-for="restaurant in recentRestaurants.slice(0, 5)" :key="restaurant.id" class="hover:bg-gray-50">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-900">{{ restaurant.name }}</div>
                                            <div class="text-sm text-gray-500">{{ restaurant.code }}</div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-600">
                                            {{ restaurant.location }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <span :class="restaurant.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                                {{ restaurant.status }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-500">
                                            {{ restaurant.created_at }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

                <!-- Restaurant Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="modern-stat-card success">
                            <div class="stat-icon">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ stats.total_restaurants }}</div>
                                <div class="stat-label">Total Restaurants</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="modern-stat-card info">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ stats.active_restaurants }}</div>
                                <div class="stat-label">Active</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="modern-stat-card danger">
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ stats.inactive_restaurants || 0 }}</div>
                                <div class="stat-label">Inactive</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="modern-stat-card secondary">
                            <div class="stat-icon">
                                <i class="fas fa-percentage"></i>
                            </div>
                            <div class="stat-content">
                                <div class="stat-number">{{ Math.round((stats.active_restaurants / stats.total_restaurants) * 100) }}%</div>
                                <div class="stat-label">Active Rate</div>
                            </div>
                        </div>
                    </div>
                </div>

    </DefaultAuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue'

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    recentRestaurants: Array,
    locationStats: Array,
})

// Reactive data
const userSearch = ref('')

// Computed properties for filtering
const filteredUsers = computed(() => {
    if (!userSearch.value) return props.recentUsers
    const search = userSearch.value.toLowerCase()
    return props.recentUsers.filter(user => 
        user.name.toLowerCase().includes(search) || 
        user.email.toLowerCase().includes(search) ||
        user.roles.some(role => role.toLowerCase().includes(search))
    )
})

// Navigation methods
const navigateToUsers = () => {
    window.location.href = route('users.index')
}

const navigateToRestaurants = () => {
    window.location.href = route('restaurants.index')
}

const filterUsers = (role) => {
    window.location.href = route('users.index', { filter: role })
}

// Helper method for role badge classes
const getRoleBadgeClass = (role) => {
    const classes = {
        'admin': 'bg-purple-100 text-purple-800',
        'area_manager': 'bg-green-100 text-green-800',
        'restaurant_manager': 'bg-blue-100 text-blue-800',
        'rm': 'bg-blue-100 text-blue-800',
        'sm': 'bg-yellow-100 text-yellow-800'
    }
    return classes[role] || 'bg-gray-100 text-gray-800'
}
</script>

<style scoped>
/* Custom animations for enhanced UX */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.5s ease-out;
}
</style>
