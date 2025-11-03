<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

const sidebarCollapsed = ref(false);
const mobileMenuOpen = ref(false);
const isLoading = ref(true);

const toggleSidebar = () => {
    sidebarCollapsed.value = !sidebarCollapsed.value;
    document.body.classList.toggle('mini-sidebar');
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
    document.body.classList.toggle('show-sidebar');
};



// Toggle dropdown functionality
const toggleDropdown = (event) => {
    event.preventDefault();
    event.stopPropagation();
    
    const link = event.currentTarget;
    const submenu = link.nextElementSibling;
    const isExpanded = link.getAttribute('aria-expanded') === 'true';
    
    // Close all other dropdowns first
    document.querySelectorAll('.sidebar-link.has-arrow').forEach(otherLink => {
        if (otherLink !== link) {
            otherLink.setAttribute('aria-expanded', 'false');
            const otherSubmenu = otherLink.nextElementSibling;
            if (otherSubmenu && otherSubmenu.classList.contains('collapse')) {
                otherSubmenu.classList.remove('show');
            }
        }
    });
    
    // Toggle current dropdown
    if (submenu && submenu.classList.contains('collapse')) {
        if (isExpanded) {
            // Close it
            link.setAttribute('aria-expanded', 'false');
            submenu.classList.remove('show');
        } else {
            // Open it
            link.setAttribute('aria-expanded', 'true');
            submenu.classList.add('show');
        }
    }
};

// Hide loader after component mounts
onMounted(() => {
    setTimeout(() => {
        isLoading.value = false;
    }, 600);
    
    // Initialize dropdown functionality - wait for DOM to be ready
    setTimeout(() => {
        const dropdownLinks = document.querySelectorAll('.sidebar-link.has-arrow');
        
        dropdownLinks.forEach((link) => {
            
            // Remove any existing event listeners first
            link.removeEventListener('click', toggleDropdown);
            // Add the event listener
            link.addEventListener('click', toggleDropdown);
            
            // Set initial state
            link.setAttribute('aria-expanded', 'false');
            const submenu = link.nextElementSibling;
            if (submenu && submenu.classList.contains('collapse')) {
                submenu.classList.remove('show');
            }
        });
    }, 500); // Increased delay to ensure DOM is ready
});
</script>

