<template>
  <BaseModal :is-open="isOpen" :title="title" @close="onCancel">
    <TextArea 
      v-model="noteText" 
      label="Add Note / Comment" 
      placeholder="Type your notes here..." 
      :rows="4" 
    />

    <template #footer>
      <ClickButton variant="secondary" @click="onCancel">Cancel</ClickButton>
      <ClickButton variant="success" @click="onSave">Save Note</ClickButton>
    </template>
  </BaseModal>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  isOpen: { type: Boolean, default: false },
  title: { type: String, default: 'Add Note' }
})

const emit = defineEmits(['update:isOpen', 'save', 'cancel'])

const noteText = ref('')

function onSave() {
  emit('save', noteText.value)
  noteText.value = ''
  emit('update:isOpen', false)
}

function onCancel() {
  emit('cancel')
  emit('update:isOpen', false)
}
</script>
