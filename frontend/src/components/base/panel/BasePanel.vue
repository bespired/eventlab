<template>
  <div class="base-panel">
    <h3 v-if="title" style="margin-bottom: 1rem; color: var(--text-main); font-size: 1.1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">
      {{ title }}
    </h3>
    <slot></slot>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' }
})
</script>
