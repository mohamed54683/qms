<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PagePermission;

class PagePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pages = [
            [
                'page_name' => 'Dashboard',
                'page_key' => 'dashboard',
                'page_route' => 'dashboard',
                'page_icon' => 'ChartBarIcon',
                'display_order' => 1,
            ],
            [
                'page_name' => 'Store Visits',
                'page_key' => 'store_visits',
                'page_route' => 'store-visits.index',
                'page_icon' => 'BuildingStorefrontIcon',
                'display_order' => 2,
            ],
            [
                'page_name' => 'Action Plans',
                'page_key' => 'action_plans',
                'page_route' => 'action-plans.index',
                'page_icon' => 'ClipboardDocumentListIcon',
                'display_order' => 3,
            ],
            [
                'page_name' => 'QSC Checklist',
                'page_key' => 'qsc_checklist',
                'page_route' => 'qsc-checklist.index',
                'page_icon' => 'ClipboardDocumentCheckIcon',
                'display_order' => 4,
            ],
            [
                'page_name' => 'Temperature Tracking',
                'page_key' => 'temperature_tracking',
                'page_route' => 'temperature-tracking.index',
                'page_icon' => 'BeakerIcon',
                'display_order' => 5,
            ],
            [
                'page_name' => 'Restaurants',
                'page_key' => 'restaurants',
                'page_route' => 'restaurants.index',
                'page_icon' => 'MapPinIcon',
                'display_order' => 6,
            ],
            [
                'page_name' => 'Users',
                'page_key' => 'users',
                'page_route' => 'users.index',
                'page_icon' => 'UsersIcon',
                'display_order' => 7,
            ],
            [
                'page_name' => 'Roles & Permissions',
                'page_key' => 'roles_permissions',
                'page_route' => 'roles.index',
                'page_icon' => 'ShieldCheckIcon',
                'display_order' => 8,
            ],
            [
                'page_name' => 'Reports',
                'page_key' => 'reports',
                'page_route' => 'reports.index',
                'page_icon' => 'DocumentChartBarIcon',
                'display_order' => 9,
            ],
            [
                'page_name' => 'Settings',
                'page_key' => 'settings',
                'page_route' => 'settings.index',
                'page_icon' => 'CogIcon',
                'display_order' => 10,
            ],
        ];

        foreach ($pages as $page) {
            PagePermission::updateOrCreate(
                ['page_key' => $page['page_key']],
                $page
            );
        }
    }
}
