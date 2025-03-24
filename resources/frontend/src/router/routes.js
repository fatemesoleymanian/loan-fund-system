const routes = [
  {
    path: '/',
    component: () => import('../layouts/LoginSignup.vue'),
    name: 'Login'
  },
  {
    path: '/',
    component: () => import('layouts/MainLayout.vue'),
    children: [
      { path: 'fund', component: () => import('src/pages/FundAccounts.vue') },
      { path: 'loans', component: () => import('src/pages/LoansPage.vue') },
      { path: 'members', component: () => import('src/pages/MembersPage.vue') },
      { path: 'installments', component: () => import('src/pages/InstallmentsPage.vue') },
      { path: 'accounts', component: () => import('src/pages/AccountsPage.vue') },
    ]
  },

  // Always leave this as last one,
  // but you can also remove it
  {
    path: '/:catchAll(.*)*',
    component: () => import('pages/ErrorNotFound.vue')
  }
]

export default routes
