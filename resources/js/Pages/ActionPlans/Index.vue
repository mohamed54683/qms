<template>
    <Head title="Action Plans" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="bg-gradient-to-r from-slate-50 to-blue-50 border-b border-gray-200 -mx-6 -mt-6 px-6 pt-6 pb-6 mb-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="p-2.5 bg-blue-100 rounded-xl shadow-sm">
                                <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Action Plans Management</h1>
                                <div class="h-0.5 w-20 bg-blue-500 rounded-full mt-1"></div>
                            </div>
                        </div>
                        <p class="text-gray-600 text-base">Monitor and manage corrective actions from store quality visits</p>
                    </div>
                    
                    <!-- Quick Search Bar -->
                    <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">
                        <div class="relative flex-1 md:w-80">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input
                                v-model="quickSearch"
                                @input="handleQuickSearch"
                                type="text"
                                placeholder="🔍 Search by Restaurant or Issue..."
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-xl leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            />
                        </div>
                        <button 
                            @click="exportExcel"
                            class="flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"></path>
                            </svg>
                            📤 Export Data
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-4 bg-gray-50">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Enhanced Statistics Cards with Click Filtering -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Total Plans Card -->
                    <div 
                        @click="filterByStatus('all')"
                        class="group cursor-pointer bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-300 p-5 rounded-xl shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-300"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-xl transition-colors duration-300">
                                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ statistics.total }}</p>
                                    <p class="text-sm font-medium text-gray-500">Total Plans</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-2 w-16 bg-gray-200 rounded-full">
                                    <div class="h-full bg-blue-500 rounded-full w-full"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block">100%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending Card -->
                    <div 
                        @click="filterByStatus('Pending')"
                        class="group cursor-pointer bg-white hover:bg-orange-50 border border-gray-200 hover:border-orange-300 p-5 rounded-xl shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-300"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-orange-100 group-hover:bg-orange-200 rounded-xl transition-colors duration-300">
                                    <svg class="w-7 h-7 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-orange-700 transition-colors">{{ statistics.pending }}</p>
                                    <p class="text-sm font-medium text-gray-500">🟠 Pending</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-2 w-16 bg-gray-200 rounded-full">
                                    <div class="h-full bg-orange-500 rounded-full transition-all duration-700" :style="`width: ${statistics.total ? (statistics.pending / statistics.total * 100) : 0}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block">{{ statistics.total ? Math.round(statistics.pending / statistics.total * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- In Progress Card -->
                    <div 
                        @click="filterByStatus('In Progress')"
                        class="group cursor-pointer bg-white hover:bg-blue-50 border border-gray-200 hover:border-blue-300 p-5 rounded-xl shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-300"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-blue-100 group-hover:bg-blue-200 rounded-xl transition-colors duration-300 relative">
                                    <svg class="w-7 h-7 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <div class="absolute -top-1 -right-1 w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-blue-700 transition-colors">{{ statistics.inProgress }}</p>
                                    <p class="text-sm font-medium text-gray-500">🔵 In Progress</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-2 w-16 bg-gray-200 rounded-full">
                                    <div class="h-full bg-blue-500 rounded-full transition-all duration-700" :style="`width: ${statistics.total ? (statistics.inProgress / statistics.total * 100) : 0}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block">{{ statistics.total ? Math.round(statistics.inProgress / statistics.total * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Completed Card -->
                    <div 
                        @click="filterByStatus('Completed')"
                        class="group cursor-pointer bg-white hover:bg-green-50 border border-gray-200 hover:border-green-300 p-5 rounded-xl shadow-sm hover:shadow-md transform hover:scale-105 transition-all duration-300"
                    >
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-3 bg-green-100 group-hover:bg-green-200 rounded-xl transition-colors duration-300 relative">
                                    <svg class="w-7 h-7 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div v-if="statistics.completed > 0" class="absolute -top-1 -right-1 w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-gray-900 group-hover:text-green-700 transition-colors">{{ statistics.completed }}</p>
                                    <p class="text-sm font-medium text-gray-500">🟢 Completed</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="h-2 w-16 bg-gray-200 rounded-full">
                                    <div class="h-full bg-green-500 rounded-full transition-all duration-700" :style="`width: ${statistics.total ? (statistics.completed / statistics.total * 100) : 0}%`"></div>
                                </div>
                                <span class="text-xs text-gray-500 mt-1 block">{{ statistics.total ? Math.round(statistics.completed / statistics.total * 100) : 0 }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Always Visible Quick Filters -->
                <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 mb-4">
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Restaurant Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-48">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <select 
                                v-model="filters.restaurant" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">🏪 All Restaurants</option>
                                <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.name">
                                    {{ restaurant.name }} ({{ restaurant.code }})
                                </option>
                            </select>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-40">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <select 
                                v-model="filters.status" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">All Status</option>
                                <option value="Pending">🟠 Pending</option>
                                <option value="In Progress">🔵 In Progress</option>
                                <option value="Completed">🟢 Completed</option>
                                <option value="Cancelled">⚫ Cancelled</option>
                            </select>
                        </div>

                        <!-- Priority Filter -->
                        <div class="flex items-center gap-2 min-w-0 flex-1 md:flex-initial md:w-36">
                            <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                            </svg>
                            <select 
                                v-model="filters.priority" 
                                @change="applyFilters"
                                class="flex-1 rounded-lg border-gray-300 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-200 transition-all"
                            >
                                <option value="">All Priority</option>
                                <option value="High">🔴 High</option>
                                <option value="Medium">🟡 Medium</option>
                                <option value="Low">🟢 Low</option>
                            </select>
                        </div>

                        <!-- Clear Filters Button -->
                        <button 
                            @click="clearFilters"
                            v-if="hasActiveFilters"
                            class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition-all duration-200"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear
                        </button>

                        <!-- Advanced Filters Toggle -->
                        <button 
                            @click="showAdvancedFilters = !showAdvancedFilters"
                            class="flex items-center gap-2 px-3 py-2 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200 ml-auto"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                            </svg>
                            {{ showAdvancedFilters ? 'Less Filters' : 'More Filters' }}
                        </button>
                    </div>
                </div>

                <!-- Enhanced Filters Panel -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform scale-95 opacity-0"
                    enter-to-class="transform scale-100 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform scale-100 opacity-100"
                    leave-to-class="transform scale-95 opacity-0"
                >
                    <div v-if="showFilters" class="bg-white border border-gray-200 rounded-2xl shadow-lg mb-8 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-slate-50 px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                                </svg>
                                Advanced Filters
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <!-- Multi-Select Restaurants Filter -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Restaurants
                                    </label>
                                    <select 
                                        v-model="filters.restaurants" 
                                        multiple 
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all"
                                        style="min-height: 100px;"
                                    >
                                        <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.name">
                                            {{ restaurant.code }} - {{ restaurant.name }}
                                        </option>
                                    </select>
                                    <p class="text-xs text-gray-500">Hold Ctrl (Cmd on Mac) to select multiple</p>
                                </div>

                                <!-- Area Manager Filter -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        👤 Area Manager
                                    </label>
                                    <select v-model="filters.area_manager" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="">👤 All Area Managers</option>
                                        <option v-for="manager in areaManagers" :key="manager.id" :value="manager.id">
                                            {{ manager.name }}
                                        </option>
                                    </select>
                                    
                                    <!-- DEBUG INFO - temporary -->
                                    <div class="p-2 bg-yellow-100 border border-yellow-400 rounded text-xs">
                                        <strong>DEBUG:</strong><br>
                                        Area Managers Count: {{ areaManagers?.length || 0 }}<br>
                                        Can View Filter: {{ props.canViewAreaManagerFilter }}<br>
                                        Is Admin: {{ props.isAdmin }}<br>
                                        Auth User: {{ props.auth?.user?.name }}<br>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Status
                                    </label>
                                    <select v-model="filters.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="">📋 All Statuses</option>
                                        <option value="Pending">⏳ Pending</option>
                                        <option value="In Progress">🔄 In Progress</option>
                                        <option value="Completed">✅ Completed</option>
                                        <option value="Cancelled">❌ Cancelled</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                        </svg>
                                        Priority
                                    </label>
                                    <select v-model="filters.priority" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                        <option value="">🎯 All Priorities</option>
                                        <option value="High">🔴 High Priority</option>
                                        <option value="Medium">🟡 Medium Priority</option>
                                        <option value="Low">🟢 Low Priority</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date From
                                    </label>
                                    <input v-model="filters.date_from" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        Date To
                                    </label>
                                    <input v-model="filters.date_to" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                        Search
                                    </label>
                                    <input v-model="filters.search" type="text" placeholder="🔍 Search actions..." class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all">
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <button 
                                    @click="applyFilters" 
                                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z"></path>
                                    </svg>
                                    Apply Filters
                                </button>
                                <button 
                                    @click="clearFilters" 
                                    class="flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2.5 px-5 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Clear All
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Enhanced Bulk Actions -->
                <Transition
                    enter-active-class="transition duration-300 ease-out"
                    enter-from-class="transform translate-y-2 opacity-0"
                    enter-to-class="transform translate-y-0 opacity-100"
                    leave-active-class="transition duration-200 ease-in"
                    leave-from-class="transform translate-y-0 opacity-100"
                    leave-to-class="transform translate-y-2 opacity-0"
                >
                    <div v-if="selectedActionPlans.length > 0" class="bg-gradient-to-r from-orange-50 to-amber-50 border-2 border-orange-200 rounded-2xl p-6 mb-8 shadow-lg">
                        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-orange-100 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-orange-800">Bulk Actions</h4>
                                    <p class="text-sm text-orange-600">{{ selectedActionPlans.length }} action plan(s) selected</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <select v-model="bulkAction.status" class="rounded-lg border-orange-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 bg-white">
                                    <option value="">📋 Change Status</option>
                                    <option value="Pending">⏳ Pending</option>
                                    <option value="In Progress">🔄 In Progress</option>
                                    <option value="Completed">✅ Completed</option>
                                    <option value="Cancelled">❌ Cancelled</option>
                                </select>
                                <select v-model="bulkAction.assigned_to" class="rounded-lg border-orange-300 shadow-sm focus:border-orange-500 focus:ring-2 focus:ring-orange-200 bg-white">
                                    <option value="">👤 Assign To</option>
                                    <option v-for="user in users" :key="user.id" :value="user.id">
                                        {{ user.name }}
                                    </option>
                                </select>
                                <button 
                                    @click="applyBulkAction" 
                                    class="flex items-center gap-2 bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Apply Changes
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>

                <!-- Enhanced Action Plans Table -->
                <div class="bg-white border border-gray-200 rounded-2xl shadow-xl overflow-hidden backdrop-blur-sm">
                    <div class="bg-gradient-to-r from-gray-900 via-blue-900 to-indigo-900 px-6 py-5 relative overflow-hidden">
                        <!-- Background Pattern -->
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600/20 to-purple-600/20"></div>
                        <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-white/10 to-transparent rounded-full -translate-y-16 translate-x-16"></div>
                        
                        <div class="relative z-10 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Action Plans Management</h3>
                                    <p class="text-blue-100 text-sm">{{ actionPlans.total || 0 }} total plans • {{ statistics.completed }} completed</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-white text-sm font-medium">
                                    Page {{ actionPlans.current_page || 1 }} of {{ actionPlans.last_page || 1 }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm table-fixed">
                            <thead class="bg-gradient-to-r from-slate-50 via-gray-50 to-blue-50 border-b border-gray-200">
                                <tr>
                                    <th class="w-12 px-3 py-3 text-center">
                                        <input 
                                            type="checkbox" 
                                            @change="toggleSelectAll" 
                                            :checked="allSelected"
                                            class="w-4 h-4 text-blue-600 bg-white border border-gray-300 rounded focus:ring-blue-500 focus:ring-1"
                                        >
                                    </th>
                                    <th class="w-56 px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700 uppercase">Restaurant & Item</span>
                                        </div>
                                    </th>
                                    <th class="px-4 py-3 text-left">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700 uppercase">Action Required</span>
                                        </div>
                                    </th>
                                    <th class="w-20 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Priority</span>
                                        </div>
                                    </th>
                                    <th class="w-28 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Status</span>
                                        </div>
                                    </th>
                                    <th class="w-32 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Assigned</span>
                                        </div>
                                    </th>
                                    <th class="w-28 px-3 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3"></path>
                                            </svg>
                                            <span class="text-xs font-semibold text-gray-700">Due Date</span>
                                        </div>
                                    </th>
                                    <th v-if="can('action_plans', 'edit')" class="w-20 px-3 py-3 text-center">
                                        <span class="text-xs font-semibold text-gray-700">Actions</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template v-for="actionPlan in actionPlans.data" :key="actionPlan.id">
                                    <tr class="group hover:bg-blue-50/30 transition-all duration-200 hover:shadow-sm">
                                        <td class="px-3 py-4 text-center">
                                            <input 
                                                type="checkbox" 
                                                :value="actionPlan.id" 
                                                v-model="selectedActionPlans"
                                                class="w-4 h-4 text-blue-600 bg-white border border-gray-300 rounded focus:ring-blue-500 focus:ring-1"
                                            >
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="space-y-2">
                                                <div class="font-semibold text-gray-900 text-sm">
                                                    {{ actionPlan.store_visit?.restaurant_name || 'N/A' }}
                                                </div>
                                                <div class="flex items-center gap-1 text-xs text-blue-600">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    {{ actionPlan.item }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ formatDate(actionPlan.created_at) }}
                                                </div>
                                                <!-- Show Comments Button -->
                                                <button 
                                                    v-if="actionPlan.store_visit?.general_comments || hasVisitComments(actionPlan)"
                                                    @click="toggleComments(actionPlan.id)"
                                                    class="flex items-center gap-1 text-xs text-amber-600 hover:text-amber-700 font-medium mt-1"
                                                >
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                    </svg>
                                                    {{ expandedComments.includes(actionPlan.id) ? 'Hide' : 'View' }} Comments
                                                </button>
                                            </div>
                                        </td>
                                    <td class="px-4 py-4">
                                        <div v-if="editingActionPlan === actionPlan.id" class="space-y-2">
                                            <textarea 
                                                v-model="editForm.action_required"
                                                class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200"
                                                rows="2"
                                                placeholder="Required action..."
                                            ></textarea>
                                        </div>
                                        <div v-else class="text-xs text-gray-700">
                                            <div class="line-clamp-2" :title="actionPlan.action_required">
                                                {{ actionPlan.action_required }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div v-if="editingActionPlan === actionPlan.id">
                                            <select v-model="editForm.priority" class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                                <option value="High">High</option>
                                                <option value="Medium">Medium</option>
                                                <option value="Low">Low</option>
                                            </select>
                                        </div>
                                        <div v-else>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full" 
                                                  :class="{
                                                      'bg-red-100 text-red-700': actionPlan.priority === 'High',
                                                      'bg-yellow-100 text-yellow-700': actionPlan.priority === 'Medium',
                                                      'bg-green-100 text-green-700': actionPlan.priority === 'Low'
                                                  }">
                                                {{ actionPlan.priority }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div v-if="editingActionPlan === actionPlan.id">
                                            <select v-model="editForm.status" class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                                <option value="Pending">Pending</option>
                                                <option value="In Progress">In Progress</option>
                                                <option value="Completed">Completed</option>
                                                <option value="Cancelled">Cancelled</option>
                                            </select>
                                        </div>
                                        <div v-else>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full" 
                                                  :class="{
                                                      'bg-orange-100 text-orange-700': actionPlan.status === 'Pending',
                                                      'bg-blue-100 text-blue-700': actionPlan.status === 'In Progress',
                                                      'bg-green-100 text-green-700': actionPlan.status === 'Completed',
                                                      'bg-gray-100 text-gray-700': actionPlan.status === 'Cancelled'
                                                  }">
                                                {{ actionPlan.status }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div v-if="editingActionPlan === actionPlan.id">
                                            <select v-model="editForm.assigned_to" class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200">
                                                <option value="">Unassigned</option>
                                                <option v-for="user in users" :key="user.id" :value="user.id">
                                                    {{ user.name }}
                                                </option>
                                            </select>
                                        </div>
                                        <div v-else>
                                            <div v-if="actionPlan.assigned_user" class="flex items-center justify-center">
                                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold text-xs">
                                                    {{ actionPlan.assigned_user.name.charAt(0).toUpperCase() }}
                                                </div>
                                            </div>
                                            <div v-else class="text-xs text-gray-400">
                                                Unassigned
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <div v-if="editingActionPlan === actionPlan.id">
                                            <input 
                                                v-model="editForm.due_date" 
                                                type="date" 
                                                class="w-full text-xs rounded-md border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-200"
                                            >
                                        </div>
                                        <div v-else>
                                            <div v-if="actionPlan.due_date" class="text-xs text-gray-700">
                                                {{ formatDate(actionPlan.due_date) }}
                                            </div>
                                            <div v-else class="text-xs text-gray-400">
                                                No due date
                                            </div>
                                        </div>
                                    </td>
                                    <td v-if="can('action_plans', 'edit')" class="px-3 py-4 text-center">
                                        <button 
                                            @click="startEditing(actionPlan)"
                                            class="inline-flex items-center gap-1 bg-blue-500 hover:bg-blue-600 text-white font-medium py-1.5 px-3 rounded-md text-xs transition-colors duration-200"
                                        >
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                    </td>
                                </tr>
                                
                                <!-- Expandable Comments Row -->
                                <tr v-if="expandedComments.includes(actionPlan.id)" class="bg-amber-50">
                                    <td colspan="7" class="px-6 py-4">
                                        <div class="space-y-3">
                                            <div class="flex items-center gap-2 mb-3">
                                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                                </svg>
                                                <h4 class="font-semibold text-gray-900">Store Visit Comments</h4>
                                            </div>
                                            
                                            <!-- General Comments -->
                                            <div v-if="actionPlan.store_visit?.general_comments" class="bg-white rounded-lg p-4 border border-amber-200">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-amber-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <div class="text-xs font-semibold text-gray-700 mb-1">General Comments:</div>
                                                        <div class="text-sm text-gray-800">{{ actionPlan.store_visit.general_comments }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Observation Summary -->
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                <div v-if="actionPlan.store_visit?.what_did_you_see" class="bg-white rounded-lg p-3 border border-amber-200">
                                                    <div class="text-xs font-semibold text-blue-700 mb-1">What Did You See:</div>
                                                    <div class="text-xs text-gray-700">{{ actionPlan.store_visit.what_did_you_see }}</div>
                                                </div>
                                                
                                                <div v-if="actionPlan.store_visit?.why_had_issue" class="bg-white rounded-lg p-3 border border-amber-200">
                                                    <div class="text-xs font-semibold text-orange-700 mb-1">Why We Had Issue:</div>
                                                    <div class="text-xs text-gray-700">{{ actionPlan.store_visit.why_had_issue }}</div>
                                                </div>
                                                
                                                <div v-if="actionPlan.store_visit?.how_to_improve" class="bg-white rounded-lg p-3 border border-amber-200">
                                                    <div class="text-xs font-semibold text-green-700 mb-1">How To Improve:</div>
                                                    <div class="text-xs text-gray-700">{{ actionPlan.store_visit.how_to_improve }}</div>
                                                </div>
                                                
                                                <div v-if="actionPlan.store_visit?.who_when_responsible" class="bg-white rounded-lg p-3 border border-amber-200">
                                                    <div class="text-xs font-semibold text-purple-700 mb-1">Who/When Responsible:</div>
                                                    <div class="text-xs text-gray-700">{{ actionPlan.store_visit.who_when_responsible }}</div>
                                                </div>
                                            </div>
                                            
                                            <!-- Specific Comments Related to Item -->
                                            <div v-if="getItemComments(actionPlan)" class="bg-white rounded-lg p-4 border border-amber-200">
                                                <div class="flex items-start gap-2">
                                                    <svg class="w-4 h-4 text-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    <div class="flex-1">
                                                        <div class="text-xs font-semibold text-gray-700 mb-1">Specific Comments for "{{ actionPlan.item }}":</div>
                                                        <div class="text-sm text-gray-800">{{ getItemComments(actionPlan) }}</div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Attached Images from Action Plan (Per-Question Images) -->
                                            <div v-if="actionPlan.images && actionPlan.images.length > 0" class="bg-red-50 rounded-lg p-4 border border-red-200">
                                                <div class="flex items-center gap-2 mb-3">
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    <div class="text-xs font-semibold text-red-900">📸 Evidence Images ({{ actionPlan.images.length }})</div>
                                                </div>
                                                <div class="grid grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-2">
                                                    <div v-for="(imageObj, index) in actionPlan.images" 
                                                         :key="index"
                                                         class="group relative cursor-pointer"
                                                         @click="openImageModal(imageObj.image_path)">
                                                        <div class="aspect-square rounded overflow-hidden border-2 border-red-200 hover:border-red-400 transition">
                                                            <img :src="`/storage/${imageObj.image_path}`" 
                                                                 :alt="`Evidence ${index + 1}`"
                                                                 class="w-full h-full object-cover group-hover:scale-110 transition duration-300"
                                                                 @error="handleImageError($event, imageObj)"
                                                                 loading="lazy">
                                                        </div>
                                                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-30 transition flex items-center justify-center rounded">
                                                            <svg class="w-5 h-5 text-white opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Enhanced Pagination with Better Navigation -->
                    <div class="bg-gradient-to-r from-slate-100 via-gray-100 to-blue-50 px-6 py-5 border-t-2 border-blue-200">
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
                                        Showing <span class="text-blue-600 font-extrabold text-base">{{ actionPlans.from || 0 }}</span> 
                                        to <span class="text-blue-600 font-extrabold text-base">{{ actionPlans.to || 0 }}</span> 
                                        of <span class="text-green-600 font-extrabold text-base">{{ actionPlans.total || 0 }}</span> results
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Desktop Pagination -->
                            <div class="hidden md:flex items-center gap-1 flex-wrap justify-center">
                                <component 
                                    v-for="(link, index) in actionPlans.links" 
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
                            
                            <!-- Mobile Pagination (Previous/Next only) -->
                            <div class="flex md:hidden items-center gap-3 w-full">
                                <Link
                                    v-if="actionPlans.prev_page_url"
                                    :href="actionPlans.prev_page_url"
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
                                        Page <span class="text-blue-600">{{ actionPlans.current_page }}</span> of <span class="text-green-600">{{ actionPlans.last_page }}</span>
                                    </span>
                                </div>
                                
                                <Link
                                    v-if="actionPlans.next_page_url"
                                    :href="actionPlans.next_page_url"
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
            </div>
        </div>

        <!-- Enhanced Edit Modal -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform scale-95 opacity-0"
            enter-to-class="transform scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="transform scale-100 opacity-100"
            leave-to-class="transform scale-95 opacity-0"
        >
            <div v-if="showEditModal" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="cancelEdit"></div>
                    
                    <div class="inline-block w-full max-w-4xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 -mx-6 -mt-6 px-6 py-4 mb-6 rounded-t-2xl">
                            <h3 class="text-xl font-bold text-white flex items-center gap-2">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Action Plan Details
                            </h3>
                            <p class="text-blue-100 mt-1">{{ editingItem?.item }} - {{ editingItem?.store_visit?.restaurant_name }}</p>
                        </div>

                        <form @submit.prevent="saveEdit" class="space-y-6">
                            <!-- Basic Information -->
                            <div class="bg-slate-50 p-4 rounded-lg">
                                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Basic Information
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Priority</label>
                                        <select v-model="editForm.priority" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                            <option value="High">🔴 High Priority</option>
                                            <option value="Medium">🟡 Medium Priority</option>
                                            <option value="Low">🟢 Low Priority</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                                        <select v-model="editForm.status" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                            <option value="Pending">⏳ Pending</option>
                                            <option value="In Progress">🔄 In Progress</option>
                                            <option value="Completed">✅ Completed</option>
                                            <option value="Cancelled">❌ Cancelled</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Due Date</label>
                                        <input v-model="editForm.due_date" type="date" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Assign To</label>
                                        <select v-model="editForm.assigned_to" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                                            <option value="">👤 Unassigned</option>
                                            <option v-for="user in users" :key="user.id" :value="user.id">
                                                {{ user.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 5W1H Analysis -->
                            <div class="bg-gradient-to-r from-orange-50 to-amber-50 p-4 rounded-lg border border-orange-200">
                                <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    5W1H Analysis
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">WHAT</span>
                                                What needs to be done?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.what" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Describe in detail what needs to be done..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">WHERE</span>
                                                Where will this be implemented?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.where" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Specify the location or area..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-purple-100 text-purple-700 px-2 py-1 rounded text-xs font-bold">WHY</span>
                                                Why is this action needed?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.why" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Explain the reason and importance..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">HOW</span>
                                                How will this be executed?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.how" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Detail the execution steps..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-indigo-100 text-indigo-700 px-2 py-1 rounded text-xs font-bold">WHO</span>
                                                Who will execute this?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.who" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Specify responsible person/team..."></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <span class="bg-yellow-100 text-yellow-700 px-2 py-1 rounded text-xs font-bold">WHEN</span>
                                                When will this happen?
                                            </span>
                                        </label>
                                        <textarea v-model="editForm.when_detail" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Detail the timeline and milestones..."></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Required & Comments -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Action Required</label>
                                    <textarea v-model="editForm.action_required" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Describe the required action..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <span class="flex items-center gap-2">
                                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold">COMMENT</span>
                                            Additional Comments
                                        </span>
                                    </label>
                                    <textarea v-model="editForm.comment" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Add any additional comments or notes..."></textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                                    <textarea v-model="editForm.notes" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" placeholder="Additional notes..."></textarea>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex justify-end gap-3 pt-4 border-t">
                                <button 
                                    type="button" 
                                    @click="cancelEdit"
                                    class="flex items-center gap-2 px-6 py-2 text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 transition-all duration-300"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Cancel
                                </button>
                                <button 
                                    type="submit" 
                                    :disabled="editForm.processing"
                                    class="flex items-center gap-2 px-6 py-2 text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 disabled:opacity-50 transition-all duration-300 transform hover:scale-105"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ editForm.processing ? 'Saving...' : 'Save Changes' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Toast Notification -->
        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="transform translate-x-full opacity-0"
            enter-to-class="transform translate-x-0 opacity-100"
            leave-active-class="transition duration-300 ease-in"
            leave-from-class="transform translate-x-0 opacity-100"
            leave-to-class="transform translate-x-full opacity-0"
        >
            <div v-if="showToast" class="fixed top-4 right-4 z-50">
                <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium">{{ toastMessage }}</span>
                </div>
            </div>
        </Transition>

        <!-- Image Modal -->
        <Teleport to="body">
            <div v-if="selectedImage" 
                 class="fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center p-4"
                 @click="closeImageModal">
                <button @click="closeImageModal" 
                        class="absolute top-4 right-4 text-white hover:text-gray-300 transition">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                <img :src="`/storage/${selectedImage}`" 
                     alt="Full size image" 
                     class="max-w-full max-h-full object-contain"
                     @click.stop>
            </div>
        </Teleport>
    </DefaultAuthenticatedLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, computed, Transition } from 'vue'
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue'
import { usePermissions } from '@/Composables/usePermissions'

// Use permissions composable
const { can, isAdmin, hasRestaurantAccess, accessibleRestaurants } = usePermissions()

const props = defineProps({
    actionPlans: Object,
    statistics: Object,
    restaurants: Array,
    areaManagers: Array,
    assignedRestaurants: Array,
    users: Array,
    filters: Object,
    isAdmin: Boolean,
    auth: Object,
    canViewAreaManagerFilter: Boolean
})

// Reactive state
const showFilters = ref(false)
const showAdvancedFilters = ref(false)
const selectedActionPlans = ref([])
const editingActionPlan = ref(null)
const showEditModal = ref(false)
const editingItem = ref(null)
const quickSearch = ref('')
const showToast = ref(false)
const toastMessage = ref('')

// Image modal state
const selectedImage = ref(null)

// Image modal functions
const openImageModal = (image) => {
    selectedImage.value = image
}

const closeImageModal = () => {
    selectedImage.value = null
}

// Handle image loading errors
const handleImageError = (event, imageObj) => {
    console.warn('Failed to load image:', imageObj.image_path)
    
    // Try alternative path or show placeholder
    const img = event.target
    if (!img.src.includes('?retry=1')) {
        // First try: add retry parameter to bust cache
        img.src = `/storage/${imageObj.image_path}?retry=1`
    } else {
        // Second try failed: show placeholder or hide
        img.style.display = 'none'
        const container = img.closest('.aspect-square')
        if (container) {
            container.innerHTML = `
                <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            `
        }
    }
}

// Filter form
const filters = ref({
    restaurants: props.filters.restaurants || [],
    restaurant: props.filters.restaurant || '',
    area_manager: props.filters.area_manager || '',
    status: props.filters.status || '',
    priority: props.filters.priority || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
    search: props.filters.search || ''
})

// Bulk action form
const bulkAction = ref({
    status: '',
    assigned_to: ''
})

// Edit form
const editForm = useForm({
    action_required: '',
    priority: '',
    status: '',
    due_date: '',
    notes: '',
    assigned_to: '',
    // 5W1H fields
    what: '',
    where: '',
    why: '',
    how: '',
    who: '',
    when_detail: '',
    comment: ''
})

// Computed properties
// Computed properties use backend statistics for total counts
const statistics = computed(() => {
    // Use statistics from backend (all records) instead of current page only
    return {
        total: props.statistics?.total || 0,
        pending: props.statistics?.pending || 0,
        inProgress: props.statistics?.inProgress || 0,
        completed: props.statistics?.completed || 0
    }
})

const allSelected = computed(() => {
    return props.actionPlans.data?.length > 0 && 
           selectedActionPlans.value.length === props.actionPlans.data.length
})

const hasActiveFilters = computed(() => {
    return filters.value.restaurant || filters.value.status || filters.value.priority || 
           filters.value.date_from || filters.value.date_to || quickSearch.value
})

// Methods
const filterByStatus = (status) => {
    if (status === 'all') {
        filters.value.status = ''
    } else {
        filters.value.status = status
    }
    applyFilters()
}

const handleQuickSearch = () => {
    // Debounce search to avoid too many requests
    clearTimeout(window.searchTimeout)
    window.searchTimeout = setTimeout(() => {
        filters.value.search = quickSearch.value
        applyFilters()
    }, 500)
}

const showToastMessage = (message) => {
    toastMessage.value = message
    showToast.value = true
    setTimeout(() => {
        showToast.value = false
    }, 3000)
}
const applyFilters = () => {
    router.get(route('action-plans.index'), filters.value, {
        preserveState: true,
        replace: true
    })
}

const clearFilters = () => {
    filters.value = {
        restaurants: [],
        restaurant: '',
        area_manager: '',
        status: '',
        priority: '',
        date_from: '',
        date_to: '',
        search: ''
    }
    quickSearch.value = ''
    applyFilters()
}

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedActionPlans.value = []
    } else {
        selectedActionPlans.value = props.actionPlans.data.map(item => item.id)
    }
}

const applyBulkAction = () => {
    if (selectedActionPlans.value.length === 0) return
    
    const form = useForm({
        action_plans: selectedActionPlans.value,
        status: bulkAction.value.status,
        assigned_to: bulkAction.value.assigned_to
    })
    
    form.post(route('action-plans.bulk-update'), {
        onSuccess: () => {
            selectedActionPlans.value = []
            bulkAction.value = { status: '', assigned_to: '' }
        }
    })
}

const startEditing = (actionPlan) => {
    editingItem.value = actionPlan
    editForm.action_required = actionPlan.action_required
    editForm.priority = actionPlan.priority
    editForm.status = actionPlan.status
    editForm.due_date = actionPlan.due_date
    editForm.notes = actionPlan.notes || ''
    editForm.assigned_to = actionPlan.assigned_to || ''
    // 5W1H fields
    editForm.what = actionPlan.what || ''
    editForm.where = actionPlan.where || ''
    editForm.why = actionPlan.why || ''
    editForm.how = actionPlan.how || ''
    editForm.who = actionPlan.who || ''
    editForm.when_detail = actionPlan.when_detail || ''
    editForm.comment = actionPlan.comment || ''
    
    showEditModal.value = true
}

const cancelEdit = () => {
    showEditModal.value = false
    editingItem.value = null
    editForm.reset()
}

const saveEdit = () => {
    if (!editingItem.value) return
    
    editForm.put(route('action-plans.update', editingItem.value.id), {
        onSuccess: () => {
            showEditModal.value = false
            editingItem.value = null
            editForm.reset()
            
            // Show success toast
            showToastMessage('✅ Action Plan updated successfully!')
        }
    })
}

const exportExcel = () => {
    window.open(route('action-plans.export-excel', filters.value), '_blank')
}

// Comments functionality
const expandedComments = ref([])

const toggleComments = (actionPlanId) => {
    const index = expandedComments.value.indexOf(actionPlanId)
    if (index > -1) {
        expandedComments.value.splice(index, 1)
    } else {
        expandedComments.value.push(actionPlanId)
    }
}

const hasVisitComments = (actionPlan) => {
    const visit = actionPlan.store_visit
    if (!visit) return false
    
    return visit.what_did_you_see || visit.why_had_issue || 
           visit.how_to_improve || visit.who_when_responsible ||
           getItemComments(actionPlan)
}

const getItemComments = (actionPlan) => {
    const visit = actionPlan.store_visit
    if (!visit) return ''
    
    const item = actionPlan.item?.toLowerCase() || ''
    
    // Direct mapping based on exact question text patterns
    // Check for multiple keywords to match accurately
    if (item.includes('oca') && item.includes('board')) {
        return visit.oca_board_comments || ''
    }
    if (item.includes('staff') && item.includes('duty')) {
        return visit.staff_duty_comments || ''
    }
    if (item.includes('coaching')) {
        return visit.coaching_comments || ''
    }
    if (item.includes('smile') || item.includes('greet')) {
        return visit.smile_comments || ''
    }
    if (item.includes('suggestive') || item.includes('selling')) {
        return visit.suggestive_comments || ''
    }
    if (item.includes('promotion')) {
        return visit.promotion_comments || ''
    }
    if (item.includes('thank')) {
        return visit.thank_comments || ''
    }
    if ((item.includes('teamwork') || item.includes('hustle')) && !item.includes('schedule')) {
        return visit.teamwork_comments || ''
    }
    if (item.includes('accuracy')) {
        return visit.accuracy_comments || ''
    }
    if (item.includes('service') && item.includes('time')) {
        return visit.service_comments || ''
    }
    if (item.includes('dine') && item.includes('in')) {
        return visit.dine_comments || ''
    }
    if (item.includes('take') && item.includes('out')) {
        return visit.takeout_comments || ''
    }
    if (item.includes('family') && !item.includes('area')) {
        return visit.family_comments || ''
    }
    if (item.includes('delivery')) {
        return visit.delivery_comments || ''
    }
    if (item.includes('drive')) {
        return visit.drive_comments || ''
    }
    if (item.includes('schedule') || item.includes('overtime')) {
        return visit.schedule_comments || ''
    }
    if (item.includes('financial')) {
        return visit.financial_comments || ''
    }
    if (item.includes('sales') || item.includes('cashier')) {
        return visit.sales_comments || ''
    }
    if (item.includes('cash') && item.includes('polic')) {
        return visit.cash_comments || ''
    }
    if (item.includes('waste')) {
        return visit.waste_comments || ''
    }
    if (item.includes('ttf')) {
        return visit.ttf_comments || ''
    }
    if (item.includes('sandwich') || item.includes('assembly')) {
        return visit.assembly_comments || ''
    }
    if (item.includes('qsc')) {
        return visit.qsc_comments || ''
    }
    if (item.includes('oil') || item.includes('shortening')) {
        return visit.oil_comments || ''
    }
    if (item.includes('label') || item.includes('day dot')) {
        return visit.labels_comments || ''
    }
    if (item.includes('equipment') && !item.includes('fryer')) {
        return visit.equipment_comments || ''
    }
    if (item.includes('fryer') || item.includes('basket')) {
        return visit.fryer_comments || ''
    }
    if (item.includes('vegetable') || item.includes('salad')) {
        return visit.vegetable_comments || ''
    }
    if ((item.includes('appearance') || item.includes('groom')) && !item.includes('dining')) {
        return visit.appearance_comments || ''
    }
    if (item.includes('wrapped') || item.includes('hang')) {
        return visit.wrapped_comments || ''
    }
    if (item.includes('sink') || item.includes('compartment')) {
        return visit.sink_comments || ''
    }
    if (item.includes('sanitizer')) {
        return visit.sanitizer_comments || ''
    }
    if (item.includes('dining') || (item.includes('family') && item.includes('area'))) {
        return visit.dining_comments || ''
    }
    if (item.includes('restroom') || item.includes('cr') || item.includes('handwash')) {
        return visit.restroom_comments || ''
    }
    
    return ''
}

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString()
}
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    max-height: 2.8em;
}

/* Table scroll styling */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Hover effects */
.group:hover .group-hover\:shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

/* Responsive table improvements */
@media (max-width: 768px) {
    .table-fixed {
        table-layout: auto;
    }
}
</style>