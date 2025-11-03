<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';

// Location verification
const locationVerified = ref(false);
const locationError = ref('');
const checkingLocation = ref(false);
const showLocationModal = ref(false); // Start as false
const formLocked = ref(false); // Start as false, will be set when restaurant is selected

// Calculate distance between two coordinates (Haversine formula)
const calculateDistance = (lat1, lon1, lat2, lon2) => {
    const R = 6371e3; // Earth's radius in meters
    const φ1 = lat1 * Math.PI / 180;
    const φ2 = lat2 * Math.PI / 180;
    const Δφ = (lat2 - lat1) * Math.PI / 180;
    const Δλ = (lon2 - lon1) * Math.PI / 180;

    const a = Math.sin(Δφ/2) * Math.sin(Δφ/2) +
              Math.cos(φ1) * Math.cos(φ2) *
              Math.sin(Δλ/2) * Math.sin(Δλ/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; // Distance in meters
};

// Verify user is at restaurant location
const verifyLocation = () => {
    const restaurant = getSelectedRestaurant.value;
    
    if (!restaurant) {
        locationError.value = 'Please select a restaurant first';
        return;
    }
    
    if (!restaurant.latitude || !restaurant.longitude) {
        locationError.value = 'Restaurant location not set. Please contact admin.';
        return;
    }
    
    checkingLocation.value = true;
    locationError.value = '';
    
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            (position) => {
                const userLat = position.coords.latitude;
                const userLon = position.coords.longitude;
                const distance = calculateDistance(
                    userLat, 
                    userLon, 
                    restaurant.latitude, 
                    restaurant.longitude
                );
                
                // Store location in form
                form.user_latitude = userLat;
                form.user_longitude = userLon;
                
                // Check if within 200 meters of restaurant
                if (distance <= 200) {
                    locationVerified.value = true;
                    locationError.value = `✅ Location verified! You are ${Math.round(distance)}m from ${restaurant.name}`;
                    showLocationModal.value = false;
                    formLocked.value = false;
                } else {
                    locationVerified.value = false;
                    locationError.value = `❌ You are ${Math.round(distance)}m away from ${restaurant.name}. You must be within 200m to submit.`;
                }
                
                checkingLocation.value = false;
            },
            (error) => {
                checkingLocation.value = false;
                locationError.value = 'Location access denied. Please enable location services.';
                console.error('Geolocation error:', error);
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        checkingLocation.value = false;
        locationError.value = 'Geolocation not supported by your browser';
    }
};

const handleLocationCancel = () => {
    router.visit('/qms/dashboard');
};

// Get selected restaurant with coordinates
const getSelectedRestaurant = computed(() => {
    return props.restaurants.find(r => r.name === form.store_name);
});

// Reset location when restaurant changes
const handleRestaurantChange = () => {
    locationVerified.value = false;
    locationError.value = '';
    form.user_latitude = null;
    form.user_longitude = null;
    
    // Show location verification modal when restaurant is selected
    if (form.store_name) {
        showLocationModal.value = true;
        formLocked.value = true;
    } else {
        showLocationModal.value = false;
        formLocked.value = false;
    }
};

onMounted(() => {
    // Don't auto-verify location on mount
    // Wait for user to select a restaurant first
});

const props = defineProps({
    errors: Object,
    restaurants: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    },
    checklist: {
        type: Object,
        default: null
    },
    isEditing: {
        type: Boolean,
        default: false
    }
});

// Helper function to get nested values safely
const getFieldValue = (section, field) => {
    if (!props.checklist) return null;
    const fieldName = `${section}_${field}`;
    return props.checklist[fieldName] ?? null;
};

