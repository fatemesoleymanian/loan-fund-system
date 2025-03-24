/* eslint-disable */

import { boot } from 'quasar/wrappers'
import axios from 'axios'
import { Notify } from 'quasar'

const api = axios.create({ baseURL: 'https://sandogh.liara.run/api' })
api.interceptors.response.use(
  response => {
    // If the response is successful, return it directly
    return response
  },
  error => {
    // If there's an error response
    if (error.response && error.response.status === 401) {
      // Handle 401 error, for example, redirect to login page or show message
      Notify.create({
        message: 'نشست کاربری شما به پایان رسیده است. لطفا وارد حساب خود شوید!.',
        color: 'negative',
        classes: 'h4 font-medium'
      })

      localStorage.removeItem('token')
      console.error('Unauthorized access!')
      window.location.href = '/'


    }
    // Return the error promise to be caught in the calling function
    return Promise.reject(error)
  }
)

// axios.defaults.baseURL = process.env.Vue_APP_API_BASE_URL;
// axios.defaults.withCredentials = true;

export default boot(({ app }) => {
  // for use inside Vue files (Options API) through this.$axios and this.$api
  app.config.globalProperties.$axios = axios
  // ^ ^ ^ this will allow you to use this.$axios (for Vue Options API form)
  //       so you won't necessarily have to import axios in each vue file

  app.config.globalProperties.$api = api
  if (localStorage.getItem('token') !== null) {
    api.defaults.headers.common.Authorization = 'Bearer ' + localStorage.getItem('token')
  }

  // ^ ^ ^ this will allow you to use this.$api (for Vue Options API form)
  //       so you can easily perform requests against your app's API
})

export { axios, api }
