<template>
  <div class="picker-placeholder image-option-picker">
    <div class="picker-title">🖼️ Image Option Picker Placeholder</div>
    <div style="display: flex; gap: 0.5rem; justify-content: center;">
      <div v-for="item in 3" :key="item" class="interactive-tile" style="width: 60px; height: 60px; background: var(--bg-surface-elevated); border: 1px solid var(--border-color); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;">
        Tile {{ item }}
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number], default: null }
})
defineEmits(['update:modelValue'])
</script>
