<template>
  <button 
    :type="type" 
    :class="['click-button', `variant-${variant}`]"
    :disabled="disabled || variant === 'disabled'"
    @click="$emit('click', $event)"
  >
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup>
defineProps({
  label: { type: String, default: '' },
  variant: { 
    type: String, 
    default: 'primary',
    validator: (val) => ['primary', 'secondary', 'success', 'warning', 'cancel', 'disabled'].includes(val)
  },
  type: { type: String, default: 'button' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['click'])
</script>
