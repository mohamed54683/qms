<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    errors: Object,
    restaurants: {
        type: Array,
        default: () => []
    },
    isAdmin: {
        type: Boolean,
        default: false
    }
});

// Form data using Inertia's useForm
const form = useForm({
    restaurant_name: '',
    mic: '',
    visit_date: new Date().toISOString().split('T')[0],
    purpose_of_visit: [],
    
    // Section 2: Customer / QSC
    oca_board_followed: null,
    oca_board_comments: '',
    staff_know_duty: null,
    staff_duty_comments: '',
    coaching_directing: null,
    coaching_comments: '',
    
    // Section 3: Cashier
    smile_greetings: null,
    smile_comments: '',
    suggestive_selling: null,
    suggestive_comments: '',
    offer_promotion: null,
    promotion_comments: '',
    thank_direction: null,
    thank_comments: '',
    
    // Section 4: Service Standards
    team_work_hustle: null,
    teamwork_comments: '',
    order_accuracy: null,
    accuracy_comments: '',
    service_time: null,
    service_comments: '',
    dine_in: null,
    dine_comments: '',
    take_out: null,
    takeout_comments: '',
    family: null,
    family_comments: '',
    delivery: null,
    delivery_comments: '',
    drive_thru: null,
    drive_comments: '',
    
    // Section 5: Financials
    weekly_schedule: null,
    schedule_comments: '',
    mod_financial_goal: null,
    financial_comments: '',
    sales_objectives: null,
    sales_comments: '',
    cash_policies: null,
    cash_comments: '',
    daily_waste: null,
    waste_comments: '',
    
    // Section 6: Quality / Pathing
    ttf_followed: null,
    ttf_comments: '',
    sandwich_assembly: null,
    assembly_comments: '',
    qsc_completed: null,
    qsc_comments: '',
    oil_standards: null,
    oil_comments: '',
    day_labels: null,
    labels_comments: '',
    equipment_working: null,
    equipment_comments: '',
    fryer_condition: null,
    fryer_comments: '',
    vegetable_prep: null,
    vegetable_comments: '',
    employee_appearance: null,
    appearance_comments: '',
    
    // Section 7: Cleanliness
    equipment_wrapped: null,
    wrapped_comments: '',
    sink_setup: null,
    sink_comments: '',
    sanitizer_standard: null,
    sanitizer_comments: '',
    dining_area_clean: null,
    dining_comments: '',
    restroom_clean: null,
    restroom_comments: '',
    
    // Section 8: Follow-Up
    last_visit_date: '',
    last_visit_summary: '',
    last_visit_update: '',
    other_follow_up: '',
    
    // Section 9: Observation Summary
    what_did_you_see: '',
    why_had_issue: '',
    how_to_improve: '',
    who_when_responsible: '',
    
    general_comments: '',
    status: 'Submitted',
    
    // Location fields for verification
    user_latitude: null,
    user_longitude: null,
    
    // Question images for action plans
    question_images: {},
});

// Location verification state
const locationVerified = ref(false);
const locationError = ref('');
const checkingLocation = ref(false);
const selectedRestaurant = ref(null);
const isSubmitting = ref(false); // Add submission state

// Watch for any form field changes and prevent them if location not verified
const originalFormValues = ref({});
// Optimized: Only watch specific fields instead of deep watching entire form
watch(() => [locationVerified.value, form.restaurant_name], ([newLocationVerified, newRestaurant], [oldLocationVerified, oldRestaurant]) => {
    if (!newLocationVerified && newRestaurant) {
        // Store original values on first restaurant selection
        if (!originalFormValues.value.stored) {
            originalFormValues.value = { ...form };
            originalFormValues.value.stored = true;
        }
    }
});

// Image upload state
const questionImages = ref({});
const questionImagePreviews = ref({});
const uploadingImages = ref(false);

