import { createRouter, createWebHistory } from 'vue-router'
import HomeView     from '@/views/HomeView.vue'
import CheckoutView from '@/views/CheckoutView.vue'

const router = createRouter({
  history: createWebHistory('/app/'),
  routes: [
    {
      path: '/',
      name: 'home',
      meta: { layout: 'HomeWindow' },
      component: HomeView
    },
    {
      path: '/split',
      name: 'split',
      meta: {
        layout: 'TwoSplitTemplate',
      },
      components: { // plural
          sideContent: () => import('../views/TestView.vue'),
          bodyContent: () => import('../views/TestView.vue'),
      },
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: () => import('../views/CheckoutView.vue')
    },
    {
      path: '/design',
      name: 'design',
      component: () => import('../views/DesignView.vue')
    }
  ]
})

export default router
