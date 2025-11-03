<template>
  <DefaultAuthenticatedLayout>
    <Head title="Temperature Tracking Report" />
    <div class="max-w-7xl mx-auto py-8 px-4">
      <!-- Breadcrumb -->
      <nav class="text-xs text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-1 items-center">
          <li><Link href="/qms/dashboard" class="hover:text-gray-700">Dashboard</Link></li>
          <li>/</li>
          <li class="text-gray-700">Temperature Tracking</li>
        </ol>
      </nav>
      <!-- Enhanced Header -->
      <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div class="flex-1">
          <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Temperature Tracking</h1>
          <div class="flex items-center gap-2 mt-2">
            <p class="text-sm text-gray-600">Monitor and manage temperature compliance across all store locations</p>
            <span v-if="isAreaManager && !isAdmin" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              📊 Area Manager View
            </span>
          </div>
        </div>
        <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row gap-3">
          <!-- Quick Stats -->
          <div class="flex items-center space-x-4 text-sm">
            <div class="flex items-center">
              <span class="w-3 h-3 bg-green-400 rounded-full mr-2"></span>
              <span class="text-gray-600">{{ getStatusCount('Reviewed') }} Reviewed</span>
            </div>
            <div class="flex items-center">
              <span class="w-3 h-3 bg-yellow-400 rounded-full mr-2"></span>
              <span class="text-gray-600">{{ getStatusCount('Submitted') }} Submitted</span>
            </div>
            <div class="flex items-center">
              <span class="w-3 h-3 bg-gray-400 rounded-full mr-2"></span>
              <span class="text-gray-600">{{ getStatusCount('Draft') }} Draft</span>
            </div>
          </div>
          <!-- Action Buttons -->
          <div class="flex flex-wrap gap-2">
            <!-- Print PDF Button (Most Prominent) -->
            <button @click="exportToPDF" 
                    class="group flex items-center justify-center gap-2 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105 relative overflow-hidden">
              <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
              <svg class="w-5 h-5 group-hover:animate-pulse relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
              </svg>
              <span class="relative z-10">Print PDF</span>
              <span class="absolute top-0 right-0 w-2 h-2 bg-yellow-300 rounded-full animate-ping"></span>
            </button>
            
            <!-- Print Page Button -->
            <button @click="printPage" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
              </svg>
              Print Page
            </button>
            
            <!-- Export Dropdown -->
            <!-- Export Dropdown (Secondary Action) -->
            <div class="relative" ref="exportDropdown">
              <button @click="toggleExportDropdown" 
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
                     class="absolute right-0 mt-2 w-72 bg-white border border-gray-200 rounded-2xl shadow-2xl z-50 overflow-hidden">
                  <!-- Header -->
                  <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3">
                    <div class="flex items-center justify-between text-white">
                      <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                        <span class="font-semibold">Export Options</span>
                      </div>
                      <span class="text-xs bg-white/20 px-2 py-1 rounded-full">{{ forms.total }} forms</span>
                    </div>
                  </div>
                  
                  <div class="p-3 space-y-1.5">
                    <!-- Excel Export -->
                    <button @click="exportToExcel(); showExportDropdown = false" 
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
                    
                    <!-- PDF Export -->
                    <button @click="exportToPDF(); showExportDropdown = false" 
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
                    </button>
                    
                    <!-- CSV Export -->
                    <button @click="exportToCSV(); showExportDropdown = false" 
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
                    <div class="flex items-center gap-2 text-xs text-gray-600">
                      <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                      </svg>
                      <span>Exporting filtered forms</span>
                    </div>
                  </div>
                </div>
              </Transition>
            </div>
            
            <!-- New Form Button -->
            <Link :href="route('temperature-tracking.create')" 
                  class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105 relative overflow-hidden group">
              <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
              <svg class="w-5 h-5 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              <span class="relative z-10">New Form</span>
            </Link>
          </div>
        </div>
      </div>
      <!-- Enhanced Filters Card -->
      <section class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">Filter & Search</h2>
            <button @click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700">Clear all</button>
          </div>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Store Location</label>
              <select v-model="filters.store_id" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">All Stores</option>
                <option v-for="s in stores" :key="s.id" :value="s.id">{{ s.name }}</option>
              </select>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
              <input type="date" v-model="filters.date_from" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
              <input type="date" v-model="filters.date_to" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
              <select v-model="filters.status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">All Statuses</option>
                <option v-for="s in statuses" :key="s" :value="s">{{ s }}</option>
              </select>
            </div>
          </div>
          
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div v-if="props.canViewAreaManagerFilter">
              <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Area Manager</label>
              <select v-model="filters.area_manager" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                <option value="">All Area Managers</option>
                <option v-for="manager in areaManagers" :key="manager.id" :value="manager.id">
                  {{ manager.name }}
                </option>
              </select>
            </div>
            <div class="flex items-end" :class="{ 'md:col-start-3': props.canViewAreaManagerFilter, 'md:col-start-1': !props.canViewAreaManagerFilter }">
              <button @click="applyFilters" 
                      class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                </svg>
                Apply Filters
              </button>
            </div>
          </div>
        </div>
      </section>
      <!-- Enhanced Table -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full">
            <thead class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Store</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Shift</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">MIC Details</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Created</th>
                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="f in forms.data" :key="f.id" class="hover:bg-gray-50 transition-colors duration-150">
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">{{ formatDate(f.date) }}</div>
                      <div class="text-xs text-gray-500">{{ f.date }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <div class="text-sm font-medium text-gray-900">{{ f.store?.name || 'N/A' }}</div>
                      <div class="text-xs text-gray-500">Store Location</div>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <span :class="shiftBadgeClass(f.shift)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize">
                    {{ f.shift || 'N/A' }}
                  </span>
                </td>
                <td class="px-4 py-4">
                  <div class="text-sm text-gray-900">
                    <div class="flex items-center mb-1">
                      <svg class="w-4 h-4 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                      </svg>
                      <span class="text-xs font-medium">Morning:</span>
                      <span class="ml-1 text-xs">{{ f.mic_morning || 'N/A' }}</span>
                    </div>
                    <div class="flex items-center">
                      <svg class="w-4 h-4 text-indigo-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                      </svg>
                      <span class="text-xs font-medium">Evening:</span>
                      <span class="ml-1 text-xs">{{ f.mic_evening || 'N/A' }}</span>
                    </div>
                  </div>
                </td>
                <td class="px-4 py-4">
                  <span :class="statusClass(f.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold">
                    <span :class="statusDotClass(f.status)" class="w-2 h-2 rounded-full mr-1.5"></span>
                    {{ f.status }}
                  </span>
                </td>
                <td class="px-4 py-4 text-sm text-gray-500">
                  <div>{{ f.creator?.name || 'Unknown' }}</div>
                  <div class="text-xs">{{ formatDateTime(f.created_at) }}</div>
                </td>
                <td class="px-4 py-4 text-center">
                  <div class="flex items-center justify-center space-x-1 print:hidden">
                    <!-- View Button -->
                    <Link v-if="can('temperature_tracking', 'view')"
                          :href="route('temperature-tracking.show', f.id)" 
                          class="inline-flex items-center px-3 py-1.5 border border-blue-300 shadow-sm text-xs font-medium rounded text-blue-700 bg-blue-50 hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150"
                          title="View Details">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      View
                    </Link>
                  </div>
                  
                  <!-- Print-friendly version -->
                  <div class="hidden print:block text-xs text-gray-900">
                    <div class="font-medium">Form #{{ f.id }}</div>
                  </div>
                </td>
              </tr>
              <tr v-if="forms.data.length===0">
                <td colspan="7" class="px-4 py-12 text-center">
                  <div class="flex flex-col items-center">
                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-sm font-medium text-gray-900 mb-1">No temperature tracking forms found</h3>
                    <p class="text-sm text-gray-500 mb-4">Get started by creating your first temperature tracking form</p>
                    <Link :href="route('temperature-tracking.create')" 
                          class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                      </svg>
                      Create New Form
                    </Link>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Enhanced Pagination -->
      <div class="bg-gradient-to-r from-slate-100 via-gray-100 to-blue-50 px-6 py-5 border-t-2 border-blue-200 mt-6 rounded-xl">
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
                Showing <span class="text-blue-600 font-extrabold text-base">{{ forms.from || 0 }}</span> 
                to <span class="text-blue-600 font-extrabold text-base">{{ forms.to || 0 }}</span> 
                of <span class="text-green-600 font-extrabold text-base">{{ forms.total || 0 }}</span> results
              </span>
            </div>
          </div>
          
          <!-- Desktop Pagination -->
          <div class="hidden md:flex items-center gap-1 flex-wrap justify-center">
            <component 
              v-for="(link, index) in forms.links" 
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
              v-if="forms.prev_page_url"
              :href="forms.prev_page_url"
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
                Page <span class="text-blue-600">{{ forms.current_page }}</span> of <span class="text-green-600">{{ forms.last_page }}</span>
              </span>
            </div>
            
            <Link
              v-if="forms.next_page_url"
              :href="forms.next_page_url"
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
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, reactive, onMounted, onUnmounted } from 'vue';
import { usePermissions } from '@/Composables/usePermissions';

