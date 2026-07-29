<template>
  <Teleport to="body">
    <div class="toast-container">
      <div 
        v-for="toast in toasts" 
        :key="toast.id" 
        class="toast-message"
        @click="removeToast(toast.id)"
      >
        <span>{{ toast.message }}</span>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { useToast } from '@/composables/useToast'

const { toasts, removeToast } = useToast()
</script>
