/* Nice Admin JavaScript Functions */

$(document).ready(function() {
    
    // Sidebar Navigation Toggle
    $(".nav-toggler").on('click', function() {
        $("body").toggleClass("show-sidebar");
    });

    // Sidebar Menu Toggle
    $(".sidebar-link.has-arrow").on('click', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        var $collapse = $this.next('.collapse');
        
        // Close other open menus
        $('.sidebar-link.has-arrow').not($this).attr('aria-expanded', 'false');
        $('.collapse').not($collapse).removeClass('show').slideUp();
        
        // Toggle current menu
        if ($collapse.hasClass('show')) {
            $collapse.removeClass('show').slideUp();
            $this.attr('aria-expanded', 'false');
        } else {
            $collapse.addClass('show').slideDown();
            $this.attr('aria-expanded', 'true');
        }
    });

    // Mini Sidebar Toggle
    $(".nav-lock").on('click', function() {
        $("body").toggleClass("mini-sidebar");
    });

    // Perfect Scrollbar for Sidebar
    if ($('.sidebar-nav').length) {
        $('.sidebar-nav').perfectScrollbar();
    }

    // Waves Effect
    Waves.attach('.waves-effect', ['waves-light']);
    Waves.init();
});

// Nice Admin Configuration
var NiceAdminConfig = {
    Theme: false, // true = dark, false = light
    Layout: 'vertical',
    LogoBg: 'skin1', // skin1-6
    NavbarBg: 'skin6', // skin1-6
    SidebarType: 'full', // full, mini-sidebar, iconbar, overlay
    SidebarColor: 'skin1', // skin1-6
    SidebarPosition: true, // true = fixed, false = absolute
    HeaderPosition: true, // true = fixed, false = absolute
    BoxedLayout: false // true = boxed, false = fluid
};

// Apply Theme Settings
function applyThemeSettings() {
    var body = $('body');
    
    // Apply Theme
    if (NiceAdminConfig.Theme) {
        body.addClass('dark-theme');
    } else {
        body.removeClass('dark-theme');
    }
    
    // Apply Sidebar Type
    body.removeClass('mini-sidebar iconbar overlay');
    if (NiceAdminConfig.SidebarType !== 'full') {
        body.addClass(NiceAdminConfig.SidebarType);
    }
    
    // Apply Logo Background
    $('.navbar-brand').removeClass('skin1 skin2 skin3 skin4 skin5 skin6')
                     .addClass(NiceAdminConfig.LogoBg);
    
    // Apply Navbar Background
    $('.topbar').removeClass('skin1 skin2 skin3 skin4 skin5 skin6')
                .addClass(NiceAdminConfig.NavbarBg);
    
    // Apply Sidebar Background
    $('.left-sidebar').removeClass('skin1 skin2 skin3 skin4 skin5 skin6')
                      .addClass(NiceAdminConfig.SidebarColor);
    
    // Apply Positions
    if (NiceAdminConfig.SidebarPosition) {
        $('.left-sidebar').addClass('fixed-sidebar');
    }
    
    if (NiceAdminConfig.HeaderPosition) {
        $('.topbar').addClass('fixed-header');
    }
    
    // Apply Layout
    if (NiceAdminConfig.BoxedLayout) {
        body.addClass('boxed-layout');
    }
}

// Theme Customizer Functions
function initCustomizer() {
    // Dark/Light Theme Toggle
    $('#theme-view').on('change', function() {
        NiceAdminConfig.Theme = $(this).is(':checked');
        applyThemeSettings();
    });
    
    // Sidebar Toggle
    $('#collapssidebar').on('change', function() {
        if ($(this).is(':checked')) {
            NiceAdminConfig.SidebarType = 'mini-sidebar';
        } else {
            NiceAdminConfig.SidebarType = 'full';
        }
        applyThemeSettings();
    });
    
    // Fixed Sidebar
    $('#sidebar-position').on('change', function() {
        NiceAdminConfig.SidebarPosition = $(this).is(':checked');
        applyThemeSettings();
    });
    
    // Fixed Header
    $('#header-position').on('change', function() {
        NiceAdminConfig.HeaderPosition = $(this).is(':checked');
        applyThemeSettings();
    });
    
    // Boxed Layout
    $('#boxed-layout').on('change', function() {
        NiceAdminConfig.BoxedLayout = $(this).is(':checked');
        applyThemeSettings();
    });
    
    // Color Themes
    $('[data-logobg], [data-navbarbg], [data-sidebarbg]').on('click', function(e) {
        e.preventDefault();
        
        var $this = $(this);
        
        if ($this.data('logobg')) {
            NiceAdminConfig.LogoBg = $this.data('logobg');
        }
        
        if ($this.data('navbarbg')) {
            NiceAdminConfig.NavbarBg = $this.data('navbarbg');
        }
        
        if ($this.data('sidebarbg')) {
            NiceAdminConfig.SidebarColor = $this.data('sidebarbg');
        }
        
        applyThemeSettings();
    });
}

// Preloader
$(window).on('load', function() {
    $('.preloader').fadeOut('slow');
});

// Mobile Navigation
function handleMobileNav() {
    if ($(window).width() <= 768) {
        $('body').addClass('mobile-view');
        $('.nav-toggler').show();
    } else {
        $('body').removeClass('mobile-view show-sidebar');
        $('.nav-toggler').hide();
    }
}

// Window Resize Handler
$(window).on('resize', function() {
    handleMobileNav();
});

// Initialize on Document Ready
$(document).ready(function() {
    applyThemeSettings();
    initCustomizer();
    handleMobileNav();
});

// Search Box Toggle
$(document).on('click', '.search-box a, .search-box .srh-btn', function(e) {
    e.preventDefault();
    $('.app-search').toggle(200);
    $('.app-search input').focus();
});

// Customizer Panel Toggle
$(document).on('click', '.service-panel-toggle', function(e) {
    e.preventDefault();
    $('.customizer').toggleClass('show-service-panel');
});

// Close customizer when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('.customizer, .service-panel-toggle').length) {
        $('.customizer').removeClass('show-service-panel');
    }
});

// Tooltip and Popover initialization
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
});

// Card Actions
$(document).on('click', '[data-action="collapse"]', function(e) {
    e.preventDefault();
    var $card = $(this).closest('.card');
    var $body = $card.find('.card-body');
    
    $body.slideToggle();
    $(this).find('i').toggleClass('ti-minus ti-plus');
});

$(document).on('click', '[data-action="expand"]', function(e) {
    e.preventDefault();
    $(this).closest('.card').toggleClass('card-fullscreen');
});

$(document).on('click', '[data-action="close"]', function(e) {
    e.preventDefault();
    $(this).closest('.card').fadeOut();
});

// Active Menu Item
function setActiveMenuItem() {
    var currentPath = window.location.pathname;
    
    $('.sidebar-nav a').each(function() {
        var href = $(this).attr('href');
        if (href && href !== '#' && currentPath.includes(href)) {
            $(this).addClass('active');
            
            // Expand parent menu if needed
            var $collapse = $(this).closest('.collapse');
            if ($collapse.length) {
                $collapse.addClass('show');
                $collapse.prev('.has-arrow').attr('aria-expanded', 'true');
            }
        }
    });
}

// Initialize Active Menu on Load
$(document).ready(function() {
    setActiveMenuItem();
});