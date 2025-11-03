<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Equipment</th>
            <th class="border p-1">Target Temp</th>
            <th class="border p-1">Status (Y/N)</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="item in equipmentList" :key="item.name">
            <td class="border p-1">{{ item.name }}</td>
            <td class="border p-1">{{ item.target }}</td>
            <td class="border p-1 text-center">
              <span v-if="isReadOnly" class="inline-flex items-center gap-1">
                <span v-if="modelValue[item.name] === 'Y'" class="text-green-600 font-bold flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                  </svg>
                  Yes
                </span>
                <span v-else-if="modelValue[item.name] === 'N'" class="text-red-600 font-bold flex items-center gap-1">
                  <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                  </svg>
                  No
                </span>
                <span v-else class="text-gray-400">-</span>
              </span>
              <select v-else v-model="modelValue[item.name]" @change="$emit('update:modelValue', { ...modelValue, [item.name]: $event.target.value })" class="border rounded px-1 py-0.5 w-full">
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
      <div v-for="item in equipmentList" :key="item.name" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-2">{{ item.name }}</h4>
        <div class="space-y-2">
          <div class="flex items-center justify-between text-xs">
            <span class="text-gray-600">Target Temp:</span>
            <span class="font-semibold">{{ item.target }}</span>
          </div>
          <div>
            <label class="block text-[10px] font-semibold text-gray-600 mb-1">Status (Y/N)</label>
            <span v-if="isReadOnly" class="inline-flex items-center gap-1 text-sm">
              <span v-if="modelValue[item.name] === 'Y'" class="text-green-600 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                </svg>
                Yes
              </span>
              <span v-else-if="modelValue[item.name] === 'N'" class="text-red-600 font-bold flex items-center gap-1">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                No
              </span>
              <span v-else class="text-gray-400">-</span>
            </span>
            <select v-else v-model="modelValue[item.name]" @change="$emit('update:modelValue', { ...modelValue, [item.name]: $event.target.value })" class="w-full border rounded px-2 py-1.5 text-xs">
              <option value="">-</option>
              <option value="Y">Y - Yes ✓</option>
              <option value="N">N - No ✗</option>
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
const equipmentList = [
  { name: 'Reach-In Chiller (Thaw)', target: '36–40°F' },
  { name: 'Reach-In Chiller (Veg)', target: '36–40°F' },
  { name: 'Walk-In Freezer (Frozen)', target: '0 ±10°F' },
  { name: 'Walk-In Freezer (Reach-In)', target: '0 ±10°F' },
];
</script>
