<template>
  <DefaultAuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
      <!-- Modern Header Section -->
      <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white rounded-2xl shadow-sm p-6">
          <div class="mb-4 md:mb-0">
            <div class="flex items-center space-x-3">
              <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
                <p class="text-gray-500">Manage system users and their permissions</p>
              </div>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <button @click="exportUsers" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              Export
            </button>
            <Link :href="route('users.create')" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold rounded-lg transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add New User
            </Link>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Total Users</p>
              <p class="text-3xl font-bold text-gray-900">{{ statistics.totalUsers }}</p>
              <p class="text-sm text-green-600 mt-1">
                <span class="inline-flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                  </svg>
                  +12% from last month
                </span>
              </p>
            </div>
            <div class="bg-blue-100 p-3 rounded-xl">
              <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Active Users</p>
              <p class="text-3xl font-bold text-green-600">{{ statistics.activeUsers }}</p>
              <p class="text-sm text-gray-500 mt-1">{{ activeUsersPercentage }}% of total</p>
            </div>
            <div class="bg-green-100 p-3 rounded-xl">
              <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Pending Approval</p>
              <p class="text-3xl font-bold text-yellow-600">{{ statistics.pendingUsers }}</p>
              <p class="text-sm text-yellow-600 mt-1 cursor-pointer hover:underline" @click="filterByStatus('pending')">
                {{ statistics.pendingUsers > 0 ? 'Requires attention' : 'All approved' }}
              </p>
            </div>
            <div class="bg-yellow-100 p-3 rounded-xl">
              <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600 mb-1">Administrators</p>
              <p class="text-3xl font-bold text-purple-600">{{ statistics.adminUsers }}</p>
              <p class="text-sm text-gray-500 mt-1">System admins</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-xl">
              <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Enhanced Filter & Search Section -->
      <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
          <!-- Search Bar -->
          <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input 
              type="text" 
              v-model="filters.search" 
              @input="debouncedSearch"
              class="block w-full pl-10 pr-3 py-3 border border-gray-200 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
              placeholder="Search users by name, email, or phone..."
            >
            <div v-if="filters.search" class="absolute inset-y-0 right-0 pr-3 flex items-center">
              <button @click="clearSearch" class="text-gray-400 hover:text-gray-600">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Filter Controls -->
          <div class="flex flex-wrap items-center space-x-4">
            <!-- Role Filter -->
            <div class="relative">
              <select v-model="filters.role" @change="applyFilters" class="appearance-none bg-white border border-gray-200 rounded-xl px-4 py-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Roles</option>
                <option value="admin">Administrator</option>
                <option value="area_manager">Area Manager</option>
                <option value="sm">Store Manager</option>
                <option value="rm">Restaurant Manager</option>
                <option value="om">Operations Manager</option>
                <option value="coo">COO</option>
                <option value="training">Training Manager</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>

            <!-- Status Filter -->
            <div class="relative">
              <select v-model="filters.status" @change="applyFilters" class="appearance-none bg-white border border-gray-200 rounded-xl px-4 py-3 pr-8 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="pending">Pending</option>
              </select>
              <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </div>
            </div>

            <!-- Reset Filters -->
            <button @click="resetFilters" class="inline-flex items-center px-4 py-3 border border-gray-200 rounded-xl text-gray-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Reset
            </button>

            <!-- Bulk Actions -->
            <div v-if="selectedUsers.length > 0" class="flex items-center space-x-2 ml-4">
              <span class="text-sm text-gray-600">{{ selectedUsers.length }} selected</span>
              <div class="flex space-x-2">
                <button @click="bulkActivate" class="px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
                  Activate
                </button>
                <button @click="bulkDeactivate" class="px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                  Deactivate
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Active Filters Display -->
        <div v-if="hasActiveFilters" class="flex flex-wrap items-center space-x-2 mt-4 pt-4 border-t border-gray-100">
          <span class="text-sm text-gray-600">Active filters:</span>
          <span v-if="filters.search" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            Search: "{{ filters.search }}"
            <button @click="filters.search = ''; applyFilters()" class="ml-2 text-blue-600 hover:text-blue-800">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </span>
          <span v-if="filters.role" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
            Role: {{ getRoleDisplayName(filters.role) }}
            <button @click="filters.role = ''; applyFilters()" class="ml-2 text-purple-600 hover:text-purple-800">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </span>
          <span v-if="filters.status" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
            Status: {{ filters.status.charAt(0).toUpperCase() + filters.status.slice(1) }}
            <button @click="filters.status = ''; applyFilters()" class="ml-2 text-gray-600 hover:text-gray-800">
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </span>
        </div>
      </div>

      <!-- Modern Users Table -->
      <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-100">
          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center space-x-3">
              <h3 class="text-lg font-semibold text-gray-900">All Users</h3>
              <span class="px-2 py-1 bg-gray-100 text-gray-600 text-sm rounded-full">{{ filteredUsers.length }} {{ filteredUsers.length === 1 ? 'user' : 'users' }}</span>
            </div>
            <div class="flex items-center space-x-3 mt-3 sm:mt-0">
              <!-- View Toggle -->
              <div class="flex bg-gray-100 rounded-lg p-1">
                <button @click="viewMode = 'table'" :class="viewMode === 'table' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm rounded-md transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                  </svg>
                </button>
                <button @click="viewMode = 'grid'" :class="viewMode === 'grid' ? 'bg-white shadow-sm' : ''" class="px-3 py-1 text-sm rounded-md transition-all">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                  </svg>
                </button>
              </div>
              
              <!-- Select All -->
              <label class="flex items-center space-x-2 cursor-pointer">
                <input 
                  type="checkbox" 
                  :checked="selectedUsers.length === filteredUsers.length && filteredUsers.length > 0"
                  @change="toggleSelectAll"
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                >
                <span class="text-sm text-gray-600">Select All</span>
              </label>
            </div>
          </div>
        </div>

        <!-- Table View -->
        <div v-if="viewMode === 'table'" class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3">
                  <input 
                    type="checkbox" 
                    :checked="selectedUsers.length === filteredUsers.length && filteredUsers.length > 0"
                    @change="toggleSelectAll"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  >
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" @click="sortBy('name')">
                  <div class="flex items-center space-x-1">
                    <span>User</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" @click="sortBy('role')">
                  <div class="flex items-center space-x-1">
                    <span>Role</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" @click="sortBy('last_login')">
                  <div class="flex items-center space-x-1">
                    <span>Last Login</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition-colors" @click="sortBy('status')">
                  <div class="flex items-center space-x-1">
                    <span>Status</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"></path>
                    </svg>
                  </div>
                </th>
                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- User Rows -->
              <tr 
                v-for="user in paginatedUsers" 
                :key="user.id" 
                @click="selectUser(user)"
                class="hover:bg-gray-50 cursor-pointer transition-colors group"
                :class="{ 'bg-blue-50': selectedUsers.includes(user.id) }"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <input 
                    type="checkbox" 
                    :checked="selectedUsers.includes(user.id)"
                    @change="toggleUserSelection(user.id)"
                    @click.stop
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  >
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <div v-if="user.avatar" class="h-12 w-12 rounded-full overflow-hidden">
                        <img :src="user.avatar" :alt="user.name" class="h-full w-full object-cover">
                      </div>
                      <div v-else class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg">
                        {{ getUserInitials(user.name) }}
                      </div>
                    </div>
                    <div class="min-w-0 flex-1">
                      <p class="text-sm font-medium text-gray-900 group-hover:text-blue-600 transition-colors">{{ user.name }}</p>
                      <p class="text-sm text-gray-500">{{ user.email }}</p>
                      <div v-if="user.department" class="flex items-center mt-1">
                        <span class="text-xs text-gray-400">{{ user.department }}</span>
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <span :class="getRoleBadgeClass(user.role)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      <span :class="getRoleIconClass(user.role)" class="w-2 h-2 rounded-full mr-2"></span>
                      {{ getRoleDisplayName(user.role) }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div v-if="user.restaurants && user.restaurants.length > 0" class="space-y-1">
                    <div v-if="user.restaurants.length === 1" class="text-sm text-gray-900">
                      {{ user.restaurants[0].name }}
                    </div>
                    <div v-else class="text-sm text-gray-900">
                      {{ user.restaurants[0].name }}
                      <span class="text-gray-500">+{{ user.restaurants.length - 1 }} more</span>
                    </div>
                  </div>
                  <div v-else class="text-sm text-gray-500 italic">No assignment</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  <div v-if="user.phone" class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    {{ user.phone }}
                  </div>
                  <div v-else class="text-gray-400 italic">Not provided</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <div v-if="user.last_login" :title="formatFullDateTime(user.last_login)">
                    <div class="text-gray-900">{{ formatRelativeTime(user.last_login) }}</div>
                    <div class="text-xs text-gray-500">{{ formatDate(user.last_login) }}</div>
                  </div>
                  <div v-else class="text-gray-400 italic">Never</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="getStatusBadgeClass(user.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                    <span :class="getStatusDotClass(user.status)" class="w-2 h-2 rounded-full mr-2"></span>
                    {{ user.status.charAt(0).toUpperCase() + user.status.slice(1) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                    <button 
                      @click.stop="viewUser(user)"
                      class="text-gray-400 hover:text-blue-600 transition-colors p-1 rounded-full hover:bg-blue-50"
                      title="View Details"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button 
                      @click.stop="editUser(user)"
                      class="text-gray-400 hover:text-yellow-600 transition-colors p-1 rounded-full hover:bg-yellow-50"
                      title="Edit User"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button 
                      @click.stop="toggleUserStatus(user)"
                      :class="user.status === 'active' ? 'text-gray-400 hover:text-red-600 hover:bg-red-50' : 'text-gray-400 hover:text-green-600 hover:bg-green-50'"
                      class="transition-colors p-1 rounded-full"
                      :title="user.status === 'active' ? 'Deactivate User' : 'Activate User'"
                    >
                      <svg v-if="user.status === 'active'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728"></path>
                      </svg>
                      <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                    </button>
                    <div class="relative" v-if="user.status === 'pending'">
                      <button 
                        @click.stop="approveUser(user)"
                        class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded-full hover:bg-green-50"
                        title="Approve User"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Grid View -->
        <div v-else class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <div 
              v-for="user in paginatedUsers" 
              :key="user.id"
              class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-lg transition-all duration-200 cursor-pointer group"
              :class="{ 'ring-2 ring-blue-500 border-blue-500': selectedUsers.includes(user.id) }"
              @click="selectUser(user)"
            >
              <div class="flex items-start space-x-3">
                <input 
                  type="checkbox" 
                  :checked="selectedUsers.includes(user.id)"
                  @change="toggleUserSelection(user.id)"
                  @click.stop
                  class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mt-1"
                >
                <div class="flex-1">
                  <!-- User Avatar -->
                  <div class="flex items-center space-x-3 mb-4">
                    <div v-if="user.avatar" class="h-12 w-12 rounded-full overflow-hidden">
                      <img :src="user.avatar" :alt="user.name" class="h-full w-full object-cover">
                    </div>
                    <div v-else class="h-12 w-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-lg">
                      {{ getUserInitials(user.name) }}
                    </div>
                    <div class="flex-1 min-w-0">
                      <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-600 transition-colors">{{ user.name }}</p>
                      <p class="text-xs text-gray-500 truncate">{{ user.email }}</p>
                    </div>
                  </div>

                  <!-- Role Badge -->
                  <div class="mb-3">
                    <span :class="getRoleBadgeClass(user.role)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                      <span :class="getRoleIconClass(user.role)" class="w-2 h-2 rounded-full mr-2"></span>
                      {{ getRoleDisplayName(user.role) }}
                    </span>
                  </div>

                  <!-- User Details -->
                  <div class="space-y-2 text-xs text-gray-600">
                    <div v-if="user.phone" class="flex items-center">
                      <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                      </svg>
                      {{ user.phone }}
                    </div>
                    <div v-if="user.last_login" class="flex items-center">
                      <svg class="w-3 h-3 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      {{ formatRelativeTime(user.last_login) }}
                    </div>
                  </div>

                  <!-- Status -->
                  <div class="flex items-center justify-between mt-4">
                    <span :class="getStatusBadgeClass(user.status)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                      <span :class="getStatusDotClass(user.status)" class="w-2 h-2 rounded-full mr-1"></span>
                      {{ user.status.charAt(0).toUpperCase() + user.status.slice(1) }}
                    </span>

                    <!-- Quick Actions -->
                    <div class="flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                      <button @click.stop="editUser(user)" class="p-1 text-gray-400 hover:text-yellow-600 rounded-full hover:bg-yellow-50" title="Edit User">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredUsers.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
          <p class="mt-1 text-sm text-gray-500">{{ hasActiveFilters ? 'Try adjusting your search or filters.' : 'Get started by creating a new user.' }}</p>
          <div class="mt-6">
            <Link v-if="!hasActiveFilters" :href="route('users.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add New User
            </Link>
            <button v-else @click="resetFilters" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
              <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              Clear Filters
            </button>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="filteredUsers.length > 0" class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
          <div class="flex items-center justify-between">
            <div class="flex-1 flex justify-between sm:hidden">
              <button 
                @click="previousPage"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
              >
                Previous
              </button>
              <button 
                @click="nextPage"
                :disabled="currentPage === totalPages"
                class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
              >
                Next
              </button>
            </div>
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
              <div>
                <p class="text-sm text-gray-700">
                  Showing {{ (currentPage - 1) * itemsPerPage + 1 }} to {{ Math.min(currentPage * itemsPerPage, filteredUsers.length) }} of {{ filteredUsers.length }} results
                </p>
              </div>
              <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                  <button 
                    @click="previousPage"
                    :disabled="currentPage === 1"
                    class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === 1 }"
                  >
                    <span class="sr-only">Previous</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                  </button>
                  
                  <button 
                    v-for="page in visiblePages" 
                    :key="page"
                    @click="goToPage(page)"
                    class="relative inline-flex items-center px-4 py-2 border text-sm font-medium"
                    :class="page === currentPage 
                      ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                      : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
                  >
                    {{ page }}
                  </button>
                  
                  <button 
                    @click="nextPage"
                    :disabled="currentPage === totalPages"
                    class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                    :class="{ 'opacity-50 cursor-not-allowed': currentPage === totalPages }"
                  >
                    <span class="sr-only">Next</span>
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                  </button>
                </nav>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';

// Props from backend
const props = defineProps({
  users: {
    type: Array,
    default: () => []
  },
  statistics: {
    type: Object,
    default: () => ({
      totalUsers: 0,
      activeUsers: 0,
      pendingUsers: 0,
      adminUsers: 0
    })
  }
});

// Reactive data
const filters = ref({
  search: '',
  role: '',
  status: ''
});

const selectedUsers = ref([]);
const viewMode = ref('table'); // 'table' or 'grid'
const currentPage = ref(1);
const itemsPerPage = ref(10);
const sortField = ref('name');
const sortDirection = ref('asc');

// Sample user data (will be replaced by real data from backend)
const sampleUsers = ref([
  {
    id: 1,
    name: 'Ahmed Al-Rashid',
    email: 'ahmed.rashid@qms.sa',
    phone: '+966 50 123 4567',
    role: 'admin',
    status: 'active',
    department: 'IT Administration',
    last_login: new Date(Date.now() - 2 * 60 * 60 * 1000), // 2 hours ago
    avatar: null,
    restaurants: []
  },
  {
    id: 2,
    name: 'Fatima Al-Zahra',
    email: 'fatima.zahra@qms.sa',
    phone: '+966 55 234 5678',
    role: 'area_manager',
    status: 'active',
    department: 'Operations',
    last_login: new Date(Date.now() - 24 * 60 * 60 * 1000), // 1 day ago
    avatar: null,
    restaurants: [{ name: 'Riyadh Central Branch' }]
  },
  {
    id: 3,
    name: 'Omar Al-Mansouri',
    email: 'omar.mansouri@qms.sa',
    phone: '+966 56 345 6789',
    role: 'sm',
    status: 'active',
    department: 'Store Management',
    last_login: new Date(Date.now() - 3 * 60 * 60 * 1000), // 3 hours ago
    avatar: null,
    restaurants: [{ name: 'Mall of Saudi Branch' }, { name: 'Kingdom Center Branch' }]
  },
  {
    id: 4,
    name: 'Aisha Al-Qurashi',
    email: 'aisha.qurashi@qms.sa',
    phone: null,
    role: 'rm',
    status: 'pending',
    department: 'Restaurant Management',
    last_login: null,
    avatar: null,
    restaurants: []
  }
]);

// Use sample data if no users provided from backend
const allUsers = computed(() => props.users.length > 0 ? props.users : sampleUsers.value);

// Computed properties
const activeUsersPercentage = computed(() => {
  if (props.statistics.totalUsers === 0) return 0;
  return Math.round((props.statistics.activeUsers / props.statistics.totalUsers) * 100);
});

const hasActiveFilters = computed(() => {
  return filters.value.search || filters.value.role || filters.value.status;
});

// Debounced search
let searchTimeout = null;
const debouncedSearch = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 300);
};

