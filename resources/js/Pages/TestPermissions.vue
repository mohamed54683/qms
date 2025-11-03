<template>
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Test Permissions Debug</h1>
        
        <div class="bg-gray-100 p-4 rounded mb-4">
            <h3 class="font-bold">Raw Page Props:</h3>
            <pre class="text-xs overflow-auto bg-white p-2 rounded mt-2">{{ JSON.stringify($page.props, null, 2) }}</pre>
        </div>
        
        <div class="bg-blue-100 p-4 rounded mb-4">
            <h3 class="font-bold">User Info:</h3>
            <p><strong>Name:</strong> {{ $page.props.auth?.user?.name }}</p>
            <p><strong>Email:</strong> {{ $page.props.auth?.user?.email }}</p>
            <p><strong>Roles:</strong> {{ $page.props.auth?.user?.roles?.map(r => r.name).join(', ') }}</p>
        </div>
        
        <div class="bg-green-100 p-4 rounded mb-4">
            <h3 class="font-bold">Permissions:</h3>
            <pre class="text-xs overflow-auto bg-white p-2 rounded mt-2">{{ JSON.stringify($page.props.auth?.user?.permissions, null, 2) }}</pre>
        </div>
        
        <div class="bg-yellow-100 p-4 rounded mb-4">
            <h3 class="font-bold">usePermissions Test:</h3>
            <p><strong>can('action_plans', 'view'):</strong> {{ can('action_plans', 'view') ? 'TRUE' : 'FALSE' }}</p>
            <p><strong>can('action_plans', 'create'):</strong> {{ can('action_plans', 'create') ? 'TRUE' : 'FALSE' }}</p>
            <p><strong>isAdmin:</strong> {{ isAdmin ? 'TRUE' : 'FALSE' }}</p>
        </div>
        
        <div class="bg-red-100 p-4 rounded mb-4">
            <h3 class="font-bold">Navigation Test:</h3>
            <div v-if="can('action_plans', 'view')" class="bg-green-500 text-white p-2 rounded">
                ✅ Action Plans SHOULD appear in navigation
            </div>
            <div v-else class="bg-red-500 text-white p-2 rounded">
                ❌ Action Plans will NOT appear in navigation
            </div>
        </div>

        <div class="bg-purple-100 p-4 rounded mb-4">
            <h3 class="font-bold">Debug Info:</h3>
            <p><strong>Permissions object exists:</strong> {{ $page.props.auth?.user?.permissions ? 'YES' : 'NO' }}</p>
            <p><strong>Action plans in permissions:</strong> {{ $page.props.auth?.user?.permissions?.action_plans ? 'YES' : 'NO' }}</p>
            <p><strong>Action plans can_view:</strong> {{ $page.props.auth?.user?.permissions?.action_plans?.can_view ? 'TRUE' : 'FALSE' }}</p>
        </div>
    </div>
</template>

<script setup>
import { usePermissions } from '@/Composables/usePermissions';

const { can, isAdmin } = usePermissions();
</script>