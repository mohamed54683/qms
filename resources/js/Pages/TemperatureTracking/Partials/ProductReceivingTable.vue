<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Product</th>
            <th class="border p-1">Date Received</th>
            <th class="border p-1">Production Date</th>
            <th class="border p-1">Expiry Date</th>
            <th class="border p-1">Frozen (Y) / Defrosted (N)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products" :key="product">
            <td class="border p-1">{{ product }}</td>
            <td class="border p-1">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.received || '-' }}</span>
              <input v-else type="date" :value="modelValue[product]?.received || ''" class="border rounded px-1 py-0.5 w-full"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), received: $event.target.value } })"
              />
            </td>
            <td class="border p-1">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.production || '-' }}</span>
              <input v-else type="date" :value="modelValue[product]?.production || ''" class="border rounded px-1 py-0.5 w-full"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), production: $event.target.value } })"
              />
            </td>
            <td class="border p-1">
              <span v-if="isReadOnly" class="font-medium">{{ modelValue[product]?.expiry || '-' }}</span>
              <input v-else type="date" :value="modelValue[product]?.expiry || ''" class="border rounded px-1 py-0.5 w-full"
                @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), expiry: $event.target.value } })"
              />
            </td>
            <td class="border p-1 text-center">
              <span v-if="isReadOnly" class="inline-flex items-center justify-center">
                <span v-if="modelValue[product]?.frozen === 'Y'" class="text-blue-600 font-bold">❄️ Frozen</span>
                <span v-else-if="modelValue[product]?.frozen === 'N'" class="text-orange-600 font-bold">🔥 Defrosted</span>
                <span v-else class="text-gray-400">-</span>
              </span>
              <select v-else :value="modelValue[product]?.frozen || ''" @change="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), frozen: $event.target.value } })" class="border rounded px-1 py-0.5 w-full">
                <option value="">-</option>
                <option value="Y">Y</option>
                <option value="N">N</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div v-for="product in products" :key="product" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-3 pb-2 border-b border-gray-300">{{ product }}</h4>
        
        <div class="space-y-2.5">
          <!-- Date Received -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Date Received</label>
            <span v-if="isReadOnly" class="block text-xs font-medium">{{ modelValue[product]?.received || '-' }}</span>
            <input v-else type="date" :value="modelValue[product]?.received || ''" 
              class="w-full border rounded px-2 py-1.5 text-xs"
              @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), received: $event.target.value } })"
            />
          </div>

          <!-- Production Date -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Production Date</label>
            <span v-if="isReadOnly" class="block text-xs font-medium">{{ modelValue[product]?.production || '-' }}</span>
            <input v-else type="date" :value="modelValue[product]?.production || ''" 
              class="w-full border rounded px-2 py-1.5 text-xs"
              @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), production: $event.target.value } })"
            />
          </div>

          <!-- Expiry Date -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Expiry Date</label>
            <span v-if="isReadOnly" class="block text-xs font-medium">{{ modelValue[product]?.expiry || '-' }}</span>
            <input v-else type="date" :value="modelValue[product]?.expiry || ''" 
              class="w-full border rounded px-2 py-1.5 text-xs"
              @input="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), expiry: $event.target.value } })"
            />
          </div>

          <!-- Frozen/Defrosted Status -->
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Frozen (Y) / Defrosted (N)</label>
            <span v-if="isReadOnly" class="inline-flex items-center">
              <span v-if="modelValue[product]?.frozen === 'Y'" class="text-blue-600 font-bold text-sm">❄️ Frozen</span>
              <span v-else-if="modelValue[product]?.frozen === 'N'" class="text-orange-600 font-bold text-sm">🔥 Defrosted</span>
              <span v-else class="text-gray-400 text-sm">-</span>
            </span>
            <select v-else :value="modelValue[product]?.frozen || ''" 
              @change="$emit('update:modelValue', { ...modelValue, [product]: { ...(modelValue[product]||{}), frozen: $event.target.value } })" 
              class="w-full border rounded px-2 py-1.5 text-xs">
              <option value="">-</option>
              <option value="Y">Y - Frozen ❄️</option>
              <option value="N">N - Defrosted 🔥</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="text-[10px] sm:text-xs text-red-600 mt-2 sm:mt-1 px-2 sm:px-0">
      ⚠️ If received product is damaged or broken, notify your Area Manager.
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
