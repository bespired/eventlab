export default [
  {
    path: '/:tenant/dashboard',
    name: 'dashboard',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/dashboard/MenuView.vue'),
      bodyContent: () => import('../../views/dashboard/MainView.vue'),
    },
  },
]
