<template>
  <label class="radio-label">
    <input 
      type="radio" 
      class="radio-button"
      :name="name"
      :value="value"
      :checked="modelValue === value"
      :disabled="disabled"
      @change="$emit('update:modelValue', value)"
    />
    <span>{{ label }}</span>
  </label>
</template>

<script setup>
defineProps({
  modelValue: { type: [String, Number, Boolean], default: '' },
  value: { type: [String, Number, Boolean], required: true },
  name: { type: String, default: '' },
  label: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>
