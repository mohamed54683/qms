<template>
  <DefaultAuthenticatedLayout>
    <div class="min-h-screen bg-gray-50 py-6">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Enhanced Page Header -->
        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl shadow-lg mb-8 p-8">
          <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div class="flex items-center space-x-4">
              <div class="bg-white/20 p-3 rounded-full">
                <i class="fas fa-user-edit text-2xl text-white"></i>
              </div>
              <div>
                <h1 class="text-3xl font-bold text-white">Edit User</h1>
                <p class="text-orange-100 mt-1">Update user account information and permissions</p>
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
                  </label>
                  <select id="role" v-model="form.role" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200" required>
                    <option value="">Select Role</option>
                    <option v-for="role in props.roles" :key="role.id" :value="role.name">
                      {{ role.display_name || role.name }}
                    </option>
                  </select>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-building text-gray-400 mr-2"></i>Assigned Restaurants
                  </label>
                  
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
                  </div>
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

                <!-- Area Manager Section (only for Area Manager role) -->
                <div v-if="form.role === 'Area Manager'" class="space-y-4">
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-crown text-gray-400 mr-2"></i>Managed Restaurants (Area Manager)
                  </label>
                  
                  <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                      <i class="fas fa-info-circle text-yellow-600 mr-2"></i>
                      <p class="text-sm text-yellow-800">
                        As an Area Manager, you can manage restaurants directly. This is different from regular restaurant assignments.
                      </p>
                    </div>
                  </div>
                  
                  <div class="flex space-x-4 mb-4">
                    <button 
                      type="button"
                      @click="selectAllManagedRestaurants"
                      class="text-xs px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full hover:bg-yellow-200 transition-colors"
                    >
                      <i class="fas fa-check-double mr-1"></i>Manage All
                    </button>
                    <button 
                      type="button"
                      @click="clearAllManagedRestaurants"
                      class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                    >
                      <i class="fas fa-times mr-1"></i>Clear All
                    </button>
                    <span class="text-xs text-gray-500 self-center">
                      {{ form.managed_restaurant_ids.length }} of {{ props.restaurants.length }} managed
                    </span>
                  </div>

                  <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                      <div 
                        v-for="restaurant in props.restaurants" 
                        :key="`managed_${restaurant.id}`"
                        class="flex items-center p-3 bg-white rounded-lg border-2 transition-all duration-200 cursor-pointer"
                        :class="{
                          'border-yellow-300 bg-yellow-50': form.managed_restaurant_ids.includes(restaurant.id),
                          'border-gray-200 hover:border-yellow-300 hover:bg-yellow-50': !form.managed_restaurant_ids.includes(restaurant.id)
                        }"
                        @click="toggleManagedRestaurant(restaurant.id)"
                      >
                        <input 
                          type="checkbox" 
                          :id="`managed_restaurant_${restaurant.id}`"
                          :value="restaurant.id"
                          v-model="form.managed_restaurant_ids"
                          class="h-4 w-4 text-yellow-600 rounded focus:ring-yellow-500 mr-3"
                          @click.stop
                        >
                        <label 
                          :for="`managed_restaurant_${restaurant.id}`" 
                          class="text-sm font-medium text-gray-700 cursor-pointer flex-1"
                        >
                          {{ restaurant.name }}
                          <span v-if="restaurant.area_manager_id && restaurant.area_manager_id !== props.user.id" 
                                class="text-xs text-red-500 block">
                            (Currently managed by someone else)
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>

                  <!-- View Assigned Users Section -->
                  <div v-if="props.assignedUsers.length > 0" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <h4 class="text-sm font-semibold text-blue-800 mb-3 flex items-center">
                      <i class="fas fa-users text-blue-600 mr-2"></i>
                      Users Assigned to Your Managed Restaurants
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                      <div 
                        v-for="assignedUser in props.assignedUsers" 
                        :key="assignedUser.id"
                        class="flex items-center p-3 bg-white rounded-lg border border-blue-200"
                      >
                        <div class="flex-1">
                          <p class="text-sm font-medium text-gray-900">{{ assignedUser.name }}</p>
                          <p class="text-xs text-gray-500">{{ assignedUser.email }}</p>
                          <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full mt-1">
                            {{ assignedUser.role }}
                          </span>
                        </div>
                        <Link 
                          :href="route('users.edit', assignedUser.id)" 
                          class="ml-3 text-blue-600 hover:text-blue-800 transition-colors"
                          title="Edit User"
                        >
                          <i class="fas fa-edit"></i>
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Area Manager Assignment Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center mb-6">
                <div class="bg-indigo-100 p-2 rounded-lg mr-3">
                  <i class="fas fa-users-cog text-indigo-600"></i>
                </div>
                <div>
                  <h2 class="text-xl font-semibold text-gray-900">Area Manager Assignment</h2>
                  <p class="text-sm text-gray-600">Assign specific area managers this user can access in filtering</p>
                </div>
              </div>
              
              <div class="space-y-6">
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                  <div class="flex items-start">
                    <i class="fas fa-info-circle text-indigo-600 mr-2 mt-1"></i>
                    <div class="text-sm text-indigo-800">
                      <p class="font-medium mb-1">How it works:</p>
                      <ul class="list-disc list-inside space-y-1 text-xs">
                        <li>Assign specific area managers to this user</li>
                        <li>User will only see restaurants managed by assigned area managers in filters</li>
                        <li>Requires "view area manager filter" permission to use</li>
                        <li>Admins always see all area managers regardless of assignments</li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-3">
                    <i class="fas fa-user-tie text-gray-400 mr-2"></i>Assigned Area Managers
                  </label>
                  
                  <div v-if="props.areaManagers && props.areaManagers.length > 0" class="space-y-4">
                    <div class="flex space-x-4 mb-4">
                      <button 
                        type="button"
                        @click="selectAllAreaManagers"
                        class="text-xs px-3 py-1 bg-indigo-100 text-indigo-700 rounded-full hover:bg-indigo-200 transition-colors"
                      >
                        <i class="fas fa-check-double mr-1"></i>Select All
                      </button>
                      <button 
                        type="button"
                        @click="clearAllAreaManagers"
                        class="text-xs px-3 py-1 bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors"
                      >
                        <i class="fas fa-times mr-1"></i>Clear All
                      </button>
                      <span class="text-xs text-gray-500 self-center">
                        {{ form.area_manager_ids.length }} of {{ props.areaManagers.length }} selected
                      </span>
                    </div>

                    <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-4 bg-gray-50">
                      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div 
                          v-for="manager in props.areaManagers" 
                          :key="manager.id"
                          class="flex items-center p-3 bg-white rounded-lg border-2 transition-all duration-200 cursor-pointer"
                          :class="{
                            'border-indigo-300 bg-indigo-50': form.area_manager_ids.includes(manager.id),
                            'border-gray-200 hover:border-indigo-300 hover:bg-indigo-50': !form.area_manager_ids.includes(manager.id)
                          }"
                          @click="toggleAreaManager(manager.id)"
                        >
                          <input 
                            type="checkbox" 
                            :id="`area_manager_${manager.id}`"
                            :value="manager.id"
                            v-model="form.area_manager_ids"
                            class="h-4 w-4 text-indigo-600 rounded focus:ring-indigo-500 mr-3"
                            @click.stop
                          >
                          <label 
                            :for="`area_manager_${manager.id}`" 
                            class="text-sm font-medium text-gray-700 cursor-pointer flex-1"
                          >
                            {{ manager.name }}
                            <span class="text-xs text-gray-500 block">
                              {{ manager.restaurants_count || 0 }} restaurants
                            </span>
                          </label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div v-else class="text-center py-8 text-gray-500">
                    <i class="fas fa-user-tie text-4xl text-gray-300 mb-3"></i>
                    <p class="text-sm">No area managers available</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Password Update Section (Optional) -->
          <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center mb-6">
              <div class="bg-green-100 p-2 rounded-lg mr-3">
                <i class="fas fa-lock text-green-600"></i>
              </div>
              <div>
                <h2 class="text-xl font-semibold text-gray-900">Update Password</h2>
                <p class="text-sm text-gray-600">Leave blank to keep current password</p>
              </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-key text-gray-400 mr-2"></i>New Password
                </label>
                <div class="relative">
                  <input 
                    :type="showPassword ? 'text' : 'password'" 
                    id="password" 
                    v-model="form.password" 
                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Enter new password (optional)"
                  >
                  <button 
                    type="button"
                    @click="showPassword = !showPassword"
                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                  >
                    <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                  </button>
                </div>
              </div>

              <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                  <i class="fas fa-check-double text-gray-400 mr-2"></i>Confirm New Password
                </label>
                <div class="relative">
                  <input 
                    :type="showConfirmPassword ? 'text' : 'password'" 
                    id="password_confirmation" 
                    v-model="form.password_confirmation" 
                    class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200"
                    placeholder="Confirm new password"
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

          <!-- Enhanced Action Buttons -->
          <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
            <Link :href="route('users.index')" 
                  class="inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-all duration-200">
              <i class="fas fa-times mr-2"></i>
              Cancel
            </Link>
            <button 
              type="submit" 
              :disabled="form.processing"
              class="inline-flex items-center justify-center px-8 py-3 border border-transparent rounded-lg text-white bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
              <i class="fas fa-save mr-2"></i>
              <span v-if="form.processing">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Updating User...
              </span>
              <span v-else>Update User</span>
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
  user: {
    type: Object,
    required: true
  },
  roles: {
    type: Array,
    default: () => []
  },
  restaurants: {
    type: Array,
    default: () => []
  },
  assignedUsers: {
    type: Array,
    default: () => []
  },
  areaManagers: {
    type: Array,
    default: () => []
  }
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  phone: props.user.phone,
  employee_id: props.user.employee_id,
  role: props.user.role,
  restaurant_ids: props.user.restaurant_ids || [],
  managed_restaurant_ids: props.user.managed_restaurant_ids || [],
  area_manager_ids: props.user.area_manager_ids || [],
  department: props.user.department,
  password: '',
  password_confirmation: '',
});