// Form data using Inertia's useForm
const form = useForm({
    store_name: props.checklist?.store_name || '',
    date: props.checklist?.date || new Date().toISOString().split('T')[0],
    mod: props.checklist?.mod || '',
    day: props.checklist?.day || new Date().toLocaleDateString('en-US', { weekday: 'long' }),
    time_option: props.checklist?.time_option || '',
    user_latitude: null,
    user_longitude: null,
    
    // Exterior section
    exterior: {
        lights_open: getFieldValue('exterior', 'lights_open'), 
        lights_open_comment: getFieldValue('exterior', 'lights_open_comment') || '',
        logo_clean: getFieldValue('exterior', 'logo_clean'), 
        logo_clean_comment: getFieldValue('exterior', 'logo_clean_comment') || '',
        parking_clean: getFieldValue('exterior', 'parking_clean'), 
        parking_clean_comment: getFieldValue('exterior', 'parking_clean_comment') || '',
        no_graffiti: getFieldValue('exterior', 'no_graffiti'), 
        no_graffiti_comment: getFieldValue('exterior', 'no_graffiti_comment') || ''
    },
    
    // Doors and Glass section
    doors_glass: {
        glass_clean: getFieldValue('doors_glass', 'clean'), 
        glass_clean_comment: getFieldValue('doors_glass', 'clean_comment') || '',
        door_handle: getFieldValue('doors', 'door_handle'), 
        door_handle_comment: getFieldValue('doors', 'door_handle_comment') || '',
        entrance_clean: getFieldValue('doors', 'entrance_clean'), 
        entrance_clean_comment: getFieldValue('doors', 'entrance_clean_comment') || ''
    },
    
    // Frontline section
    frontline: {
        areas_organized: getFieldValue('frontline', 'areas_organized'), 
        areas_organized_comment: getFieldValue('frontline', 'areas_organized_comment') || '',
        customers_greeted: getFieldValue('frontline', 'customers_greeted'), 
        customers_greeted_comment: getFieldValue('frontline', 'customers_greeted_comment') || '',
        menu_available: getFieldValue('frontline', 'menu_available'), 
        menu_available_comment: getFieldValue('frontline', 'menu_available_comment') || '',
        seven_steps: getFieldValue('frontline', 'seven_steps'), 
        seven_steps_comment: getFieldValue('frontline', 'seven_steps_comment') || '',
        tables_clean: getFieldValue('frontline', 'tables_clean'), 
        tables_clean_comment: getFieldValue('frontline', 'tables_clean_comment') || '',
        high_chairs: getFieldValue('frontline', 'high_chairs'), 
        high_chairs_comment: getFieldValue('frontline', 'high_chairs_comment') || '',
        no_damaged_tables: getFieldValue('frontline', 'no_damaged_tables'), 
        no_damaged_tables_comment: getFieldValue('frontline', 'no_damaged_tables_comment') || ''
    },
    
    // Restroom section
    restroom: {
        no_full_trash: getFieldValue('restroom', 'no_full_trash'), 
        no_full_trash_comment: getFieldValue('restroom', 'no_full_trash_comment') || '',
        soap_available: getFieldValue('restroom', 'soap_available'), 
        soap_available_comment: getFieldValue('restroom', 'soap_available_comment') || '',
        hand_dryer: getFieldValue('restroom', 'hand_dryer'), 
        hand_dryer_comment: getFieldValue('restroom', 'hand_dryer_comment') || ''
    },
    
    // Holding Heating section
    holding_heating: {
        product_temp: getFieldValue('holding', 'product_temp'), 
        product_temp_comment: getFieldValue('holding', 'product_temp_comment') || '',
        product_age: getFieldValue('holding', 'product_age'), 
        product_age_comment: getFieldValue('holding', 'product_age_comment') || '',
        check_product: getFieldValue('holding', 'check_product'), 
        check_product_comment: getFieldValue('holding', 'check_product_comment') || '',
        products_fresh: getFieldValue('holding', 'products_fresh'), 
        products_fresh_comment: getFieldValue('holding', 'products_fresh_comment') || '',
        internal_temp: getFieldValue('holding', 'internal_temp'), 
        internal_temp_comment: getFieldValue('holding', 'internal_temp_comment') || '',
        shortening_level: getFieldValue('holding', 'shortening_level'), 
        shortening_level_comment: getFieldValue('holding', 'shortening_level_comment') || '',
        check_shortening: getFieldValue('holding', 'check_shortening'), 
        check_shortening_comment: getFieldValue('holding', 'check_shortening_comment') || '',
        fryer_maintenance: getFieldValue('holding', 'fryer_maintenance'), 
        fryer_maintenance_comment: getFieldValue('holding', 'fryer_maintenance_comment') || '',
        use_tray: getFieldValue('holding', 'use_tray'), 
        use_tray_comment: getFieldValue('holding', 'use_tray_comment') || '',
        fry_basket: getFieldValue('holding', 'fry_basket'), 
        fry_basket_comment: getFieldValue('holding', 'fry_basket_comment') || '',
        fries_salted: getFieldValue('holding', 'fries_salted'), 
        fries_salted_comment: getFieldValue('holding', 'fries_salted_comment') || '',
        fries_cooking: getFieldValue('holding', 'fries_cooking'), 
        fries_cooking_comment: getFieldValue('holding', 'fries_cooking_comment') || '',
        buns_quality: getFieldValue('holding', 'buns_quality'), 
        buns_quality_comment: getFieldValue('holding', 'buns_quality_comment') || '',
        teflon_sheet: getFieldValue('holding', 'teflon_sheet'), 
        teflon_sheet_comment: getFieldValue('holding', 'teflon_sheet_comment') || '',
        bread_standard: getFieldValue('holding', 'bread_standard'), 
        bread_standard_comment: getFieldValue('holding', 'bread_standard_comment') || ''
    },
    
    // Thawing section
    thawing: {
        day_labels: getFieldValue('thawing', 'day_labels'), 
        day_labels_comment: getFieldValue('thawing', 'day_labels_comment') || '',
        no_tampering: getFieldValue('thawing', 'no_tampering'), 
        no_tampering_comment: getFieldValue('thawing', 'no_tampering_comment') || '',
        temp_range: getFieldValue('thawing', 'temp_range'), 
        temp_range_comment: getFieldValue('thawing', 'temp_range_comment') || '',
        no_overstock: getFieldValue('thawing', 'no_overstock'), 
        no_overstock_comment: getFieldValue('thawing', 'no_overstock_comment') || '',
        utensils_clean: getFieldValue('thawing', 'utensils_clean'), 
        utensils_clean_comment: getFieldValue('thawing', 'utensils_clean_comment') || '',
        sink_setup: getFieldValue('thawing', 'sink_setup'), 
        sink_setup_comment: getFieldValue('thawing', 'sink_setup_comment') || '',
        portion_standards: getFieldValue('thawing', 'portion_standards'), 
        portion_standards_comment: getFieldValue('thawing', 'portion_standards_comment') || '',
        sultan_sauce: getFieldValue('thawing', 'sultan_sauce'), 
        sultan_sauce_comment: getFieldValue('thawing', 'sultan_sauce_comment') || '',
        vegetables_date: getFieldValue('thawing', 'vegetables_date'), 
        vegetables_date_comment: getFieldValue('thawing', 'vegetables_date_comment') || '',
        follow_fifo: getFieldValue('thawing', 'follow_fifo'), 
        follow_fifo_comment: getFieldValue('thawing', 'follow_fifo_comment') || ''
    },
    
    // Burger Assembly section
    burger_assembly: {
        standard_setup: getFieldValue('burger', 'standard_setup'), 
        standard_setup_comment: getFieldValue('burger', 'standard_setup_comment') || '',
        sauce_spiral: getFieldValue('burger', 'sauce_spiral'), 
        sauce_spiral_comment: getFieldValue('burger', 'sauce_spiral_comment') || '',
        ingredients_order: getFieldValue('burger', 'ingredients_order'), 
        ingredients_order_comment: getFieldValue('burger', 'ingredients_order_comment') || ''
    },
    
    manager_signature: props.checklist?.manager_signature || ''
});

