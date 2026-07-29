<template>
  <div class="picker-placeholder tag-picker">
    <div class="picker-title">🏷️ Tag Picker Placeholder</div>
    <div style="display: flex; gap: 0.4rem; justify-content: center; flex-wrap: wrap;">
      <span v-for="tag in ['VIP', 'Speaker', 'Sponsor']" :key="tag" style="background: var(--color-primary); color: white; padding: 0.2rem 0.6rem; border-radius: 12px; font-size: 0.8rem;">
        {{ tag }} ✕
      </span>
    </div>
  </div>
</template>

<script setup>
defineProps({
  tags: { type: Array, default: () => [] }
})
defineEmits(['update:tags'])
</script>