<template>
  <!-- Enhanced Loading Animation -->
  <div v-if="isLoading" class="page-loader" :class="{ 'hide': !isLoading }">
    <div class="loader-content">
      <div class="spinner"></div>
      <h3 style="color: white; margin: 0;">QMS Dashboard</h3>
      <p style="color: rgba(255,255,255,0.8); margin: 5px 0 0 0;">Loading your experience...</p>
    </div>
  </div>

  <div class="main-wrapper" :class="{ 'main-wrapper-visible': !isLoading }">

    <!-- Sidebar -->
    <aside class="left-sidebar" :class="{ 'mini-sidebar': sidebarCollapsed }">
      <a class="navbar-brand" :href="route('dashboard')">
        <b class="logo-icon">
          <ApplicationLogo height="40px" max-height="40px" max-width="40px" />
        </b>
        <span class="logo-text hide-menu">
          <ApplicationLogo height="25px" max-height="25px" max-width="120px" />
        </span>
      </a>
      <div class="scroll-sidebar">
        <nav class="sidebar-nav">
          <ul id="sidebarnav">
            <li>
              <div class="user-profile d-flex no-block dropdown m-t-20">
                <div class="user-pic">
                  <img src="https://via.placeholder.com/40" alt="users" class="rounded-circle" width="40" />
                </div>
                <div class="user-content hide-menu ml-2">
                  <h5 class="mb-0 user-name font-medium">{{ $page.props.auth.user.name }}</h5>
                  <span class="op-5 user-email">{{ $page.props.auth.user.email }}</span>
                </div>
              </div>
            </li>
            <li class="nav-small-cap"><i class="ti-more-alt"></i> <span class="hide-menu">Navigation</span></li>
            <li class="sidebar-item">
              <Link :href="route('dashboard')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('dashboard') }">
                <i class="icon">📊</i>
                <span class="hide-menu">Main Dashboard</span>
              </Link>
            </li>
            
            <!-- Quick Actions -->
            <li class="nav-small-cap"><i class="ti-more-alt"></i> <span class="hide-menu">Quick Actions</span></li>
            <li class="sidebar-item">
              <Link :href="route('store-visits.create')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('store-visits.create') }">
                <i class="icon">🚀</i>
                <span class="hide-menu">New Operation Form</span>
              </Link>
            </li>


            <li class="nav-small-cap"><i class="ti-more-alt"></i> <span class="hide-menu">QMS Management</span></li>
            <li class="sidebar-item store-visits-section">
              <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false" @click="toggleDropdown">
                <i class="icon">📋</i>
                <span class="hide-menu">Operation Forms</span>
                <span v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'admin')" class="badge badge-warning ml-2">Admin</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <!-- Main Functions -->
                <li class="sidebar-item">
                  <Link :href="route('store-visits.index')" class="sidebar-link" :class="{ 'active': route().current('store-visits.index') }">
                    <i class="icon">📊</i>
                    <span class="hide-menu">Dashboard & Reports</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('store-visits.create')" class="sidebar-link" :class="{ 'active': route().current('store-visits.create') }">
                    <i class="icon">➕</i>
                    <span class="hide-menu">Create New Visit</span>
                  </Link>
                </li>
                
                <!-- Management -->

                

              </ul>
            </li>

              <!-- TEST NAV ITEM -->
              <li class="sidebar-item">
                <a class="sidebar-link waves-effect waves-dark" href="#">
                  <i class="icon">🧪</i>
                  <span class="hide-menu">TEST NAV ITEM</span>
                </a>
              </li>

              <!-- Q.S.C CHECKLIST Navigation Item -->
              <li class="sidebar-item qsc-checklist-section">
                <Link :href="route('qsc-checklist.index')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('qsc-checklist.index') }">
                  <i class="icon">✅</i>
                  <span class="hide-menu">Q.S.C CHECKLIST</span>
                </Link>
              </li>

              <!-- TTF QMS Logo Navigation Item -->
              <li class="sidebar-item ttf-section">
                <Link :href="route('ttf.index')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('ttf.index') }">
                  <i class="icon">📝</i>
                  <span class="hide-menu">TTF QMS Logo</span>
                </Link>
              </li>

              <!-- Q.S.C CHECKLIST Navigation Item -->
              <li class="sidebar-item qsc-checklist-section">
                <Link :href="route('qsc-checklist.index')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('qsc-checklist.index') }">
                  <i class="icon">✅</i>
                  <span class="hide-menu">Q.S.C CHECKLIST</span>
                </Link>
              </li>

              <!-- TTF –s Navigation Item -->
              <li class="sidebar-item ttf-section">
                <Link :href="route('ttf.index')" class="sidebar-link waves-effect waves-dark" :class="{ 'active': route().current('ttf.index') }">
                  <i class="icon">📝</i>
                  <span class="hide-menu">TTF –s</span>
                </Link>
              </li>

            <!-- Q.S.C Checklist Management -->

            <!-- Action Plans Management -->
            <li class="sidebar-item action-plans-section">
              <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false" @click="toggleDropdown">
                <i class="icon">⚡</i>
                <span class="hide-menu">Action Plans</span>
                <span v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => ['admin', 'restaurant_manager'].includes(role.name))" class="badge badge-success ml-2">Plans</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <Link :href="route('action-plans.index')" class="sidebar-link" :class="{ 'active': route().current('action-plans.index') }">
                    <i class="icon">📊</i>
                    <span class="hide-menu">All Action Plans</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('action-plans.create')" class="sidebar-link" :class="{ 'active': route().current('action-plans.create') }">
                    <i class="icon">➕</i>
                    <span class="hide-menu">Create Plan</span>
                  </Link>
                </li>
                
                <!-- Status Views -->
                <li class="nav-divider"></li>
                <li class="nav-small-cap"><span class="hide-menu">By Status</span></li>
                <li class="sidebar-item">
                  <Link :href="route('action-plans.index', { status: 'pending' })" class="sidebar-link">
                    <i class="icon">⏳</i>
                    <span class="hide-menu">Pending Plans</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('action-plans.index', { status: 'in_progress' })" class="sidebar-link">
                    <i class="icon">🔄</i>
                    <span class="hide-menu">In Progress</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('action-plans.index', { status: 'completed' })" class="sidebar-link">
                    <i class="icon">✅</i>
                    <span class="hide-menu">Completed</span>
                  </Link>
                </li>
                
                <!-- Reports -->
                <li class="nav-small-cap"><span class="hide-menu">Analytics</span></li>
                <li class="sidebar-item">
                  <Link :href="route('action-plans.index')" class="sidebar-link">
                    <i class="icon">📊</i>
                    <span class="hide-menu">Plans Analytics</span>
                  </Link>
                </li>
              </ul>
            </li>

            <li class="sidebar-item" v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'admin')">
              <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false" @click="toggleDropdown">
                <i class="icon">🏪</i>
                <span class="hide-menu">Restaurant Management</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <Link :href="route('restaurants.index')" class="sidebar-link" :class="{ 'active': route().current('restaurants.index') }">
                    <i class="icon">📋</i>
                    <span class="hide-menu">All Restaurants</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('restaurants.create')" class="sidebar-link" :class="{ 'active': route().current('restaurants.create') }">
                    <i class="icon">➕</i>
                    <span class="hide-menu">Add Restaurant</span>
                  </Link>
                </li>
              </ul>
            </li>
            <li class="sidebar-item" v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'admin')">
              <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false" @click="toggleDropdown">
                <i class="icon">👥</i>
                <span class="hide-menu">User Management</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <a href="#" class="sidebar-link">
                    <i class="icon">📋</i>
                    <span class="hide-menu">Performance Reports</span>
                  </a>
                </li>
                <li class="sidebar-item">
                  <a href="#" class="sidebar-link">
                    <i class="icon">📈</i>
                    <span class="hide-menu">Analytics Dashboard</span>
                  </a>
                </li>
              </ul>
            </li>
            <!-- Admin Settings Section -->
            <li class="nav-small-cap" v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'admin')"><i class="ti-more-alt"></i> <span class="hide-menu">Administration</span></li>
            <li class="sidebar-item" v-if="$page.props.auth.user.roles && $page.props.auth.user.roles.some(role => role.name === 'admin')">
              <a class="sidebar-link has-arrow waves-effect waves-dark" href="javascript:void(0)" aria-expanded="false" @click="toggleDropdown">
                <i class="icon">⚙️</i>
                <span class="hide-menu">System Settings</span>
              </a>
              <ul aria-expanded="false" class="collapse first-level">
                <li class="sidebar-item">
                  <Link :href="route('settings.index')" class="sidebar-link" :class="{ 'active': route().current('settings.index') }">
                    <i class="icon">🔧</i>
                    <span class="hide-menu">General Settings</span>
                  </Link>
                </li>
                <li class="sidebar-item">
                  <Link :href="route('settings.users')" class="sidebar-link" :class="{ 'active': route().current('settings.users') }">
                    <i class="icon">👥</i>
                    <span class="hide-menu">User Management</span>
                  </Link>
                </li>
              </ul>
            </li>
            <li class="nav-small-cap"><i class="ti-more-alt"></i> <span class="hide-menu">Account</span></li>
            <li class="sidebar-item">
              <Link :href="route('profile.edit')" class="sidebar-link waves-effect waves-dark">
                <i class="icon">👤</i>
                <span class="hide-menu">Profile</span>
              </Link>
            </li>
            <li class="sidebar-item">
              <Link :href="route('logout')" method="post" as="button" class="sidebar-link waves-effect waves-dark">
                <i class="icon">🚪</i>
                <span class="hide-menu">Logout</span>
              </Link>
            </li>
          </ul>
        </nav>
      </div>
    </aside>

    <!-- Page Wrapper -->
    <div class="page-wrapper">
      <!-- Topbar -->
      <header class="topbar">
        <nav class="navbar top-navbar navbar-expand-md navbar-light">
          <div class="navbar-header">
            <!-- Mobile menu toggle -->
            <a class="nav-toggler waves-effect waves-light d-md-none" @click="toggleMobileMenu" href="javascript:void(0)">
              <i class="icon">☰</i>
            </a>
            <!-- Mini sidebar toggle -->
            <a class="nav-lock waves-effect waves-light d-none d-md-block" @click="toggleSidebar" href="javascript:void(0)">
              <i class="icon">⚡</i>
            </a>
          </div>
          <div class="navbar-collapse collapse" id="navbarSupportedContent">
            <!-- Search Box -->
            <ul class="navbar-nav float-left mr-auto ml-3">
              <li class="nav-item search-box">
                <a class="nav-link waves-effect waves-dark" href="javascript:void(0)">
                  <div class="d-flex align-items-center">
                    <i class="mdi mdi-magnify font-20 mr-1"></i>
                    <div class="ml-1 d-none d-sm-block">
                      <span>Search</span>
                    </div>
                  </div>
                </a>
              </li>
            </ul>
            <!-- User Profile -->
            <ul class="navbar-nav float-right">
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <img src="https://via.placeholder.com/32" alt="user" class="rounded-circle" width="32">
                  <span class="ml-2 d-none d-lg-inline-block">
                    <span>Hello,</span>
                    <span class="text-dark">{{ $page.props.auth.user.name }}</span>
                    <i class="mdi mdi-chevron-down"></i>
                  </span>
                </a>
                <div class="dropdown-menu dropdown-menu-right user-dd animated flipInY">
                  <div class="dropdown-divider"></div>
                  <Link :href="route('profile.edit')" class="dropdown-item">
                    <i class="mdi mdi-account mr-1 ml-1"></i> My Profile
                  </Link>
                  <div class="dropdown-divider"></div>
                  <Link :href="route('logout')" method="post" as="button" class="dropdown-item">
                    <i class="mdi mdi-logout mr-1 ml-1"></i> Logout
                  </Link>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!-- Enhanced Main Content -->
      <div class="container-fluid page-content">
        <slot />
      </div>
      
      <!-- Footer -->
      <footer class="footer">
        © {{ new Date().getFullYear() }} QMS by Your Company
      </footer>
    </div>
  </div>