// Time options
const timeOptions = ['12PM', '5PM', '8PM', '11PM'];

// Create sections data for easier rendering
const sections = [
    {
        id: 'exterior',
        title: '🟦 EXTERIOR',
        color: 'bg-blue-50',
        questions: [
            { field: 'lights_open', label: 'Lights open outside (night time)', comment: 'lights_open_comment' },
            { field: 'logo_clean', label: 'Logo clean and no busted lights', comment: 'logo_clean_comment' },
            { field: 'parking_clean', label: 'Parking area clean', comment: 'parking_clean_comment' },
            { field: 'no_graffiti', label: 'No graffiti / no littering', comment: 'no_graffiti_comment' }
        ]
    },
    {
        id: 'doors_glass',
        title: '🟧 DOORS AND GLASS',
        color: 'bg-orange-50',
        questions: [
            { field: 'glass_clean', label: 'The glass areas are clean and no fingerprint', comment: 'glass_clean_comment' },
            { field: 'door_handle', label: 'Door handle not broken', comment: 'door_handle_comment' },
            { field: 'entrance_clean', label: 'Entrance area clean', comment: 'entrance_clean_comment' }
        ]
    },
    {
        id: 'frontline',
        title: '🟨 FRONTLINE AREA / TABLES AND CHAIRS',
        color: 'bg-yellow-50',
        questions: [
            { field: 'areas_organized', label: 'All areas are neat, complete stocked and organized', comment: 'areas_organized_comment' },
            { field: 'customers_greeted', label: 'Customers are greeted and served immediately', comment: 'customers_greeted_comment' },
            { field: 'menu_available', label: 'Menu available updated with the offer', comment: 'menu_available_comment' },
            { field: 'seven_steps', label: 'Provide 7 steps of good customer service (following scripting)', comment: 'seven_steps_comment' },
            { field: 'tables_clean', label: 'Tables are clean and free of bust', comment: 'tables_clean_comment' },
            { field: 'high_chairs', label: 'High chairs available and clean', comment: 'high_chairs_comment' },
            { field: 'no_damaged_tables', label: 'No damaged or movable tables', comment: 'no_damaged_tables_comment' }
        ]
    },
    {
        id: 'restroom',
        title: '🟩 REST ROOM',
        color: 'bg-green-50',
        questions: [
            { field: 'no_full_trash', label: 'No full trash and no bad odor', comment: 'no_full_trash_comment' },
            { field: 'soap_available', label: 'Soap available and tissue present', comment: 'soap_available_comment' },
            { field: 'hand_dryer', label: 'Hand dryer clean and not dusty', comment: 'hand_dryer_comment' }
        ]
    },
    {
        id: 'holding_heating',
        title: '🟥 HOLDING HEATING AREA / ROUND UP TOASTER',
        color: 'bg-red-50',
        questions: [
            { field: 'product_temp', label: 'Product still at 140°F holding temperature', comment: 'product_temp_comment' },
            { field: 'product_age', label: 'No product should be more than 30 min old', comment: 'product_age_comment' },
            { field: 'check_product', label: 'Check product very well (no overstock)', comment: 'check_product_comment' },
            { field: 'products_fresh', label: 'Products cooked fresh', comment: 'products_fresh_comment' },
            { field: 'internal_temp', label: 'Product internal temp 160–165°F', comment: 'internal_temp_comment' },
            { field: 'shortening_level', label: 'Shortening below 2 inch of fill line', comment: 'shortening_level_comment' },
            { field: 'check_shortening', label: 'Check shortening level all time', comment: 'check_shortening_comment' },
            { field: 'fryer_maintenance', label: 'Fryer skimmed and filtered if needed', comment: 'fryer_maintenance_comment' },
            { field: 'use_tray', label: 'Use tray for transferring product', comment: 'use_tray_comment' },
            { field: 'fry_basket', label: 'Fry basket clean', comment: 'fry_basket_comment' },
            { field: 'fries_salted', label: 'Fries well salted', comment: 'fries_salted_comment' },
            { field: 'fries_cooking', label: 'Fries cooked 1/3 basket, 5 min holding', comment: 'fries_cooking_comment' },
            { field: 'buns_quality', label: 'Buns golden brown and not undercooked', comment: 'buns_quality_comment' },
            { field: 'teflon_sheet', label: 'Teflon sheet changed every 3 months', comment: 'teflon_sheet_comment' },
            { field: 'bread_standard', label: '4 pcs standard for bread in toaster', comment: 'bread_standard_comment' }
        ]
    },
    {
        id: 'thawing',
        title: '🟦 THAWING AND TEMPERING',
        color: 'bg-blue-50',
        questions: [
            { field: 'day_labels', label: 'Day labels updated', comment: 'day_labels_comment' },
            { field: 'no_tampering', label: 'No day tampering', comment: 'no_tampering_comment' },
            { field: 'temp_range', label: 'Temp 36–40°F for chilled products (not 41°F)', comment: 'temp_range_comment' },
            { field: 'no_overstock', label: 'No overstock (enough for the day)', comment: 'no_overstock_comment' },
            { field: 'utensils_clean', label: 'Utensils clean properly', comment: 'utensils_clean_comment' },
            { field: 'sink_setup', label: '3-compartment sink set up properly', comment: 'sink_setup_comment' },
            { field: 'portion_standards', label: '2 inch for sandwich, 1 inch for salad', comment: 'portion_standards_comment' },
            { field: 'sultan_sauce', label: 'Sultan sauce ratio (5L ketchup, 4L mayo, 2oz mustard)', comment: 'sultan_sauce_comment' },
            { field: 'vegetables_date', label: 'Vegetables have receiving date', comment: 'vegetables_date_comment' },
            { field: 'follow_fifo', label: 'Always follow FIFO', comment: 'follow_fifo_comment' }
        ]
    },
    {
        id: 'burger_assembly',
        title: '🟪 BURGER ASSEMBLY',
        color: 'bg-purple-50',
        questions: [
            { field: 'standard_setup', label: 'Follow standard build-up setup', comment: 'standard_setup_comment' },
            { field: 'sauce_spiral', label: 'Sauce spiral and well placed', comment: 'sauce_spiral_comment' },
            { field: 'ingredients_order', label: 'Heel, sauce, pickles, lettuce placed first', comment: 'ingredients_order_comment' }
        ]
    }
];

