<template>
  <footer style="text-align: center; padding: 1.5rem; color: var(--text-muted); font-size: 0.85rem; border-top: 1px solid var(--border-color); margin-top: auto; background-color: var(--bg-surface);">
    <p>&copy; {{ currentYear }} EventLab Management GUI. All rights reserved.</p>
  </footer>
</template>

<script setup>
import { computed } from 'vue'

const currentYear = computed(() => new Date().getFullYear())
</script>