const { can, isAdmin } = usePermissions();

const props = defineProps({
    forms: Object,
    stores: Array,
    statuses: Array,
    areaManagers: Array,
    filters: Object,
    isAdmin: Boolean,
    isAreaManager: Boolean,
    canViewAreaManagerFilter: Boolean,
});

const form = useForm({});

// Reactive variables
const showExportDropdown = ref(false);
const exportDropdown = ref(null);
const filters = reactive({
  store_id: props.filters?.store_id || '',
  date_from: props.filters?.date_from || '',
  date_to: props.filters?.date_to || '',
  status: props.filters?.status || '',
  area_manager: props.filters?.area_manager || ''
});

// Badge class functions
function shiftBadgeClass(shift) {
  const classes = {
    'Morning': 'bg-amber-100 text-amber-800',
    'Evening': 'bg-indigo-100 text-indigo-800',
    'Night': 'bg-slate-700 text-white',
  };
  return classes[shift] || 'bg-gray-100 text-gray-800';
}

function statusBadgeClass(status) {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-700',
    'Submitted': 'bg-yellow-100 text-yellow-800',
    'Reviewed': 'bg-green-100 text-green-800',
    'Approved': 'bg-blue-100 text-blue-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
}

function statusClass(status) {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-700',
    'Submitted': 'bg-yellow-100 text-yellow-800',
    'Reviewed': 'bg-green-100 text-green-800',
    'Approved': 'bg-blue-100 text-blue-800',
  };
  return classes[status] || 'bg-gray-100 text-gray-800';
}

