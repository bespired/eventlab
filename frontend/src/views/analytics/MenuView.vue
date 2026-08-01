<template>
    <div class="menu-bar">
        <h2 class="menu-header">Analytics</h2>
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
