<template>
  <DefaultAuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 p-4 md:p-6">
      <!-- Modern Header Section -->
      <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between bg-white rounded-2xl shadow-sm p-6">
          <div class="mb-4 md:mb-0">
            <div class="flex items-center space-x-3">
              <div class="bg-gradient-to-r from-green-500 to-emerald-600 p-3 rounded-xl">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Add New Restaurant</h1>
                <p class="text-gray-500">Create a new restaurant location</p>
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

      <!-- Progress Indicator -->
      <div class="mb-8 bg-white rounded-2xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold text-gray-900">Restaurant Setup Progress</h3>
          <div class="flex items-center space-x-4">
            <div v-if="lastSaved" class="text-xs text-gray-500 flex items-center space-x-1">
              <svg class="w-3 h-3 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
              <span>Auto-saved {{ formatTime(lastSaved) }}</span>
            </div>
            <span class="text-sm text-gray-500">{{ completedSteps }}/2 sections completed</span>
          </div>
        </div>
        <div class="flex space-x-4">
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <div :class="getStepIconClass(1)" class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                <svg v-if="isStepCompleted(1)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span v-else>1</span>
              </div>
              <span :class="isStepCompleted(1) ? 'text-green-600 font-medium' : 'text-gray-500'" class="text-sm">Basic Information</span>
            </div>
            <div :class="isStepCompleted(1) ? 'bg-green-500' : 'bg-gray-200'" class="h-1 rounded-full"></div>
          </div>
          <div class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <div :class="getStepIconClass(2)" class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                <svg v-if="isStepCompleted(2)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span v-else>2</span>
              </div>
              <span :class="isStepCompleted(2) ? 'text-green-600 font-medium' : 'text-gray-500'" class="text-sm">Location & Contact</span>
            </div>
            <div :class="isStepCompleted(2) ? 'bg-green-500' : 'bg-gray-200'" class="h-1 rounded-full"></div>
          </div>
          <div v-if="false" class="flex-1">
            <div class="flex items-center space-x-2 mb-2">
              <div :class="getStepIconClass(3)" class="w-8 h-8 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                <svg v-if="isStepCompleted(3)" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span v-else>3</span>
              </div>
              <span :class="isStepCompleted(3) ? 'text-green-600 font-medium' : 'text-gray-500'" class="text-sm">Additional Details</span>
            </div>
            <div :class="isStepCompleted(3) ? 'bg-green-500' : 'bg-gray-200'" class="h-1 rounded-full"></div>
          </div>
        </div>
      </div>

      <!-- Enhanced Form with Collapsible Sections -->
      <form @submit.prevent="submitForm" class="space-y-6">
        
        <!-- Section 1: Basic Information -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
          <div 
            @click="toggleSection('basic')"
            class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-3 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                <p class="text-sm text-gray-500">Restaurant name, code, brand and status</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <div v-if="isStepCompleted(1)" class="flex items-center text-green-600 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Complete
              </div>
              <svg 
                :class="{ 'transform rotate-180': expandedSections.basic }"
                class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
          </div>
          
          <div v-if="expandedSections.basic" class="border-t border-gray-100">
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Restaurant Name -->
              <div class="lg:col-span-1">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                  Restaurant Name <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="name" 
                    v-model="form.name"
                    @input="validateField('name')"
                    :class="getInputClass('name')"
                    class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter restaurant name"
                    required
                  >
                  <div v-if="form.errors?.name" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <p v-if="form.errors?.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}</p>
              </div>

              <!-- Restaurant Code -->
              <div class="lg:col-span-1">
                <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                  Restaurant Code <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                  <input 
                    type="text" 
                    id="code" 
                    v-model="form.code"
                    @input="validateCode"
                    :class="getInputClass('code')"
                    class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                    placeholder="e.g., REST001"
                    required
                  >
                  <div v-if="codeValidation.loading" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="animate-spin w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                  </div>
                  <div v-else-if="codeValidation.available === false" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <div v-else-if="codeValidation.available === true" class="absolute inset-y-0 right-0 flex items-center pr-3">
                    <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                </div>
                <p v-if="form.errors?.code || codeValidation.message" class="mt-2 text-sm" :class="codeValidation.available === false ? 'text-red-600' : 'text-green-600'">
                  {{ form.errors?.code || codeValidation.message }}
                </p>
              </div>

              <!-- Restaurant Brand -->
              <div class="lg:col-span-1">
                <label for="brand" class="block text-sm font-medium text-gray-700 mb-2">
                  Restaurant Brand
                  <button type="button" @click="showBrandTooltip = !showBrandTooltip" class="ml-1 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </button>
                </label>
                <div class="relative">
                  <select 
                    id="brand" 
                    v-model="form.brand"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white transition-all duration-200"
                  >
                    <option value="">Select Brand</option>
                    <option value="SDB">SDB</option>
                    <option value="FB">FB</option>
                    <option value="TNDR">TNDR</option>
                    <option value="Pizza Dealer">Pizza Dealer</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
                <!-- Brand Tooltip -->
                <div v-if="showBrandTooltip" class="mt-2 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
                  <p><strong>SDB:</strong> SDB Restaurant Brand</p>
                  <p><strong>FB:</strong> FB Restaurant Brand</p>
                  <p><strong>TNDR:</strong> TNDR Restaurant Brand</p>
                  <p><strong>Pizza Dealer:</strong> Pizza Dealer Restaurant Brand</p>
                </div>
              </div>

              <!-- Status -->
              <div class="lg:col-span-1">
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <div class="relative">
                  <select 
                    id="status" 
                    v-model="form.status"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent appearance-none bg-white transition-all duration-200"
                  >
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="under-review">Under Review</option>
                  </select>
                  <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                  </div>
                </div>
                <div v-if="form.status === 'inactive'" class="mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                  <div class="flex items-start space-x-2">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <p class="text-sm text-yellow-800">Inactive restaurants cannot be assigned to users until activated.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Section 2: Location & Contact -->
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
          <div 
            @click="toggleSection('location')"
            class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-gradient-to-r from-purple-500 to-pink-600 p-3 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Location & Contact</h3>
                <p class="text-sm text-gray-500">Address, contact details and management information</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <div v-if="isStepCompleted(2)" class="flex items-center text-green-600 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Complete
              </div>
              <svg 
                :class="{ 'transform rotate-180': expandedSections.location }"
                class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
          </div>
          
          <div v-if="expandedSections.location" class="border-t border-gray-100">
            <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-6">
              <!-- Address - Hidden -->
              <div v-if="false" class="lg:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                  Address <span class="text-red-500">*</span>
                </label>
                <textarea 
                  id="address" 
                  v-model="form.address"
                  @input="validateField('address')"
                  :class="getInputClass('address')"
                  class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  rows="3"
                  placeholder="Enter full address including street, district, and postal code"
                  required
                ></textarea>
                <p v-if="form.errors?.address" class="mt-2 text-sm text-red-600">{{ form.errors.address }}</p>
              </div>

              <!-- City -->
              <div class="lg:col-span-1">
                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City</label>
                <div class="relative">
                  <select 
                    id="city" 
                    v-model="form.city"
                    class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white"
                    required
                  >
                    <option value="" disabled>Select a city</option>
                    <option 
                      v-for="city in props.cities" 
                      :key="city"
                      :value="city"
                    >
                      {{ city }}
                    </option>
                  </select>
                  <!-- City Icon -->
                  <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                </div>
              </div>

              <!-- GPS Coordinates Section -->
              <div class="lg:col-span-2">
                <div class="border-t pt-4 mt-4">
                  <div class="flex items-center justify-between mb-4">
                    <h4 class="text-lg font-medium text-gray-900">GPS Coordinates</h4>
                    <button 
                      type="button"
                      @click="getCurrentLocation"
                      :disabled="gettingLocation"
                      class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
                    >
                      <svg v-if="!gettingLocation" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                      {{ gettingLocation ? 'Getting Location...' : 'Get Current Location' }}
                    </button>
                  </div>
                  
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Latitude -->
                    <div>
                      <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                        Latitude <span class="text-red-500">*</span>
                      </label>
                      <input 
                        type="number" 
                        id="latitude" 
                        v-model="form.latitude"
                        @input="validateField('latitude')"
                        :class="getInputClass('latitude')"
                        class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        step="0.000001"
                        min="-90"
                        max="90"
                        placeholder="e.g., 24.7136"
                        required
                      >
                      <p v-if="form.errors?.latitude" class="mt-2 text-sm text-red-600">{{ form.errors.latitude }}</p>
                    </div>

                    <!-- Longitude -->
                    <div>
                      <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                        Longitude <span class="text-red-500">*</span>
                      </label>
                      <input 
                        type="number" 
                        id="longitude" 
                        v-model="form.longitude"
                        @input="validateField('longitude')"
                        :class="getInputClass('longitude')"
                        class="block w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                        step="0.000001"
                        min="-180"
                        max="180"
                        placeholder="e.g., 46.6753"
                        required
                      >
                      <p v-if="form.errors?.longitude" class="mt-2 text-sm text-red-600">{{ form.errors.longitude }}</p>
                    </div>
                  </div>

                  <!-- Location Info -->
                  <div v-if="form.latitude && form.longitude" class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center">
                      <svg class="w-5 h-5 text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                      </svg>
                      <p class="text-sm text-green-800">
                        Location set: {{ form.latitude }}, {{ form.longitude }}
                        <a :href="`https://maps.google.com/?q=${form.latitude},${form.longitude}`" target="_blank" class="ml-2 text-green-600 hover:text-green-500 underline">
                          View on Maps
                        </a>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Phone - Hidden -->
              <div v-if="false" class="lg:col-span-1">
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                <input 
                  type="tel" 
                  id="phone" 
                  v-model="form.phone"
                  @input="formatPhone"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  placeholder="+966 50 123 4567"
                >
              </div>

              <!-- Restaurant Manager -->
              <div class="lg:col-span-1">
                <label for="restaurant_manager_id" class="block text-sm font-medium text-gray-700 mb-2">Restaurant Manager</label>
                <select 
                  id="restaurant_manager_id"
                  v-model="form.restaurant_manager_id"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white"
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
              </div>

              <!-- Area Manager -->
              <div class="lg:col-span-1">
                <label for="area_manager_id" class="block text-sm font-medium text-gray-700 mb-2">Area Manager</label>
                <select 
                  id="area_manager_id"
                  v-model="form.area_manager_id"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white"
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
              </div>
            </div>
          </div>
        </div>

        <!-- Section 3: Additional Details - Hidden -->
        <div v-if="false" class="bg-white rounded-2xl shadow-sm overflow-hidden transition-all duration-300">
          <div 
            @click="toggleSection('additional')"
            class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50 transition-colors"
          >
            <div class="flex items-center space-x-4">
              <div class="bg-gradient-to-r from-orange-500 to-red-600 p-3 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
              </div>
              <div>
                <h3 class="text-lg font-semibold text-gray-900">Additional Details</h3>
                <p class="text-sm text-gray-500">Operating hours, capacity and other information</p>
              </div>
            </div>
            <div class="flex items-center space-x-3">
              <div v-if="isStepCompleted(3)" class="flex items-center text-green-600 text-sm font-medium">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Complete
              </div>
              <svg 
                :class="{ 'transform rotate-180': expandedSections.additional }"
                class="w-5 h-5 text-gray-400 transition-transform duration-200" 
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
              </svg>
            </div>
          </div>
          
          <div v-if="expandedSections.additional" class="border-t border-gray-100">
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Opening Hours - Hidden -->
              <div v-if="false">
                <label for="opening_hours" class="block text-sm font-medium text-gray-700 mb-2">Opening Hours</label>
                <input 
                  type="text" 
                  id="opening_hours" 
                  v-model="form.opening_hours"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  placeholder="e.g., 8:00 AM - 10:00 PM"
                >
              </div>

              <!-- Seating Capacity - Hidden -->
              <div v-if="false">
                <label for="seating_capacity" class="block text-sm font-medium text-gray-700 mb-2">Seating Capacity</label>
                <input 
                  type="number" 
                  id="seating_capacity" 
                  v-model="form.seating_capacity"
                  min="0"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  placeholder="e.g., 50"
                >
              </div>

              <!-- Staff Count - Hidden -->
              <div v-if="false">
                <label for="staff_count" class="block text-sm font-medium text-gray-700 mb-2">Staff Count</label>
                <input 
                  type="number" 
                  id="staff_count" 
                  v-model="form.staff_count"
                  min="0"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  placeholder="e.g., 15"
                >
              </div>

              <!-- Notes - Hidden -->
              <div v-if="false" class="lg:col-span-3">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                <textarea 
                  id="notes" 
                  v-model="form.notes"
                  class="block w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                  rows="3"
                  placeholder="Any additional notes about this restaurant (special features, renovation status, etc.)"
                ></textarea>
              </div>
            </div>
          </div>
        </div>

        <!-- Enhanced Action Buttons -->
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0">
            <div class="text-sm text-gray-600">
              <div class="flex items-center space-x-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ completedSteps }}/2 sections completed</span>
              </div>
            </div>
            
            <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3">
              <Link 
                :href="route('restaurants.index')" 
                class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancel
              </Link>
              
              <button 
                type="submit" 
                :disabled="processing || !canSubmit"
                :class="processing || !canSubmit ? 'opacity-50 cursor-not-allowed' : 'hover:from-green-600 hover:to-emerald-700 transform hover:-translate-y-0.5 hover:shadow-xl'"
                class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-all duration-200 shadow-lg"
              >
                <svg v-if="processing" class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                {{ processing ? 'Creating Restaurant...' : 'Create Restaurant' }}
              </button>
            </div>
          </div>
          
          <!-- Success Message -->
          <div v-if="showSuccessMessage" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-xl">
            <div class="flex items-center">
              <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <p class="text-green-800 font-medium">Restaurant created successfully! Redirecting...</p>
            </div>
          </div>
        </div>
      </form>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted } from 'vue';

