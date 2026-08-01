export default [
  {
    path: '/:tenant/settings',
    name: 'settings',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/settings/MenuView.vue'),
      bodyContent: () => import('../../views/settings/MainView.vue'),
    },
  },
]
