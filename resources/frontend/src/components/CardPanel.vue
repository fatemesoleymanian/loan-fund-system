<template>
  <q-card class="card-panel-style style" :class="`${flat == null ?'' : ' normal-shadow'}`"
  :style="`min-width: ${size};`" >
    <form
      @submit.prevent.stop="loading=true; this.$emit('on-submit', this);">
          <q-card-section v-if="hide_header == null">
            <div>
              <!-- <q-btn v-if="x_icon" icon="close" size="md"  flat round dense v-close-popup /> -->
              <slot name="top-header"></slot>
            </div>
            <div class="col-12 col-md-6">
              <slot name="header-right">
                <!-- <q-btn v-if="x_icon" icon="close" size="md"  flat round dense v-close-popup /> -->

                  <div class="gt-xs h2 font-bold q-px-sm " :class="titleClass">{{ title }}
                  <q-icon :name="helpIcon" style="cursor: pointer;">
                    <q-tooltip>{{ description }}</q-tooltip>
                  </q-icon>
                  </div>
                  <div class="lt-sm h4 font-demi-bold q-px-sm " :class="titleClass">{{ title }}
                  <q-icon :name="helpIcon" style="cursor: pointer;">
                    <q-tooltip>{{ description }}</q-tooltip>
                  </q-icon>
                  </div>
                  <!--div style="justify-content: left;" class="row items-center" v-if="active_toggle != null">
                      <div class="h3 font-demi-bold q-mr-xs">{{ toggle_title }}:</div>
                      <q-toggle v-model="status"/>
                      <div class="h4 font-regular">{{ status ? active_toggle : inactive_toggle }}</div>
                  </div-->
              </slot>
            </div>

            <div style="justify-content: left;" class="col-12 col-md-6 row">
              <slot name="header-left"></slot>
            </div>
          </q-card-section>
          <slot name="card-sections" ></slot>
          <q-card-section v-if="hideBody == null" >
              <slot name="body"></slot>
          </q-card-section>
          <q-card-actions align="center" v-if="hide_actions == null" >
              <slot name="actions">
                  <q-btn label="ثبت" unelevated color="primary" class="col-6 style col-sm-3" style="max-width: 200px;" :loading="loading" type="submit"
                  v-if="show_deafult_btns==null"/>
                  <q-btn unelevated v-if="show_deafult_btns==null && def_cancel_btn==null" :label="`لغو${cancel ?'/بستن':''}`" outline color="negative" class="col-6 style font-demi-bold col-sm-3" style="max-width: 200px;" v-close-popup
                  @click="this.$emit('on-cancel')"/>
              </slot>
          </q-card-actions>
    </form>
  </q-card>
</template>

<script>
import { useQuasar } from 'quasar'
import { api } from 'src/boot/axios'
import { ref } from 'vue'
export default {
props: ['action_id', 'x_icon', 'title', 'titleClass', 'helpIcon', 'cancel', 'description', 'toggle_title', 'size', 'def_cancel_btn', 'hide_header', 'hide_actions', 'show_deafult_btns', 'hideBody', 'flat', 'disableLoading', 'disableNotify'],

setup () {
const $q = useQuasar()
return {
loading: ref(false),
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

methods: {
async submit (postObj,method='post') {
await api[method](postObj.url, postObj.value).then(response => {

  this.$emit('on-success', response.data)
  if (!this.disableNotify) this.showNotify(response.data.msg)

  this.loading = false
}).catch(error => {
  // console.log(error)
  this.loading = false
  if (error.response != null ) {
    alert(error.response.data.message || error.response.data.msg)
  }
})
},

async submitForm (postObj) {
await api.postForm(postObj.url, postObj.value).then(response => {
  // console.log(response)
  if (!this.disableNotify) this.showNotify('با موفقیت ثبت شد')
  this.$emit('on-success')
  this.loading = false
}).catch(error => {
  // console.log(error)
  this.loading = false
  if (!this.disableNotify ) {
    alert(error.response.data.message)
  }
})
}
}
}
</script>
