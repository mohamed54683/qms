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
                <h1 class="text-2xl font-bold text-gray-900">{{ restaurant.name }}</h1>
                <p class="text-gray-500">Restaurant Details</p>
              </div>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <Link :href="route('restaurants.edit', restaurant.id)" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
              Edit Restaurant
            </Link>
            <Link :href="route('restaurants.index')" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Restaurants
            </Link>
          </div>
        </div>
      </div>

      <!-- Restaurant Details -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Main Details -->
        <div class="lg:col-span-2">
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Restaurant Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Restaurant Name</label>
                <p class="text-lg font-medium text-gray-900">{{ restaurant.name }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Restaurant Code</label>
                <p class="text-lg font-medium text-gray-900">{{ restaurant.code }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Brand</label>
                <p class="text-lg font-medium text-gray-900">{{ restaurant.brand || 'Not specified' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Status</label>
                <span 
                  :class="getStatusClass(restaurant.status)"
                  class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium"
                >
                  {{ getStatusText(restaurant.status) }}
                </span>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">City</label>
                <p class="text-lg font-medium text-gray-900">{{ restaurant.city || 'Not specified' }}</p>
              </div>
              
              <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">GPS Coordinates</label>
                <p class="text-lg font-medium text-gray-900">
                  {{ restaurant.latitude }}, {{ restaurant.longitude }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Management Team -->
        <div class="lg:col-span-1">
          <div class="bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Management Team</h2>
            
            <div class="space-y-6">
              <!-- Restaurant Manager -->
              <div v-if="restaurant.restaurant_manager">
                <label class="block text-sm font-medium text-gray-500 mb-2">Restaurant Manager</label>
                <div class="flex items-center space-x-3">
                  <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <span class="text-sm font-medium text-blue-700">
                      {{ getInitials(restaurant.restaurant_manager.name) }}
                    </span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ restaurant.restaurant_manager.name }}</p>
                    <p class="text-sm text-gray-500">{{ restaurant.restaurant_manager.email }}</p>
                  </div>
                </div>
              </div>
              <div v-else>
                <label class="block text-sm font-medium text-gray-500 mb-2">Restaurant Manager</label>
                <p class="text-sm text-gray-400 italic">Not assigned</p>
              </div>

              <!-- Area Manager -->
              <div v-if="restaurant.area_manager">
                <label class="block text-sm font-medium text-gray-500 mb-2">Area Manager</label>
                <div class="flex items-center space-x-3">
                  <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <span class="text-sm font-medium text-green-700">
                      {{ getInitials(restaurant.area_manager.name) }}
                    </span>
                  </div>
                  <div>
                    <p class="text-sm font-medium text-gray-900">{{ restaurant.area_manager.name }}</p>
                    <p class="text-sm text-gray-500">{{ restaurant.area_manager.email }}</p>
                  </div>
                </div>
              </div>
              <div v-else>
                <label class="block text-sm font-medium text-gray-500 mb-2">Area Manager</label>
                <p class="text-sm text-gray-400 italic">Not assigned</p>
              </div>
            </div>
          </div>

          <!-- Map Preview -->
          <div class="mt-6 bg-white rounded-2xl shadow-sm p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Location</h2>
            <div class="bg-gray-100 rounded-lg h-48 flex items-center justify-center">
              <div class="text-center">
                <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                <p class="text-sm text-gray-500">Map Preview</p>
                <p class="text-xs text-gray-400">{{ restaurant.latitude }}, {{ restaurant.longitude }}</p>
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
import { Link } from '@inertiajs/vue3';

// Props
const props = defineProps({
  restaurant: {
    type: Object,
    required: true
  }
});

// Helper functions
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
</script>