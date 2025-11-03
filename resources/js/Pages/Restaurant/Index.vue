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
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Restaurant Management</h1>
                <p class="text-gray-500">Manage and monitor all restaurant locations</p>
              </div>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <button
              @click="toggleBulkActions"
              v-if="selectedRestaurants.length > 0"
              class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
              Bulk Actions ({{ selectedRestaurants.length }})
            </button>
            <Link :href="route('restaurants.create')" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 shadow-lg">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              Add New Restaurant
            </Link>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Restaurants</p>
              <p class="text-2xl font-bold text-gray-900">{{ statistics.total }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-xl">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Active Restaurants</p>
              <p class="text-2xl font-bold text-green-600">{{ statistics.active }}</p>
            </div>
            <div class="bg-green-100 p-3 rounded-xl">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Inactive Restaurants</p>
              <p class="text-2xl font-bold text-red-600">{{ statistics.inactive }}</p>
            </div>
            <div class="bg-red-100 p-3 rounded-xl">
              <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Managers</p>
              <p class="text-2xl font-bold text-purple-600">{{ statistics.totalManagers }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-xl">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="bg-white rounded-2xl shadow-sm p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0 lg:space-x-4">
          <!-- Search Bar -->
          <div class="relative flex-1 max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
            <input 
              v-model="searchQuery"
              type="text" 
              placeholder="Search restaurants, managers, or cities..."
              class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>
          
          <!-- Filters -->
          <div class="flex flex-wrap gap-3">
            <select v-model="selectedBrand" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">All Brands</option>
              <option value="SDB">SDB</option>
              <option value="FB">FB</option>
              <option value="TNDR">TNDR</option>
              <option value="Pizza Dealer">Pizza Dealer</option>
            </select>
            
            <select v-model="selectedStatus" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            
            <select v-model="selectedCity" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">All Cities</option>
              <option v-for="city in uniqueCities" :key="city" :value="city">{{ city }}</option>
            </select>
            
            <select v-model="selectedRestaurantManager" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">All Restaurant Managers</option>
              <option v-for="manager in uniqueRestaurantManagers" :key="manager.id" :value="manager.id">{{ manager.name }}</option>
            </select>
            
            <select v-model="selectedAreaManager" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <option value="">All Area Managers</option>
              <option v-for="manager in uniqueAreaManagers" :key="manager.id" :value="manager.id">{{ manager.name }}</option>
            </select>
            
            <button 
              @click="resetFilters"
              class="px-4 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-colors"
            >
              Reset Filters
            </button>
          </div>
          
          <!-- View Toggle -->
          <div class="flex bg-gray-100 rounded-lg p-1">
            <button 
              @click="viewMode = 'table'"
              :class="viewMode === 'table' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors"
            >
              Table
            </button>
            <button 
              @click="viewMode = 'grid'"
              :class="viewMode === 'grid' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-600'"
              class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors"
            >
              Grid
            </button>
          </div>
        </div>
      </div>

      <!-- Bulk Actions Bar -->
      <div v-if="selectedRestaurants.length > 0" class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <span class="text-sm font-medium text-blue-800">{{ selectedRestaurants.length }} restaurant(s) selected</span>
          </div>
          <div class="flex space-x-2">
            <button 
              @click="bulkActivate"
              class="px-3 py-1.5 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-sm font-medium transition-colors"
            >
              Activate
            </button>
            <button 
              @click="bulkDeactivate"
              class="px-3 py-1.5 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-sm font-medium transition-colors"
            >
              Deactivate
            </button>
            <button 
              @click="clearSelection"
              class="px-3 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-lg text-sm font-medium transition-colors"
            >
              Clear
            </button>
          </div>
        </div>
      </div>

      <!-- Table View -->
      <div v-if="viewMode === 'table'" class="bg-white rounded-2xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-900">All Restaurants</h3>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left">
                  <input 
                    type="checkbox" 
                    @change="toggleSelectAll"
                    :checked="isAllSelected"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  >
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Restaurant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Managers</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr 
                v-for="restaurant in filteredRestaurants" 
                :key="restaurant.id"
                class="hover:bg-gray-50 transition-colors"
              >
                <td class="px-6 py-4">
                  <input 
                    type="checkbox" 
                    :value="restaurant.id"
                    v-model="selectedRestaurants"
                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                  >
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-12 w-12">
                      <div class="h-12 w-12 rounded-xl bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                      </div>
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ restaurant.name }}</div>
                      <div class="text-sm text-gray-500">{{ restaurant.code }}</div>
                      <div v-if="restaurant.brand" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 mt-1">
                        {{ restaurant.brand }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ restaurant.address }}</div>
                  <div class="text-sm text-gray-500">{{ restaurant.city }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="space-y-2">
                    <!-- Restaurant Manager -->
                    <div v-if="restaurant.restaurant_manager" class="flex items-center">
                      <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        <span class="text-xs font-medium text-blue-700">{{ getInitials(restaurant.restaurant_manager.name) }}</span>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ restaurant.restaurant_manager.name }}</div>
                        <div class="text-xs text-blue-600 font-medium">Restaurant Manager</div>
                      </div>
                    </div>
                    
                    <!-- Area Manager -->
                    <div v-if="restaurant.area_manager" class="flex items-center">
                      <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <span class="text-xs font-medium text-green-700">{{ getInitials(restaurant.area_manager.name) }}</span>
                      </div>
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ restaurant.area_manager.name }}</div>
                        <div class="text-xs text-green-600 font-medium">Area Manager</div>
                      </div>
                    </div>
                    
                    <!-- No managers assigned -->
                    <div v-if="!restaurant.restaurant_manager && !restaurant.area_manager" class="text-sm text-gray-400">
                      No managers assigned
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ restaurant.phone }}</div>
                  <div class="text-sm text-gray-500">{{ restaurant.email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    :class="getStatusClass(restaurant.status)"
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                  >
                    {{ getStatusText(restaurant.status) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
                    <button @click="viewRestaurant(restaurant)" class="text-blue-600 hover:text-blue-900 p-1" title="View Restaurant">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                      </svg>
                    </button>
                    <button @click="editRestaurant(restaurant)" class="text-yellow-600 hover:text-yellow-900 p-1" title="Edit Restaurant">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                      </svg>
                    </button>
                    <button @click="deleteRestaurant(restaurant)" class="text-red-600 hover:text-red-900 p-1" title="Delete Restaurant">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                      </svg>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Grid View -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <div 
          v-for="restaurant in filteredRestaurants" 
          :key="restaurant.id"
          class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all duration-200 overflow-hidden"
        >
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <div class="h-12 w-12 rounded-xl bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <input 
                type="checkbox" 
                :value="restaurant.id"
                v-model="selectedRestaurants"
                class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
              >
            </div>
            
            <div class="mb-4">
              <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ restaurant.name }}</h3>
              <p class="text-sm text-gray-500 mb-2">{{ restaurant.code }}</p>
              <div v-if="restaurant.brand" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                {{ restaurant.brand }}
              </div>
            </div>
            
            <div class="space-y-3 mb-4">
              <div class="flex items-start space-x-2">
                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <div class="text-sm">
                  <div class="text-gray-900">{{ restaurant.address }}</div>
                  <div class="text-gray-500">{{ restaurant.city }}</div>
                </div>
              </div>
              
              <!-- Restaurant Manager -->
              <div v-if="restaurant.restaurant_manager" class="flex items-center space-x-2">
                <div class="h-6 w-6 rounded-full bg-blue-100 flex items-center justify-center">
                  <span class="text-xs font-medium text-blue-700">{{ getInitials(restaurant.restaurant_manager.name) }}</span>
                </div>
                <div class="text-sm">
                  <div class="text-gray-900">{{ restaurant.restaurant_manager.name }}</div>
                  <div class="text-blue-600 font-medium">Restaurant Manager</div>
                </div>
              </div>
              
              <!-- Area Manager -->
              <div v-if="restaurant.area_manager" class="flex items-center space-x-2">
                <div class="h-6 w-6 rounded-full bg-green-100 flex items-center justify-center">
                  <span class="text-xs font-medium text-green-700">{{ getInitials(restaurant.area_manager.name) }}</span>
                </div>
                <div class="text-sm">
                  <div class="text-gray-900">{{ restaurant.area_manager.name }}</div>
                  <div class="text-green-600 font-medium">Area Manager</div>
                </div>
              </div>
              
              <div v-if="restaurant.phone" class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                </svg>
                <span class="text-sm text-gray-900">{{ restaurant.phone }}</span>
              </div>
            </div>
            
            <div class="flex items-center justify-between">
              <span 
                :class="getStatusClass(restaurant.status)"
                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
              >
                {{ getStatusText(restaurant.status) }}
              </span>
              
              <div class="flex space-x-1">
                <button @click="viewRestaurant(restaurant)" class="text-blue-600 hover:text-blue-900 p-1" title="View Restaurant">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                </button>
                <button @click="editRestaurant(restaurant)" class="text-yellow-600 hover:text-yellow-900 p-1" title="Edit Restaurant">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                <button @click="deleteRestaurant(restaurant)" class="text-red-600 hover:text-red-900 p-1" title="Delete Restaurant">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="filteredRestaurants.length > 0" class="mt-8 flex items-center justify-between">
        <div class="text-sm text-gray-700">
          Showing {{ ((currentPage - 1) * perPage) + 1 }} to {{ Math.min(currentPage * perPage, filteredRestaurants.length) }} of {{ filteredRestaurants.length }} restaurants
        </div>
        <div class="flex space-x-1">
          <button 
            @click="currentPage--"
            :disabled="currentPage === 1"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Previous
          </button>
          <button 
            v-for="page in visiblePages" 
            :key="page"
            @click="currentPage = page"
            :class="page === currentPage ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'"
            class="px-3 py-2 text-sm font-medium border rounded-md"
          >
            {{ page }}
          </button>
          <button 
            @click="currentPage++"
            :disabled="currentPage === totalPages"
            class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            Next
          </button>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="filteredRestaurants.length === 0" class="text-center py-12">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">No restaurants found</h3>
        <p class="mt-1 text-sm text-gray-500">Get started by creating a new restaurant.</p>
        <div class="mt-6">
          <Link :href="route('restaurants.create')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Add Restaurant
          </Link>
        </div>
      </div>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';

// Props
const props = defineProps({
  restaurants: {
    type: Array,
    default: () => []
  },
  statistics: {
    type: Object,
    default: () => ({
      total: 0,
      active: 0,
      inactive: 0,
      totalManagers: 0
    })
  }
});

// Reactive state
const searchQuery = ref('');
const selectedBrand = ref('');
const selectedStatus = ref('');
const selectedCity = ref('');
const selectedRestaurantManager = ref('');
const selectedAreaManager = ref('');
const viewMode = ref('table');
const selectedRestaurants = ref([]);
const currentPage = ref(1);
const perPage = ref(10);

// Computed properties
const filteredRestaurants = computed(() => {
  let filtered = props.restaurants;

  // Search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(restaurant => 
      restaurant.name.toLowerCase().includes(query) ||
      restaurant.code.toLowerCase().includes(query) ||
      restaurant.address?.toLowerCase().includes(query) ||
      restaurant.city?.toLowerCase().includes(query) ||
      restaurant.restaurant_manager?.name?.toLowerCase().includes(query) ||
      restaurant.area_manager?.name?.toLowerCase().includes(query)
    );
  }

  // Brand filter
  if (selectedBrand.value) {
    filtered = filtered.filter(restaurant => restaurant.brand === selectedBrand.value);
  }

  // Status filter
  if (selectedStatus.value) {
    filtered = filtered.filter(restaurant => restaurant.status === selectedStatus.value);
  }

  // City filter
  if (selectedCity.value) {
    filtered = filtered.filter(restaurant => restaurant.city === selectedCity.value);
  }

  // Restaurant Manager filter
  if (selectedRestaurantManager.value) {
    filtered = filtered.filter(restaurant => 
      restaurant.restaurant_manager && restaurant.restaurant_manager.id == selectedRestaurantManager.value
    );
  }

  // Area Manager filter
  if (selectedAreaManager.value) {
    filtered = filtered.filter(restaurant => 
      restaurant.area_manager && restaurant.area_manager.id == selectedAreaManager.value
    );
  }

  // Pagination
  const start = (currentPage.value - 1) * perPage.value;
  const end = start + perPage.value;
  return filtered.slice(start, end);
});