// Unique restaurants (remove any duplicates)
const uniqueRestaurants = computed(() => {
    const seen = new Set();
    return props.restaurants.filter(restaurant => {
        const key = `${restaurant.name}-${restaurant.code}`;
        if (seen.has(key)) {
            return false;
        }
        seen.add(key);
        return true;
    });
});

// Computed compliance score
const totalScore = computed(() => {
    let totalQuestions = 0;
    let yesAnswers = 0;
    
    sections.forEach(section => {
        section.questions.forEach(question => {
            const value = form[section.id][question.field];
            if (value === 'yes' || value === 'no') {
                totalQuestions++;
                if (value === 'yes') yesAnswers++;
            }
        });
    });
    
    return totalQuestions > 0 ? Math.round((yesAnswers / totalQuestions) * 100) : 0;
});

// Action items (questions answered "No")
const actionItems = computed(() => {
    const items = [];
    sections.forEach(section => {
        section.questions.forEach(question => {
            const value = form[section.id][question.field];
            if (value === 'no') {
                items.push({
                    section: section.title,
                    question: question.label,
                    comment: form[section.id][question.comment] || 'No comment provided'
                });
            }
        });
    });
    return items;
});

// Validation function
const validateForm = () => {
    const errors = [];
    
    // Basic required fields
    if (!form.store_name) errors.push('Store Name is required');
    if (!form.date) errors.push('Date is required');
    if (!form.mod) errors.push('MOD is required');
    if (!form.time_option) errors.push('Time Option is required');
    if (!form.manager_signature) errors.push('Manager Signature is required');
    
    // Check for required comments only on "No" responses
    sections.forEach(section => {
        section.questions.forEach(question => {
            const value = form[section.id][question.field];
            const comment = form[section.id][question.comment];
            
            // Only require comment if answer is "no"
            if (value === 'no' && (!comment || comment.trim() === '')) {
                errors.push(`Comment required for "${question.label}" since it was marked as No`);
            }
        });
    });
    
    return errors;
};

