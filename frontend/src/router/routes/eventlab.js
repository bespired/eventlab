export default [
  {
    path: '/',
    name: 'home',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/eventlab/MenuView.vue'),
      bodyContent: () => import('../../views/eventlab/HomeView.vue'),
    },
  },
  {
    path: '/checkout',
    name: 'checkout',
    component: () => import('../../views/eventlab/CheckoutView.vue'),
  },
  {
    path: '/design',
    name: 'design',
    component: () => import('../../views/eventlab/DesignView.vue'),
  },
]
