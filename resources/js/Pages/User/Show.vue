<template>
  <DefaultAuthenticatedLayout>
    <Head :title="`View User - ${user.name}`" />
    
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
      <!-- Breadcrumb -->
      <nav class="text-xs text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-1 items-center">
          <li><Link :href="route('dashboard')" class="hover:text-gray-700">Dashboard</Link></li>
          <li>/</li>
          <li><Link :href="route('users.index')" class="hover:text-gray-700">Users</Link></li>
          <li>/</li>
          <li class="text-gray-700">{{ user.name }}</li>
        </ol>
      </nav>

      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 print:hidden">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
          <p class="mt-1 text-sm text-gray-600">View user information and assigned restaurants</p>
        </div>
        <div class="mt-4 sm:mt-0 flex gap-2">
          <Link :href="route('users.edit', user.id)" 
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit User
          </Link>
          <button @click="printPage" 
                  class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
            </svg>
            Print
          </button>
        </div>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Information Card -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">User Information</h2>
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Full Name</label>
              <p class="mt-1 text-gray-900 font-medium">{{ user.name }}</p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Email</label>
              <p class="mt-1 text-gray-900">{{ user.email }}</p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Phone</label>
              <p class="mt-1 text-gray-900">{{ user.phone || 'N/A' }}</p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Employee ID</label>
              <p class="mt-1 text-gray-900">{{ user.employee_id || 'N/A' }}</p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Department</label>
              <p class="mt-1 text-gray-900">{{ user.department || 'N/A' }}</p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Role</label>
              <p class="mt-1">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                  {{ user.role_display }}
                </span>
              </p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Status</label>
              <p class="mt-1">
                <span v-if="user.is_active" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                  Active
                </span>
                <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                  <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                  Inactive
                </span>
              </p>
            </div>
            
            <div>
              <label class="text-xs font-medium text-gray-500 uppercase">Email Verified</label>
              <p class="mt-1">
                <span v-if="user.email_verified_at" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                  </svg>
                  Verified
                </span>
                <span v-else class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                  <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                  </svg>
                  Pending
                </span>
              </p>
            </div>
          </div>
          
          <div class="mt-6 pt-6 border-t">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Created At</label>
                <p class="mt-1 text-gray-900">{{ formatDateTime(user.created_at) }}</p>
              </div>
              
              <div>
                <label class="text-xs font-medium text-gray-500 uppercase">Last Updated</label>
                <p class="mt-1 text-gray-900">{{ formatDateTime(user.updated_at) }}</p>
              </div>
              
              <div v-if="user.last_login_at" class="md:col-span-2">
                <label class="text-xs font-medium text-gray-500 uppercase">Last Login</label>
                <p class="mt-1 text-gray-900">{{ formatDateTime(user.last_login_at) }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Assigned Restaurants Card -->
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Assigned Restaurants</h2>
          
          <div v-if="user.restaurants && user.restaurants.length > 0" class="space-y-3">
            <div v-for="restaurant in user.restaurants" :key="restaurant.id" 
                 class="p-3 bg-gray-50 rounded-lg border border-gray-200">
              <h3 class="font-medium text-gray-900">{{ restaurant.name }}</h3>
              <div class="mt-1 text-xs text-gray-600 space-y-1">
                <p><span class="font-medium">Code:</span> {{ restaurant.code }}</p>
                <p><span class="font-medium">Brand:</span> {{ restaurant.brand }}</p>
              </div>
            </div>
          </div>
          
          <div v-else class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <p class="mt-2 text-sm text-gray-500">No restaurants assigned</p>
          </div>
        </div>
      </div>
    </div>
  </DefaultAuthenticatedLayout>
</template>

<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
  user: Object,
});

function formatDateTime(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    weekday: 'short',
    year: 'numeric', 
    month: 'short', 
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
}

function printPage() {
  window.print();
}
</script>

<style scoped>
@media print {
  .print\:hidden {
    display: none !important;
  }
}
</style>
