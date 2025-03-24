<template>
  <q-dialog >
      <CardPanel class="bg-white" hide_header>
          <template #body>
              <div class="row items-center">
                  <div class="h4 font-medium q-mr-sm">{{ message }}</div>
                  <q-avatar v-if="icon != null" :icon="icon" :color="color" :text-color="textColor" size="md"/>
              </div>
          </template>

          <template #actions>
              <q-btn unelevated
              label="بله"
              color="primary"
              class="col-6 style col-sm-3"
              style="max-width: 200px;"
              @click="$emit('on-ok')"
              v-close-popup/>

              <q-btn label="خیر" outline
              color="negative"
              class="col-6 style col-sm-3"
              style="max-width: 200px;"
              @click="$emit('on-cancel')"
              v-close-popup/>
          </template>
      </CardPanel>
  </q-dialog>
</template>

<script>
import CardPanel from '../CardPanel.vue';
export default {
props: ['message', 'icon', 'color', 'textColor'],

components: {
  CardPanel
}
}
</script>
