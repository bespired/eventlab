<template>
    <div class="menu-bar">
        <h2 class="menu-header">Dashboard</h2>
        <h3 class="menu-header">General</h3>
        <ul>
            <li>
                <router-link :to="`/${tenant}/dashboard/visitors`" class="">
                    <div class="menu-popup">Visitors</div>
                </router-link>
            </li>
        </ul>
        <h3 class="menu-header">Personas</h3>
        <div>No personas defined</div>
        <h3 class="menu-header">Qualifiers</h3>
        <div>No qualifiers defined</div>
    </div>
</template>

<script setup>
  import { computed } from 'vue';
  import { useRoute } from 'vue-router';

  const route = useRoute();

  const tenant = computed(() => {
    return route.params.tenant || 'a0'
  });

</script>