const submitForm = () => {
  form.put(route('users.update', props.user.id), {
    preserveScroll: true,
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

// Managed restaurant selection methods (for Area Managers)
const selectAllManagedRestaurants = () => {
  form.managed_restaurant_ids = props.restaurants.map(restaurant => restaurant.id);
};

const clearAllManagedRestaurants = () => {
  form.managed_restaurant_ids = [];
};

const toggleManagedRestaurant = (restaurantId) => {
  const index = form.managed_restaurant_ids.indexOf(restaurantId);
  if (index > -1) {
    form.managed_restaurant_ids.splice(index, 1);
  } else {
    form.managed_restaurant_ids.push(restaurantId);
  }
};

// Area manager assignment methods
const selectAllAreaManagers = () => {
  form.area_manager_ids = props.areaManagers.map(manager => manager.id);
};

const clearAllAreaManagers = () => {
  form.area_manager_ids = [];
};

const toggleAreaManager = (managerId) => {
  const index = form.area_manager_ids.indexOf(managerId);
  if (index > -1) {
    form.area_manager_ids.splice(index, 1);
  } else {
    form.area_manager_ids.push(managerId);
  }
};
</script>

<style scoped>
/* Reuse the same styles from Create.vue */
@keyframes slideIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

input:focus, select:focus, textarea:focus {
  transform: translateY(-1px);
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

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
</style>