// Props
const props = defineProps({
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
const showSuccessMessage = ref(false);
const showTypeTooltip = ref(false);
const showBrandTooltip = ref(false);
const gettingLocation = ref(false);

// Section expansion state
const expandedSections = ref({
  basic: true,
  location: false,
  additional: false
});

// Validation states
const codeValidation = ref({
  loading: false,
  available: null,
  message: ''
});

const emailValidation = ref({
  valid: null,
  message: ''
});

// City suggestions
// Form instance
const form = useForm({
  name: '',
  code: '',
  brand: '',
  type: '', // Keep for backward compatibility
  status: 'active',
  address: '',
  city: '',
  latitude: '',
  longitude: '',
  phone: '',
  restaurant_manager_id: '',
  area_manager_id: '',
  manager: '', // Keep for backward compatibility
  email: '',
  opening_hours: '',
  seating_capacity: '',
  staff_count: '',
  notes: ''
});

// Auto-save functionality
const autoSaveKey = 'restaurant_draft';
const lastSaved = ref(null);

// Load draft on component mount
onMounted(() => {
  const savedDraft = localStorage.getItem(autoSaveKey);
  if (savedDraft) {
    try {
      const draft = JSON.parse(savedDraft);
      // Only restore if draft is less than 24 hours old
      if (Date.now() - draft.timestamp < 24 * 60 * 60 * 1000) {
        Object.keys(draft.data).forEach(key => {
          if (form.hasOwnProperty(key)) {
            form[key] = draft.data[key];
          }
        });
        lastSaved.value = new Date(draft.timestamp);
      } else {
        localStorage.removeItem(autoSaveKey);
      }
    } catch (error) {
      console.error('Failed to load draft:', error);
    }
  }
});

// Auto-save watcher
let autoSaveTimeout = null;
const autoSave = () => {
  clearTimeout(autoSaveTimeout);
  autoSaveTimeout = setTimeout(() => {
    const draft = {
      data: { ...form },
      timestamp: Date.now()
    };
    localStorage.setItem(autoSaveKey, JSON.stringify(draft));
    lastSaved.value = new Date();
  }, 2000); // Save after 2 seconds of inactivity
};

// Watch form changes for auto-save
watch(form, autoSave, { deep: true });

// Clear draft on successful submit
const clearDraft = () => {
  localStorage.removeItem(autoSaveKey);
  lastSaved.value = null;
};

// Computed properties
const completedSteps = computed(() => {
  let completed = 0;
  if (isStepCompleted(1)) completed++;
  if (isStepCompleted(2)) completed++;
  // Step 3 (Additional Details) is hidden
  return completed;
});

const canSubmit = computed(() => {
  return form.name && form.code && codeValidation.value.available !== false;
});

// Methods
const toggleSection = (section) => {
  expandedSections.value[section] = !expandedSections.value[section];
};

const isStepCompleted = (step) => {
  switch (step) {
    case 1:
      return form.name && form.code && codeValidation.value.available === true;
    case 2:
      return form.latitude && form.longitude; // Address no longer required
    case 3:
      return true; // Optional section
    default:
      return false;
  }
};

const getStepIconClass = (step) => {
  return isStepCompleted(step) 
    ? 'bg-green-500' 
    : step === 1 
      ? 'bg-blue-500' 
      : step === 2 
        ? 'bg-purple-500' 
        : 'bg-orange-500';
};

// Validation methods
const validateField = (field) => {
  // Clear any existing errors for this field
  if (form.errors && form.errors[field]) {
    delete form.errors[field];
  }
};

const getInputClass = (field) => {
  const hasError = form.errors && form.errors[field];
  const baseClass = 'border';
  
  if (hasError) {
    return `${baseClass} border-red-300 bg-red-50`;
  } else if (field === 'name' && form.name) {
    return `${baseClass} border-green-300 bg-green-50`;
  } else if (field === 'address' && form.address) {
    return `${baseClass} border-green-300 bg-green-50`;
  } else if (field === 'code' && codeValidation.value.available === true) {
    return `${baseClass} border-green-300 bg-green-50`;
  } else if (field === 'email' && emailValidation.value.valid === true) {
    return `${baseClass} border-green-300 bg-green-50`;
  }
  
  return `${baseClass} border-gray-300`;
};

// Code validation with debouncing
let codeTimeout = null;
const validateCode = () => {
  clearTimeout(codeTimeout);
  
  if (!form.code) {
    codeValidation.value = { loading: false, available: null, message: '' };
    return;
  }

  codeValidation.value = { loading: true, available: null, message: 'Checking availability...' };
  
  codeTimeout = setTimeout(() => {
    // Simulate API call for code validation
    // In real implementation, this would be an actual API call
    const isDuplicate = ['REST001', 'REST002', 'MAIN001'].includes(form.code.toUpperCase());
    
    if (isDuplicate) {
      codeValidation.value = { 
        loading: false, 
        available: false, 
        message: 'This restaurant code is already in use. Please choose another.' 
      };
    } else {
      codeValidation.value = { 
        loading: false, 
        available: true, 
        message: 'Restaurant code is available!' 
      };
    }
  }, 800);
};

// Email validation
const validateEmail = () => {
  if (!form.email) {
    emailValidation.value = { valid: null, message: '' };
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const isValid = emailRegex.test(form.email);
  
  if (isValid) {
    emailValidation.value = { valid: true, message: 'Valid email format' };
  } else {
    emailValidation.value = { valid: false, message: 'Please enter a valid email address' };
  }
};

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

// Phone number formatting
const formatPhone = () => {
  // Remove all non-digit characters
  let cleaned = form.phone.replace(/\D/g, '');
  
  // Saudi phone number formatting
  if (cleaned.startsWith('966')) {
    // International format
    cleaned = cleaned.substring(3);
  } else if (cleaned.startsWith('0')) {
    // Local format, remove leading 0
    cleaned = cleaned.substring(1);
  }
  
  // Format as +966 XX XXX XXXX
  if (cleaned.length >= 9) {
    form.phone = `+966 ${cleaned.substring(0, 2)} ${cleaned.substring(2, 5)} ${cleaned.substring(5, 9)}`;
  } else if (cleaned.length > 0) {
    form.phone = `+966 ${cleaned}`;
  }
};

// Form submission
// Format time for auto-save display
const formatTime = (date) => {
  const now = new Date();
  const diff = now - date;
  
  if (diff < 60000) { // Less than 1 minute
    return 'just now';
  } else if (diff < 3600000) { // Less than 1 hour
    const minutes = Math.floor(diff / 60000);
    return `${minutes} minute${minutes === 1 ? '' : 's'} ago`;
  } else {
    return date.toLocaleTimeString();
  }
};

const submitForm = () => {
  processing.value = true;
  
  // Show confirmation dialog
  if (!confirm('Are you sure you want to create this restaurant? Please review all information before proceeding.')) {
    processing.value = false;
    return;
  }
  
  form.post(route('restaurants.store'), {
    onFinish: () => {
      processing.value = false;
    },
    onSuccess: () => {
      clearDraft(); // Clear auto-saved draft
      showSuccessMessage.value = true;
      setTimeout(() => {
        // Redirect will happen automatically via Inertia
      }, 1500);
    },
    onError: (errors) => {
      // Auto-expand sections with errors
      if (errors.name || errors.code || errors.type) {
        expandedSections.value.basic = true;
      }
      if (errors.address || errors.city || errors.phone || errors.manager || errors.email) {
        expandedSections.value.location = true;
      }
      if (errors.opening_hours || errors.seating_capacity || errors.staff_count || errors.notes) {
        expandedSections.value.additional = true;
      }
    }
  });
};

// Watch for form changes to auto-expand next section
watch(() => isStepCompleted(1), (completed) => {
  if (completed && !expandedSections.value.location) {
    setTimeout(() => {
      expandedSections.value.location = true;
    }, 300);
  }
});

watch(() => isStepCompleted(2), (completed) => {
  if (completed && !expandedSections.value.additional) {
    setTimeout(() => {
      expandedSections.value.additional = true;
    }, 300);
  }
});

// Auto-format code to uppercase
watch(() => form.code, (newCode) => {
  if (newCode) {
    form.code = newCode.toUpperCase();
  }
});

// Close tooltips when clicking outside
const handleClickOutside = (event) => {
  if (showTypeTooltip.value && !event.target.closest('.tooltip-container')) {
    showTypeTooltip.value = false;
  }
};

// Add event listener for outside clicks
document.addEventListener('click', handleClickOutside);
</script>

<style scoped>
/* Enhanced animations and transitions */
@keyframes slideIn {
  from { 
    opacity: 0; 
    transform: translateY(-10px); 
  }
  to { 
    opacity: 1; 
    transform: translateY(0); 
  }
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.8; }
}

@keyframes bounceIn {
  0% { 
    opacity: 0; 
    transform: scale(0.95); 
  }
  60% { 
    opacity: 1; 
    transform: scale(1.02); 
  }
  100% { 
    opacity: 1; 
    transform: scale(1); 
  }
}

/* Section expansion animation */
.section-content {
  animation: slideIn 0.3s ease-out;
}

/* Form input animations */
input:focus, textarea:focus, select:focus {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1), 0 0 0 3px rgba(34, 197, 94, 0.1);
}

/* Button hover effects */
button:hover {
  transform: translateY(-1px);
}

button:active {
  transform: translateY(0);
}

/* Loading animation for validation */
.loading-pulse {
  animation: pulse 1.5s ease-in-out infinite;
}

/* Success message animation */
.success-message {
  animation: bounceIn 0.5s ease-out;
}

/* City suggestions animation */
.city-suggestion {
  animation: fadeIn 0.2s ease-out;
}

/* Progress indicator animations */
.progress-step {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.progress-step.completed {
  animation: bounceIn 0.6s ease-out;
}

/* Gradient text effect */
.gradient-text {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

/* Custom scrollbar for dropdown lists */
.city-suggestions::-webkit-scrollbar {
  width: 4px;
}

.city-suggestions::-webkit-scrollbar-track {
  background: #f1f5f9;
}

.city-suggestions::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 2px;
}

.city-suggestions::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Enhanced focus styles */
.focus-ring:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1), 0 0 0 1px rgba(34, 197, 94, 0.2);
}