// Check if form is valid (only basic fields required)
const isFormValid = computed(() => {
    // Only check basic required fields for submit button
    return form.store_name && form.date && form.mod && form.time_option && form.manager_signature;
});

// Insert test data function (admin only)
const insertTestData = () => {
    if (!confirm('Insert test data? This will fill all fields with sample data.')) return;
    
    form.store_name = props.restaurants[0]?.name || 'Test Store';
    form.mod = 'John Manager';
    form.time_option = '12PM';
    form.manager_signature = 'J.Manager';
    
    // Fill all sections with "Yes" and sample comments
    form.exterior = {
        lights_open: 1, lights_open_comment: 'All lights functional',
        logo_clean: 1, logo_clean_comment: 'Logo clean and bright',
        parking_clean: 1, parking_clean_comment: 'Parking area swept',
        no_graffiti: 1, no_graffiti_comment: 'No graffiti observed'
    };
    
    form.doors_glass = {
        glass_clean: 1, glass_clean_comment: 'Windows spotless',
        door_handle: 1, door_handle_comment: 'Handles intact',
        entrance_clean: 1, entrance_clean_comment: 'Entrance clean'
    };
    
    form.frontline = {
        areas_organized: 1, areas_organized_comment: 'Well organized',
        customers_greeted: 1, customers_greeted_comment: 'Excellent service',
        menu_available: 1, menu_available_comment: 'Menus visible',
        seven_steps: 1, seven_steps_comment: 'All steps followed',
        tables_clean: 1, tables_clean_comment: 'Tables wiped',
        high_chairs: 1, high_chairs_comment: 'High chairs clean',
        no_damaged_tables: 1, no_damaged_tables_comment: 'All furniture good'
    };
    
    form.restroom = {
        no_full_trash: 1, no_full_trash_comment: 'Trash emptied',
        soap_available: 1, soap_available_comment: 'Soap refilled',
        hand_dryer: 1, hand_dryer_comment: 'Dryer working'
    };
    
    form.holding_heating = {
        product_temp: 1, product_temp_comment: 'Temp in range',
        product_age: 1, product_age_comment: 'Fresh products',
        check_product: 1, check_product_comment: 'Quality checked',
        products_fresh: 1, products_fresh_comment: 'All fresh',
        internal_temp: 1, internal_temp_comment: 'Proper temp',
        shortening_level: 1, shortening_level_comment: 'Level good',
        check_shortening: 1, check_shortening_comment: 'Checked',
        fryer_maintenance: 1, fryer_maintenance_comment: 'Well maintained',
        use_tray: 1, use_tray_comment: 'Trays used',
        fry_basket: 1, fry_basket_comment: 'Baskets clean',
        fries_salted: 1, fries_salted_comment: 'Properly salted',
        fries_cooking: 1, fries_cooking_comment: 'Cooked well',
        buns_quality: 1, buns_quality_comment: 'Good quality',
        teflon_sheet: 1, teflon_sheet_comment: 'Sheet used',
        bread_standard: 1, bread_standard_comment: 'Standard met'
    };
    
    form.thawing = {
        day_labels: 1, day_labels_comment: 'Labels present',
        no_tampering: 1, no_tampering_comment: 'No tampering',
        temp_range: 1, temp_range_comment: 'Temp correct',
        no_overstock: 1, no_overstock_comment: 'Stock level good',
        utensils_clean: 1, utensils_clean_comment: 'Utensils clean',
        sink_setup: 1, sink_setup_comment: 'Sink proper',
        portion_standards: 1, portion_standards_comment: 'Standards met',
        sultan_sauce: 1, sultan_sauce_comment: 'Sauce good',
        vegetables_date: 1, vegetables_date_comment: 'Dates current',
        follow_fifo: 1, follow_fifo_comment: 'FIFO followed'
    };
    
    form.burger_assembly = {
        standard_setup: 1, standard_setup_comment: 'Setup correct',
        sauce_spiral: 1, sauce_spiral_comment: 'Spiral proper',
        ingredients_order: 1, ingredients_order_comment: 'Order correct'
    };
    
    alert('Test data inserted successfully!');
};