// Filtered and sorted users
const filteredUsers = computed(() => {
  let result = [...allUsers.value];

  // Apply search filter
  if (filters.value.search) {
    const searchTerm = filters.value.search.toLowerCase();
    result = result.filter(user => 
      user.name.toLowerCase().includes(searchTerm) ||
      user.email.toLowerCase().includes(searchTerm) ||
      (user.phone && user.phone.includes(searchTerm))
    );
  }

  // Apply role filter
  if (filters.value.role) {
    result = result.filter(user => user.role === filters.value.role);
  }

  // Apply status filter
  if (filters.value.status) {
    result = result.filter(user => user.status === filters.value.status);
  }

  // Apply sorting
  result.sort((a, b) => {
    let aValue = a[sortField.value] || '';
    let bValue = b[sortField.value] || '';

    if (sortField.value === 'last_login') {
      aValue = a.last_login ? new Date(a.last_login) : new Date(0);
      bValue = b.last_login ? new Date(b.last_login) : new Date(0);
    }

    if (typeof aValue === 'string') {
      aValue = aValue.toLowerCase();
      bValue = bValue.toLowerCase();
    }

    if (sortDirection.value === 'asc') {
      return aValue < bValue ? -1 : aValue > bValue ? 1 : 0;
    } else {
      return aValue > bValue ? -1 : aValue < bValue ? 1 : 0;
    }
  });

  return result;
});

