<template>
  <Teleport to="body">
    <div v-if="isOpen" class="modal-backdrop" @click.self="closeOnBackdrop && close()">
      <div class="base-modal">
        <header class="modal-header">
          <h3 style="color: var(--text-main); font-size: 1.15rem;">{{ title }}</h3>
          <IconButton title="Close" @click="close">✕</IconButton>
        </header>

        <main class="modal-body">
          <slot></slot>
        </main>

        <footer v-if="$slots.footer" class="modal-footer">
          <slot name="footer"></slot>
        </footer>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
const props = defineProps({
  isOpen: { type: Boolean, default: false },
  title: { type: String, default: 'Modal Dialog' },
  closeOnBackdrop: { type: Boolean, default: true }
})

const emit = defineEmits(['update:isOpen', 'close'])

function close() {
  emit('update:isOpen', false)
  emit('close')
}
</script>
