<template>
  <div class="user-avatar" :title="name">
    {{ initials }}
  </div>
</template>

<script setup>
defineProps({
  initials: { type: String, default: 'US' },
  name: { type: String, default: 'User' }
})
</script>