// Handle image selection for questions
const handleQuestionImageSelect = (event, fieldName) => {
    // Prevent image upload if location is not verified
    if (!locationVerified.value) {
        event.target.value = ''; // Clear the file input
        return;
    }
    
    const files = Array.from(event.target.files);
    
    if (files.length === 0) return;
    
    console.log(`🖼️ Image selected for field "${fieldName}": ${files.length} files`);
    
    // Validate file types
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const invalidFiles = files.filter(file => !validTypes.includes(file.type));
    
    if (invalidFiles.length > 0) {
        alert('Please select only image files (JPEG, PNG, GIF)');
        return;
    }
    
    // Validate file sizes (max 5MB each)
    const oversizedFiles = files.filter(file => file.size > 5 * 1024 * 1024);
    if (oversizedFiles.length > 0) {
        alert('Please select images smaller than 5MB each');
        return;
    }
    
    // Store files for this field
    if (!questionImages.value[fieldName]) {
        questionImages.value[fieldName] = [];
    }
    
    files.forEach(file => {
        questionImages.value[fieldName].push(file);
        console.log(`  ✅ Added: ${file.name} (${file.size} bytes)`);
        
        // Create preview URL
        const reader = new FileReader();
        reader.onload = (e) => {
            if (!questionImagePreviews.value[fieldName]) {
                questionImagePreviews.value[fieldName] = [];
            }
            questionImagePreviews.value[fieldName].push({
                file: file,
                url: e.target.result,
                name: file.name
            });
        };
        reader.readAsDataURL(file);
    });
    
    console.log(`Total images for "${fieldName}": ${questionImages.value[fieldName].length}`);
    console.log('All questionImages:', questionImages.value);
    
    // Clear the input so same file can be selected again
    event.target.value = '';
};

// Remove image from question
const removeQuestionImage = (fieldName, index) => {
    if (questionImages.value[fieldName]) {
        questionImages.value[fieldName].splice(index, 1);
        questionImagePreviews.value[fieldName].splice(index, 1);
        
        if (questionImages.value[fieldName].length === 0) {
            delete questionImages.value[fieldName];
            delete questionImagePreviews.value[fieldName];
        }
    }
};

// Open camera for photo capture
const openCamera = (fieldName) => {
    // Prevent camera access if location is not verified
    if (!locationVerified.value) {
        return;
    }
    
    // Create a hidden file input with camera capture
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.capture = 'environment'; // Use back camera on mobile
    input.multiple = true;
    
    input.onchange = (event) => {
        handleQuestionImageSelect(event, fieldName);
    };
    
    input.click();
};

// Get selected restaurant with coordinates
const getSelectedRestaurant = computed(() => {
    return props.restaurants.find(r => r.name === form.restaurant_name);
});

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
    // Prevent multiple concurrent location requests
    if (checkingLocation.value) {
        return;
    }
    
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
                enableHighAccuracy: false, // Changed: Use less accurate but faster positioning
                timeout: 5000, // Reduced timeout from 10000 to 5000ms
                maximumAge: 60000 // Allow cached position within 1 minute
            }
        );
    } else {
        checkingLocation.value = false;
        locationError.value = 'Geolocation not supported by your browser';
    }
};

// Reset location when restaurant changes
const handleRestaurantChange = () => {
    locationVerified.value = false;
    locationError.value = '';
    form.user_latitude = null;
    form.user_longitude = null;
};

// Handle Yes/No answer change
const handleAnswerChange = (fieldName, commentField, value) => {
    // Prevent form changes if location is not verified
    if (!locationVerified.value) {
        return;
    }
    
    form[fieldName] = value;
    
    // If answer is "Yes", automatically set comment to "DONE"
    if (value === true) {
        form[commentField] = 'DONE';
    } else if (value === false) {
        // If answer is "No", clear the comment to let user enter their own
        if (form[commentField] === 'DONE') {
            form[commentField] = '';
        }
    }
};

// State management for confirmation flow
const showConfirmation = ref(false);
const submittedVisitId = ref(null);
const actionItemsPreview = ref([]);

// Options
const micOptions = ['Morning', 'Evening'];
const purposeOptions = [
    'Quality Audit',
    'Operational Assessment', 
    'Training Audit',
    'Measurement & Coaching'
];

