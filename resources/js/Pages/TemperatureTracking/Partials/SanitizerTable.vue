<template>
  <div>
    <!-- Desktop Table View -->
    <div class="hidden md:block overflow-x-auto">
      <table class="min-w-full border text-xs">
        <thead>
          <tr>
            <th class="border p-1">Time</th>
            <th class="border p-1">PPM Reading</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="slot in timeSlots" :key="slot">
            <td class="border p-1">{{ slot }}</td>
            <td class="border p-1" :class="ppmClass(modelValue[slot])">
              <span v-if="isReadOnly">{{ modelValue[slot] || '' }}</span>
              <input v-else type="number" v-model="modelValue[slot]" class="w-20 border rounded px-1 py-0.5 bg-transparent"
                @input="$emit('update:modelValue', { ...modelValue, [slot]: $event.target.value })"
              />
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Mobile Card View -->
    <div class="md:hidden space-y-3">
      <div v-for="slot in timeSlots" :key="slot" class="bg-gray-50 rounded-lg border border-gray-200 p-3">
        <h4 class="font-bold text-sm text-gray-800 mb-2">{{ slot }}</h4>
        <div>
          <label class="block text-[10px] font-semibold text-gray-600 mb-1">PPM Reading</label>
          <span v-if="isReadOnly" class="block text-sm font-medium" :class="ppmClass(modelValue[slot])">
            {{ modelValue[slot] || '-' }}
          </span>
          <input v-else type="number" v-model="modelValue[slot]" 
            class="w-full border rounded px-2 py-1.5 text-xs bg-transparent"
            :class="ppmClass(modelValue[slot])"
            placeholder="150-300 ppm"
            @input="$emit('update:modelValue', { ...modelValue, [slot]: $event.target.value })"
          />
        </div>
      </div>
    </div>

    <div class="text-[10px] sm:text-xs text-red-600 mt-1">* PPM below recommended triggers alert</div>
  </div>
</template>
<script setup>
const props = defineProps({ modelValue: Object, isReadOnly: Boolean });
const timeSlots = ['11AM-2PM','3PM-6PM','7PM-10PM','11PM-2AM']; // Use regular dash to match database
function ppmClass(val) {
  if (!val) return '';
  if (val < 200) return 'bg-red-200'; // Example threshold
  return '';
}
</script>
