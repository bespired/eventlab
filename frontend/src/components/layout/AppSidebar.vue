<template>
  <aside v-if="isOpen" class="side-drawer">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
      <h3 style="color: var(--text-main); font-size: 1.1rem;">Navigation Drawer</h3>
      <IconButton title="Close drawer" @click="$emit('close')">✕</IconButton>
    </div>

    <nav style="display: flex; flex-direction: column; gap: 0.75rem;">
      <router-link to="/" class="click-button variant-secondary" style="justify-content: flex-start;" @click="$emit('close')">
        🏠 Dashboard Home
      </router-link>
      <router-link to="/checkout" class="click-button variant-secondary" style="justify-content: flex-start;" @click="$emit('close')">
        🛒 Checkout Wireframe
      </router-link>
    </nav>
  </aside>
</template>

<script setup>
defineProps({
  isOpen: { type: Boolean, default: false }
})

defineEmits(['close'])
</script>
