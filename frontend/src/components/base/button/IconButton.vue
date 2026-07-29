<template>
  <button 
    class="icon-button"
    :title="title"
    :disabled="disabled"
    @click="$emit('click', $event)"
  >
    <slot>⚙️</slot>
  </button>
</template>

<script setup>
defineProps({
  title: { type: String, default: '' },
  disabled: { type: Boolean, default: false }
})

defineEmits(['click'])
</script>