/* Icon animations */
.section-icon {
  transition: transform 0.2s ease-in-out;
}

.section-icon:hover {
  transform: scale(1.1) rotate(5deg);
}

/* Tooltip styling */
.tooltip-container {
  position: relative;
}

.tooltip {
  transform: translateY(5px);
  animation: fadeIn 0.3s ease-out;
}

/* Status indicator animations */
.status-indicator {
  animation: pulse 2s ease-in-out infinite;
}

/* Form validation styling */
.input-error {
  animation: shakeError 0.5s ease-in-out;
}

@keyframes shakeError {
  0%, 20%, 40%, 60%, 80% { transform: translateX(0); }
  10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
}

.input-success {
  animation: successGlow 0.5s ease-in-out;
}

@keyframes successGlow {
  0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4); }
  70% { box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
  100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
  .mobile-stack {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  
  .mobile-full {
    width: 100%;
  }
  
  .section-header {
    padding: 1rem;
  }
  
  .form-grid {
    grid-template-columns: 1fr;
    gap: 1rem;
  }
}

/* Print styles */
@media print {
  .no-print {
    display: none !important;
  }
  
  .section-content {
    display: block !important;
  }
  
  .bg-gradient-to-r {
    background: #6b7280 !important;
  }
}

/* Dark mode support (if needed in future) */
@media (prefers-color-scheme: dark) {
  .dark-mode {
    background-color: #1f2937;
    color: #f9fafb;
  }
}

/* Accessibility improvements */
@media (prefers-reduced-motion: reduce) {
  *, *::before, *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
  }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .border {
    border-width: 2px !important;
  }
  
  .text-gray-500 {
    color: #000 !important;
  }
  
  .bg-gray-50 {
    background-color: #fff !important;
  }
}
</style>
