import { boot } from 'quasar/wrappers'

export default boot(async (router) => {
  router.router.beforeEach((to, from, next) => {
    console.log(from.href)
    console.log(to.href)
    const token = localStorage.getItem('token')
    // if (from.href !== '#/' && token == null) router.router.push('/')
       next()

  })
})
