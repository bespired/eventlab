<template>
  <div class="input-wrapper">
    <label v-if="label" class="input-label" :for="id">{{ label }}</label>
    <input
      :id="id"
      ref="inputRef"
      type="text"
      class="text-input"
      :value="modelValue"
      :placeholder="placeholder"
      :disabled="disabled"
      v-bind="$attrs"
      @input="$emit('update:modelValue', $event.target.value)"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'

defineOptions({
  inheritAttrs: false
})

defineProps({
  modelValue: { type: [String, Number], default: '' },
  label: { type: String, default: '' },
  placeholder: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

const id = 'ti' + Math.floor(Math.random() * 9E+14).toString(26);
const inputRef = ref(null)

const focus = () => {
  inputRef.value?.focus()
}

const select = () => {
  inputRef.value?.select()
}

defineExpose({
  focus,
  select,
  inputRef
})

defineEmits(['update:modelValue'])
</script>
