<template>
    <DefaultAuthenticatedLayout>
        <Head title="Permission & Role Management" />

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Permission & Role Management</h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Manage page access permissions and operations for each user role
                                </p>
                                <!-- Debug Info -->
                                <div class="mt-2 text-xs text-gray-500">
                                    Roles: {{ roles?.length || 0 }} | Pages: {{ pages?.length || 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Tabs -->
                <div v-if="roles && roles.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex -mb-px" aria-label="Tabs">
                            <button
                                v-for="role in roles"
                                :key="role.id"
                                @click="selectedRole = role"
                                :class="[
                                    selectedRole?.id === role.id
                                        ? 'border-blue-500 text-blue-600'
                                        : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                                    'flex-1 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm'
                                ]"
                            >
                                {{ role.name }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- No Roles Message -->
                <div v-else class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                    <div class="flex items-center">
                        <svg class="h-6 w-6 text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800">No Roles Found</h3>
                            <p class="mt-1 text-sm text-yellow-700">
                                Please create roles first before managing permissions.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Permissions Table -->
                <div v-if="selectedRole" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="savePermissions">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Page
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                View
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Create
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Edit
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Delete
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Approve
                                            </th>
                                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Dashboard
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="page in pages" :key="page.id" :class="{ 'bg-gray-50': !page.is_active }">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ page.page_name }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                                />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].can_create"
                                                    :disabled="!permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50"
                                                />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].can_edit"
                                                    :disabled="!permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50"
                                                />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].can_delete"
                                                    :disabled="!permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50"
                                                />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].can_approve"
                                                    :disabled="!permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50"
                                                />
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="permissions[page.id].show_in_dashboard"
                                                    :disabled="!permissions[page.id].can_view"
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded disabled:opacity-50"
                                                />
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-6 flex justify-end space-x-3 space-x-reverse">
                                <button
                                    type="button"
                                    @click="resetPermissions"
                                    class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                >
                                    Reset
                                </button>
                                <button
                                    type="submit"
                                    :disabled="saving"
                                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    <span v-if="saving">Saving...</span>
                                    <span v-else>Save Changes</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Important Notes</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>"View" permission is required to enable other permissions</li>
                                    <li>"Approve" permission grants users the ability to approve operations</li>
                                    <li>"Dashboard" option controls page visibility in the sidebar menu</li>
                                    <li>Permissions work with the user's assigned restaurants only</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';

const props = defineProps({
    roles: Array,
    pages: Array,
    rolePagePermissions: Object,
});

const selectedRole = ref(props.roles[0] || null);
const permissions = ref({});
const saving = ref(false);

// Initialize permissions for the selected role
const initializePermissions = () => {
    const perms = {};
    props.pages.forEach(page => {
        const rolePermission = props.rolePagePermissions[selectedRole.value?.id]?.find(
            p => p.page_permission_id === page.id
        );
        
        perms[page.id] = {
            page_permission_id: page.id,
            can_view: rolePermission?.can_view || false,
            can_create: rolePermission?.can_create || false,
            can_edit: rolePermission?.can_edit || false,
            can_delete: rolePermission?.can_delete || false,
            can_approve: rolePermission?.can_approve || false,
            show_in_dashboard: rolePermission?.show_in_dashboard ?? true,
        };
    });
    permissions.value = perms;
};

// Watch for role changes
watch(selectedRole, () => {
    initializePermissions();
});

// Initialize on mount
initializePermissions();

const savePermissions = () => {
    saving.value = true;
    
    const permissionsArray = Object.values(permissions.value);
    
    router.post(
        route('permission-management.update-role', selectedRole.value.id),
        { permissions: permissionsArray },
        {
            onSuccess: () => {
                saving.value = false;
            },
            onError: () => {
                saving.value = false;
            }
        }
    );
};

const resetPermissions = () => {
    initializePermissions();
};
</script>