// Watch for store name changes
watch(() => form.store_name, () => {
    handleRestaurantChange();
});

// Submit form
const submitForm = () => {
    // Check location verification first
    if (!locationVerified.value) {
        alert('Please verify your location before submitting the form.');
        return;
    }
    
    // Only validate basic required fields and "No" comments
    const validationErrors = validateForm();
    if (validationErrors.length > 0) {
        alert('Please fix the following issues:\n\n' + validationErrors.join('\n'));
        return;
    }
    
    console.log('Submitting form data:', form.data());
    
    // Determine if we're editing or creating
    if (props.isEditing && props.checklist) {
        // Update existing checklist
        form.put(route('qsc-checklist.update', props.checklist.id), {
            onSuccess: (page) => {
                console.log('Form updated successfully');
                // Redirect handled by backend
            },
            onError: (errors) => {
                console.log('Form submission errors:', errors);
                let errorMessage = 'There were validation errors:\n\n';
                Object.keys(errors).forEach(key => {
                    errorMessage += `• ${errors[key]}\n`;
                });
                alert(errorMessage);
            }
        });
    } else {
        // Create new checklist
        form.post(route('qsc-checklist.store'), {
            onSuccess: (page) => {
                console.log('Form submitted successfully');
                // Redirect handled by backend
            },
            onError: (errors) => {
                console.log('Form submission errors:', errors);
                let errorMessage = 'There were validation errors:\n\n';
                Object.keys(errors).forEach(key => {
                    errorMessage += `• ${errors[key]}\n`;
                });
                alert(errorMessage);
            }
        });
    }
};