const uniqueCities = computed(() => {
  const cities = props.restaurants
    .map(restaurant => restaurant.city)
    .filter(city => city)
    .filter((city, index, arr) => arr.indexOf(city) === index);
  return cities.sort();
});

const uniqueRestaurantManagers = computed(() => {
  const managers = props.restaurants
    .map(restaurant => restaurant.restaurant_manager)
    .filter(manager => manager)
    .filter((manager, index, arr) => arr.findIndex(m => m?.id === manager.id) === index);
  return managers.sort((a, b) => a.name.localeCompare(b.name));
});

const uniqueAreaManagers = computed(() => {
  const managers = props.restaurants
    .map(restaurant => restaurant.area_manager)
    .filter(manager => manager)
    .filter((manager, index, arr) => arr.findIndex(m => m?.id === manager.id) === index);
  return managers.sort((a, b) => a.name.localeCompare(b.name));
});

const totalPages = computed(() => {
  return Math.ceil(props.restaurants.length / perPage.value);
});

const visiblePages = computed(() => {
  const pages = [];
  const start = Math.max(1, currentPage.value - 2);
  const end = Math.min(totalPages.value, currentPage.value + 2);
  
  for (let i = start; i <= end; i++) {
    pages.push(i);
  }
  return pages;
});

const isAllSelected = computed(() => {
  return filteredRestaurants.value.length > 0 && 
         selectedRestaurants.value.length === filteredRestaurants.value.length;
});