function statusDotClass(status) {
  const classes = {
    'Draft': 'bg-gray-500',
    'Submitted': 'bg-yellow-500',
    'Reviewed': 'bg-green-500',
    'Approved': 'bg-blue-500',
  };
  return classes[status] || 'bg-gray-500';
}

function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    weekday: 'short', 
    year: 'numeric', 
    month: 'short', 
    day: 'numeric' 
  });
}

function formatDateTime(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    month: 'short', 
    day: 'numeric'
  }) + ' at ' + date.toLocaleTimeString('en-US', { 
    hour: '2-digit', 
    minute: '2-digit' 
  });
}

function confirmDelete(id, storeName, date) {
  if (confirm(`Are you sure you want to delete the temperature tracking form for ${storeName} on ${date}?\n\nThis action cannot be undone.`)) {
    router.delete(route('temperature-tracking.destroy', id), {
      onSuccess: () => {
        // Optional: Add success notification
      }
    });
  }
}

function confirmSubmission(id) {
  if (confirm('Are you sure you want to submit this form?\n\nOnce submitted, it will be ready for review.')) {
    router.patch(route('temperature-tracking.update', id), {
      status: 'Submitted'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: Add success notification
      }
    });
  }
}

function confirmReview(id) {
  if (confirm('Mark this form as reviewed?\n\nThis indicates the form has been reviewed and approved.')) {
    router.patch(route('temperature-tracking.update', id), {
      status: 'Reviewed'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: Add success notification
      }
    });
  }
}

function getStatusCount(status) {
  return props.forms.data.filter(form => form.status === status).length;
}

function clearFilters() {
  filters.store_id = '';
  filters.date_from = '';
  filters.date_to = '';
  filters.status = '';
  filters.area_manager = '';
  applyFilters();
}

function applyFilters() {
  // Build query parameters from filters
  const params = {};
  if (filters.store_id) params.store_id = filters.store_id;
  if (filters.date_from) params.date_from = filters.date_from;
  if (filters.date_to) params.date_to = filters.date_to;
  if (filters.status) params.status = filters.status;
  if (filters.area_manager) params.area_manager = filters.area_manager;
  
  // Navigate with filters
  router.get(route('temperature-tracking.index'), params, {
    preserveState: true,
    preserveScroll: true
  });
}

