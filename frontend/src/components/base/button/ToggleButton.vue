<template>
  <button 
    :class="['toggle-button', { 'is-active': modelValue }]"
    @click="toggle"
  >
    <span class="toggle-indicator">{{ modelValue ? 'ON' : 'OFF' }}</span>
    <slot>{{ label }}</slot>
  </button>
</template>

<script setup>
const props = defineProps({
  modelValue: { type: Boolean, default: false },
  label: { type: String, default: 'Toggle' }
})

const emit = defineEmits(['update:modelValue', 'change'])

function toggle() {
  const newValue = !props.modelValue
  emit('update:modelValue', newValue)
  emit('change', newValue)
}
</script>