// Methods
const resetFilters = () => {
  searchQuery.value = '';
  selectedBrand.value = '';
  selectedStatus.value = '';
  selectedCity.value = '';
  selectedRestaurantManager.value = '';
  selectedAreaManager.value = '';
  currentPage.value = 1;
};

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedRestaurants.value = [];
  } else {
    selectedRestaurants.value = filteredRestaurants.value.map(restaurant => restaurant.id);
  }
};

const clearSelection = () => {
  selectedRestaurants.value = [];
};

const toggleBulkActions = () => {
  // Toggle selection of all visible restaurants
  if (selectedRestaurants.value.length === filteredRestaurants.value.length) {
    selectedRestaurants.value = [];
  } else {
    selectedRestaurants.value = filteredRestaurants.value.map(r => r.id);
  }
};

const bulkActivate = () => {
  if (selectedRestaurants.value.length === 0) {
    alert('Please select restaurants to activate');
    return;
  }
  
  router.post(route('restaurants.bulk-action'), {
    action: 'activate',
    restaurant_ids: selectedRestaurants.value
  }, {
    onSuccess: () => {
      selectedRestaurants.value = [];
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Bulk activate failed:', errors);
    }
  });
};

const bulkDeactivate = () => {
  if (selectedRestaurants.value.length === 0) {
    alert('Please select restaurants to deactivate');
    return;
  }
  
  if (!confirm('Are you sure you want to deactivate the selected restaurants?')) {
    return;
  }
  
  router.post(route('restaurants.bulk-action'), {
    action: 'deactivate',
    restaurant_ids: selectedRestaurants.value
  }, {
    onSuccess: () => {
      selectedRestaurants.value = [];
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Bulk deactivate failed:', errors);
    }
  });
};

