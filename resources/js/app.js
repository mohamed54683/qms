import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#5d87ff',
    },
});

// Nice Admin JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Nice Admin
    initNiceAdmin();
});

function initNiceAdmin() {
    // Sidebar menu toggle functionality
    document.addEventListener('click', function(e) {
        // Sidebar dropdown toggle
        if (e.target.closest('.has-arrow')) {
            e.preventDefault();
            const link = e.target.closest('.has-arrow');
            const collapse = link.nextElementSibling;
            
            // Close other open menus
            document.querySelectorAll('.has-arrow').forEach(item => {
                if (item !== link) {
                    item.setAttribute('aria-expanded', 'false');
                    const otherCollapse = item.nextElementSibling;
                    if (otherCollapse) {
                        otherCollapse.classList.remove('show');
                        otherCollapse.style.display = 'none';
                    }
                }
            });
            
            // Toggle current menu
            if (collapse) {
                const isExpanded = link.getAttribute('aria-expanded') === 'true';
                link.setAttribute('aria-expanded', !isExpanded);
                
                if (isExpanded) {
                    collapse.classList.remove('show');
                    collapse.style.display = 'none';
                } else {
                    collapse.classList.add('show');
                    collapse.style.display = 'block';
                }
            }
        }
    });
    
    // Mobile responsiveness
    function handleResize() {
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-view');
        } else {
            document.body.classList.remove('mobile-view', 'show-sidebar');
        }
    }
    
    window.addEventListener('resize', handleResize);
    handleResize();
    
    // Hide preloader after page load
    setTimeout(() => {
        const preloader = document.querySelector('.preloader');
        if (preloader) {
            preloader.style.opacity = '0';
            setTimeout(() => {
                preloader.style.display = 'none';
            }, 300);
        }
    }, 1000);
    
    // Set active menu item
    setActiveMenuItem();
}

function setActiveMenuItem() {
    const currentPath = window.location.pathname;
    
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        const href = link.getAttribute('href');
        if (href && href !== '#' && currentPath.includes(href.replace(window.location.origin, ''))) {
            link.classList.add('active');
            
            // Expand parent menu if needed
            const collapse = link.closest('.collapse');
            if (collapse) {
                collapse.classList.add('show');
                collapse.style.display = 'block';
                const parentLink = collapse.previousElementSibling;
                if (parentLink) {
                    parentLink.setAttribute('aria-expanded', 'true');
                }
            }
        }
    });
}