</template>

<style scoped>
.user-dd {
  min-width: 240px;
}
.navbar-nav .nav-link {
  color: #545454;
}
.navbar-nav .nav-link:hover {
  color: #5d87ff;
}

/* Dropdown/Collapse Functionality */
.collapse {
  display: none;
  overflow: hidden;
  max-height: 0;
  opacity: 0;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.collapse.show {
  display: block !important;
  max-height: 400px;
  opacity: 1;
  transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes slideDown {
  from {
    max-height: 0;
    opacity: 0;
  }
  to {
    max-height: 300px;
    opacity: 1;
  }
}

.sidebar-link.has-arrow {
  cursor: pointer !important;
  position: relative;
}

.sidebar-link.has-arrow::after {
  content: '▼';
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  transition: transform 0.3s ease;
  font-size: 12px;
  color: #666;
}

.sidebar-link.has-arrow[aria-expanded="true"]::after {
  transform: translateY(-50%) rotate(180deg);
  color: #5d87ff;
}

.sidebar-link.has-arrow:hover::after {
  color: #5d87ff;
}

/* Enhanced Store Visits Sub-Navigation Styling */
.sidebar-item .first-level {
  background: rgba(93, 135, 255, 0.05);
  border-left: 3px solid #5d87ff;
  margin-left: 10px;
  border-radius: 0 8px 8px 0;
  padding: 5px 0;
}

.sidebar-item .first-level .sidebar-item {
  margin-bottom: 2px;
}

.sidebar-item .first-level .sidebar-link {
  padding: 8px 20px 8px 35px;
  color: #2a3547;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.3s ease;
  border-radius: 0 20px 20px 0;
  margin-right: 10px;
}

.sidebar-item .first-level .sidebar-link:hover {
  background: linear-gradient(135deg, #5d87ff 0%, #7b68ee 100%);
  color: white;
  padding-left: 45px;
  box-shadow: 0 4px 15px rgba(93, 135, 255, 0.2);
}

.sidebar-item .first-level .sidebar-link.active {
  background: linear-gradient(135deg, #5d87ff 0%, #764ba2 100%);
  color: white;
  box-shadow: 0 4px 20px rgba(93, 135, 255, 0.3);
}

.sidebar-item .first-level .sidebar-link .icon {
  margin-right: 10px;
  font-size: 16px;
  width: 20px;
  text-align: center;
}

/* Store Visits Main Item Enhancement */
.sidebar-item .sidebar-link.has-arrow {
  font-weight: 600;
  font-size: 14px;
}

.sidebar-item .sidebar-link.has-arrow .badge {
  background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%);
  color: #333;
  font-weight: bold;
  font-size: 10px;
  padding: 3px 6px;
  border-radius: 10px;
  margin-left: auto;
}

/* Active parent highlighting */
.sidebar-item:has(.first-level .sidebar-link.active) > .sidebar-link {
  background: rgba(93, 135, 255, 0.1);
  color: #5d87ff;
  border-radius: 0 25px 25px 0;
  margin-right: 5px;
}

/* Sub-navigation dividers */
.first-level .nav-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent, rgba(93, 135, 255, 0.3), transparent);
  margin: 8px 20px;
  border: none;
}

/* Enhanced Store Visits Section */
.store-visits-section .sidebar-link {
  background: linear-gradient(135deg, rgba(93, 135, 255, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-left: 4px solid #5d87ff;
  color: #5d87ff;
  font-weight: 700;
}

.store-visits-section .sidebar-link:hover {
  background: linear-gradient(135deg, rgba(93, 135, 255, 0.2) 0%, rgba(118, 75, 162, 0.2) 100%);
  transform: translateX(3px);
  box-shadow: 0 4px 15px rgba(93, 135, 255, 0.2);
}
</style>
