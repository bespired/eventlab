<template>
  <div class="picker-placeholder swatch-picker">
    <div class="picker-title">🔴 Swatch Picker Placeholder</div>
    <div style="display: flex; gap: 0.5rem; justify-content: center;">
      <span v-for="color in ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']" :key="color" :style="{ backgroundColor: color }" class="interactive-tile" style="width: 24px; height: 24px; border-radius: 50%; display: inline-block;"></span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '#3b82f6' }
})
defineEmits(['update:modelValue'])
</script>
