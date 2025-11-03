<template>
  <DefaultAuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
      <!-- Modern Header Section -->
      <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white rounded-2xl shadow-sm p-6">
          <div class="mb-4 md:mb-0">
            <div class="flex items-center space-x-3">
              <div class="bg-gradient-to-r from-orange-500 to-amber-600 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Restaurant</h1>
                <p class="text-gray-500">Update restaurant information</p>
              </div>
            </div>
          </div>
          <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
            <Link :href="route('restaurants.index')" class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
              </svg>
              Back to Restaurants
            </Link>
          </div>
        </div>
      </div>

      <!-- Edit Form -->
      <div class="bg-white rounded-2xl shadow-sm p-6">
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Restaurant Name -->
            <div class="lg:col-span-1">
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Name *</label>
              <input 
                type="text" 
                id="name"
                v-model="form.name"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                placeholder="Enter restaurant name"
                required
              >
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
            </div>

            <!-- Restaurant Code -->
            <div class="lg:col-span-1">
              <label for="code" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Code *</label>
              <input 
                type="text" 
                id="code"
                v-model="form.code"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 uppercase"
                placeholder="e.g., RYD001"
                required
              >
              <p v-if="form.errors.code" class="mt-1 text-sm text-red-600">{{ form.errors.code }}</p>
            </div>

            <!-- Brand -->
            <div class="lg:col-span-1">
              <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
              <select 
                id="brand"
                v-model="form.brand"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Select Brand</option>
                <option value="SDB">SDB</option>
                <option value="FB">FB</option>
                <option value="TNDR">TNDR</option>
                <option value="Pizza Dealer">Pizza Dealer</option>
              </select>
              <p v-if="form.errors.brand" class="mt-1 text-sm text-red-600">{{ form.errors.brand }}</p>
            </div>

            <!-- Status -->
            <div class="lg:col-span-1">
              <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
              <select 
                id="status"
                v-model="form.status"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                required
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
                <option value="under-review">Under Review</option>
              </select>
              <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
            </div>

            <!-- City -->
            <div class="lg:col-span-1">
              <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
              <select 
                id="city"
                v-model="form.city"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Select City</option>
                <option 
                  v-for="city in props.cities" 
                  :key="city" 
                  :value="city"
                >
                  {{ city }}
                </option>
              </select>
              <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
            </div>

            <!-- GPS Coordinates -->
            <div class="lg:col-span-1">
              <label class="block text-sm font-medium text-gray-700 mb-2">GPS Coordinates *</label>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <input 
                    type="number" 
                    step="any"
                    v-model="form.latitude"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                    placeholder="Latitude"
                    required
                  >
                  <p v-if="form.errors.latitude" class="mt-1 text-sm text-red-600">{{ form.errors.latitude }}</p>
                </div>
                <div>
                  <input 
                    type="number" 
                    step="any"
                    v-model="form.longitude"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
                    placeholder="Longitude"
                    required
                  >
                  <p v-if="form.errors.longitude" class="mt-1 text-sm text-red-600">{{ form.errors.longitude }}</p>
                </div>
              </div>
              <div class="mt-2">
                <button 
                  type="button"
                  @click="getCurrentLocation"
                  :disabled="gettingLocation"
                  class="inline-flex items-center px-3 py-1 text-xs font-medium text-orange-600 bg-orange-50 border border-orange-200 rounded-lg hover:bg-orange-100 focus:outline-none focus:ring-2 focus:ring-orange-500 disabled:opacity-50"
                >
                  <svg v-if="!gettingLocation" class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                  <svg v-else class="animate-spin w-3 h-3 mr-1" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  {{ gettingLocation ? 'Getting...' : 'Get Current Location' }}
                </button>
              </div>
            </div>

            <!-- Restaurant Manager -->
            <div class="lg:col-span-1">
              <label for="restaurant_manager_id" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Manager</label>
              <select 
                id="restaurant_manager_id"
                v-model="form.restaurant_manager_id"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Select Restaurant Manager</option>
                <option 
                  v-for="manager in props.restaurantManagers" 
                  :key="manager.id" 
                  :value="manager.id"
                >
                  {{ manager.name }} ({{ manager.email }})
                </option>
              </select>
              <p v-if="form.errors.restaurant_manager_id" class="mt-1 text-sm text-red-600">{{ form.errors.restaurant_manager_id }}</p>
            </div>

            <!-- Area Manager -->
            <div class="lg:col-span-1">
              <label for="area_manager_id" class="block text-sm font-medium text-gray-700 mb-2">Area Manager</label>
              <select 
                id="area_manager_id"
                v-model="form.area_manager_id"
                class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200"
              >
                <option value="">Select Area Manager</option>
                <option 
                  v-for="manager in props.areaManagers" 
                  :key="manager.id" 
                  :value="manager.id"
                >
                  {{ manager.name }} ({{ manager.email }})
                </option>
              </select>
              <p v-if="form.errors.area_manager_id" class="mt-1 text-sm text-red-600">{{ form.errors.area_manager_id }}</p>
            </div>

          </div>

          <!-- Submit Button -->
          <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
            <Link 
              :href="route('restaurants.index')" 
              class="inline-flex items-center px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
              Cancel
            </Link>
            <button 
              type="submit"
              :disabled="processing"
              class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-600 text-white font-semibold rounded-lg hover:from-orange-600 hover:to-amber-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
            >
              <svg v-if="processing" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ processing ? 'Updating...' : 'Update Restaurant' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

// Props
const props = defineProps({
  restaurant: {
    type: Object,
    required: true
  },
  restaurantManagers: {
    type: Array,
    default: () => []
  },
  areaManagers: {
    type: Array,
    default: () => []
  },
  cities: {
    type: Array,
    default: () => []
  }
});

// Reactive state
const processing = ref(false);
const gettingLocation = ref(false);

// Form data
const form = useForm({
  name: props.restaurant.name || '',
  code: props.restaurant.code || '',
  brand: props.restaurant.brand || '',
  status: props.restaurant.status || 'active',
  city: props.restaurant.city || '',
  latitude: props.restaurant.latitude || '',
  longitude: props.restaurant.longitude || '',
  restaurant_manager_id: props.restaurant.restaurant_manager_id || '',
  area_manager_id: props.restaurant.area_manager_id || ''
});

// Get current location using GPS
const getCurrentLocation = () => {
  if (!navigator.geolocation) {
    alert('Geolocation is not supported by this browser.');
    return;
  }

  gettingLocation.value = true;
  
  navigator.geolocation.getCurrentPosition(
    (position) => {
      form.latitude = position.coords.latitude.toFixed(8);
      form.longitude = position.coords.longitude.toFixed(8);
      gettingLocation.value = false;
    },
    (error) => {
      gettingLocation.value = false;
      let message = 'Unable to get your location. ';
      
      switch(error.code) {
        case error.PERMISSION_DENIED:
          message += 'Please allow location access and try again.';
          break;
        case error.POSITION_UNAVAILABLE:
          message += 'Location information is unavailable.';
          break;
        case error.TIMEOUT:
          message += 'Location request timed out.';
          break;
        default:
          message += 'An unknown error occurred.';
          break;
      }
      
      alert(message);
    },
    {
      enableHighAccuracy: true,
      timeout: 5000,
      maximumAge: 0
    }
  );
};

// Submit form
const submitForm = () => {
  processing.value = true;
  
  form.put(route('restaurants.update', props.restaurant.id), {
    onFinish: () => {
      processing.value = false;
    }
  });
};
</script>