// Pagination
const totalPages = computed(() => Math.ceil(filteredUsers.value.length / itemsPerPage.value));

const paginatedUsers = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredUsers.value.slice(start, end);
});

const visiblePages = computed(() => {
  const pages = [];
  const maxVisible = 5;
  const start = Math.max(1, currentPage.value - Math.floor(maxVisible / 2));
  const end = Math.min(totalPages.value, start + maxVisible - 1);

  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

// Methods
const resetFilters = () => {
  filters.value = {
    search: '',
    role: '',
    status: ''
  };
  currentPage.value = 1;
};

const clearSearch = () => {
  filters.value.search = '';
  applyFilters();
};

const applyFilters = () => {
  currentPage.value = 1;
};

const sortBy = (field) => {
  if (sortField.value === field) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc';
  } else {
    sortField.value = field;
    sortDirection.value = 'asc';
  }
};

const filterByStatus = (status) => {
  filters.value.status = status;
  applyFilters();
};

// User selection
const toggleUserSelection = (userId) => {
  const index = selectedUsers.value.indexOf(userId);
  if (index > -1) {
    selectedUsers.value.splice(index, 1);
  } else {
    selectedUsers.value.push(userId);
  }
};

const toggleSelectAll = () => {
  if (selectedUsers.value.length === filteredUsers.value.length) {
    selectedUsers.value = [];
  } else {
    selectedUsers.value = filteredUsers.value.map(user => user.id);
  }
};

const selectUser = (user) => {
  // Handle user selection (could navigate to user details)
  console.log('Selected user:', user);
};

// Pagination methods
const nextPage = () => {
  if (currentPage.value < totalPages.value) {
    currentPage.value++;
  }
};

const previousPage = () => {
  if (currentPage.value > 1) {
    currentPage.value--;
  }
};

const goToPage = (page) => {
  currentPage.value = page;
};

// User actions
const editUser = (user) => {
  router.visit(route('users.edit', user.id));
};

const toggleUserStatus = (user) => {
  const newStatus = user.is_active ? 'inactive' : 'active';
  const action = user.is_active ? 'deactivate' : 'activate';
  
  if (!confirm(`Are you sure you want to ${action} this user?`)) {
    return;
  }
  
  router.post(route('users.toggle-status', user.id), {}, {
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Toggle status failed:', errors);
    }
  });
};

