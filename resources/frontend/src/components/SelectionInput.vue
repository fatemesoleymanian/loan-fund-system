<template>
  <q-select v-if="is_in_table" class="q-select-in-table"
     :class="extra_class" dropdown-icon=""
     popup-content-class="font-medium h4-5"
     clearable
     clear-icon="close"
     dense
     v-model="model"
     :style="extra_style"
     input-debounce="0"
     hide-selected
     behavior="menu"
     :options="options"
     @update:model-value="onUpdateValue"
      @filter="filterFn"
     @filter-abort="abortFilterFn"
     @input-value="onInputValue"
     :multiple="multible"
     options-selected-class="selected-option-qselect">

       <template v-slot:append >
       <q-icon clickable name="fa-solid fa-filter" style="font-size: 12px!important;" />
       </template>
       <template v-slot:selected-item="scope">
         <slot name="selected-item" :scope="scope">
           <div v-html="scope.opt.label"></div>
         </slot>
       </template>
     <!-- <template v-slot:prepend v-if="prepend_icon">
         <q-icon  :name="prepend_icon" @click.stop.prevent="onPrependIcon"
         style="cursor: pointer;"/>
       </template> -->
     <template v-slot:no-option>
       <q-item>
         <q-item-section class="text-grey font-demi-bold">
           اطلاعاتی در دسترس نیست
         </q-item-section>
       </q-item>
     </template>

   </q-select>
   <q-select v-else
     :class="'style font-demi-bold h4-5 ' + extra_class"
     popup-content-class="font-medium h4-5"
     outlined
     :dense="dense"
     v-model="model"
     clearable
     clear-icon="close"
     use-input
     :style="extra_style"
     :fill-input="multible"
     input-debounce="0"
     :hide-selected="multible && !showSelected"
     :label="label"
     :hint="hint"
     behavior="menu"
     :options="options"
     @update:model-value="onUpdateValue"
     @input-value="onInputValue"
     @filter="filterFn"
     @filter-abort="abortFilterFn"
     :multiple="multible"
     options-selected-class="selected-option-qselect"
     @blur="onBlur">
   <template v-slot:after v-if="has_after">
         <q-btn dense :label="after_label" :color="after_color" unelevated
         @click="onClickAfterSlot"/>
         <!-- <q-btn dense :icon="after_icon" :label="after_label" :color="after_color" unelevated
         @click="onClickAfterSlot"/> -->
       </template>
     <template v-slot:option="scope" v-if="hasSlot">
       <slot name="option" :scope="scope"></slot>
     </template>
     <template v-slot:selected-item="scope">
       <slot name="selected-item" :scope="scope">
         <div v-html="scope.opt.label"></div>
       </slot>
     </template>
     <template v-slot:append v-if="multible">
         <q-icon style="cursor: pointer;" v-if="multible" name="task_alt" size="xs"  @click="selectAll">
           <q-tooltip>
             انتخاب همه
           </q-tooltip>
       </q-icon>
       <!-- <q-icon style="cursor: pointer;" name="o_cancel" size="xs" v-if="model!==null" @click="selectNone">
           <q-tooltip>
             هیچکدام
           </q-tooltip>
       </q-icon> -->
       </template>
       <template v-slot:append v-else>
       <q-icon clickable :name="append_icon" v-if="append_icon!==''"/>
       </template>
     <template v-slot:prepend v-if="prepend_icon">
         <q-icon  :name="prepend_icon" @click.stop.prevent="onPrependIcon"
         style="cursor: pointer;"/>
       </template>
     <template v-slot:no-option>
       <q-item>
         <q-item-section class="text-grey font-demi-bold">
           اطلاعاتی در دسترس نیست
         </q-item-section>
       </q-item>
     </template>

   </q-select>
</template>

<script>
import { api } from 'src/boot/axios'
import { ref } from 'vue'

export default {
 props: {
   is_in_table: {
     type: Boolean,
     default: false
   },
   extra_class:
 { type: String, default: '' },
   prepend_icon:
  { type: String, default: '' },
   has_after:
  { type: Boolean, default: false },
   after_icon:
 { type: String, default: '' },
   after_label:
 { type: String, default: '' },
   after_color:
 { type: String, default: '' },
   extra_style:
  { type: String, default: '' },
   select_all:
  { type: String, default: '' },
   select_none:
  { type: String, default: '' },
   dense:
  { type: Boolean, default: false },
   optionList:
  { type: Object, default: null },
   multible:
  { type: Boolean, default: false },
   showSelected:
  { type: Boolean, default: false },
   value:
  { default: null },
   multiple: {
     type: Boolean,
     default: false
   },
   hasSlot: {
     type: Boolean,
     default: false
   },
   append_icon: {
     type: String,
     default: ''
   },
   doReverse: {
     type: Boolean,
     default: false
   },
   disableRuntimeSearch: {
     type: Boolean,
     default: false
   }
 },
 // props: ['extra_class', 'prepend_icon', 'has_after', 'after_icon', 'after_label', 'after_color', 'extra_style', 'select_all', 'select_none', 'dense', 'optionList', 'multible', 'showSelected', 'value', 'multiple', 'hasSlot', 'append_icon'],

 data () {
   const model = ref(this.multible ? [] : null)
   const options = ref([])
   const totalOptions = ref(null)
   return {
     model,
     options,
     totalOptions
   }
 },

 beforeMount () {
   this.loadValue()
   this.loadOptions()
 },

 methods: {
   onClickAfterSlot () {
     this.$emit('click-on-afer-btn')
   },
   onBlur () {
     this.$emit('on-blur')
   },
   onPrependIcon () {
     this.$emit('on-prepend-icon')
   },
   loadValue () {
     if (this.value == null) return
     this.model = this.value
   },

   loadOptions () {
     if (this.optionList == null) return
     if (this.optionList.url != null) {
       api.get(this.optionList.url).then(response => {
         const items = this.optionList.arrayKey == null ? response.data : response.data[this.optionList.arrayKey]

         if (items.length !== 0) {
           this.totalOptions = []

           items.forEach(item => {
             this.totalOptions.push({
               label: item[this.optionList.labelKey],
               value: item
             })
           })
         }
         if (this.doReverse) this.totalOptions.reverse()
         this.options = this.totalOptions
       })
     } else {
       this.totalOptions = this.options = this.optionList
     }
   },

   filterFn (val, update, abort) {
     // call abort() at any time if you can't retrieve data somehow
     update(() => {
       if (this.totalOptions == null) this.loadOptions()
       const total = this.optionList.url == null ? this.optionList : this.totalOptions
       if (val === '') {
         this.options = total
       } else {
         const needle = val.toLowerCase()
         if (this.disableRuntimeSearch) this.options = total
         else this.options = total.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
       }
     })
   },

   abortFilterFn () {
     // console.log('delayed filter aborted')
   },

   onUpdateValue () {
     this.$emit('on-update-model', this.model)
   },
   selectAll () {
     this.model = this.options
     this.onUpdateValue()
   },
   selectNone () {
     // this.model = []
     this.model = null
     this.onUpdateValue()
   },

   loadTotalValues () {
   },

   setModelValue (model) {
     this.model = model
   },

   onInputValue (value) {
     this.$emit('on-input', value)
   }
 }
}
</script>