function exportData() {
  // TODO: Implement export functionality
  alert('Export functionality will be implemented soon!');
}

function confirmApproval(id) {
  if (confirm('Confirm final approval for this form?\n\nThis will mark the form as fully approved and compliant.')) {
    router.patch(route('temperature-tracking.update', id), {
      status: 'Approved'
    }, {
      preserveScroll: true,
      onSuccess: () => {
        // Optional: Add success notification
      }
    });
  }
}

// Export dropdown functionality
function toggleExportDropdown() {
  showExportDropdown.value = !showExportDropdown.value;
}

function closeExportDropdown(event) {
  if (exportDropdown.value && !exportDropdown.value.contains(event.target)) {
    showExportDropdown.value = false;
  }
}

// Print functionality
function printPage() {
  window.print();
}

function printForm(formId) {
  // Open the form in a new window for printing
  const url = route('temperature-tracking.show', formId);
  const printWindow = window.open(url, '_blank', 'width=800,height=600');
  printWindow.addEventListener('load', () => {
    setTimeout(() => {
      printWindow.print();
    }, 1000);
  });
}

// Export functions
function exportToExcel() {
  showExportDropdown.value = false;
  
  // Build query string with current filters
  const params = new URLSearchParams();
  if (filters.store_id) params.append('store_id', filters.store_id);
  if (filters.date_from) params.append('date_from', filters.date_from);
  if (filters.date_to) params.append('date_to', filters.date_to);
  if (filters.status) params.append('status', filters.status);
  
  // Navigate to export route with filters
  const queryString = params.toString();
  window.location.href = route('temperature-tracking.export') + (queryString ? '?' + queryString : '');
}

function exportToPDF() {
  showExportDropdown.value = false;
  
  // Generate PDF export URL with current filters
  const params = new URLSearchParams({
    ...filters,
    format: 'pdf'
  });
  
  // Open PDF export in new window
  window.open(`${route('temperature-tracking.index')}?${params.toString()}&export=pdf`, '_blank');
}

function exportToCSV() {
  showExportDropdown.value = false;
  
  // Create CSV data
  const data = forms.data.map(form => ({
    Date: form.date,
    Store: form.store?.name || 'N/A',
    Shift: form.shift || 'N/A',
    'MIC Morning': form.mic_morning || 'N/A',
    'MIC Evening': form.mic_evening || 'N/A',
    Status: form.status,
    'Created By': form.creator?.name || 'Unknown',
    'Created At': new Date(form.created_at).toLocaleDateString()
  }));
  
  const headers = Object.keys(data[0] || {});
  const csvContent = [
    headers.join(','),
    ...data.map(row => headers.map(header => `"${row[header] || ''}"`).join(','))
  ].join('\n');
  
  downloadFile(csvContent, 'temperature-tracking-forms.csv', 'text/csv');
}

function downloadFile(content, filename, mimeType) {
  const blob = new Blob([content], { type: mimeType });
  const url = window.URL.createObjectURL(blob);
  const link = document.createElement('a');
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  window.URL.revokeObjectURL(url);
}

// Event listeners for dropdown
onMounted(() => {
  document.addEventListener('click', closeExportDropdown);
});

onUnmounted(() => {
  document.removeEventListener('click', closeExportDropdown);
});
</script>

<style scoped>
@media print {
  .print\\:hidden {
    display: none !important;
  }
  
  .bg-white {
    background: white !important;
  }
  
  .shadow-sm, .shadow, .shadow-lg {
    box-shadow: none !important;
  }
  
  .border {
    border: 1px solid #000 !important;
  }
  
  .text-gray-500, .text-gray-600, .text-gray-700 {
    color: #000 !important;
  }
  
  table {
    border-collapse: collapse !important;
  }
  
  th, td {
    border: 1px solid #000 !important;
    padding: 8px !important;
  }
  
  .bg-gradient-to-r {
    background: #f3f4f6 !important;
  }
}

/* Dropdown positioning */
.relative {
  position: relative;
}

.absolute {
  position: absolute;
}
</style>
<style scoped>
table { border-collapse: separate; border-spacing: 0; }
</style>