const approveUser = (user) => {
  if (!confirm('Are you sure you want to approve this user?')) {
    return;
  }
  
  router.post(route('users.approve', user.id), {}, {
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('User approval failed:', errors);
    }
  });
};

// Bulk actions
const bulkActivate = () => {
  if (selectedUsers.value.length === 0) {
    alert('Please select users to activate');
    return;
  }
  
  router.post(route('users.bulk-action'), {
    action: 'activate',
    user_ids: selectedUsers.value
  }, {
    onSuccess: () => {
      selectedUsers.value = [];
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Bulk activate failed:', errors);
    }
  });
};

const bulkDeactivate = () => {
  if (selectedUsers.value.length === 0) {
    alert('Please select users to deactivate');
    return;
  }
  
  if (!confirm('Are you sure you want to deactivate the selected users?')) {
    return;
  }
  
  router.post(route('users.bulk-action'), {
    action: 'deactivate',
    user_ids: selectedUsers.value
  }, {
    onSuccess: () => {
      selectedUsers.value = [];
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Bulk deactivate failed:', errors);
    }
  });
};

// Export functionality
const exportUsers = () => {
  // Create a download link to the export endpoint
  window.open(route('users.export'), '_blank');
};

// Helper methods
const getUserInitials = (name) => {
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const getRoleDisplayName = (role) => {
  const roleNames = {
    'admin': 'Administrator',
    'area_manager': 'Area Manager', 
    'sm': 'Store Manager',
    'rm': 'Restaurant Manager',
    'om': 'Operations Manager',
    'coo': 'Chief Operations Officer',
    'training': 'Training Manager'
  };
  return roleNames[role] || role;
};

const getRoleBadgeClass = (role) => {
  const classes = {
    'admin': 'bg-red-100 text-red-800',
    'area_manager': 'bg-purple-100 text-purple-800',
    'sm': 'bg-blue-100 text-blue-800', 
    'rm': 'bg-green-100 text-green-800',
    'om': 'bg-yellow-100 text-yellow-800',
    'coo': 'bg-indigo-100 text-indigo-800',
    'training': 'bg-pink-100 text-pink-800'
  };
  return classes[role] || 'bg-gray-100 text-gray-800';
};

const getRoleIconClass = (role) => {
  const classes = {
    'admin': 'bg-red-500',
    'area_manager': 'bg-purple-500',
    'sm': 'bg-blue-500',
    'rm': 'bg-green-500', 
    'om': 'bg-yellow-500',
    'coo': 'bg-indigo-500',
    'training': 'bg-pink-500'
  };
  return classes[role] || 'bg-gray-500';
};

const getStatusBadgeClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'inactive': 'bg-red-100 text-red-800',
    'pending': 'bg-yellow-100 text-yellow-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusDotClass = (status) => {
  const classes = {
    'active': 'bg-green-500',
    'inactive': 'bg-red-500',
    'pending': 'bg-yellow-500'
  };
  return classes[status] || 'bg-gray-500';
};

