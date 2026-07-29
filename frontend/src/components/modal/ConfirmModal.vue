<template>
  <BaseModal :is-open="isOpen" :title="title" @close="onCancel">
    <p style="color: var(--text-main); margin-bottom: 1rem;">{{ message }}</p>
    
    <template #footer>
      <ClickButton variant="secondary" @click="onCancel">Cancel</ClickButton>
      <ClickButton variant="primary" @click="onConfirm">Confirm</ClickButton>
    </template>
  </BaseModal>
</template>

<script setup>
defineProps({
  isOpen: { type: Boolean, default: false },
  title: { type: String, default: 'Confirm Action' },
  message: { type: String, default: 'Are you sure you want to perform this action?' }
})

const emit = defineEmits(['update:isOpen', 'confirm', 'cancel'])

function onConfirm() {
  emit('confirm')
  emit('update:isOpen', false)
}

function onCancel() {
  emit('cancel')
  emit('update:isOpen', false)
}
</script>
