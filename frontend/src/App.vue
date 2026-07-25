<template>
  <div class="gui-container">
    <header class="gui-header">
      <div class="brand">
        <span class="logo">EL</span>
        <h2>EventLab Management GUI</h2>
      </div>
      <span class="status-pill">Vue 3 SPA</span>
    </header>
    <main class="gui-content">
      <div class="dashboard-card">
        <h3>Management Portal Placeholder</h3>
        <p>The Vue.js SPA frontend will be compiled here into <code>frontend/dist</code>.</p>
        <div class="actions">
          <a href="/" class="btn secondary">← Back to Public Site</a>
          <a href="/_api_test" class="btn primary">Test API Engine</a>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
// Future Vue 3 composition logic for EventLab management GUI
</script>

<style scoped>
.gui-container {
  min-height: 100vh;
  background: #0f172a;
  color: #f8fafc;
  font-family: system-ui, -apple-system, sans-serif;
}
.gui-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.25rem 2rem;
  background: #1e293b;
  border-bottom: 1px solid #334155;
}
.brand {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}
.logo {
  background: #38bdf8;
  color: #0f172a;
  font-weight: 800;
  padding: 0.4rem 0.6rem;
  border-radius: 6px;
}
.status-pill {
  background: rgba(56, 189, 248, 0.2);
  color: #38bdf8;
  padding: 0.3rem 0.8rem;
  border-radius: 20px;
  font-size: 0.85rem;
}
.gui-content {
  max-width: 800px;
  margin: 4rem auto;
  padding: 0 1rem;
}
.dashboard-card {
  background: #1e293b;
  border: 1px solid #334155;
  border-radius: 12px;
  padding: 2.5rem;
  text-align: center;
}
.dashboard-card h3 {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}
.dashboard-card p {
  color: #94a3b8;
  margin-bottom: 2rem;
}
.actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
}
.btn {
  padding: 0.6rem 1.2rem;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
}
.btn.primary {
  background: #38bdf8;
  color: #0f172a;
}
.btn.secondary {
  background: #334155;
  color: #f8fafc;
}
</style>