// Date formatting
const formatRelativeTime = (date) => {
  if (!date) return 'Never';
  
  const now = new Date();
  const diffInMs = now - new Date(date);
  const diffInHours = Math.floor(diffInMs / (1000 * 60 * 60));
  const diffInDays = Math.floor(diffInMs / (1000 * 60 * 60 * 24));

  if (diffInHours < 1) {
    return 'Less than an hour ago';
  } else if (diffInHours < 24) {
    return `${diffInHours} ${diffInHours === 1 ? 'hour' : 'hours'} ago`;
  } else if (diffInDays < 30) {
    return `${diffInDays} ${diffInDays === 1 ? 'day' : 'days'} ago`;
  } else {
    return formatDate(date);
  }
};

const formatDate = (date) => {
  if (!date) return 'Never';
  return new Date(date).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  });
};

const formatFullDateTime = (date) => {
  if (!date) return 'Never';
  return new Date(date).toLocaleString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

// Update statistics when sample data is used
const statistics = computed(() => {
  if (props.users.length > 0) {
    return props.statistics;
  }
  
  // Calculate from sample data
  const total = sampleUsers.value.length;
  const active = sampleUsers.value.filter(u => u.status === 'active').length;
  const pending = sampleUsers.value.filter(u => u.status === 'pending').length;
  const admin = sampleUsers.value.filter(u => u.role === 'admin').length;
  
  return {
    totalUsers: total,
    activeUsers: active,
    pendingUsers: pending,
    adminUsers: admin
  };
});

// Watch for filter changes
watch(filters.value, () => {
  currentPage.value = 1;
});
</script>

<style scoped>
/* Custom animations and transitions */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes slideIn {
  from { transform: translateX(-10px); opacity: 0; }
  to { transform: translateX(0); opacity: 1; }
}

.fade-in {
  animation: fadeIn 0.5s ease-out;
}

.slide-in {
  animation: slideIn 0.3s ease-out;
}

/* Custom scrollbar for tables */
.overflow-x-auto::-webkit-scrollbar {
  height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Hover effects for interactive elements */
.hover-lift {
  transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Custom focus styles */
.focus-ring:focus {
  outline: none;
  box-shadow: 0 0 0 2px #3b82f6, 0 0 0 4px rgba(59, 130, 246, 0.3);
}

/* Status indicator pulse animation */
.status-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

/* Enhanced table row hover */
tbody tr:hover {
  background-color: #f8fafc;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.04);
}

/* Card hover effects */
.card-hover {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.card-hover:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

/* Button loading state */
.btn-loading {
  position: relative;
  color: transparent;
}

.btn-loading::after {
  content: "";
  position: absolute;
  width: 16px;
  height: 16px;
  top: 50%;
  left: 50%;
  margin-left: -8px;
  margin-top: -8px;
  border: 2px solid transparent;
  border-top-color: currentColor;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Smooth transitions for all interactive elements */
* {
  transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 150ms;
}

/* Custom gradient backgrounds */
.gradient-bg {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

/* Enhanced shadow utilities */
.shadow-soft {
  box-shadow: 0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04);
}

.shadow-strong {
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
  .mobile-stack {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .mobile-hide {
    display: none;
  }
  
  .mobile-full {
    width: 100%;
  }
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  .print-full {
    width: 100% !important;
    max-width: none !important;
  }
}
</style>
