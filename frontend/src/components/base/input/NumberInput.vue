<template>
  <div class="input-wrapper">
    <label v-if="label" class="input-label">{{ label }}</label>
    <input 
      type="number" 
      class="number-input" 
      :value="modelValue" 
      :min="min"
      :max="max"
      :step="step"
      :placeholder="placeholder"
      :disabled="disabled"
      @input="$emit('update:modelValue', Number($event.target.value))"
    />
  </div>
</template>

<script setup>
defineProps({
  modelValue: { type: [Number, String], default: 0 },
  label: { type: String, default: '' },
  min: { type: Number, default: undefined },
  max: { type: Number, default: undefined },
  step: { type: Number, default: 1 },
  placeholder: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['update:modelValue'])
</script>
