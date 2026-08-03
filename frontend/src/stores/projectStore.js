import { ref, computed } from 'vue';

const API_URL = import.meta.env.VITE_API_BASE_URL || 'http://localhost/_';

const projects = ref([
  { tenant: 'a0', projectname: 'Eventlab' },
  { tenant: 'c3', projectname: 'Project2' }
]);
const loading = ref(false);
const error = ref(null);

export function useProjectStore() {
  const knownTenants = computed(() => {
    const tenants = projects.value.map((p) => p.tenant).filter(Boolean);
    return tenants.length > 0 ? tenants : ['a0', 'c3'];
  });

  async function fetchProjects() {
    loading.value = true;
    error.value = null;
    try {
      const response = await fetch(API_URL, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          package: 'system',
          controller: 'project',
          action: 'list'
        })
      });

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }

      const data = await response.json();
      if (data.status === 'success' && Array.isArray(data.projects) && data.projects.length > 0) {
        projects.value = data.projects;
      }
    } catch (err) {
      error.value = err.message;
      console.error('Failed to fetch projects:', err);
    } finally {
      loading.value = false;
    }
  }

  return {
    projects,
    knownTenants,
    loading,
    error,
    fetchProjects
  };
}
