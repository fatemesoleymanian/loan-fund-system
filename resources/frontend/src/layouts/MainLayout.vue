<template>
  <q-layout view="hHh lpR fFf">
    <q-header dense elevated class="bg-primary text-white" >
      <q-toolbar>
        <q-toolbar-title>
          <q-avatar>
            <img src="https://cdn.quasar.dev/logo-v2/svg/logo-mono-white.svg">
          </q-avatar>
          نرم افزار صندوق
        </q-toolbar-title>

        <!-- <q-btn dense flat round icon="menu" @click="toggleRightDrawer" /> -->
      </q-toolbar>
    </q-header>

    <q-drawer dense show-if-above v-model="rightDrawerOpen" side="right" bordered :width="200">
      <q-list>
        <q-item-label
          header
        >
          صفحات
        </q-item-label>

        <EssentialLink
          v-for="link in linksList"
          :key="link.title"
          v-bind="link"
        />
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view @on-notify="showNotify"
      @on-ok-dialog="onOkDialog"/>
      <OkCancelDialog
      v-model="okDialogOpen"
      :message="okDialogAttrs.message"
      :icon="okDialogAttrs.icon"
      :color="okDialogAttrs.color"
      :text-color="okDialogAttrs.textColor"
      @on-ok="okDialogAttrs.onOk"
      @on-cancel="okDialogAttrs.onCancel"/>
    </q-page-container>
  </q-layout>
</template>

<script setup>
import { ref } from 'vue'
import EssentialLink from 'components/EssentialLink.vue'
import { useQuasar } from 'quasar';
import OkCancelDialog from 'src/components/Dialogs/OkCancelDialog.vue';
import { provide } from 'vue';
defineOptions({
  name: 'MainLayout'
})

const linksList = [
  {
    title: 'حساب ها',
    icon: 'school',
    link: '#/accounts'
  },
  {
    title: 'وام ها',
    icon: 'code',
    link: '#/loans'
  },
  {
    title: 'اقساط و ماهیانه ها',
    icon: 'code',
    link: '#/installments'
  },
  {
    title: 'دفترتلفن',
    icon: 'record_voice_over',
    link: '#/members'
  },
  {
    title: 'مدیریت حساب ها',
    icon: 'rss_feed',
    link: '#/fund'
  }
]

const rightDrawerOpen = ref(true)
const $q = useQuasar()
const okDialogAttrs = ref({
  message:'',
  icon:'',
  color:'',
  textColor:'',
  onOk: null,
  onCancel: null
})
const okDialogOpen = ref(false)

function toggleRightDrawer () {
        rightDrawerOpen.value = !rightDrawerOpen.value
      }
function showNotify (message, type = 'positive') {
  $q.notify({
    timeout: 2000,
    progress: true,
    message,
    type,
    classes: 'h4 font-medium',
    group: 'uniqueGroupName' // Group your notifications

  })
}
function onOkDialog (dialogData) {
      okDialogAttrs.value = dialogData
      okDialogOpen.value = true
}
provide('onOkDialog', (dialogData) => {
  okDialogAttrs.value = dialogData;
  okDialogOpen.value = true;
})
</script>