const getInitials = (name) => {
  if (!name) return 'N/A';
  return name.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2);
};

const getStatusClass = (status) => {
  const classes = {
    'active': 'bg-green-100 text-green-800',
    'inactive': 'bg-red-100 text-red-800',
    'under-review': 'bg-yellow-100 text-yellow-800'
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
};

const getStatusText = (status) => {
  const texts = {
    'active': 'Active',
    'inactive': 'Inactive',
    'under-review': 'Under Review'
  };
  return texts[status] || status;
};

// Action methods
const viewRestaurant = (restaurant) => {
  router.visit(route('restaurants.show', restaurant.id));
};

const editRestaurant = (restaurant) => {
  router.visit(route('restaurants.edit', restaurant.id));
};

const deleteRestaurant = (restaurant) => {
  if (!confirm(`Are you sure you want to delete "${restaurant.name}"? This action cannot be undone.`)) {
    return;
  }
  
  router.delete(route('restaurants.destroy', restaurant.id), {
    onSuccess: () => {
      // Refresh the page to show updated data
      router.reload();
    },
    onError: (errors) => {
      console.error('Delete failed:', errors);
    }
  });
};
</script>

<style scoped>
/* Custom animations for smooth transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

/* Hover effects for interactive elements */
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
}

/* Custom scrollbar for table */
.overflow-x-auto::-webkit-scrollbar {
  height: 6px;
}

.overflow-x-auto::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>
