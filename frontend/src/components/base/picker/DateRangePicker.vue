<template>
  <div class="picker-placeholder date-range-picker">
    <div class="picker-title">📅 Date Range Picker Placeholder</div>
    <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center;">
      <input type="date" class="date-input" style="max-width: 140px;" />
      <span>to</span>
      <input type="date" class="date-input" style="max-width: 140px;" />
    </div>
  </div>
</template>

<script setup>
defineProps({
  startDate: { type: String, default: '' },
  endDate: { type: String, default: '' }
})
defineEmits(['update:startDate', 'update:endDate'])
</script>
