<template>
  <DefaultAuthenticatedLayout>
    <Head title="Temperature Tracking Report" />
    <div class="max-w-7xl mx-auto py-4 sm:py-8 px-2 sm:px-4">
      <!-- Breadcrumb -->
      <nav class="text-xs text-gray-500 mb-4 print:hidden" aria-label="Breadcrumb">
        <ol class="flex flex-wrap gap-1 items-center">
          <li><Link href="/qms/dashboard" class="hover:text-gray-700">Dashboard</Link></li>
          <li>/</li>
          <li><Link :href="route('temperature-tracking.index')" class="hover:text-gray-700">Temperature Tracking</Link></li>
          <li>/</li>
          <li class="text-gray-700 truncate">Report #{{ form?.id || 'N/A' }}</li>
        </ol>
      </nav>
      
      <!-- Simple Header -->
      <div class="bg-white rounded-lg shadow p-3 sm:p-4 mb-4">
        <div class="flex items-center justify-between mb-3 print:hidden">
          <h1 class="text-sm sm:text-base font-bold text-gray-900 truncate">Temperature Tracking Report #{{ form?.id || 'N/A' }}</h1>
          <Link :href="route('temperature-tracking.index')" 
                class="flex items-center gap-1 px-2 py-1 rounded text-xs font-medium text-gray-600 hover:bg-gray-100 transition-all flex-shrink-0">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="hidden sm:inline">Back</span>
          </Link>
        </div>
        
        <!-- Simple Info List -->
        <div class="space-y-1.5 sm:space-y-1 text-[11px] sm:text-xs">
          <div class="flex items-start sm:items-center gap-2">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">Store:</span>
            <span class="font-medium text-gray-900 break-words">{{ form.store?.name || 'N/A' }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">Date:</span>
            <span class="font-medium text-gray-900">{{ form.date || 'N/A' }}</span>
          </div>
          <div class="flex items-start sm:items-center gap-2">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">MIC Morning:</span>
            <span class="font-medium text-gray-900 break-words">{{ form.mic_morning || 'N/A' }}</span>
          </div>
          <div class="flex items-start sm:items-center gap-2">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">MIC Evening:</span>
            <span class="font-medium text-gray-900 break-words">{{ form.mic_evening || 'N/A' }}</span>
          </div>
          <div class="flex items-center gap-2">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">Status:</span>
            <span :class="statusBadgeClass(form.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] sm:text-xs font-bold">
              {{ form.status || 'Draft' }}
            </span>
          </div>
          <div class="flex items-center gap-2" v-if="form.shift">
            <span class="text-gray-500 w-20 sm:w-24 flex-shrink-0">Shift:</span>
            <span class="font-medium text-gray-900">
              <span v-if="Array.isArray(form.shift)">{{ form.shift.join(', ') }}</span>
              <span v-else>{{ form.shift }}</span>
            </span>
          </div>
          <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2 text-gray-400 pt-1 border-t">
            <span class="w-20 sm:w-24 flex-shrink-0">Created by:</span>
            <span class="break-words">{{ form.creator?.name || 'Unknown' }} • {{ formatDateTime(form.created_at) }}</span>
          </div>
        </div>
      </div>
      
      <!-- Temperature Data Sections -->
      <section class="space-y-4 sm:space-y-8">
        <Card title="Cooked Product Temperature">
          <TemperatureTable :products="cookedProducts" :modelValue="form.cooked_products" :minTemp="160" warningText="Below 160°F: To discard" isReadOnly />
        </Card>
        <Card title="Holding Temperature">
          <TemperatureTable :products="holdingProducts" :modelValue="form.holding_products" :minTemp="140" warningText="Below 140°F: To discard" isReadOnly />
        </Card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <Card v-if="hasSideCookedData" title="Cooked Products">
            <TemperatureTable :products="cookedSideProducts" :modelValue="form.side_cooked" :minTemp="160" warningText="Temp <160°F" isReadOnly />
          </Card>
          <Card title="Vegetables (36–40°F)">
            <TemperatureTable :products="vegetableProducts" :modelValue="form.vegetables" :maxTemp="40" :minTemp="36" warningText="36–40°F only" isReadOnly />
          </Card>
        </div>
        <Card title="Sanitizer Solution">
          <SanitizerTable :modelValue="form.sanitizer" isReadOnly />
        </Card>
        <Card title="Corrective Action">
          <div class="whitespace-pre-line p-3 sm:p-4 bg-yellow-50 border border-yellow-200 rounded-lg text-xs sm:text-sm">
            {{ form.corrective_action_upper || form.corrective_action_lower || form.corrective_action || 'No corrective action required' }}
          </div>
        </Card>
        <Card title="Equipment Temperature">
          <EquipmentTable :modelValue="form.equipment" isReadOnly />
        </Card>
        <Card title="Vegetable Receiving Log">
          <ReceivingTable :modelValue="form.vegetable_receiving" :products="vegetableProducts" isReadOnly />
        </Card>
        <Card title="Product Receiving Date">
          <ProductReceivingTable :modelValue="form.receiving_products" :products="receivingProducts" isReadOnly />
        </Card>
        <Card title="Receiving Shift Turn Over">
          <ShiftTurnoverTable :modelValue="form.shift_turnover" isReadOnly />
        </Card>
      </section>
    </div>
  </DefaultAuthenticatedLayout>
</template>
<script setup>
import { computed } from 'vue';
import DefaultAuthenticatedLayout from '@/Layouts/DefaultAuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import TemperatureTable from './Partials/TemperatureTable.vue';
import SanitizerTable from './Partials/SanitizerTable.vue';
import EquipmentTable from './Partials/EquipmentTable.vue';
import ReceivingTable from './Partials/ReceivingTable.vue';
import ProductReceivingTable from './Partials/ProductReceivingTable.vue';
import ShiftTurnoverTable from './Partials/ShiftTurnoverTable.vue';

const props = defineProps({ 
  form: {
    type: Object,
    required: true,
    default: () => ({})
  }
});

// Master Data - Product Lists
const cookedProducts = [
  'KIDS CHICKEN BURGER','REGULAR CHICKEN','SULTAN BEEF','DOUBLE G BEEF','CRISPY CHICKEN','GRILLED CHICKEN'
];
const holdingProducts = [...cookedProducts];
const cookedSideProducts = ['JULIENNE','MAC & CHEESE','NACHOS'];
const vegetableProducts = ['LETTUCE','TOMATO','PICKLES','ONION'];
const receivingProducts = [...cookedProducts, 'NACHOS']; // Products that need receiving dates (not JULIENNE, MAC & CHEESE)
const allProducts = [...cookedProducts, ...cookedSideProducts];

// Helper Functions
function formatDate(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', { 
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });
}

