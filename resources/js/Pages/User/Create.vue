<template>
  <DefaultAuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Page Header -->
        <div class="bg-gradient-to-r from-blue-600 to-purple-700 rounded-xl shadow-lg mb-8 p-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4">
              <div class="bg-white/20 p-3 rounded-full">
                <i class="fas fa-user-plus text-2xl text-white"></i>
              </div>
              <div>
                <h1 class="text-3xl font-bold text-white">Add New User</h1>
                <p class="text-blue-100 mt-1">Create a new system user account with appropriate permissions</p>
              </div>
            </div>
            <div class="mt-4 md:mt-0">
              <Link :href="route('users.index')" 
                    class="inline-flex items-center px-6 py-3 bg-white/20 border border-white/30 rounded-lg text-white font-medium hover:bg-white/30 transition-all duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Users
              </Link>
            </div>
          </div>
        </div>

        <!-- Enhanced Form Layout -->
        <form @submit.prevent="submitForm" class="space-y-8">
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Personal Information Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center mb-6">
                <div class="bg-blue-100 p-2 rounded-lg mr-3">
                  <i class="fas fa-user text-blue-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Personal Information</h2>
              </div>
              
              <div class="space-y-6">
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-id-card text-gray-400 mr-2"></i>Full Name <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="text" 
                    id="name" 
                    v-model="form.name" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter full name"
                    required
                  >
                </div>

                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-envelope text-gray-400 mr-2"></i>Email Address <span class="text-red-500">*</span>
                  </label>
                  <input 
                    type="email" 
                    id="email" 
                    v-model="form.email" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="user@company.com"
                    required
                  >
                </div>

                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-phone text-gray-400 mr-2"></i>Phone Number
                  </label>
                  <input 
                    type="tel" 
                    id="phone" 
                    v-model="form.phone" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="+1 (555) 123-4567"
                  >
                </div>

                <div>
                  <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-id-badge text-gray-400 mr-2"></i>Employee ID
                  </label>
                  <input 
                    type="text" 
                    id="employee_id" 
                    v-model="form.employee_id" 
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="EMP001"
                  >
                </div>
              </div>
            </div>

            <!-- Role & Access Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center mb-6">
                <div class="bg-purple-100 p-2 rounded-lg mr-3">
                  <i class="fas fa-shield-alt text-purple-600"></i>
                </div>
                <div>
                  <h2 class="text-xl font-semibold text-gray-900">Role & Access</h2>
                  <p class="text-sm text-gray-600">Define user permissions and system access</p>
                </div>
              </div>
              
              <div class="space-y-6">
                <div>
                  <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-user-tag text-gray-400 mr-2"></i>User Role <span class="text-red-500">*</span>
                    <i class="fas fa-info-circle text-blue-400 ml-1 cursor-help" title="Each role has different permissions and access levels"></i>
                  </label>
                  <select id="role" v-model="form.role" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                    <option value="">Select Role</option>
                    <option v-for="role in props.roles" :key="role.id" :value="role.name">
                      {{ role.display_name || role.name }}
                    </option>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">Determines user permissions and access levels</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-building text-gray-400 mr-2"></i>Assigned Restaurants
                  </label>
                  
                  <!-- Select All / None Options -->
                  <div class="flex space-x-4 mb-4">
                    <button 
                      type="button"
                      @click="selectAllRestaurants"
                      class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors"
                    >
                      <i class="fas fa-check-double mr-1"></i>Select All
                    </button>
                    <button 
                      type="button"
                      @click="clearAllRestaurants"
                      class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                    >
                      <i class="fas fa-times mr-1"></i>Clear All
                    </button>
                    <span class="text-xs text-gray-500 self-center">
                      {{ form.restaurant_ids.length }} of {{ props.restaurants.length }} selected
                    </span>
                  </div>

                  <!-- Restaurant Checkboxes Grid -->
                  <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                      <div 
                        v-for="restaurant in props.restaurants" 
                        :key="restaurant.id"
                        class="flex items-center p-3 bg-white rounded-lg border border-gray-200 hover:border-blue-300 hover:bg-blue-50 transition-all duration-200 cursor-pointer"
                        @click="toggleRestaurant(restaurant.id)"
                      >
                        <input 
                          type="checkbox" 
                          :id="`restaurant_${restaurant.id}`"
                          :value="restaurant.id"
                          v-model="form.restaurant_ids"
                          class="h-4 w-4 text-blue-600 rounded focus:ring-blue-500 mr-3"
                          @click.stop
                        >
                        <label 
                          :for="`restaurant_${restaurant.id}`" 
                          class="text-sm font-medium text-gray-700 cursor-pointer flex-1"
                        >
                          {{ restaurant.name }}
                        </label>
                      </div>
                    </div>
                    
                    <!-- Empty State -->
                    <div v-if="props.restaurants.length === 0" class="text-center py-8 text-gray-500">
                      <i class="fas fa-building text-2xl mb-2"></i>
                      <p>No restaurants available</p>
                    </div>
                  </div>
                  
                  <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Select one or multiple restaurants. Leave empty for all restaurants access (Admin only).
                  </p>
                </div>

                <div>
                  <label for="department" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-sitemap text-gray-400 mr-2"></i>Department
                  </label>
                  <select id="department" v-model="form.department" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">Select Department</option>
                    <option value="management">Management</option>
                    <option value="operations">Operations</option>
                    <option value="quality">Quality Assurance</option>
                    <option value="kitchen">Kitchen</option>
                    <option value="service">Customer Service</option>
                    <option value="training">Training</option>
                    <option value="finance">Finance</option>
                    <option value="hr">Human Resources</option>
                  </select>
                </div>

                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-toggle-on text-gray-400 mr-2"></i>Account Status
                  </label>
                  <select id="status" v-model="form.status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="active">✅ Active</option>
                    <option value="pending">⏳ Pending Approval</option>
                    <option value="inactive">❌ Inactive</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Security & Permissions Row -->
          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Account Security Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center mb-6">
                <div class="bg-green-100 p-2 rounded-lg mr-3">
                  <i class="fas fa-lock text-green-600"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-900">Account Security</h2>
              </div>
              
              <div class="space-y-6">
                <div>
                  <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-key text-gray-400 mr-2"></i>Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input 
                      :type="showPassword ? 'text' : 'password'" 
                      id="password" 
                      v-model="form.password" 
                      class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      placeholder="Enter secure password"
                      required
                    >
                    <button 
                      type="button"
                      @click="showPassword = !showPassword"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                      <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                  <p class="text-xs text-gray-500 mt-1">
                    <i class="fas fa-info-circle mr-1"></i>Password must be at least 8 characters long
                  </p>
                </div>

                <div>
                  <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    <i class="fas fa-check-double text-gray-400 mr-2"></i>Confirm Password <span class="text-red-500">*</span>
                  </label>
                  <div class="relative">
                    <input 
                      :type="showConfirmPassword ? 'text' : 'password'" 
                      id="password_confirmation" 
                      v-model="form.password_confirmation" 
                      class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                      placeholder="Confirm your password"
                      required
                    >
                    <button 
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                    >
                      <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Permissions Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center mb-6">
                <div class="bg-orange-100 p-2 rounded-lg mr-3">
                  <i class="fas fa-cogs text-orange-600"></i>
                </div>
                <div>
                  <h2 class="text-xl font-semibold text-gray-900">System Permissions</h2>
                  <p class="text-sm text-gray-600">Define specific access rights</p>
                </div>
              </div>
              <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <i class="fas fa-plus-circle text-blue-500 mr-3"></i>
                    <div>
                      <label for="perm_create_visits" class="text-sm font-medium text-gray-700">Create Store Visits</label>
                      <p class="text-xs text-gray-500">Allow creating new quality inspection visits</p>
                    </div>
                  </div>
                  <input 
                    type="checkbox" 
                    id="perm_create_visits" 
                    v-model="form.permissions.create_visits"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                  >
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <i class="fas fa-edit text-yellow-500 mr-3"></i>
                    <div>
                      <label for="perm_edit_visits" class="text-sm font-medium text-gray-700">Edit Store Visits</label>
                      <p class="text-xs text-gray-500">Allow editing existing inspection visits</p>
                    </div>
                  </div>
                  <input 
                    type="checkbox" 
                    id="perm_edit_visits" 
                    v-model="form.permissions.edit_visits"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                  >
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <i class="fas fa-chart-bar text-green-500 mr-3"></i>
                    <div>
                      <label for="perm_view_reports" class="text-sm font-medium text-gray-700">View Reports</label>
                      <p class="text-xs text-gray-500">Access system reports and analytics</p>
                    </div>
                  </div>
                  <input 
                    type="checkbox" 
                    id="perm_view_reports" 
                    v-model="form.permissions.view_reports"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                  >
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <i class="fas fa-users text-purple-500 mr-3"></i>
                    <div>
                      <label for="perm_manage_users" class="text-sm font-medium text-gray-700">Manage Users</label>
                      <p class="text-xs text-gray-500">Create, edit, and manage system users</p>
                    </div>
                  </div>
                  <input 
                    type="checkbox" 
                    id="perm_manage_users" 
                    v-model="form.permissions.manage_users"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                  >
                </div>

                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex items-center">
                    <i class="fas fa-cog text-red-500 mr-3"></i>
                    <div>
                      <label for="perm_system_settings" class="text-sm font-medium text-gray-700">System Settings</label>
                      <p class="text-xs text-gray-500">Access and modify system configurations</p>
                    </div>
                  </div>
                  <input 
                    type="checkbox" 
                    id="perm_system_settings" 
                    v-model="form.permissions.system_settings"
                    class="h-5 w-5 text-blue-600 rounded focus:ring-blue-500"
                  >
                </div>
              </div>
            </div>
          </div>

          <!-- Additional Information Card -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
              <div class="bg-gray-100 p-2 rounded-lg mr-3">
                <i class="fas fa-sticky-note text-gray-600"></i>
              </div>
              <h2 class="text-xl font-semibold text-gray-900">Additional Information</h2>
            </div>
            
            <div>
              <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                <i class="fas fa-comment-alt text-gray-400 mr-2"></i>Notes
              </label>
              <textarea 
                id="notes" 
                v-model="form.notes" 
                rows="4"
                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                placeholder="Any additional notes or comments about this user..."
              ></textarea>
            </div>
          </div>

          <!-- Enhanced Action Buttons -->
          <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
            <Link :href="route('users.index')" 
                  class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
              <i class="fas fa-times mr-2"></i>
              Cancel
            </Link>
            <button 
              type="submit" 
              :disabled="processing"
              @click="showConfirmation = true"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-lg text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-user-plus mr-2"></i>
              <span v-if="processing">
                <i class="fas fa-spinner animate-spin mr-2"></i>
                Creating User...
              </span>
              <span v-else>Create User</span>
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