</script>

<template>
    <Head title="Q.S.C Checklist Form" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ props.isEditing ? 'Edit Q.S.C Checklist' : 'Q.S.C Checklist Form' }}
            </h2>
        </template>

        <!-- Location Verification Section -->
        <div v-if="showLocationModal && form.store_name" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg p-6 max-w-md mx-4">
                <div class="text-center">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                        <span class="text-2xl">📍</span>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Location Verification Required</h3>
                    <p class="text-sm text-gray-700 mb-6">You must verify your location at the restaurant before you can fill out this form. This ensures you are physically present at the location.</p>
                    
                    <button 
                        @click="verifyLocation"
                        :disabled="checkingLocation"
                        class="w-full mb-3 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <span v-if="checkingLocation">🔄 Verifying Location...</span>
                        <span v-else>📍 Verify My Location</span>
                    </button>
                    
                    <div v-if="locationError" class="mb-3 text-sm text-red-600">
                        {{ locationError }}
                    </div>
                    
                    <button 
                        @click="handleLocationCancel"
                        class="w-full px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                </div>
            </div>
        </div>

        <div class="py-6" :class="{ 'opacity-50 pointer-events-none': formLocked }">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Location Warning if needed -->
                <div v-if="formLocked" class="mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a 1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                Location verification is required to access this form. Please enable location services.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm text-gray-500">
                            <li><Link href="/qms/dashboard" class="hover:text-gray-700">Home</Link></li>
                            <li><span>›</span></li>
                            <li><Link href="/qms/qsc-checklist/report" class="hover:text-gray-700">Q.S.C Checklist</Link></li>
                            <li><span>›</span></li>
                            <li class="text-gray-900 font-medium">{{ props.isEditing ? 'Edit Form' : 'New Form' }}</li>
                        </ol>
                    </nav>
                </div>

                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <!-- Section 1: General Information -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                📋 Section 1 – General Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Name <span class="text-red-500">*</span></label>
                                    <select v-model="form.store_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Store</option>
                                        <option v-for="restaurant in uniqueRestaurants" :key="restaurant.id" :value="restaurant.name">
                                            {{ restaurant.name }}
                                        </option>
                                    </select>
                                    <div v-if="errors.store_name" class="text-red-500 text-sm mt-1">{{ errors.store_name }}</div>
                                </div>
                                
                                <!-- Location Verification -->
                                <div v-if="form.store_name && !locationVerified && !showLocationModal" class="md:col-span-2 lg:col-span-3">
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-blue-800 mb-2">📍 LOCATION VERIFICATION</h4>
                                        <p class="text-sm text-gray-700 mb-3">You must verify your location at the restaurant before you can submit this form. This ensures you are physically present at the location.</p>
                                        <button 
                                            type="button"
                                            @click="verifyLocation"
                                            :disabled="locationVerified || checkingLocation"
                                            class="px-4 py-2 text-sm font-medium rounded transition-colors"
                                            :class="locationVerified 
                                              ? 'bg-green-600 text-white cursor-default' 
                                              : 'bg-blue-600 hover:bg-blue-700 text-white'"
                                        >
                                            <span v-if="checkingLocation">🔄 Verifying your location...</span>
                                            <span v-else-if="locationVerified">✓ Location Verified at {{ form.store_name }}</span>
                                            <span v-else>📍 Verify My Location</span>
                                        </button>
                                        <div v-if="locationError" class="mt-2 text-sm" 
                                             :class="locationVerified ? 'text-green-600' : 'text-red-600'">
                                            {{ locationError }}
                                        </div>
                                        <p class="text-xs text-gray-600 mt-2">You must be within 200 meters of the restaurant to submit</p>
                                    </div>
                                </div>
                                
                                <!-- Location Verification Success -->
                                <div v-if="form.store_name && locationVerified" class="md:col-span-2 lg:col-span-3">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-green-800 mb-2">✅ LOCATION VERIFIED</h4>
                                        <p class="text-sm text-green-700 mb-2">{{ locationError }}</p>
                                        <p class="text-xs text-green-600">You can now fill out and submit the form.</p>
                                    </div>
                                </div>
                                
                                <!-- Location Verification Success -->
                                <div v-if="form.store_name && locationVerified" class="md:col-span-2 lg:col-span-3">
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-green-800 mb-2">✅ LOCATION VERIFIED</h4>
                                        <p class="text-sm text-green-700 mb-2">{{ locationError }}</p>
                                        <p class="text-xs text-green-600">You can now fill out and submit the form.</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Date <span class="text-red-500">*</span></label>
                                    <input type="date" v-model="form.date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                    <div v-if="errors.date" class="text-red-500 text-sm mt-1">{{ errors.date }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">MOD (Manager on Duty) <span class="text-red-500">*</span></label>
                                    <input type="text" v-model="form.mod" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Enter MOD name">
                                    <div v-if="errors.mod" class="text-red-500 text-sm mt-1">{{ errors.mod }}</div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Day</label>
                                    <input type="text" v-model="form.day" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Day of week">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Time Option <span class="text-red-500">*</span></label>
                                    <select v-model="form.time_option" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">Select Time</option>
                                        <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                                    </select>
                                    <div v-if="errors.time_option" class="text-red-500 text-sm mt-1">{{ errors.time_option }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Sections -->
                    <div v-for="section in sections" :key="section.id" class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200" :class="section.color">
                            <h3 class="text-lg font-semibold text-gray-900">{{ section.title }}</h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-6">
                                <div v-for="question in section.questions" :key="question.field" class="border border-gray-200 p-4 rounded-lg">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-900 flex-1">{{ question.label }}</h4>
                                    </div>
                                    <div class="flex items-start space-x-6">
                                        <div class="flex space-x-4">
                                            <label class="inline-flex items-center">
                                                <input type="radio" :name="`${section.id}_${question.field}`" value="yes" v-model="form[section.id][question.field]" class="form-radio text-green-600">
                                                <span class="ml-2 text-green-600 font-medium">Yes</span>
                                            </label>
                                            <label class="inline-flex items-center">
                                                <input type="radio" :name="`${section.id}_${question.field}`" value="no" v-model="form[section.id][question.field]" class="form-radio text-red-600">
                                                <span class="ml-2 text-red-600 font-medium">No</span>
                                            </label>
                                        </div>
                                        <div class="flex-1">
                                            <textarea v-model="form[section.id][question.comment]" 
                                                rows="2" 
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" 
                                                :placeholder="form[section.id][question.field] === 'no' ? 'Comment required for No response' : 'Optional comment'">
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manager Signature Section -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">🟫 Manager in Charge</h3>
                        </div>
                        <div class="p-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Manager Signature <span class="text-red-500">*</span></label>
                                <input type="text" v-model="form.manager_signature" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Manager signature and confirmation">
                                <div v-if="errors.manager_signature" class="text-red-500 text-sm mt-1">{{ errors.manager_signature }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Score and Summary -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg shadow-sm">
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">📊 Current Score</h3>
                                    <div class="text-3xl font-bold" :class="totalScore >= 80 ? 'text-green-600' : totalScore >= 60 ? 'text-yellow-600' : 'text-red-600'">
                                        {{ totalScore }}%
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">Compliance Score</p>
                                </div>
                                <div v-if="actionItems.length > 0">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-3">⚠️ Action Items Required</h3>
                                    <div class="text-2xl font-bold text-red-600">{{ actionItems.length }}</div>
                                    <p class="text-sm text-gray-600 mt-1">Items need attention</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 flex justify-between items-center">
                            <Link :href="route('qsc-checklist.report')" 
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                ← Back to Reports
                            </Link>
                            <div class="space-x-3 flex items-center">
                                <!-- Insert Test Data Button (Admin Only) - Hidden as per request -->
                                <!--
                                <button v-if="isAdmin" 
                                    type="button"
                                    @click="insertTestData"
                                    class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Insert Test Data
                                </button>
                                -->
                                <button type="submit" 
                                    :disabled="!isFormValid || form.processing"
                                    class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white font-bold py-2 px-6 rounded inline-flex items-center"
                                    :class="{ 'opacity-50 cursor-not-allowed': !isFormValid || form.processing }">
                                    <span v-if="form.processing">Submitting...</span>
                                    <span v-else>Submit Checklist</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>