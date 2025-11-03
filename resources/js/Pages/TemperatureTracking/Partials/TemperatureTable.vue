<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Product</th>
            <th v-for="slot in timeSlots" :key="slot" class="border p-1">{{ slot }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product">
            <td class="border p-1">{{ product }}</td>
            <td v-for="slot in timeSlots" :key="slot" class="border p-1" :class="cellClass(modelValue[product]?.[slot])">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.[slot] || '-' }}</span>
              <input v-else type="number" :value="modelValue[product]?.[slot] || ''" class="w-16 border rounded px-1 py-0.5 bg-transparent"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), [slot]: $event.target.value } })"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div v-for="product in products" :key="product" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-3 pb-2 border-b border-gray-300">{{ product }}</h4>
        
        <div class="grid grid-cols-2 gap-2">
          <div v-for="slot in timeSlots" :key="slot">
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">{{ slot }}</label>
            <div :class="cellClass(modelValue[product]?.[slot])" class="rounded">
              <span v-if="isReadOnly" class="block text-xs font-medium px-2 py-1.5">{{ modelValue[product]?.[slot] || '-' }}</span>
              <input v-else type="number" :value="modelValue[product]?.[slot] || ''" 
                class="w-full border rounded px-2 py-1.5 text-xs bg-transparent"
                placeholder="°F"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), [slot]: $event.target.value } })"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-if="warningText" class="text-[10px] sm:text-xs text-red-600 mt-2 sm:mt-1 px-2 sm:px-0">
      * {{ warningText }}
    </div>
  </div>
</template>
<script setup>
const props = defineProps({
  products: Array,
  modelValue: Object,
  minTemp: Number,
  maxTemp: Number,
  warningText: String,
  isReadOnly: {
    type: Boolean,
    default: false
  }
});

// DEBUG: Check if modelValue is received
console.log('TemperatureTable modelValue:', props.modelValue);
console.log('TemperatureTable products:', props.products);

const timeSlots = ['11AM-2PM','3PM-6PM','7PM-10PM','11PM-2AM']; // Use regular dash to match database

function cellClass(val) {
  console.log('cellClass called with:', val, 'minTemp:', props.minTemp, 'maxTemp:', props.maxTemp);
  if (!val) return '';
  const numVal = parseFloat(val);
  if (isNaN(numVal)) return '';
  if (props.minTemp && numVal < props.minTemp) {
    console.log('❌ BELOW MIN - Returning bg-red-200');
    return 'bg-red-200';
  }
  if (props.maxTemp && numVal > props.maxTemp) {
    console.log('❌ ABOVE MAX - Returning bg-red-200');
    return 'bg-red-200';
  }
  console.log('✅ IN RANGE');
  return '';
}
</script>
