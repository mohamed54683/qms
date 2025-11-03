<script setup>
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted } from 'vue';
import { usePermissions } from '@/Composables/usePermissions';

// Import route helper
const route = window.route;

// Permission checks
const { can, isAdmin } = usePermissions();

// Receive data from backend
const props = defineProps({
    checklists: Object,
    restaurants: Array,
    areaManagers: Array,
    filters: Object,
    isAdmin: Boolean,
    isAreaManager: Boolean,
    canExportAll: Boolean,
    canViewAllRestaurants: Boolean,
    canViewAreaManagerFilter: Boolean,
    auth: Object
});

// Filter form data
const filters = ref({
    restaurant: props.filters?.restaurant || '',
    time_option: props.filters?.time_option || '',
    date_from: props.filters?.date_from || '',
    date_to: props.filters?.date_to || '',
    search: props.filters?.search || '',
    status: props.filters?.status || '',
    area_manager: props.filters?.area_manager || ''
});

// UI state
const showExportDropdown = ref(false);
const exportDropdown = ref(null);
const showFilters = ref(false);

// Filter options
const timeOptions = ['12PM', '5PM', '8PM', '11PM'];
const statusOptions = ['Draft', 'Submitted', 'Reviewed', 'Approved'];

// Export functions
const exportToExcel = async () => {
    try {
        const query = new URLSearchParams(filters.value).toString();
        window.location.href = route('qsc-checklist.export') + '?' + query;
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const exportToPDF = async () => {
    try {
        const query = new URLSearchParams(filters.value).toString();
        const response = await fetch(route('qsc-checklist.export-pdf') + '?' + query);
        const blob = await response.blob();
        downloadFile(blob, 'qsc-checklist-report.pdf');
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const exportToCSV = async () => {
    try {
        const data = props.checklists.data.map(checklist => ({
            'Date': formatDate(checklist.date),
            'Store Name': checklist.store_name,
            'MOD': checklist.mod,
            'Time Option': checklist.time_option,
            'Status': checklist.status,
            'Compliance Score': checklist.compliance_score || 'N/A',
            'Manager Signature': checklist.manager_signature,
            'Created By': checklist.user?.name || 'N/A',
            'Created At': formatDate(checklist.created_at)
        }));
        
        const csv = convertToCSV(data);
        const blob = new Blob([csv], { type: 'text/csv' });
        downloadFile(blob, 'qsc-checklist-data.csv');
        showExportDropdown.value = false;
    } catch (error) {
        console.error('Export failed:', error);
        alert('Export failed. Please try again.');
    }
};

const convertToCSV = (data) => {
    if (!data.length) return '';
    
    const headers = Object.keys(data[0]);
    const csvContent = [
        headers.join(','),
        ...data.map(row => 
            headers.map(header => {
                const value = row[header];
                return typeof value === 'string' && value.includes(',') 
                    ? `"${value}"` 
                    : value;
            }).join(',')
        )
    ].join('\n');
    
    return csvContent;
};

const downloadFile = (blob, filename) => {
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
};

// Print functions
const printPage = () => {
    window.print();
};

const printForm = (checklistId) => {
    const printWindow = window.open('', '_blank');
    const checklist = props.checklists.data.find(c => c.id === checklistId);
    
    if (checklist) {
        // Helper function to format field value
        const formatValue = (value) => {
            if (value === null || value === undefined || value === '') return 'N/A';
            return value;
        };
        
        // Helper function to get status badge HTML
        const getStatusBadge = (status) => {
            const statusLower = (status || 'draft').toLowerCase();
            return `<span class="status-badge status-${statusLower}">${status || 'Draft'}</span>`;
        };
        
        printWindow.document.write(`
            <html>
                <head>
                    <title>QSC Checklist - ${checklist.store_name}</title>
                    <style>
                        @media print {
                            @page { margin: 0.5cm; size: A4; }
                            body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                        }
                        * { margin: 0; padding: 0; box-sizing: border-box; }
                        body { 
                            font-family: 'Arial', sans-serif; 
                            padding: 15px;
                            color: #333;
                            line-height: 1.4;
                            font-size: 11px;
                        }
                        .print-header {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            padding: 20px;
                            border-radius: 8px;
                            margin-bottom: 20px;
                            text-align: center;
                        }
                        .print-header h1 {
                            font-size: 24px;
                            margin-bottom: 8px;
                            font-weight: bold;
                        }
                        .print-header h2 {
                            font-size: 18px;
                            font-weight: normal;
                            opacity: 0.95;
                        }
                        .print-header .checklist-id {
                            margin-top: 8px;
                            font-size: 11px;
                            opacity: 0.8;
                        }
                        .info-grid {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            gap: 10px;
                            margin-bottom: 20px;
                        }
                        .info-card {
                            background: #f8f9fa;
                            border: 1px solid #e9ecef;
                            border-radius: 6px;
                            padding: 12px;
                        }
                        .info-card .label {
                            font-size: 9px;
                            color: #6c757d;
                            text-transform: uppercase;
                            letter-spacing: 0.3px;
                            margin-bottom: 6px;
                            font-weight: 600;
                        }
                        .info-card .value {
                            font-size: 13px;
                            color: #212529;
                            font-weight: bold;
                        }
                        .score-section {
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            border-radius: 8px;
                            padding: 20px;
                            text-align: center;
                            margin-bottom: 20px;
                        }
                        .score-value {
                            font-size: 48px;
                            font-weight: bold;
                            margin: 10px 0;
                        }
                        .score-label {
                            font-size: 14px;
                            opacity: 0.9;
                        }
                        .status-badge {
                            display: inline-block;
                            padding: 6px 12px;
                            border-radius: 15px;
                            font-weight: 600;
                            font-size: 11px;
                            margin-top: 8px;
                        }
                        .status-draft { background: #6c757d; color: white; }
                        .status-submitted { background: #0d6efd; color: white; }
                        .status-reviewed { background: #ffc107; color: #000; }
                        .status-approved { background: #198754; color: white; }
                        .section {
                            background: white;
                            border: 1px solid #e9ecef;
                            border-radius: 8px;
                            padding: 15px;
                            margin-bottom: 15px;
                            page-break-inside: avoid;
                        }
                        .section-header {
                            font-size: 14px;
                            color: #667eea;
                            margin-bottom: 12px;
                            padding-bottom: 8px;
                            border-bottom: 2px solid #667eea;
                            font-weight: bold;
                        }
                        .field-row {
                            display: grid;
                            grid-template-columns: 300px 80px 1fr;
                            gap: 10px;
                            padding: 8px 0;
                            border-bottom: 1px solid #f1f3f5;
                            align-items: start;
                        }
                        .field-row:last-child {
                            border-bottom: none;
                        }
                        .field-label {
                            font-weight: 600;
                            color: #495057;
                            font-size: 11px;
                        }
                        .field-value {
                            font-weight: bold;
                            font-size: 11px;
                            padding: 4px 8px;
                            border-radius: 4px;
                            text-align: center;
                        }
                        .field-value.yes {
                            background: #d1fae5;
                            color: #065f46;
                        }
                        .field-value.no {
                            background: #fee2e2;
                            color: #991b1b;
                        }
                        .field-value.na {
                            background: #f3f4f6;
                            color: #6b7280;
                        }
                        .field-comment {
                            color: #6c757d;
                            font-size: 10px;
                            font-style: italic;
                        }
                        .footer {
                            margin-top: 20px;
                            padding-top: 15px;
                            border-top: 2px solid #e9ecef;
                            text-align: center;
                            color: #6c757d;
                            font-size: 10px;
                        }
                        .footer .logo {
                            font-size: 14px;
                            font-weight: bold;
                            color: #667eea;
                            margin-bottom: 4px;
                        }
                    </style>
                </head>
                <body>
                    <!-- Header -->
                    <div class="print-header">
                        <h1>🎯 Q.S.C Checklist Report</h1>
                        <h2>${checklist.store_name}</h2>
                        <div class="checklist-id">Checklist ID: #${checklist.id} | Date: ${formatDate(checklist.date)}</div>
                    </div>

                    <!-- Score Section -->
                    <div class="score-section">
                        <div class="score-label">Compliance Score</div>
                        <div class="score-value">${checklist.compliance_score || 'N/A'}${checklist.compliance_score ? '%' : ''}</div>
                        ${getStatusBadge(checklist.status)}
                    </div>

                    <!-- Basic Information Grid -->
                    <div class="info-grid">
                        <div class="info-card">
                            <div class="label">MOD</div>
                            <div class="value">${formatValue(checklist.mod)}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Day</div>
                            <div class="value">${formatValue(checklist.day)}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Time</div>
                            <div class="value">${formatValue(checklist.time_option)}</div>
                        </div>
                        <div class="info-card">
                            <div class="label">Created By</div>
                            <div class="value">${checklist.user?.name || 'N/A'}</div>
                        </div>
                    </div>

                    <!-- Exterior Section -->
                    <div class="section">
                        <div class="section-header">🏢 Exterior</div>
                        <div class="field-row">
                            <div class="field-label">Lights are open on time at the branch</div>
                            <div class="field-value ${(checklist.exterior_lights_open || '').toLowerCase()}">${formatValue(checklist.exterior_lights_open)}</div>
                            <div class="field-comment">${formatValue(checklist.exterior_lights_open_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Sultan Company logo is clean without dust</div>
                            <div class="field-value ${(checklist.exterior_logo_clean || '').toLowerCase()}">${formatValue(checklist.exterior_logo_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.exterior_logo_clean_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Parking and pavement in front of the restaurant are clean</div>
                            <div class="field-value ${(checklist.exterior_parking_clean || '').toLowerCase()}">${formatValue(checklist.exterior_parking_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.exterior_parking_clean_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">There is no graffiti on the walls or the sign</div>
                            <div class="field-value ${(checklist.exterior_no_graffiti || '').toLowerCase()}">${formatValue(checklist.exterior_no_graffiti)}</div>
                            <div class="field-comment">${formatValue(checklist.exterior_no_graffiti_comment)}</div>
                        </div>
                    </div>

                    <!-- Doors and Glass Section -->
                    <div class="section">
                        <div class="section-header">🚪 Doors and Glass</div>
                        <div class="field-row">
                            <div class="field-label">Doors and glass are cleaned</div>
                            <div class="field-value ${(checklist.doors_glass_clean || '').toLowerCase()}">${formatValue(checklist.doors_glass_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.doors_glass_clean_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Door handle is clean and free of stickers</div>
                            <div class="field-value ${(checklist.doors_door_handle || '').toLowerCase()}">${formatValue(checklist.doors_door_handle)}</div>
                            <div class="field-comment">${formatValue(checklist.doors_door_handle_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Restaurant entrance is clean</div>
                            <div class="field-value ${(checklist.doors_entrance_clean || '').toLowerCase()}">${formatValue(checklist.doors_entrance_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.doors_entrance_clean_comment)}</div>
                        </div>
                    </div>

                    <!-- Frontline Section -->
                    <div class="section">
                        <div class="section-header">👥 Frontline</div>
                        <div class="field-row">
                            <div class="field-label">Working areas are organized</div>
                            <div class="field-value ${(checklist.frontline_areas_organized || '').toLowerCase()}">${formatValue(checklist.frontline_areas_organized)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_areas_organized_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Customers are greeted in an authentic way</div>
                            <div class="field-value ${(checklist.frontline_customers_greeted || '').toLowerCase()}">${formatValue(checklist.frontline_customers_greeted)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_customers_greeted_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Menu and offers are available and visible</div>
                            <div class="field-value ${(checklist.frontline_menu_available || '').toLowerCase()}">${formatValue(checklist.frontline_menu_available)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_menu_available_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Seven steps of service are implemented</div>
                            <div class="field-value ${(checklist.frontline_seven_steps || '').toLowerCase()}">${formatValue(checklist.frontline_seven_steps)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_seven_steps_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Tables are clean, organized and sanitized</div>
                            <div class="field-value ${(checklist.frontline_tables_clean || '').toLowerCase()}">${formatValue(checklist.frontline_tables_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_tables_clean_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">High chairs are clean and safe to use</div>
                            <div class="field-value ${(checklist.frontline_high_chairs || '').toLowerCase()}">${formatValue(checklist.frontline_high_chairs)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_high_chairs_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">There are no damaged tables or damaged chairs</div>
                            <div class="field-value ${(checklist.frontline_no_damaged_tables || '').toLowerCase()}">${formatValue(checklist.frontline_no_damaged_tables)}</div>
                            <div class="field-comment">${formatValue(checklist.frontline_no_damaged_tables_comment)}</div>
                        </div>
                    </div>

                    <!-- Restroom Section -->
                    <div class="section">
                        <div class="section-header">🚽 Restroom</div>
                        <div class="field-row">
                            <div class="field-label">There is no full trash bin</div>
                            <div class="field-value ${(checklist.restroom_no_full_trash || '').toLowerCase()}">${formatValue(checklist.restroom_no_full_trash)}</div>
                            <div class="field-comment">${formatValue(checklist.restroom_no_full_trash_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Soap is available</div>
                            <div class="field-value ${(checklist.restroom_soap_available || '').toLowerCase()}">${formatValue(checklist.restroom_soap_available)}</div>
                            <div class="field-comment">${formatValue(checklist.restroom_soap_available_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Hand dryer is working</div>
                            <div class="field-value ${(checklist.restroom_hand_dryer || '').toLowerCase()}">${formatValue(checklist.restroom_hand_dryer)}</div>
                            <div class="field-comment">${formatValue(checklist.restroom_hand_dryer_comment)}</div>
                        </div>
                    </div>

                    <!-- Holding/Heating Section -->
                    <div class="section">
                        <div class="section-header">🔥 Holding / Heating</div>
                        <div class="field-row">
                            <div class="field-label">Product temperature is within standard</div>
                            <div class="field-value ${(checklist.holding_product_temp || '').toLowerCase()}">${formatValue(checklist.holding_product_temp)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_product_temp_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Product age is within standard</div>
                            <div class="field-value ${(checklist.holding_product_age || '').toLowerCase()}">${formatValue(checklist.holding_product_age)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_product_age_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Check product availability</div>
                            <div class="field-value ${(checklist.holding_check_product || '').toLowerCase()}">${formatValue(checklist.holding_check_product)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_check_product_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Products are fresh and not dry</div>
                            <div class="field-value ${(checklist.holding_products_fresh || '').toLowerCase()}">${formatValue(checklist.holding_products_fresh)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_products_fresh_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Internal temperature of fryers is according to standard</div>
                            <div class="field-value ${(checklist.holding_internal_temp || '').toLowerCase()}">${formatValue(checklist.holding_internal_temp)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_internal_temp_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Shortening level is according to standard</div>
                            <div class="field-value ${(checklist.holding_shortening_level || '').toLowerCase()}">${formatValue(checklist.holding_shortening_level)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_shortening_level_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Check shortening quality</div>
                            <div class="field-value ${(checklist.holding_check_shortening || '').toLowerCase()}">${formatValue(checklist.holding_check_shortening)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_check_shortening_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Fryer routine maintenance is done</div>
                            <div class="field-value ${(checklist.holding_fryer_maintenance || '').toLowerCase()}">${formatValue(checklist.holding_fryer_maintenance)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_fryer_maintenance_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Use tray during frying process</div>
                            <div class="field-value ${(checklist.holding_use_tray || '').toLowerCase()}">${formatValue(checklist.holding_use_tray)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_use_tray_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Fry basket is clean and not damaged</div>
                            <div class="field-value ${(checklist.holding_fry_basket || '').toLowerCase()}">${formatValue(checklist.holding_fry_basket)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_fry_basket_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Fries are salted according to standard</div>
                            <div class="field-value ${(checklist.holding_fries_salted || '').toLowerCase()}">${formatValue(checklist.holding_fries_salted)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_fries_salted_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Fries cooking time is according to standard</div>
                            <div class="field-value ${(checklist.holding_fries_cooking || '').toLowerCase()}">${formatValue(checklist.holding_fries_cooking)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_fries_cooking_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Buns quality is good</div>
                            <div class="field-value ${(checklist.holding_buns_quality || '').toLowerCase()}">${formatValue(checklist.holding_buns_quality)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_buns_quality_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Teflon sheet is used</div>
                            <div class="field-value ${(checklist.holding_teflon_sheet || '').toLowerCase()}">${formatValue(checklist.holding_teflon_sheet)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_teflon_sheet_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Bread toast is according to standard</div>
                            <div class="field-value ${(checklist.holding_bread_standard || '').toLowerCase()}">${formatValue(checklist.holding_bread_standard)}</div>
                            <div class="field-comment">${formatValue(checklist.holding_bread_standard_comment)}</div>
                        </div>
                    </div>

                    <!-- Thawing Section -->
                    <div class="section">
                        <div class="section-header">❄️ Thawing</div>
                        <div class="field-row">
                            <div class="field-label">Day dot labels are used</div>
                            <div class="field-value ${(checklist.thawing_day_labels || '').toLowerCase()}">${formatValue(checklist.thawing_day_labels)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_day_labels_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">No tampering with raw materials</div>
                            <div class="field-value ${(checklist.thawing_no_tampering || '').toLowerCase()}">${formatValue(checklist.thawing_no_tampering)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_no_tampering_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Temperature range is 0 to 4 degrees</div>
                            <div class="field-value ${(checklist.thawing_temp_range || '').toLowerCase()}">${formatValue(checklist.thawing_temp_range)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_temp_range_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">No overstock in refrigerators</div>
                            <div class="field-value ${(checklist.thawing_no_overstock || '').toLowerCase()}">${formatValue(checklist.thawing_no_overstock)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_no_overstock_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Utensils are clean</div>
                            <div class="field-value ${(checklist.thawing_utensils_clean || '').toLowerCase()}">${formatValue(checklist.thawing_utensils_clean)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_utensils_clean_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Three compartment sink setup is correct</div>
                            <div class="field-value ${(checklist.thawing_sink_setup || '').toLowerCase()}">${formatValue(checklist.thawing_sink_setup)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_sink_setup_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Portion standards are followed</div>
                            <div class="field-value ${(checklist.thawing_portion_standards || '').toLowerCase()}">${formatValue(checklist.thawing_portion_standards)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_portion_standards_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Sultan sauce is refrigerated after use</div>
                            <div class="field-value ${(checklist.thawing_sultan_sauce || '').toLowerCase()}">${formatValue(checklist.thawing_sultan_sauce)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_sultan_sauce_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Vegetables have day dot and date labels</div>
                            <div class="field-value ${(checklist.thawing_vegetables_date || '').toLowerCase()}">${formatValue(checklist.thawing_vegetables_date)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_vegetables_date_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">FIFO method is followed</div>
                            <div class="field-value ${(checklist.thawing_follow_fifo || '').toLowerCase()}">${formatValue(checklist.thawing_follow_fifo)}</div>
                            <div class="field-comment">${formatValue(checklist.thawing_follow_fifo_comment)}</div>
                        </div>
                    </div>

                    <!-- Burger Assembly Section -->
                    <div class="section">
                        <div class="section-header">🍔 Burger Assembly</div>
                        <div class="field-row">
                            <div class="field-label">Standard setup is followed</div>
                            <div class="field-value ${(checklist.burger_standard_setup || '').toLowerCase()}">${formatValue(checklist.burger_standard_setup)}</div>
                            <div class="field-comment">${formatValue(checklist.burger_standard_setup_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Sauce spiral application is correct</div>
                            <div class="field-value ${(checklist.burger_sauce_spiral || '').toLowerCase()}">${formatValue(checklist.burger_sauce_spiral)}</div>
                            <div class="field-comment">${formatValue(checklist.burger_sauce_spiral_comment)}</div>
                        </div>
                        <div class="field-row">
                            <div class="field-label">Ingredients order is according to standard</div>
                            <div class="field-value ${(checklist.burger_ingredients_order || '').toLowerCase()}">${formatValue(checklist.burger_ingredients_order)}</div>
                            <div class="field-comment">${formatValue(checklist.burger_ingredients_order_comment)}</div>
                        </div>
                    </div>

                    <!-- Manager Signature -->
                    <div class="section">
                        <div class="section-header">✍️ Signature</div>
                        <div class="field-row">
                            <div class="field-label">Manager Signature</div>
                            <div class="field-value na">${formatValue(checklist.manager_signature) === 'N/A' ? 'Not Signed' : formatValue(checklist.manager_signature)}</div>
                            <div class="field-comment">Digital signature confirmation</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="footer">
                        <div class="logo">🎯 GHIDAS QMS</div>
                        <div>Quality Management System - Operations Platform</div>
                        <div style="margin-top: 8px; color: #adb5bd; font-size: 9px;">
                            Printed on: ${new Date().toLocaleString()} | Checklist ID: #${checklist.id}
                        </div>
                    </div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
};

// Filter functions
const applyFilters = () => {
    router.get(route('qsc-checklist.report'), filters.value, {
        preserveState: true,
        replace: true
    });
};

const clearFilters = () => {
    filters.value = {
        restaurant: '',
        time_option: '',
        date_from: '',
        date_to: '',
        search: '',
        status: '',
        area_manager: ''
    };
    applyFilters();
};

const quickFilterToday = () => {
    const today = new Date().toISOString().split('T')[0];
    filters.value.date_from = today;
    filters.value.date_to = today;
    applyFilters();
};

const quickFilterWeek = () => {
    const today = new Date();
    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
    filters.value.date_from = weekAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
    applyFilters();
};

const quickFilterMonth = () => {
    const today = new Date();
    const monthAgo = new Date(today.getFullYear(), today.getMonth() - 1, today.getDate());
    filters.value.date_from = monthAgo.toISOString().split('T')[0];
    filters.value.date_to = today.toISOString().split('T')[0];
    applyFilters();
};

// Utility functions
const formatDate = (date) => {
    return new Date(date).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
};

const getStatusBadgeClass = (status) => {
    switch (status) {
        case 'Draft':
            return 'bg-gray-100 text-gray-800';
        case 'Submitted':
            return 'bg-blue-100 text-blue-800';
        case 'Reviewed':
            return 'bg-yellow-100 text-yellow-800';
        case 'Approved':
            return 'bg-green-100 text-green-800';
        default:
            return 'bg-gray-100 text-gray-800';
    }
};

const getScoreColor = (score) => {
    if (score >= 90) return 'text-green-600';
    if (score >= 80) return 'text-yellow-600';
    if (score >= 70) return 'text-orange-600';
    return 'text-red-600';
};

const confirmChecklist = (id) => {
    if (!confirm('Confirm this checklist status?')) return;
    router.post(route('qsc-checklist.confirm', id), {}, { preserveScroll: true });
};

const deleteChecklist = (id) => {
    if (!confirm('Are you sure you want to delete this checklist? This action cannot be undone.')) return;
    router.delete(route('qsc-checklist.destroy', id), { 
        preserveScroll: true,
        onSuccess: () => {
            alert('Checklist deleted successfully');
        }
    });
};

// Statistics
const statistics = computed(() => {
    const data = props.checklists?.data || [];
    const total = data.length;
    const avgScore = total > 0 ? 
        data.reduce((sum, checklist) => sum + (checklist.compliance_score || 0), 0) / total : 0;
    
    return {
        total,
        draft: data.filter(c => c.status === 'Draft').length,
        submitted: data.filter(c => c.status === 'Submitted').length,
        reviewed: data.filter(c => c.status === 'Reviewed').length,
        approved: data.filter(c => c.status === 'Approved').length,
        avgScore: Math.round(avgScore),
        highPerformance: data.filter(c => (c.compliance_score || 0) >= 90).length,
        needsAttention: data.filter(c => (c.compliance_score || 0) < 70).length
    };
});

// Handle clicks outside dropdown
const handleClickOutside = (event) => {
    if (exportDropdown.value && !exportDropdown.value.contains(event.target)) {
        showExportDropdown.value = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});
</script>

<template>
    <Head title="Q.S.C Checklist Reports" />

    <DefaultAuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Q.S.C Checklist Reports</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <p class="text-sm text-gray-600">Manage and track quality, service, and cleanliness checklists</p>
                        <span v-if="isAreaManager && !isAdmin" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            📊 Area Manager View
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <!-- Print Buttons -->
                    <button @click="printPage" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print Page
                    </button>

                    <!-- Export Dropdown -->
                    <div class="relative" ref="exportDropdown">
                        <button @click="showExportDropdown = !showExportDropdown" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Export
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <div v-show="showExportDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                            <div class="py-1">
                                <button @click="exportToExcel" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export as Excel
                                </button>
                                <button @click="exportToPDF" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    Export as PDF
                                </button>
                                <button @click="exportToCSV" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <svg class="w-4 h-4 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export as CSV
                                </button>
                            </div>
                        </div>
                    </div>

                    <Link :href="route('qsc-checklist.form')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 text-sm font-medium transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        New Checklist
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Total Checklists</dt>
                                        <dd class="text-2xl font-bold text-gray-900">{{ statistics.total }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v6a2 2 0 002 2h2m-7 6v-2a3 3 0 013-3h4a3 3 0 013 3v2m-8-4h8m-8-8V5a2 2 0 012-2h2a2 2 0 012 2v2"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Draft</dt>
                                        <dd class="text-2xl font-bold text-yellow-600">{{ statistics.draft }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Submitted</dt>
                                        <dd class="text-2xl font-bold text-blue-600">{{ statistics.submitted }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-4 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">Reviewed</dt>
                                        <dd class="text-2xl font-bold text-green-600">{{ statistics.reviewed }}</dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="bg-white rounded-lg shadow-sm mb-6 border border-gray-200">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-gray-900">Filters & Search</h3>
                            <button @click="showFilters = !showFilters" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                                {{ showFilters ? 'Hide' : 'Show' }} Filters
                            </button>
                        </div>
                    </div>
                    
                    <div v-show="showFilters" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Restaurant</label>
                                <select v-model="filters.restaurant" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Restaurants</option>
                                    <option v-for="restaurant in restaurants" :key="restaurant.id" :value="restaurant.name">
                                        {{ restaurant.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Time Option</label>
                                <select v-model="filters.time_option" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Times</option>
                                    <option v-for="time in timeOptions" :key="time" :value="time">{{ time }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select v-model="filters.status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Status</option>
                                    <option v-for="status in statusOptions" :key="status" :value="status">{{ status }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                                <input v-model="filters.search" type="text" placeholder="Search checklists..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                                <input v-model="filters.date_from" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                                <input v-model="filters.date_to" type="date" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                            </div>

                            <div v-if="props.canViewAreaManagerFilter">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Area Manager</label>
                                <select v-model="filters.area_manager" @change="applyFilters" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">All Area Managers</option>
                                    <option v-for="manager in areaManagers" :key="manager.id" :value="manager.id">
                                        {{ manager.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <button @click="quickFilterToday" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Today
                            </button>
                            <button @click="quickFilterWeek" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Last 7 Days
                            </button>
                            <button @click="quickFilterMonth" class="px-3 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors">
                                Last 30 Days
                            </button>
                            <button @click="clearFilters" class="px-3 py-1 text-xs bg-gray-100 text-gray-700 rounded-full hover:bg-gray-200 transition-colors">
                                Clear Filters
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Q.S.C Checklists</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Store Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">MOD</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Score</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="checklist in props.checklists.data" :key="checklist.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ formatDate(checklist.date) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ checklist.store_name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.mod }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.time_option }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="getStatusBadgeClass(checklist.status)">
                                            {{ checklist.status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm" :class="getScoreColor(checklist.compliance_score)">
                                        {{ checklist.compliance_score ? checklist.compliance_score + '%' : 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ checklist.user?.name || 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- View Button -->
                                            <Link v-if="can('qsc_checklist', 'view')" 
                                                  :href="route('qsc-checklist.show', checklist.id)" 
                                                  class="text-blue-600 hover:text-blue-900 px-2 py-1 rounded hover:bg-blue-50 transition-colors"
                                                  title="View Details">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </Link>
                                            
                                            <!-- Edit Button -->
                                            <Link v-if="can('qsc_checklist', 'edit')" 
                                                  :href="route('qsc-checklist.edit', checklist.id)" 
                                                  class="text-yellow-600 hover:text-yellow-900 px-2 py-1 rounded hover:bg-yellow-50 transition-colors"
                                                  title="Edit">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </Link>
                                            
                                            <!-- Delete Button -->
                                            <button v-if="can('qsc_checklist', 'delete')" 
                                                    @click="deleteChecklist(checklist.id)" 
                                                    class="text-red-600 hover:text-red-900 px-2 py-1 rounded hover:bg-red-50 transition-colors"
                                                    title="Delete">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <!-- Empty State -->
                        <div v-if="!props.checklists.data.length" class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No checklists found</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new Q.S.C checklist.</p>
                            <div class="mt-6">
                                <Link :href="route('qsc-checklist.form')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    New Checklist
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Pagination -->
                    <div v-if="props.checklists.links && props.checklists.links.length > 3" class="bg-gradient-to-br from-white to-gray-50 px-4 py-4 border-t border-gray-200 sm:px-6">
                        <!-- Mobile Pagination -->
                        <div class="flex items-center justify-between sm:hidden">
                            <Link 
                                v-if="props.checklists.prev_page_url" 
                                :href="props.checklists.prev_page_url" 
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-150"
                            >
                                « Previous
                            </Link>
                            <span v-else class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                                « Previous
                            </span>
                            
                            <div class="text-sm text-gray-700">
                                <span class="font-medium">{{ props.checklists.current_page }}</span>
                                <span class="mx-1">of</span>
                                <span class="font-medium">{{ props.checklists.last_page }}</span>
                            </div>
                            
                            <Link 
                                v-if="props.checklists.next_page_url" 
                                :href="props.checklists.next_page_url" 
                                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition-colors duration-150"
                            >
                                Next »
                            </Link>
                            <span v-else class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-md cursor-not-allowed">
                                Next »
                            </span>
                        </div>

                        <!-- Desktop Pagination -->
                        <div class="hidden sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing 
                                    <span class="font-semibold text-gray-900">{{ props.checklists.from || 0 }}</span> 
                                    to 
                                    <span class="font-semibold text-gray-900">{{ props.checklists.to || 0 }}</span> 
                                    of 
                                    <span class="font-semibold text-gray-900">{{ props.checklists.total || 0 }}</span> 
                                    results
                                </p>
                            </div>
                            
                            <div>
                                <nav class="relative z-0 inline-flex rounded-lg shadow-sm -space-x-px" aria-label="Pagination">
                                    <!-- Previous Button -->
                                    <Link 
                                        v-if="props.checklists.prev_page_url" 
                                        :href="props.checklists.prev_page_url"
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:border-blue-300 hover:text-blue-700 transition-all duration-150"
                                    >
                                        « Previous
                                    </Link>
                                    <span 
                                        v-else 
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-l-lg cursor-not-allowed"
                                    >
                                        « Previous
                                    </span>

                                    <!-- Page Numbers -->
                                    <template v-for="(link, index) in props.checklists.links" :key="index">
                                        <Link 
                                            v-if="link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                            :href="link.url"
                                            :class="[
                                                'relative inline-flex items-center px-4 py-2 text-sm font-medium border transition-all duration-150',
                                                link.active 
                                                    ? 'z-10 bg-gradient-to-br from-blue-500 to-blue-600 border-blue-600 text-white shadow-md' 
                                                    : 'bg-white border-gray-300 text-gray-700 hover:bg-gradient-to-r hover:from-gray-50 hover:to-gray-100 hover:text-gray-900'
                                            ]"
                                            v-html="link.label"
                                        ></Link>
                                        <span 
                                            v-else-if="!link.url && !link.label.includes('Previous') && !link.label.includes('Next')"
                                            class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300"
                                            v-html="link.label"
                                        ></span>
                                    </template>

                                    <!-- Next Button -->
                                    <Link 
                                        v-if="props.checklists.next_page_url" 
                                        :href="props.checklists.next_page_url"
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gradient-to-r hover:from-blue-50 hover:to-blue-100 hover:border-blue-300 hover:text-blue-700 transition-all duration-150"
                                    >
                                        Next »
                                    </Link>
                                    <span 
                                        v-else 
                                        class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-r-lg cursor-not-allowed"
                                    >
                                        Next »
                                    </span>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DefaultAuthenticatedLayout>
</template>

<style scoped>
@media print {
    .no-print {
        display: none !important;
    }
    
    .print-only {
        display: block !important;
    }
    
    body {
        print-color-adjust: exact;
        -webkit-print-color-adjust: exact;
    }
    
    .bg-gray-50 {
        background-color: #f9fafb !important;
    }
    
    .border-gray-200 {
        border-color: #e5e7eb !important;
    }
    
    table {
        page-break-inside: auto;
    }
    
    tr {
        page-break-inside: avoid;
        page-break-after: auto;
    }
    
    thead {
        display: table-header-group;
    }
    
    tfoot {
        display: table-footer-group;
    }
}

.export-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    z-index: 50;
}
</style>