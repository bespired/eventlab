export default [
  {
    path: '/:tenant/profiles',
    name: 'profile',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/profile/MenuView.vue'),
      bodyContent: () => import('../../views/profile/MainView.vue'),
    },
  },
]
