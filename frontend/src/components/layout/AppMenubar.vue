<template>
    <aside class="side-menu">
        <ul>
            <li>
                <router-link to="/" class="">
                    <div class="menu-popup">Eventlab</div>
                    <big-icon name="menu-eventlab" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/dashboard`" :class="selectedRoute('dashboard')">
                    <div class="menu-popup">Dashboard</div>
                    <big-icon name="menu-gauge" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/profiles`" :class="selectedRoute('profile')">
                    <div class="menu-popup">Profiles</div>
                    <big-icon name="menu-profile" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/analytics`" :class="selectedRoute('analytics')">
                    <div class="menu-popup">Analytics</div>
                    <big-icon name="menu-segment" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/mailer`" :class="selectedRoute('mailer')">
                    <div class="menu-popup">Mailer</div>
                    <big-icon name="menu-mail" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/automation`" :class="selectedRoute('automation')">
                    <div class="menu-popup">Automation</div>
                    <big-icon name="menu-rocket" />
                </router-link>
            </li>
            <li>
                <router-link :to="`/${tenant}/settings`" :class="selectedRoute('setting')">
                    <div class="menu-popup">Settings</div>
                    <big-icon name="menu-cog" />
                </router-link>
            </li>
        </ul>
    </aside>
</template>

<script setup>
  import { computed } from 'vue';
  import { useRoute } from 'vue-router';

  const route = useRoute();

  const tenant = computed(() => {
    const localTenant = localStorage.getItem('tenant') || 'a0'
    return route.params.tenant || localTenant
  })

  const selectedRoute = (word) => {
    const parts = route.path.split('/').filter(Boolean)
    const tenantIndex = parts.indexOf(tenant.value)
    const afterTenant = parts.slice(tenantIndex + 1)
    return afterTenant.includes(word) ? 'selected' : ''
  }
</script>
