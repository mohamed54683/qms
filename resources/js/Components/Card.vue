<template>
  <div class="bg-white shadow rounded border border-gray-200 mb-4">
    <div v-if="title" class="px-4 py-2 border-b border-gray-200 font-semibold text-sm tracking-wide bg-gray-50">{{ title }}</div>
    <div class="p-4">
      <slot />
    </div>
  </div>
</template>
<script setup>
defineProps({ title: { type: String, default: '' } });
</script>