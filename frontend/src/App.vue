<template>
  <component v-if="layout" :is="layout" :key="layout" />
  <main v-else class="app-main-content" :key="layout" >
      <router-view />
  </main>
  <toast-container />
</template>

<script setup>
  import { computed, defineAsyncComponent } from 'vue'
  import { useRoute } from 'vue-router';

  const route = useRoute();

  const layout = computed(() => {
    const name = route.meta?.layout
    if (!name) return null

    return defineAsyncComponent(() =>
      import(`@/components/templates/${name}.vue`)
    )
  })
</script>