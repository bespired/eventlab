<template>
  <div class="app-layout">
    <AppHeader @toggle-sidebar="sidebarOpen = !sidebarOpen" />
    <AppSidebar :is-open="sidebarOpen" @close="sidebarOpen = false" />

    <main class="app-main-content">
      <router-view />
    </main>

    <AppFooter />
    <ToastContainer />
  </div>
</template>

<script setup>
import { ref } from 'vue'

const sidebarOpen = ref(false)
</script>

<style scoped>
.app-layout {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
  background-color: var(--bg-app);
}

.app-main-content {
  flex: 1;
  max-width: 1200px;
  width: 100%;
  margin: 0 auto;
  padding: 2rem 1.5rem;
}
</style>
