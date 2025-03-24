<template>
  <div class="row items-center bg-white justify-center" style="max-height: 100vh!important;height: 100vh;">
    <div class="col-12 col-sm-6 q-pa-xl" style="border:1px solid grey;border-radius:10px;" >
      <q-form
        @submit="loginSubmit"
        @reset="onReset" class="q-px-lg">
        <!-- <div class="row text-center justify-center items-center">
          <div class="h1 text-primary font-bold q-ml-sm q-pt-sm">صندوق</div>
        </div> -->
      <LoginSignupElements title="ورود به حساب کاربری" >
        <template #body>
          <div v-if="login" style="margin-top: -15px!important">
            <q-input
            outline
            v-model="username"
            class="style"
            maxlength="40"
            type="text"
            outlined
            @update:model-value="console.log(username)"
            placeholder="نام کاربری">
        </q-input>
            <q-input v-model="password" class="style"
             style="margin-top: 18px;" :type="isPwd ? 'password' : 'text'"  outlined
               placeholder="رمز عبور" maxlength="12" :rules="[ val => val.length >= 4 || 'طول  رمز عبور نباید کمتر از 4  کاراکتر باشد']">
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>
          </div>
        </template>
        <template #actions>
          <div class="row col-12 justify-center q-px-md">
            <!-- <q-btn v-if="login" unelevated dense color="primary" class="style" label="فراموشی کلمه عبور؟" flat/> -->
            <q-btn color="primary" unelevated type="submit"
             class="font-bold col-12 style" label="ورود" :loading="loading"
            icon="login" size="lg"/>
          </div>
        </template>
      </LoginSignupElements>
    </q-form>
      </div>

      <q-inner-loading :showing="loading">
          <q-spinner-gears size="10em" color="primary" />
      </q-inner-loading>
  </div>
</template>

<script>
/* eslint-disable */
import LoginSignupElements from '../components/Login/Login-Signup-Elements.vue';
import { ref } from 'vue'
import { api } from 'src/boot/axios'
import { useQuasar } from 'quasar'

export default {
setup (props) {
  return {
    $q: useQuasar(),
    loading: ref(false)
  }
},
data () {
  const $q = useQuasar()
  return {
    login: ref(true),
    isPwd: ref(true),
    username: ref(null),
    password: ref(null),
    showNotify (message, type = 'positive') {
      $q.notify({
        timeout: 2000,
        progress: true,
        message,
        type,
        classes: 'h4 font-medium'
      })
    }
  }
},

components: {
  LoginSignupElements
},

methods: {

  async loginSubmit () {
      this.loading = true
       await api.post('/login', {
        username: this.username,
        password: this.password
      }).then(async response => {
        const token = response.data.token
        api.defaults.headers.common.Authorization = 'Bearer ' + token

        if (token != null) {
          localStorage.setItem('token', token)
            this.showNotify('با موفقیت وارد شدید')
            this.$router.push('/fund')
        } else {
          this.showNotify('خطا در ورود به حساب کاربری', 'negative')
        }
      }).catch(obj => {
        const data = obj.response.data.errorsList
        this.loading = false
        for (const k of data) {
          this.showNotify(k, 'negative')
        }
      })
  },

}
}
</script>
<style>
.vue_client_recaptcha_icon{
position: absolute;
}
</style>
