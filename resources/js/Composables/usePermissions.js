import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const page = usePage();
    
    const user = computed(() => page.props.auth?.user);
    const permissions = computed(() => page.props.auth?.user?.permissions || {});
    const restaurants = computed(() => page.props.auth?.user?.restaurants || []);
    const isAdmin = computed(() => {
        const roles = page.props.auth?.user?.roles || [];
        return roles.some(role => role.name === 'admin' || role.name === 'Super Admin');
    });
    
    /**
     * Check if user has permission for a specific page and action
     */
    const can = (pageKey, action = 'view') => {
        if (isAdmin.value) {
            return true; // Admin has all permissions
        }
        
        // For AM and RM users, force Action Plans permissions based on roles
        if (pageKey === 'action_plans') {
            const roles = page.props.auth?.user?.roles || [];
            const hasRMRole = roles.some(role => role.name === 'rm');
            const hasAMRole = roles.some(role => role.name === 'area_manager');
            
            if (hasRMRole) {
                // RM can view, create, edit, and approve
                if (action === 'view' || action === 'create' || action === 'edit' || action === 'approve') {
                    return true;
                }
                return false;
            }
            
            if (hasAMRole) {
                // AM can view, create, and approve (but not edit)
                if (action === 'view' || action === 'create' || action === 'approve') {
                    return true;
                }
                return false;
            }
        }
        
        const pagePermissions = permissions.value[pageKey];
        
        if (!pagePermissions) {
            return false;
        }
        
        switch (action) {
            case 'view':
                return pagePermissions.can_view;
            case 'create':
                return pagePermissions.can_create;
            case 'edit':
                return pagePermissions.can_edit;
            case 'delete':
                return pagePermissions.can_delete;
            case 'approve':
                return pagePermissions.can_approve;
            default:
                return false;
        }
    };
    
    /**
     * Check if page should show in dashboard
     */
    const showInDashboard = (pageKey) => {
        if (isAdmin.value) {
            return true;
        }
        
        const pagePermissions = permissions.value[pageKey];
        return pagePermissions ? pagePermissions.show_in_dashboard : false;
    };
    
    /**
     * Check if user has access to specific restaurant
     */
    const hasRestaurantAccess = (restaurantName) => {
        if (isAdmin.value) {
            return true;
        }
        
        return restaurants.value.some(r => r.name === restaurantName);
    };
    
    /**
     * Get all accessible restaurant names
     */
    const accessibleRestaurants = computed(() => {
        if (isAdmin.value) {
            return []; // Empty means all restaurants
        }
        
        return restaurants.value.map(r => r.name);
    });
    
    return {
        user,
        permissions,
        restaurants,
        isAdmin,
        can,
        showInDashboard,
        hasRestaurantAccess,
        accessibleRestaurants,
    };
}

export default usePermissions;
