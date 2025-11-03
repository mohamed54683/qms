<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Category</th>
            <th class="border p-1">MIC (Morning)</th>
            <th class="border p-1">MIC (Evening)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="cat in categories" :key="cat">
            <td class="border p-1">{{ cat }}</td>
            <td class="border p-1 text-center">
              <span v-if="isReadOnly" class="inline-flex items-center justify-center">
                <span v-if="modelValue[cat]?.morning === 'Y'" class="text-green-600 font-bold text-lg">✓</span>
                <span v-else-if="modelValue[cat]?.morning === 'N'" class="text-red-600 font-bold text-lg">✗</span>
                <span v-else class="text-gray-400">-</span>
              </span>
              <select v-else :value="modelValue[cat]?.morning || ''" @change="$emit('update:modelValue', { ...modelValue, [cat]: { ...(modelValue[cat]||{}), morning: $event.target.value } })" class="border rounded px-1 py-0.5">
                <option value="">-</option>
                <option value="Y">✓</option>
                <option value="N">✗</option>
              </select>
            </td>
            <td class="border p-1 text-center">
              <span v-if="isReadOnly" class="inline-flex items-center justify-center">
                <span v-if="modelValue[cat]?.evening === 'Y'" class="text-green-600 font-bold text-lg">✓</span>
                <span v-else-if="modelValue[cat]?.evening === 'N'" class="text-red-600 font-bold text-lg">✗</span>
                <span v-else class="text-gray-400">-</span>
              </span>
              <select v-else :value="modelValue[cat]?.evening || ''" @change="$emit('update:modelValue', { ...modelValue, [cat]: { ...(modelValue[cat]||{}), evening: $event.target.value } })" class="border rounded px-1 py-0.5">
                <option value="">-</option>
                <option value="Y">✓</option>
                <option value="N">✗</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div v-for="cat in categories" :key="cat" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-2">{{ cat }}</h4>
        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">MIC (Morning)</label>
            <span v-if="isReadOnly" class="inline-flex items-center justify-center w-full py-1">
              <span v-if="modelValue[cat]?.morning === 'Y'" class="text-green-600 font-bold text-lg">✓ Yes</span>
              <span v-else-if="modelValue[cat]?.morning === 'N'" class="text-red-600 font-bold text-lg">✗ No</span>
              <span v-else class="text-gray-400">-</span>
            </span>
            <select v-else :value="modelValue[cat]?.morning || ''" 
              @change="$emit('update:modelValue', { ...modelValue, [cat]: { ...(modelValue[cat]||{}), morning: $event.target.value } })" 
              class="w-full border rounded px-2 py-1.5 text-xs">
              <option value="">-</option>
              <option value="Y">✓ Yes</option>
              <option value="N">✗ No</option>
            </select>
          </div>
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">MIC (Evening)</label>
            <span v-if="isReadOnly" class="inline-flex items-center justify-center w-full py-1">
              <span v-if="modelValue[cat]?.evening === 'Y'" class="text-green-600 font-bold text-lg">✓ Yes</span>
              <span v-else-if="modelValue[cat]?.evening === 'N'" class="text-red-600 font-bold text-lg">✗ No</span>
              <span v-else class="text-gray-400">-</span>
            </span>
            <select v-else :value="modelValue[cat]?.evening || ''" 
              @change="$emit('update:modelValue', { ...modelValue, [cat]: { ...(modelValue[cat]||{}), evening: $event.target.value } })" 
              class="w-full border rounded px-2 py-1.5 text-xs">
              <option value="">-</option>
              <option value="Y">✓ Yes</option>
              <option value="N">✗ No</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script setup>
const props = defineProps({ 
  modelValue: Object,
  isReadOnly: { type: Boolean, default: false }
});
const categories = ['Frozen','Packaging','Bread','Maintenance'];
</script>
