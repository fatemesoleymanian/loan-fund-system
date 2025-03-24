<!-- eslint-disable vue/no-use-v-if-with-v-for -->
<template>
  <div class="col-12" >
    <q-card :class="`${has_style} header-tabs ${shadow}`"
      style="max-width: 95vw;"
    :style="height != null ? 'height: ' + height : ''">
      <q-tabs
        outside-arrows
        mobile-arrows
        v-model="tab"
        align="justify"
        animated
        transition-prev="fade"
        transition-next="fade"
        :style="`height: 100%;background-color:${tabs_bg_color};`"
        :dense="dense"
        :indicator-color="indicator_color"
        class="q-mb-sm"
        :active-color="color"
        :active-bg-color="bg_color"
        @update:model-value="$emit('value-updated', tabValue())">
        <q-route-tab class="h3-4 font-demi-bold" v-if="routeable"
         v-for="t in tabs" :key="t" :name="t.name" :label="t.label"
         :to="{ query: { tab: t.label } }" exact replace />

         <q-tab class="h3-4 font-demi-bold" v-else
         v-for="t in tabs" :key="t.label" :name="t.name" :label="t.label" :disable="t.disable!=null"/>
      </q-tabs>
    </q-card>
</div>
</template>

<script>
import { getPreference } from 'src/stores/localStoreVariables'
import { ref } from 'vue'
import { useRoute } from 'vue-router'
export default {
  props: {
    indicator_color: {
      type: String,
      default: 'transparent'
    },
    bg_color: {
      type: String,
      default: 'primary'
    },
    tabs_bg_color: {
      type: String,
      default: 'white'
    },
    shadow: {
      type: String,
      default: 'normal-shadow'
    },
    color: {
      type: String,
      default: 'white'
    },

    tabs: {
      type: Array
    },
    value: {
      type: Object
    },
    dense: {
      type: Boolean
    },
    height: {
      type: String
    },
    has_style: {
      type: String,
      default: 'style'
    },
    routeable: {
      type: Boolean,
      default: false
    },
    last: {
      type: Boolean,
      default: true
    }
  },

  data () {
    let v = null
    if (this.last) {
      const route = useRoute()
      let savedTab = getPreference(route.meta.nodeId) == null ? 0 : getPreference(route.meta.nodeId)
      savedTab = parseInt(savedTab)

      let indexx = null

      this.tabs.forEach((item, index) => {
        if (parseInt(item.name) === savedTab) {
          indexx = index
        }
      })
      indexx = savedTab == null ? 0 : indexx
      v = this.value == null ? this.tabs[indexx] : this.tabs.filter(item => item.name === this.value)[0]
    } else {
      v = this.tabs[0]
    }

    this.$emit('value-updated', v)
    return {
      tab: ref(this.tabs != null && v != null ? v.name : null)
    }
  },

  methods: {
    tabValue () {
      for (const t of this.tabs) {
        if (t.name === this.tab) return t
      }
      return null
    }
  }
}
</script>