// Check if side cooked products have any data
const hasSideCookedData = computed(() => {
  if (!props.form?.side_cooked) return false;
  
  // Check if any product has at least one non-null temperature
  return Object.values(props.form.side_cooked).some(productData => {
    if (!productData || typeof productData !== 'object') return false;
    return Object.values(productData).some(temp => temp !== null && temp !== '');
  });
});

function formatDateTime(dateString) {
  if (!dateString) return 'N/A';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric'
  }) + ' at ' + date.toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  });
}

function statusBadgeClass(status) {
  const classes = {
    'Draft': 'bg-gray-100 text-gray-800 border border-gray-300',
    'Submitted': 'bg-yellow-100 text-yellow-800 border border-yellow-300',
    'Reviewed': 'bg-green-100 text-green-800 border border-green-300'
  };
  return classes[status] || classes['Draft'];
}
</script>

<style scoped>
.header-gradient {
  background: linear-gradient(to right, #2563eb, #7c3aed);
  background-color: #2563eb; /* Fallback */
}

.card-overlay {
  background: rgba(255, 255, 255, 0.1);
  backdrop-filter: blur(10px);
}

@media print {
  .print\:hidden {
    display: none !important;
  }
  
  .header-gradient {
    background: linear-gradient(to right, #2563eb, #7c3aed) !important;
    color: white !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  .card-overlay {
    background: rgba(255, 255, 255, 0.2) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  /* Ensure all content is visible */
  body {
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  
  /* Fix table borders */
  table {
    border-collapse: collapse !important;
  }
  
  table td, table th {
    border: 1px solid #ddd !important;
    padding: 8px !important;
  }
  
  /* Ensure cards have borders */
  .bg-white {
    border: 1px solid #e5e7eb !important;
  }
  
  /* Page breaks */
  .space-y-8 > * {
    page-break-inside: avoid;
  }
}
</style>
