export default [
  {
    path: '/:tenant/mailer',
    name: 'mailer',
    meta: { layout: 'TwoSplitTemplate' },
    components: {
      sideContent: () => import('../../views/mailer/MenuView.vue'),
      bodyContent: () => import('../../views/mailer/MainView.vue'),
    },
  },
]
