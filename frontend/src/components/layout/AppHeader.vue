<template>
  <header style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; background-color: var(--bg-surface); border-bottom: 1px solid var(--border-color);">
    <div style="display: flex; align-items: center; gap: 1rem;">
      <IconButton title="Toggle Drawer" @click="$emit('toggle-sidebar')">☰</IconButton>
      <h2 style="font-size: 1.2rem; color: var(--text-main); font-weight: 700;">EventLab Management Portal</h2>
    </div>
    
    <nav style="display: flex; align-items: center; gap: 1rem;">
      <router-link to="/" class="click-button variant-secondary" style="font-size: 0.85rem;">Home View</router-link>
      <router-link to="/checkout" class="click-button variant-primary" style="font-size: 0.85rem;">Checkout View</router-link>
      <UserAvatar initials="EL" />
    </nav>
  </header>
</template>

<script setup>
defineEmits(['toggle-sidebar'])
</script>
