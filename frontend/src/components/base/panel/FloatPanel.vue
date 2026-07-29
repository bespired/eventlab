<template>
  <div class="float-panel">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
      <h4 style="color: var(--text-main); font-size: 1rem;">{{ title }}</h4>
      <IconButton v-if="closable" title="Close" @click="$emit('close')">✕</IconButton>
    </div>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: 'Floating Panel' },
  closable: { type: Boolean, default: true }
})
defineEmits(['close'])
</script>
