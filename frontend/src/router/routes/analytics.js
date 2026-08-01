export default [
  {
    path: '/:tenant/analytics',
    name: 'analytics',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/analytics/MenuView.vue'),
      bodyContent: () => import('../../views/analytics/MainView.vue'),
    },
  },
]
