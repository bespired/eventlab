import { createRouter, createWebHistory } from 'vue-router'

import dashboardRoutes  from './routes/dashboard'
import settingsRoutes   from './routes/settings'
import profileRoutes    from './routes/profile'
import eventlabRoutes   from './routes/eventlab'
import mailerRoutes     from './routes/mailer'
import automationRoutes from './routes/automation'
import analyticsRoutes  from './routes/analytics'

const router = createRouter({
  history: createWebHistory('/app/'),
  routes: [
    ...eventlabRoutes,
    ...dashboardRoutes,
    ...settingsRoutes,
    ...profileRoutes,
    ...mailerRoutes,
    ...automationRoutes,
    ...analyticsRoutes,
  ],
})

export default router
