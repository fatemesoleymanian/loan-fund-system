<template>
  <q-card-section class="text-center">
      <div class="h2 font-demi-bold q-pt-lg">{{ title }}</div>
      <div class="h4 font-medium" v-if="header">{{ header }}</div>
  </q-card-section>
  <q-card-section>
      <slot name="body"></slot>
  </q-card-section>
  <q-card-actions align="center">
      <slot name="actions"></slot>
  </q-card-actions>
  <q-card-actions align="center">
      <slot name="extra-actions"></slot>
  </q-card-actions>
</template>

<script>
export default {
props: ['title', 'header']
}
</script>
