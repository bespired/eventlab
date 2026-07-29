<template>
  <div :class="['notification-banner', `variant-${variant}`]">
    <div style="display: flex; align-items: center; gap: 0.75rem;">
      <span style="font-weight: 700;">{{ icon }}</span>
      <span style="font-size: 0.9rem;"><slot>{{ message }}</slot></span>
    </div>
    <IconButton v-if="dismissable" title="Dismiss" @click="$emit('dismiss')">✕</IconButton>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  message: { type: String, default: '' },
  variant: {
    type: String,
    default: 'info',
    validator: (v) => ['info', 'success', 'warning', 'cancel'].includes(v)
  },
  dismissable: { type: Boolean, default: true }
})

defineEmits(['dismiss'])

const icon = computed(() => {
  switch (props.variant) {
    case 'success': return '✅'
    case 'warning': return '⚠️'
    case 'cancel': return '🚨'
    default: return 'ℹ️'
  }
})
</script>
