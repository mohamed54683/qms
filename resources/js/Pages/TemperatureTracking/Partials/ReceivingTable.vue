<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Product</th>
            <th class="border p-1">Quantity</th>
            <th class="border p-1">Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product">
            <td class="border p-1">{{ product }}</td>
            <td class="border p-1">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.qty || '-' }}</span>
              <input v-else type="number" :value="modelValue[product]?.qty || ''" class="w-16 border rounded px-1 py-0.5"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), qty: $event.target.value } })"
              />
            </td>
            <td class="border p-1">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.date || '-' }}</span>
              <input v-else type="date" :value="modelValue[product]?.date || ''" class="border rounded px-1 py-0.5"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), date: $event.target.value } })"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div v-for="product in products" :key="product" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-2">{{ product }}</h4>
        <div class="space-y-2">
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Quantity</label>
            <span v-if="isReadOnly" class="block text-sm font-medium">{{ modelValue[product]?.qty || '-' }}</span>
            <input v-else type="number" :value="modelValue[product]?.qty || ''" 
              class="w-full border rounded px-2 py-1.5 text-xs"
              placeholder="Enter quantity"
              @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), qty: $event.target.value } })"
            />
          </div>
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Date</label>
            <span v-if="isReadOnly" class="block text-sm font-medium">{{ modelValue[product]?.date || '-' }}</span>
            <input v-else type="date" :value="modelValue[product]?.date || ''" 
              class="w-full border rounded px-2 py-1.5 text-xs"
              @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), date: $event.target.value } })"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({ 
  products: Array, 
  modelValue: Object,
  isReadOnly: { type: Boolean, default: false }
});
</script>