const props = defineProps({
  roles: {
    type: Array,
    default: () => []
  },
  restaurants: {
    type: Array,
    default: () => []
  }
});

console.log('Props received:', props);
console.log('Roles:', props.roles);
console.log('Restaurants:', props.restaurants);

const processing = ref(false);
const showPassword = ref(false);
const showConfirmPassword = ref(false);
const showConfirmation = ref(false);

const form = useForm({
  name: '',
  email: '',
  phone: '',
  employee_id: '',
  role: '',
  restaurant_ids: [], // Changed to array for multiple selection
  department: '',
  status: 'active',
  password: '',
  password_confirmation: '',
  permissions: {
    create_visits: true,
    edit_visits: false,
    view_reports: true,
    manage_users: false,
    system_settings: false
  },
  notes: ''
});

const submitForm = () => {
  processing.value = true;
  
  form.post(route('users.store'), {
    onFinish: () => {
      processing.value = false;
    },
    onSuccess: () => {
      // Form will redirect on success
    }
  });
};

// Restaurant selection methods
const selectAllRestaurants = () => {
  form.restaurant_ids = props.restaurants.map(restaurant => restaurant.id);
};

const clearAllRestaurants = () => {
  form.restaurant_ids = [];
};

const toggleRestaurant = (restaurantId) => {
  const index = form.restaurant_ids.indexOf(restaurantId);
  if (index > -1) {
    form.restaurant_ids.splice(index, 1);
  } else {
    form.restaurant_ids.push(restaurantId);
  }
};
</script>

