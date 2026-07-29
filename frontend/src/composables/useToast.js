import { ref } from 'vue'

const toasts = ref([])

export function useToast() {
  function addToast(message, type = 'info', duration = 3000) {
    const id = Date.now() + Math.random()
    toasts.value.push({ id, message, type })
    
    if (duration > 0) {
      setTimeout(() => {
        removeToast(id)
      }, duration)
    }
  }

  function removeToast(id) {
    toasts.value = toasts.value.filter(t => t.id !== id)
  }

  return {
    toasts,
    addToast,
    removeToast
  }
}
