<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { usePermissions } from '@/Composables/usePermissions';

const showingNavigationDropdown = ref(false);
const { can, isAdmin } = usePermissions();
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <nav class="bg-white border-b border-gray-100">
            <!-- Primary Navigation Menu -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <Link :href="route('dashboard')">
                                <ApplicationLogo class="block h-9 w-auto fill-current text-gray-800" />
                            </Link>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </NavLink>
                            
                            <!-- Store Visits Dropdown -->
                            <div class="relative">
                                <Dropdown align="left" width="60">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                Store Visits
                                                <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('store-visits.create')">Store Visits Form</DropdownLink>
                                        <DropdownLink :href="route('store-visits.index')">Store Visits Report</DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Q.S.C Checklist Navigation with Dropdown -->
                            <div class="relative">
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                Q.S.C Checklist
                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('qsc-checklist.form')">
                                            Q.S.C Checklist Form
                                        </DropdownLink>
                                        <DropdownLink :href="route('qsc-checklist.report')">
                                            Q.S.C Checklist Report
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Temperature Tracking Navigation with Dropdown -->
                            <div class="relative">
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                Temperature Tracking
                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('temperature-tracking.create')">
                                            Temperature Tracking Form
                                        </DropdownLink>
                                        <DropdownLink :href="route('temperature-tracking.index')">
                                            Temperature Tracking Report
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>

                            <!-- Action Plans Navigation (Restaurant Managers and Admins) -->
                            <div v-if="can('action_plans', 'view')">
                                <NavLink :href="route('action-plans.index')" :active="route().current('action-plans.*')">
                                    Action Plans
                                </NavLink>
                            </div>

                            <!-- Admin Navigation (conditionally shown) -->
                            <div v-if="isAdmin" class="relative">
                                <Dropdown align="left" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                            >
                                                Admin
                                                <svg
                                                    class="ml-2 -mr-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('users.index')">
                                            Users
                                        </DropdownLink>
                                        <DropdownLink :href="route('restaurants.index')">
                                            Restaurants
                                        </DropdownLink>
                                        <DropdownLink :href="route('settings.index')">
                                            Settings
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <!-- Settings Dropdown -->
                        <div class="ml-3 relative">
                            <Dropdown align="right" width="48">
                                <template #trigger>
                                    <span class="inline-flex rounded-md">
                                        <button
                                            type="button"
                                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150"
                                        >
                                            {{ $page.props.auth.user.name }}

                                            <svg
                                                class="ml-2 -mr-0.5 h-4 w-4"
                                                xmlns="http://www.w3.org/2000/svg"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                    clip-rule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </span>
                                </template>

                                <template #content>
                                    <DropdownLink :href="route('profile.edit')"> Profile </DropdownLink>
                                    <DropdownLink :href="route('logout')" method="post" as="button">
                                        Log Out
                                    </DropdownLink>
                                </template>
                            </Dropdown>
                        </div>
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button
                            @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out"
                        >
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{
                                        hidden: showingNavigationDropdown,
                                        'inline-flex': !showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"
                                />
                                <path
                                    :class="{
                                        hidden: !showingNavigationDropdown,
                                        'inline-flex': showingNavigationDropdown,
                                    }"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div
                :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }"
                class="sm:hidden"
            >
                <div class="pt-2 pb-3 space-y-1">
                    <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                        Dashboard
                    </ResponsiveNavLink>
                </div>

                <!-- Store Visits Mobile Menu -->
                <div class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-sm text-gray-800">Store Visits</div>
                    </div>
                    <ResponsiveNavLink :href="route('store-visits.create')">
                        Store Visits Form
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('store-visits.index')">
                        Store Visits Report
                    </ResponsiveNavLink>
                </div>

                <!-- Q.S.C Checklist Mobile Menu -->
                <div class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-sm text-gray-800">Q.S.C Checklist</div>
                    </div>
                    <ResponsiveNavLink :href="route('qsc-checklist.form')">
                        Q.S.C Checklist Form
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('qsc-checklist.report')">
                        Q.S.C Checklist Report
                    </ResponsiveNavLink>
                </div>

                <!-- Temperature Tracking Mobile Menu -->
                <div class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-sm text-gray-800">Temperature Tracking</div>
                    </div>
                    <ResponsiveNavLink :href="route('temperature-tracking.create')">
                        Temperature Tracking Form
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('temperature-tracking.index')">
                        Temperature Tracking Report
                    </ResponsiveNavLink>
                </div>

                <!-- Action Plans Mobile Menu (Restaurant Managers and Admins) -->
                <div v-if="can('action_plans', 'view')" class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <ResponsiveNavLink :href="route('action-plans.index')" :active="route().current('action-plans.*')">
                        Action Plans
                    </ResponsiveNavLink>
                </div>

                <!-- Admin Mobile Menu -->
                <div v-if="isAdmin" class="pt-2 pb-3 space-y-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-sm text-gray-800">Administration</div>
                    </div>
                    <ResponsiveNavLink :href="route('users.index')">
                        Users
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('restaurants.index')">
                        Restaurants
                    </ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('settings.index')">
                        Settings
                    </ResponsiveNavLink>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="font-medium text-sm text-gray-500">{{ $page.props.auth.user.email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')"> Profile </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        <header class="bg-white shadow" v-if="$slots.header">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>