<style scoped>
.page-header {
  background: linear-gradient(135deg, #6f42c1 0%, #e83e8c 100%);
  padding: 2rem;
  border-radius: 10px;
  margin-bottom: 2rem;
  color: white;
}

.page-title {
  color: white;
  margin-bottom: 0.5rem;
}

.card {
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 0.5rem;
}

.form-control {
  border-radius: 8px;
  border: 2px solid #e9ecef;
  padding: 0.75rem;
  transition: all 0.3s ease;
}

.form-control:focus {
  border-color: #6f42c1;
  box-shadow: 0 0 0 0.2rem rgba(111, 66, 193, 0.25);
}

.permissions-grid {
  display: grid;
  gap: 1rem;
}

.form-check {
  padding: 0.5rem;
  border-radius: 8px;
  background-color: #f8f9fa;
  border: 1px solid #e9ecef;
}

.form-check-input {
  margin-right: 0.5rem;
}

.form-check-label {
  font-weight: 500;
  color: #495057;
  cursor: pointer;
}

.form-actions {
  border-top: 1px solid #e9ecef;
  padding-top: 2rem;
}

.btn {
  border-radius: 8px;
  padding: 0.75rem 1.5rem;
  font-weight: 600;
}

h5 {
  color: #2c3e50;
  border-bottom: 2px solid #e9ecef;
  padding-bottom: 0.5rem;
}

/* Enhanced modern styling */
@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

/* Card hover effects */
.bg-white:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
}

/* Enhanced focus states */
input:focus, select:focus, textarea:focus {
  transform: translateY(-1px);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Button enhancements */
button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Restaurant checkbox styling */
.restaurant-checkbox-item {
  transition: all 0.2s ease;
}

.restaurant-checkbox-item:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.restaurant-checkbox-item input:checked + label {
  color: #1d4ed8;
  font-weight: 600;
}

/* Scrollbar styling */
.max-h-60::-webkit-scrollbar {
  width: 6px;
}

.max-h-60::-webkit-scrollbar-track {
  background: #f1f5f9;
  border-radius: 3px;
}

.max-h-60::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}

.max-h-60::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}

/* Responsive improvements */
@media (max-width: 768px) {
  .max-w-7xl { padding: 0 1rem; }
  .grid { gap: 1rem; }
  .p-8 { padding: 1.5rem; }
  select[multiple] { min-height: 100px !important; }
}
</style>
