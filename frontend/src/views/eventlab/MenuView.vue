<template>
    <div class="menu-bar">
        <h2 class="menu-header">Eventlab</h2>
        <h4 class="menu-header">Projects</h4>
        <ul>
            <li v-for="project in projects" :key="project.tenant">
                <router-link :to="`/${project.tenant}/dashboard`">
                    <big-icon name="menu-gauge" />
                    <div class="menu-popup">{{ project.projectname || project.clientname || project.tenant }}</div>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<style>
  .menu-bar li a {
    display: flex;
    flex-direction: row;
    gap: 4px;
    text-decoration: none;
  }
</style>

<script setup>
  import { onMounted, onBeforeUnmount } from 'vue';
  import { useRoute } from 'vue-router';
  import { useProjectStore } from '@/stores/projectStore';

  const route = useRoute();
  const { projects, knownTenants, fetchProjects } = useProjectStore();

  onMounted(() => {
    fetchProjects();
  });

  onBeforeUnmount(() => {
    const tenant = route.params.tenant;
    if (tenant && knownTenants.value.includes(tenant)) {
      localStorage.setItem('tenant', tenant);
    }
  });

</script>

