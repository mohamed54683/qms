<template>
    <Head title="Test Inertia Data" />
    
    <div style="padding: 20px; font-family: monospace;">
        <h1>Inertia Data Debug</h1>
        
        <h2>Page Props:</h2>
        <pre>{{ JSON.stringify($page.props, null, 2) }}</pre>
        
        <h2>Auth Data:</h2>
        <pre>{{ JSON.stringify($page.props.auth, null, 2) }}</pre>
        
        <h2>User Data:</h2>
        <pre>{{ JSON.stringify($page.props.auth?.user, null, 2) }}</pre>
        
        <h2>Permissions Data:</h2>
        <pre>{{ JSON.stringify($page.props.auth?.user?.permissions, null, 2) }}</pre>
        
        <h2>Action Plans Permission:</h2>
        <pre>{{ JSON.stringify($page.props.auth?.user?.permissions?.action_plans, null, 2) }}</pre>
        
        <h2>usePermissions Test:</h2>
        <div v-if="permissions">
            <p>Permissions object exists: ✅</p>
            <p>Action plans exists: {{ permissions.action_plans ? '✅' : '❌' }}</p>
            <p>Can view action plans: {{ can('action_plans', 'view') ? '✅' : '❌' }}</p>
        </div>
        <div v-else>
            <p>❌ Permissions object does not exist</p>
        </div>
    </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/usePermissions';

const { permissions, can } = usePermissions();
</script>