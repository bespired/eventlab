export default [
  {
    path: '/:tenant/automation',
    name: 'automation',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/automation/MenuView.vue'),
      bodyContent: () => import('../../views/automation/MainView.vue'),
    },
  },
]