// Create sections data for easier rendering
const sections = [
    {
        id: 2,
        title: '👥 Section 2 – Customer / QSC',
        color: 'bg-green-50',
        questions: [
            { field: 'oca_board_followed', label: 'OCA Board is Completely Followed/Communicated', comment: 'oca_board_comments' },
            { field: 'staff_know_duty', label: 'Staff Know their Side Duty', comment: 'staff_duty_comments' },
            { field: 'coaching_directing', label: 'Coaching and Directing', comment: 'coaching_comments' }
        ]
    },
    {
        id: 3,
        title: '💰 Section 3 – Cashier',
        color: 'bg-yellow-50',
        questions: [
            { field: 'smile_greetings', label: 'Smile and Friendly Greetings', comment: 'smile_comments' },
            { field: 'suggestive_selling', label: 'Suggestive Selling', comment: 'suggestive_comments' },
            { field: 'offer_promotion', label: 'Offer new Promotion', comment: 'promotion_comments' },
            { field: 'thank_direction', label: 'Saying Thank You and Provides Direction', comment: 'thank_comments' }
        ]
    },
    {
        id: 4,
        title: '🚀 Section 4 – Service Standards',
        color: 'bg-blue-50',
        questions: [
            { field: 'team_work_hustle', label: 'Team Work and Hustle', comment: 'teamwork_comments' },
            { field: 'order_accuracy', label: 'Order Accuracy', comment: 'accuracy_comments' },
            { field: 'service_time', label: 'Service Time', comment: 'service_comments' },
            { field: 'dine_in', label: 'Dine In', comment: 'dine_comments' },
            { field: 'take_out', label: 'Take Out', comment: 'takeout_comments' },
            { field: 'family', label: 'Family', comment: 'family_comments' },
            { field: 'delivery', label: 'Delivery', comment: 'delivery_comments' },
            { field: 'drive_thru', label: 'Drive Thru', comment: 'drive_comments' }
        ]
    },
    {
        id: 5,
        title: '💼 Section 5 – Financials',
        color: 'bg-purple-50',
        questions: [
            { field: 'weekly_schedule', label: 'Weekly Schedule and Overtime', comment: 'schedule_comments' },
            { field: 'mod_financial_goal', label: 'MOD aware of Financial Goal', comment: 'financial_comments' },
            { field: 'sales_objectives', label: 'Sales (Cashier Objectives)', comment: 'sales_comments' },
            { field: 'cash_policies', label: 'Cash Policies followed Spot Check', comment: 'cash_comments' },
            { field: 'daily_waste', label: 'Daily Waste Followed Properly (Daily)', comment: 'waste_comments' }
        ]
    },
    {
        id: 6,
        title: '🔧 Section 6 – Quality / Pathing',
        color: 'bg-indigo-50',
        questions: [
            { field: 'ttf_followed', label: 'TTF followed properly', comment: 'ttf_comments' },
            { field: 'sandwich_assembly', label: 'Sandwich Assembly being followed', comment: 'assembly_comments' },
            { field: 'qsc_completed', label: 'QSC was completed and followed properly', comment: 'qsc_comments' },
            { field: 'oil_standards', label: 'Oil/Shortening Meets Standards', comment: 'oil_comments' },
            { field: 'day_labels', label: 'Day Labels updated', comment: 'labels_comments' },
            { field: 'equipment_working', label: 'Equipments are all working properly', comment: 'equipment_comments' },
            { field: 'fryer_condition', label: 'Fryer Basket in Good Condition (not broken or rusty)', comment: 'fryer_comments' },
            { field: 'vegetable_prep', label: 'Vegetable Preparation meets standards and Salad Prep', comment: 'vegetable_comments' },
            { field: 'employee_appearance', label: 'Employee Appearance well groomed', comment: 'appearance_comments' }
        ]
    },
    {
        id: 7,
        title: '🧹 Section 7 – Cleanliness',
        color: 'bg-teal-50',
        questions: [
            { field: 'equipment_wrapped', label: 'Equipment are wrapped and hang', comment: 'wrapped_comments' },
            { field: 'sink_setup', label: 'Compartment sink are set-up properly', comment: 'sink_comments' },
            { field: 'sanitizer_standard', label: 'Sanitizer meets standard', comment: 'sanitizer_comments' },
            { field: 'dining_area_clean', label: 'Dining Area/Family area no busting', comment: 'dining_comments' },
            { field: 'restroom_clean', label: 'CR or handwash area has tissue and clean', comment: 'restroom_comments' }
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

// Computed score
const totalScore = computed(() => {
    let totalQuestions = 0;
    let yesAnswers = 0;
    
    sections.forEach(section => {
        section.questions.forEach(question => {
            if (form[question.field] === true || form[question.field] === false) {
                totalQuestions++;
                if (form[question.field] === true) yesAnswers++;
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
            if (form[question.field] === false) {
                items.push({
                    section: section.title,
                    question: question.label,
                    comment: form[question.comment] || 'No comment provided'
                });
            }
        });
    });
    return items;
});

// Validation function to check all required fields
const validateForm = () => {
    const errors = [];
    
    // Basic required fields
    if (!form.restaurant_name) errors.push('Restaurant is required');
    if (!form.mic) errors.push('MIC is required');
    if (!form.visit_date) errors.push('Visit Date is required');
    if (!form.purpose_of_visit || form.purpose_of_visit.length === 0) errors.push('Purpose of Visit is required');
    
    // Location verification required (DISABLED FOR TESTING)
    // if (!locationVerified.value) {
    //     errors.push('Location verification is required - please verify you are at the restaurant');
    // }
    
    // All section questions (Yes/No fields)
    sections.forEach(section => {
        section.questions.forEach(question => {
            if (form[question.field] === null) {
                errors.push(`${question.label} is required`);
            }
            if (!form[question.comment] || form[question.comment].trim() === '') {
                errors.push(`Comment for "${question.label}" is required`);
            }
        });
    });
    
    // Section 8 fields
    if (!form.last_visit_date) errors.push('Last Visit Date is required');
    if (!form.last_visit_summary || form.last_visit_summary.trim() === '') errors.push('Last Visit Summary is required');
    if (!form.last_visit_update || form.last_visit_update.trim() === '') errors.push('Updates on Previous Issues is required');
    if (!form.other_follow_up || form.other_follow_up.trim() === '') errors.push('Other Follow-up Items is required');
    
    // Section 9 fields
    if (!form.what_did_you_see || form.what_did_you_see.trim() === '') errors.push('What did you see? is required');
    if (!form.why_had_issue || form.why_had_issue.trim() === '') errors.push('Why did it happen? is required');
    if (!form.how_to_improve || form.how_to_improve.trim() === '') errors.push('How to improve? is required');
    if (!form.who_when_responsible || form.who_when_responsible.trim() === '') errors.push('Who & When? is required');
    
    // General comments
    if (!form.general_comments || form.general_comments.trim() === '') errors.push('Additional Comments is required');
    
    return errors;
};

// Check if form is valid
const isFormValid = computed(() => {
    return validateForm().length === 0;
});

// Insert test data function (admin only)
const insertTestData = () => {
    if (!confirm('Insert test data? This will fill all fields with sample data.')) return;
    
    form.restaurant_name = props.restaurants[0]?.name || 'Test Restaurant';
    form.mic = 'Morning';
    form.purpose_of_visit = ['Routine Visit', 'Quality Check'];
    
    // Fill all sections with "Yes" and sample comments
    form.oca_board_followed = 1;
    form.oca_board_comments = 'Board properly followed';
    form.staff_know_duty = 1;
    form.staff_duty_comments = 'Staff well trained';
    form.coaching_directing = 1;
    form.coaching_comments = 'Good coaching observed';
    
    form.smile_greetings = 1;
    form.smile_comments = 'Friendly greetings';
    form.suggestive_selling = 1;
    form.suggestive_comments = 'Good upselling';
    form.offer_promotion = 1;
    form.promotion_comments = 'Promotions offered';
    form.thank_direction = 1;
    form.thank_comments = 'Polite thank you';
    
    form.team_work_hustle = 1;
    form.teamwork_comments = 'Excellent teamwork';
    form.order_accuracy = 1;
    form.accuracy_comments = 'Orders accurate';
    form.service_time = 1;
    form.service_comments = 'Fast service';
    form.dine_in = 1;
    form.dine_comments = 'Good dine-in';
    form.take_out = 1;
    form.takeout_comments = 'Takeout handled well';
    form.family = 1;
    form.family_comments = 'Family friendly';
    
    form.product_temp = 1;
    form.product_comments = 'Temperatures correct';
    form.product_presentation = 1;
    form.presentation_comments = 'Well presented';
    form.fresh_hot = 1;
    form.fresh_comments = 'Fresh products';
    
    form.external_clean = 1;
    form.external_comments = 'Exterior clean';
    form.internal_clean = 1;
    form.internal_comments = 'Interior clean';
    form.kitchen_clean = 1;
    form.kitchen_comments = 'Kitchen spotless';
    form.staff_grooming = 1;
    form.grooming_comments = 'Staff well groomed';
    form.stock_fresh = 1;
    form.stock_comments = 'Fresh stock';
    form.cooling_equipment = 1;
    form.cooling_comments = 'Equipment working';
    
    form.manager_office = 1;
    form.office_comments = 'Office organized';
    form.updated_schedule = 1;
    form.schedule_comments = 'Schedule current';
    form.food_waste = 1;
    form.waste_comments = 'Waste minimized';
    
    alert('Test data inserted successfully!');
};

// Submit form using Inertia with file upload support
const submitForm = () => {
    console.log('🚀 submitForm called!');
    
    // Prevent multiple submissions
    if (isSubmitting.value) {
        console.log('⏳ Already submitting, ignoring...');
        return;
    }
    
    // Prevent form submission if location is not verified
    if (!locationVerified.value) {
        alert('Please verify your location before submitting the form.');
        return;
    }
    
    const validationErrors = validateForm();
    console.log('Validation errors:', validationErrors);
    if (validationErrors.length > 0) {
        alert('Please fill in all required fields:\n\n' + validationErrors.join('\n'));
        return;
    }
    
    isSubmitting.value = true; // Set submitting state
    
    console.log('📤 Preparing form submission with images...');
    
    // Count images
    let imageCount = 0;
    console.log('🖼️ Question Images Object:', questionImages.value);
    
    Object.keys(questionImages.value).forEach(fieldName => {
        const images = questionImages.value[fieldName];
        console.log(`  Field "${fieldName}":`, images);
        if (images && images.length > 0) {
            imageCount += images.length;
            images.forEach((image, index) => {
                console.log(`    Will upload [${index}]:`, image.name, image.size, 'bytes');
            });
        }
    });
    
    console.log(`📤 Submitting form with ${imageCount} images using Inertia`);
    
    // Submit using Inertia with transform to add files at submission time
    form.transform((data) => {
        // Clone the data
        const formData = { ...data };
        
        // Add flattened images
        Object.keys(questionImages.value).forEach(fieldName => {
            const images = questionImages.value[fieldName];
            if (images && images.length > 0) {
                images.forEach((image, index) => {
                    const key = `question_images_${fieldName}_${index}`;
                    formData[key] = image;
                });
            }
        });
        
        return formData;
    }).post(route('store-visits.store'), {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: (response) => {
            console.log('✅ Form submitted successfully');
            isSubmitting.value = false; // Reset submission state
        },
        onError: (errors) => {
            console.error('❌ Form submission errors:', errors);
            alert('Error submitting form. Please check the fields and try again.');
            isSubmitting.value = false; // Reset submission state on error
        },
        onFinish: () => {
            console.log('📋 Form submission finished');
            isSubmitting.value = false; // Always reset submission state
        }
    });
};

// Generate preview of action items that will be created
const generateActionItemsPreview = () => {
    const actionItems = [];
    
    // Check all question fields for "No" answers
    sections.forEach(section => {
        section.questions.forEach(question => {
            if (form[question.field] === false) {
                actionItems.push({
                    question: question.label,
                    field: question.field,
                    comment: form[question.comment] || ''
                });
            }
        });
    });
    
    actionItemsPreview.value = actionItems;
};

// Confirm and create action plans
const confirmAndCreateActionPlans = () => {
    if (!submittedVisitId.value) return;
    
    const confirmForm = useForm({});
    confirmForm.post(route('store-visits.confirm-action-plans', submittedVisitId.value), {
        onSuccess: () => {
            // Redirect to index with success message
        }
    });
};

// Save as draft
const saveDraft = () => {
    // Prevent multiple submissions
    if (isSubmitting.value) {
        return;
    }
    
    // Prevent saving draft if location is not verified
    if (!locationVerified.value) {
        alert('Please verify your location before saving the form.');
        return;
    }
    
    isSubmitting.value = true;
    form.status = 'Draft';
    form.post(route('store-visits.store'), {
        onSuccess: () => {
            isSubmitting.value = false;
        },
        onError: () => {
            isSubmitting.value = false;
        },
        onFinish: () => {
            isSubmitting.value = false;
        }
    });
};
</script>

<template>
    <Head title="New Store Visit Report" />
    
    <DefaultAuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📝 New Store Visit Report
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Navigation Breadcrumb -->
                <div class="mb-6">
                    <nav class="flex" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-2 text-sm text-gray-500">
                            <li><Link href="/dashboard" class="hover:text-gray-700">Home</Link></li>
                            <li><span>›</span></li>
                            <li><Link href="/store-visits" class="hover:text-gray-700">Store Visits</Link></li>
                            <li><span>›</span></li>
                            <li class="text-gray-900 font-medium">New Report</li>
                        </ol>
                    </nav>
                </div>

                <form @submit.prevent="submitForm" :class="[
                    'space-y-6 relative',
                    !locationVerified && form.restaurant_name ? 'opacity-60' : ''
                ]">
                    
                    <!-- Location Verification Overlay -->
                    <div v-if="!locationVerified && form.restaurant_name" 
                         class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center">
                        <div class="bg-white p-8 rounded-lg shadow-2xl border-2 border-red-500 text-center max-w-md mx-4">
                            <div class="text-red-600 mb-4">
                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3">🚫 Location Verification Required</h3>
                            <p class="text-sm text-gray-700 mb-6">You must verify your location at the restaurant before you can fill out this form. This ensures you are physically present at the location.</p>
                            <button type="button" 
                                    @click="verifyLocation"
                                    :disabled="checkingLocation"
                                    class="px-6 py-3 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                <span v-if="checkingLocation">� Checking Location...</span>
                                <span v-else>�📍 Verify My Location</span>
                            </button>
                            <p v-if="locationError" class="mt-3 text-sm" :class="locationVerified ? 'text-green-600' : 'text-red-600'">
                                {{ locationError }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Section 1: General Information -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                📋 Section 1 – General Information
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant * ({{ uniqueRestaurants.length }} locations)</label>
                                    <select v-model="form.restaurant_name" 
                                           @change="handleRestaurantChange"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                           required>
                                        <option value="">Select Restaurant ({{ uniqueRestaurants.length }} available)</option>
                                        <option v-for="restaurant in uniqueRestaurants" 
                                               :key="restaurant.id" 
                                               :value="restaurant.name">
                                            {{ restaurant.code }} - {{ restaurant.name }}
                                        </option>
                                    </select>
                                    <p v-if="form.errors.restaurant_name" class="mt-1 text-sm text-red-600">{{ form.errors.restaurant_name }}</p>
                                    
                                    <!-- Location Verification -->
                                    <div v-if="form.restaurant_name" class="mt-2">
                                        <button type="button"
                                               @click="verifyLocation"
                                               :disabled="checkingLocation"
                                               class="w-full px-3 py-2 text-sm font-medium rounded-md transition-colors"
                                               :class="locationVerified ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-50'">
                                            <span v-if="checkingLocation">📍 Checking location...</span>
                                            <span v-else-if="locationVerified">✅ Location Verified</span>
                                            <span v-else>📍 Verify Location</span>
                                        </button>
                                        <p v-if="locationError" 
                                           class="mt-1 text-xs"
                                           :class="locationVerified ? 'text-green-600' : 'text-red-600'">
                                            {{ locationError }}
                                        </p>
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">MIC *</label>
                                    <select v-model="form.mic" 
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                           required>
                                        <option value="">Select Shift</option>
                                        <option v-for="mic in micOptions" :key="mic" :value="mic">{{ mic }}</option>
                                    </select>
                                    <p v-if="form.errors.mic" class="mt-1 text-sm text-red-600">{{ form.errors.mic }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Visit Date *</label>
                                    <input v-model="form.visit_date" 
                                          type="date"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500" 
                                          required>
                                    <p v-if="form.errors.visit_date" class="mt-1 text-sm text-red-600">{{ form.errors.visit_date }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Auto Score</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                                        <span class="text-xl font-bold" :class="totalScore >= 80 ? 'text-green-600' : totalScore >= 60 ? 'text-yellow-600' : 'text-red-600'">
                                            {{ totalScore }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Verification Required Warning -->
                            <div v-if="!locationVerified && form.restaurant_name" class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-yellow-800">
                                            ⚠️ Location verification required
                                        </p>
                                        <p class="mt-1 text-sm text-yellow-700">
                                            Please verify your location at the restaurant before filling the form. Click the "📍 Verify Location" button above.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-6">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Purpose of Visit *</label>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    <label v-for="purpose in purposeOptions" :key="purpose" class="flex items-center" :class="!locationVerified ? 'opacity-50 cursor-not-allowed' : ''">
                                        <input type="checkbox" 
                                              :value="purpose" 
                                              v-model="form.purpose_of_visit"
                                              :disabled="!locationVerified"
                                              class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                        <span class="ml-2 text-sm text-gray-700">{{ purpose }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.purpose_of_visit" class="mt-1 text-sm text-red-600">{{ form.errors.purpose_of_visit }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Sections 2-7 -->
                    <div v-for="section in sections" :key="section.id" class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200" :class="section.color">
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ section.title }}
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div v-for="question in section.questions" :key="question.field" class="space-y-4">
                                <!-- Question Grid -->
                                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 items-center">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">{{ question.label }} <span class="text-red-500">*</span></p>
                                    </div>
                                    <div class="flex space-x-4">
                                        <button type="button" 
                                               @click="!locationVerified ? null : handleAnswerChange(question.field, question.comment, true)"
                                               :disabled="!locationVerified"
                                               :class="[
                                                   form[question.field] === true ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-700',
                                                   !locationVerified ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-80'
                                               ]"
                                               class="px-4 py-2 rounded-md font-medium transition-colors disabled:cursor-not-allowed">
                                            Yes
                                        </button>
                                        <button type="button" 
                                               @click="!locationVerified ? null : handleAnswerChange(question.field, question.comment, false)"
                                               :disabled="!locationVerified"
                                               :class="[
                                                   form[question.field] === false ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700',
                                                   !locationVerified ? 'opacity-50 cursor-not-allowed' : 'hover:opacity-80'
                                               ]"
                                               class="px-4 py-2 rounded-md font-medium transition-colors disabled:cursor-not-allowed">
                                            No
                                        </button>
                                    </div>
                                    <div>
                                        <textarea v-model="form[question.comment]" 
                                                 rows="4" 
                                                 placeholder="Comments... (Required)"
                                                 required
                                                 :disabled="!locationVerified"
                                                 class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical disabled:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-60"></textarea>
                                    </div>
                                </div>
                                
                                <!-- Image Upload for "No" Answers -->
                                <div v-if="form[question.field] === false" class="bg-red-50 border border-red-200 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-sm font-medium text-red-800">📷 Evidence Images (Optional)</h4>
                                        <span class="text-xs text-red-600">Upload images to support this finding</span>
                                    </div>
                                    
                                    <!-- Upload Buttons -->
                                    <div class="flex space-x-3 mb-4">
                                        <button type="button"
                                               @click="!locationVerified ? null : openCamera(question.field)"
                                               :disabled="!locationVerified"
                                               class="flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed disabled:bg-gray-400">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            Take Photo
                                        </button>
                                        
                                        <label :class="[
                                            'flex items-center px-4 py-2 text-white text-sm rounded-md cursor-pointer',
                                            !locationVerified ? 'bg-gray-400 cursor-not-allowed opacity-50' : 'bg-red-600 hover:bg-red-700'
                                        ]">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Upload Images
                                            <input type="file"
                                                   accept="image/*"
                                                   multiple
                                                   :disabled="!locationVerified"
                                                   @change="handleQuestionImageSelect($event, question.field)"
                                                   class="hidden">
                                        </label>
                                    </div>
                                    
                                    <!-- Image Previews -->
                                    <div v-if="questionImagePreviews[question.field] && questionImagePreviews[question.field].length > 0" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                        <div v-for="(preview, index) in questionImagePreviews[question.field]" :key="index" class="relative group">
                                            <img :src="preview.url" 
                                                 :alt="preview.name"
                                                 class="w-full h-24 object-cover rounded-lg border border-gray-300">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all rounded-lg flex items-center justify-center">
                                                <button type="button"
                                                       @click="removeQuestionImage(question.field, index)"
                                                       class="opacity-0 group-hover:opacity-100 bg-red-600 text-white rounded-full p-1 hover:bg-red-700 transition-all">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-600 mt-1 truncate">{{ preview.name }}</p>
                                        </div>
                                    </div>
                                    
                                    <!-- No Images Placeholder -->
                                    <div v-else class="text-center py-6 text-gray-500">
                                        <svg class="w-12 h-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-sm">No images uploaded yet</p>
                                        <p class="text-xs">Upload evidence images to support this finding</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 8: Follow-Up -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-orange-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                📅 Section 8 – Follow-Up
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Last Visit Date <span class="text-red-500">*</span></label>
                                    <input v-model="form.last_visit_date" 
                                          type="date" 
                                          required
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Last Visit Summary <span class="text-red-500">*</span></label>
                                <textarea v-model="form.last_visit_summary" 
                                         rows="6" 
                                         placeholder="Summary of previous visit findings... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Updates on Previous Issues <span class="text-red-500">*</span></label>
                                <textarea v-model="form.last_visit_update" 
                                         rows="6" 
                                         placeholder="Updates and progress on previous action items... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Other Follow-up Items <span class="text-red-500">*</span></label>
                                <textarea v-model="form.other_follow_up" 
                                         rows="6" 
                                         placeholder="Additional follow-up items or notes... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 9: Observation Summary -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                            <h3 class="text-lg font-semibold text-gray-900">
                                📊 Section 9 – Observation Summary
                            </h3>
                        </div>
                        <div class="p-6 space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">What did you see? <span class="text-red-500">*</span></label>
                                <textarea v-model="form.what_did_you_see" 
                                         rows="6" 
                                         placeholder="Describe what you observed during this visit... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Why did it happen? <span class="text-red-500">*</span></label>
                                <textarea v-model="form.why_had_issue" 
                                         rows="6" 
                                         placeholder="Root cause analysis of any issues identified... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">How to improve? <span class="text-red-500">*</span></label>
                                <textarea v-model="form.how_to_improve" 
                                         rows="6" 
                                         placeholder="Recommendations and solutions for improvement... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Who & When? <span class="text-red-500">*</span></label>
                                <textarea v-model="form.who_when_responsible" 
                                         rows="6" 
                                         placeholder="Responsible person and timeline for implementation... (Required)"
                                         required
                                         class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Action Items Preview (if any "No" answers) -->
                    <div v-if="actionItems.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="px-6 py-4 border-b border-yellow-200">
                            <h3 class="text-lg font-semibold text-yellow-900">
                                ⚠️ Action Items Required ({{ actionItems.length }})
                            </h3>
                            <p class="text-sm text-yellow-700 mt-1">These items will automatically generate action plans that require follow-up:</p>
                        </div>
                        <div class="p-6">
                            <div class="space-y-3">
                                <div v-for="(item, index) in actionItems" :key="index" class="flex items-start space-x-3 text-sm">
                                    <span class="flex-shrink-0 w-6 h-6 bg-yellow-500 text-white rounded-full flex items-center justify-center text-xs font-bold">{{ index + 1 }}</span>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ item.question }}</p>
                                        <p class="text-gray-600">{{ item.section }}</p>
                                        <p class="text-gray-500 italic">{{ item.comment }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Comments -->
                    <div class="bg-white rounded-lg shadow-sm">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">💬 Additional Comments <span class="text-red-500">*</span></h3>
                        </div>
                        <div class="p-6">
                            <textarea v-model="form.general_comments" 
                                     rows="8" 
                                     placeholder="Any additional comments or observations... (Required)"
                                     required
                                     :disabled="!locationVerified"
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 resize-vertical disabled:bg-gray-100 disabled:cursor-not-allowed"></textarea>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-6">
                                <div>
                                    <p class="text-sm text-gray-600">Overall Score:</p>
                                    <p class="text-2xl font-bold" :class="totalScore >= 80 ? 'text-green-600' : totalScore >= 60 ? 'text-yellow-600' : 'text-red-600'">
                                        {{ totalScore }}%
                                    </p>
                                </div>
                                <div v-if="actionItems.length > 0">
                                    <p class="text-sm text-yellow-600">Action Items:</p>
                                    <p class="text-xl font-bold text-yellow-600">{{ actionItems.length }}</p>
                                </div>
                                <div>
                                    <p class="text-sm" :class="isFormValid ? 'text-green-600' : 'text-red-600'">
                                        {{ isFormValid ? '✅ All fields completed' : '⚠️ Missing required fields' }}
                                    </p>
                                    <p v-if="!isFormValid" class="text-xs text-red-500">{{ validateForm().length }} field(s) required</p>
                                </div>
                            </div>
                            <div class="flex space-x-4">
                                <Link href="/store-visits" 
                                     class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors">
                                    Cancel
                                </Link>
                                <!-- Insert Test Data Button (Admin Only) - Hidden as per request -->
                                <!--
                                <button v-if="isAdmin" 
                                       type="button"
                                       @click="insertTestData"
                                       class="px-6 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 transition-colors inline-flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Insert Test Data
                                </button>
                                -->
                                <button type="submit" 
                                       :disabled="form.processing || isSubmitting || !isFormValid || !locationVerified"
                                       class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 transition-colors">
                                    {{ form.processing || isSubmitting ? 'Submitting...' : 'Submit Report' }}
                                </button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div v-if="showConfirmation" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    ✅ Store Visit Report Submitted Successfully!
                                </h3>
                                <div class="mt-4">
                                    <p class="text-sm text-gray-500 mb-4">
                                        Your store visit report has been saved. 
                                        <span v-if="actionItemsPreview.length > 0">
                                            We found <strong>{{ actionItemsPreview.length }}</strong> items that require follow-up action plans.
                                        </span>
                                        <span v-else>
                                            Great job! All items passed inspection - no action plans needed.
                                        </span>
                                    </p>
                                    
                                    <!-- Action Items Preview -->
                                    <div v-if="actionItemsPreview.length > 0" class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                                        <h4 class="font-medium text-yellow-800 mb-2">📋 Action Plans Will Be Created For:</h4>
                                        <ul class="space-y-1">
                                            <li v-for="item in actionItemsPreview" :key="item.field" class="text-sm text-yellow-700">
                                                • {{ item.question }}
                                                <span v-if="item.comment" class="text-yellow-600 italic"> - {{ item.comment }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <h4 class="font-medium text-blue-800 mb-2">🎯 What happens next?</h4>
                                        <ul class="text-sm text-blue-700 space-y-1">
                                            <li v-if="actionItemsPreview.length > 0">
                                                • Click "Confirm and Create Action Plans" to automatically generate detailed action plans
                                            </li>
                                            <li v-else>
                                                • Click "Complete" to finish the process
                                            </li>
                                            <li>• Action plans will be assigned priorities and due dates</li>
                                            <li>• Restaurant managers will be notified to implement the required actions</li>
                                            <li>• Progress tracking will be available in the Action Plans section</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button v-if="actionItemsPreview.length > 0"
                                @click="confirmAndCreateActionPlans"
                                type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            🎯 Confirm and Create Action Plans ({{ actionItemsPreview.length }})
                        </button>
                        <button v-else
                                @click="$inertia.visit(route('store-visits.index'))"
                                type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            ✅ Complete
                        </button>
                        <button @click="$inertia.visit(route('store-visits.index'))"
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            📋 View All Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
/* Custom animations */
.transition-colors {
    transition-property: color, background-color, border-color, text-decoration-color, fill, stroke;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

button:disabled {
    cursor: not-allowed;
}

.hover\:opacity-80:hover:not(:disabled) {
    opacity: 0.8;
}
</style>