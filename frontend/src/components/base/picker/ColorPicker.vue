<template>
  <div class="picker-placeholder color-picker">
    <div class="picker-title">🎨 Color Picker Placeholder</div>
    <div style="display: flex; gap: 0.5rem; justify-content: center; align-items: center; margin-top: 0.5rem;">
      <input type="color" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" style="border: none; background: transparent; cursor: pointer; width: 32px; height: 32px;" />
      <span>Selected: {{ modelValue }}</span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: String, default: '#3b82f6' }
})
defineEmits(['update:modelValue'])
</script>
