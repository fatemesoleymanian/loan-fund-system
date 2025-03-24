<template>

  <div class="q-card-margin bg-white q-table-list q-pa-md text-center justify-center" style="border-radius: 15px;">
    <div class="font-bold h3-4 q-pb-xs q-px-md" style="text-align: right;" v-if="title != null">{{ title }}</div>
    <header>
    <slot name="header"></slot>
  </header>
    <q-table
    :grid="grid"
    v-if="loaded"
    pagination-label=""
    :style="`border-radius: 15px; max-height: ${max_height}!important;`"
      :class="`style ${extra_class}+ ${stickyTop ? ' custom-sticky-header-table': ''}${fit_width ? ' q-table-max-width': ''}`"
      flat :bordered="bordered"
      :hide-bottom="hide_buttom"
      :rows="rowsValue != null ? rowsValue : [] "
      :columns="columns"
      color="primary"
      :loading="rowsValue == null"
      :visible-columns="visibleColumns"
      v-model:pagination="pagination"
      @request="onRequest"
      virtual-scroll
      separator="cell"
      no-data-label="اطلاعاتی یافت نشد!"
      no-results-label="متاسفانه اطلاعاتی یافت نشد!"
    >
      <template v-slot:body="props">
        <q-tr
        key="name" :props="props" bordered :no-hover="noHover"
        @mouseup="selectedRow = props.row" :class="props.row.class"
        @mousedown="handleRepeat">
          <slot name="row" :props="props">
            <q-td v-for="p in props.cols" :key="p" :class="`item ${p.class}`" bordered
            :style="breakCol ?{maxWidth: '300px', whiteSpace: 'pre-wrap',minWidth:'fit-content'}:{minWidth:'fit-content'}" auto-width>
              <div >
                <slot
                  :name="`row-${p.name}`"
                  :col="p"
                  :row="props.row"
                >
                <div class="row" >
                  <div
                   v-if="!p.hide_row_text"
                   :class="' row-font col-10 ' + p.extra_class">
                   {{ rowText(props.row[p.name]) }}
                  </div>

                <div v-for="t in p.tools" :key="t" :class="`${p.hide_row_text ? 'col-12 text-center justify-center ':'col-1'}`" >
                  <q-avatar v-if="t['img']">
                    <img :src="props.row[t['img'].srcKey] != null ? props.row[t['img'].srcKey] : ''" >
                  </q-avatar>
                  <q-toggle
                    v-if="t.toggle && props.row[t.toggle.value_key] != null"
                    v-model="props.row[t.toggle.value_key]"
                    @update:model-value="$emit(t.toggle.emit, props.row)"
                    checked-icon="fa-solid fa-circle-check"
                    color="primary"
                    class="style" :style="`${!hide_row_text ? 'float: right;' : ''}`"
                    :label="props.row[t.toggle.value_key] ? t.toggle.active_label : t.toggle.inactive_label"
                    unchecked-icon="fa-solid fa-xmark"
                    :disable="t.toggle.disable"
                  />
                  <q-input v-if="t['q-input']" class="q-input-style"
                  outlined
                  dense
                  autogrow
                  v-model="props.row[t['q-input'].valueKey]"
                  :hint="t['q-input'].hint"
                  :label="t['q-input'].label"
                  :prefix="t['q-input'].prefix"
                  :suffix="t['q-input'].suffix"/>

                  <SelectionInput v-if="t['selection-input']"
                    :label="t['selection-input'].label"
                    :hint="t['selection-input'].hint"
                    :dense="t['selection-input'].dense"
                    :optionList="t['selection-input'].optionList"
                    @on-update-model="$emit(t['selection-input'].onUpdateModel, $model)"
                    :value="props.row[t['selection-input'].valueKey]"
                  />

                  <q-checkbox v-if="t['q-checkbox']" v-model="props.row[t['q-checkbox'].value_key]" :label="t['q-checkbox'].label" />
                  <!-- <ButtonOptions v-if="t['q-button-options']" :items="t['q-button-options'].items" :active_class="t['q-button-options'].active_class"
                  :inactive_class="t['q-button-options'].inactive_class" /> -->

                  <q-btn class="action-btn q-my-sm" style="float:left!important;" v-if="t['q-btn']"
                   :size="t['q-btn'].size != null ? t['q-btn'].size : 'xs'" :round="t['q-btn'].round" :outline="t['q-btn'].outline"
                  :icon="t['q-btn'].icon" :align="t['q-btn'].align"
                  :label="t['q-btn'].label"
                  unelevated
                  :color="t['q-btn'].color"
                  :disable="t['q-btn'].disable"
                  @click="t['q-btn'].emit != null ? $emit(t['q-btn'].emit, props.row) : null">
                    <q-menu v-if="t['q-btn'].menu != null" class="font-demi-bold h4-5">
                      <q-list style="min-width: 100px">
                        <q-item v-for="menu in t['q-btn'].menu" :key="menu" clickable v-close-popup @click="menu.emit != null ? $emit(menu.emit, props.row) : null">
                          <q-item-section>{{ menu.title }}</q-item-section>
                          <q-avatar>
                            <q-icon :color="menu.icon_color" :name="menu.icon_name" />
                          </q-avatar>
                          <q-toggle
                          v-if="menu.toggle"
                          v-model="props.row[menu.toggle.value_key]"
                          :label="props.row[menu.toggle.value_key] ? menu.toggle.active_label : menu.toggle.inactive_label"
                          @update:model-value="$emit(menu.toggle.emit, props.row, props.row[menu.toggle.value_key])"/>
                        </q-item>
                      </q-list>
                    </q-menu>
                  </q-btn>
                </div>
              </div>

              </slot>
              </div>
            </q-td>
          </slot>
        </q-tr>

        <!-- <q-tr :props="props" :key="`e_${props.row.index}`"
          class="q-virtual-scroll--with-prev">
          <q-td colspan="100%" classs="column"  style="margin: 0;padding:0;">
            <div class="align-right" v-if="props.cols[0] != null && props.cols[0].name === 'select'">
            <q-checkbox  dense v-model="props.row.name" />
          </div>
          </q-td>
        </q-tr> -->
        <!--q-tr v-if="child_component != null && props.row.expanded" v-show="child_component != null && props.row.expanded" :props="props" class="bg-grey-3">
          <q-td colspan="100%">
            <TableList :rows="tableRows" :columns="tableColumns"/>
          </q-td>
        </q-tr-->
      </template>

      <template v-slot:header-cell="props">
        <q-th :props="props" bordered style="padding: 0">
          <div class="row justify-sm-between" style="margin:0;">
            <div class="col-2"  >
              <q-icon v-if="props.col.sortabled"
            :name="sort.by !== (props.col.sortKey || props.col.name) ? 'swap_vert' : sortItems[sort.index].icon"
            color="black"
            class="cursor-pointer"
            @click="setSort(props.col)"/>
            </div>
            <div
            class="font-demi-bold h4-5 justify-center text-center q-pb-sm "
            :class="props.col['button'] ? 'col-4 ': 'col-8 '+ props.col.class">{{ props.col.label }}
          </div>
          <div class="col-4" v-if="props.col['button']" >
              <q-btn  style="max-width: fit-content;" dense size="sm"
                 flat :disable="props.col['disableButton']"
               color="primary" icon="fa-solid fa-circle-plus"
              unelevated class="q-mx-md sm-style" @click="$emit('header-btn-on-click',rowsValue)"/>
              <q-btn style="max-width: fit-content;" dense size="sm"
              flat :disable="props.col['disableButton']"
               color="negative" icon="fa-solid fa-trash"
              unelevated class="q-mx-md sm-style" @click="$emit('header-btn-on-click2',rowsValue)"/>
            </div>
              <div class="col-2" >
              <SelectionInput v-if="props.col['hasFilter']"
                :is_in_table="true"
                class="filter-in-table q-px-sm"
                :option-list="props.col['filter'].optionList"
                @on-update-model="$emit('on-filter-change',{
                  key:props.col['filter'].valueKey,
                  value:$event
                  })" />

              </div>
          </div>
          <div  v-if="props.col['hasSlot']">
            <slot name="qth-slot">
              </slot>
          </div>
          <div class="row" style="margin:0;"
           v-if="!props.col['disable_search'] && !disable_column_search">
            <q-input dense borderless
             style="border-top:1px solid #0000001f;height:35px;margin-bottom: -5px;width: 100%;"
             v-model="searchColumns.find(item => item.field === props.col.field).search"
             @update:model-value="onColumnSearch">
              <template v-slot:append>
                <q-icon name="search" size="xs" class="color-grey"  />
              </template>
            </q-input>
            </div>
        </q-th>
      </template>

        <template v-slot:top-right v-if="!hide_header" >
          <div class="row" style="width:100%;margin-bottom: 30px">
          <slot name="top-right">
                <q-btn
                id="gt-export-table-btn"
                style="position: absolute;left: 1%;top: 12px;"
                unelevated
                  class="gt-sm sm-style download-table-btn q-py-sm"
                  color="primary"
                  icon-right="file_download"
                  label="دریافت اطلاعات"

                  no-caps
                  @click="exportTable"/>
          </slot>

        </div>
        </template>
      <template v-slot:top-left v-if="!hide_header_top_left">
        <div class="row" style="width:100%;">
          <slot name="top-left">

            <!-- <div class="font-bold h2 col-sm-1" v-if="title != null">{{ title }}</div> -->

            <div class="col-sm-6 col-12" v-if="hide_searchInput && !hide_header_top_left">
              <!-- <q-input class="q-mx-md style" outlined dense debounce="300" style="min-width: 150px;margin-top: 0px!important;"
              v-model="filter" :placeholder="search_placeholder"
              @keyup.enter="onTotalFilter"
              @update:model-value="if (filter.length === 0) { onTotalFilter() };">
                <template v-slot:prepend>
                  <q-icon name="fa-solid fa-magnifying-glass" @click="onTotalFilter" />
                </template>
                <template v-slot:append>
                  <q-btn label="جستجو" color="primary" unelevated style="border-radius:10px;margin-left: -10px!important;"
                  @click="onTotalFilter"/>
                </template>
                  </q-input> -->
            </div>
            <!-- <div class="col-sm-6 col-7" v-if="!hide_header_top_left" id="column-selection-btn">
                <q-select
                v-model="visibleColumns"
                multiple
                outlined
                dense
                options-dense
                :display-value="$q.lang.table.columns"
                emit-value
                map-options
                label="انتخاب ستون ها"
                :options="columns"
                option-value="name"
                options-cover
                class=" q-mx-sm"
                transition-show="scale"
                transition-hide="scale"
                style="min-width: 160px;"
                @update:model-value="onUpdateVisibleColumns"
                @blur="this.$emit('on-blur-visible-columns')" >

                <template v-slot:prepend>
                  <q-icon name="fa-solid fa-table-cells" size="xs" />
                </template>

                </q-select>
            </div> -->
            <!-- <div class="col-5 lt-xsc" v-if="!hide_header">
              <q-btn
                unelevated
                  id="lt-export-table-btn"
                  class="style download-table-btn"
                  color="primary"
                  icon-right="fa-solid fa-file-arrow-down"
                  label="دریافت اطلاعات"

                  no-caps
                  @click="exportTable"/>
            </div> -->
            </slot>
            <div class="col-sm-12">
              <div v-if="extra_buttons != null" class="gt-sm text-left justify-start row" >
            <q-btn  v-for="(b, i) in extra_buttons" :key="b" :label="b.label" :outline="b.outline"
            unelevated size="md" dense :id="`extra-buttons-${i}`"
            :icon="b.icon" class="style q-mx-xs extra-buttons" :loading="b.loader"
              :color="b.color == null ? 'primary' : b.color" @click="$emit(b.emit);"/>
            </div>
            </div>
        </div>
      </template>
      <template v-slot:bottom>

      </template>

      <!-- <template v-slot:pagination>
        <div class="row justify-center q-mt-md">

           <q-pagination
          v-model="pagination.page"
          color="grey-8"
          max="10"
          size="sm"
        />
        </div>
      </template> -->

      <!-- <template v-slot:bottom-right>
        Bottom
      </template> -->
    </q-table>
    <div class="fixed-bottom" style="text-align: right!important;right:1%;bottom: 0%;width: fit-content;">
      <q-btn v-if="add_button != null"
        unelevated id="add-btn"
         :label="add_button.label" :icon="add_button.icon"
          :class="`style q-mx-xs ${add_button.smallerIcon !=null ?'smaller-icon' :''}`"
          color="primary" @click="$emit('on-add-button')"/>
          <!-- <div v-if="extra_buttons != null" class="col-sm-6 lt-xsc" >
          <q-btn v-for=" (b, i) in extra_buttons" :key="b" :label="b.label"
          :outline="b.outline"
          unelevated :id="`extra-buttons-${i}`"
           :icon="b.icon" class="style q-mx-xs extra-buttons" :loading="b.loader"
            :color="b.color == null ? 'primary' : b.color" @click="$emit(b.emit);"/>
        </div> -->
    </div>

  </div>
</template>

<script>
import { exportFile, useQuasar } from 'quasar'
import { ref } from 'vue'
import { api } from 'src/boot/axios'
import SelectionInput from './SelectionInput.vue'

function wrapCsvValue (val, formatFn, row) {
  let formatted = formatFn !== void 0
    ? formatFn(val, row)
    : val

  formatted = formatted === void 0 || formatted === null
    ? ''
    : String(formatted)

  formatted = formatted.split('"').join('""')
  /**
   * Excel accepts \n and \r in strings, but some other CSV parsers do not
   * Uncomment the next two lines to escape new lines
   */
  // .split('\n').join('\\n')
  // .split('\r').join('\\r')

  return `"${formatted}"`
}
const sortItems = [
  {
    value: null,
    icon: 'horizontal_rule'
  },
  {
    value: 'asc',
    icon: 'arrow_upward'
  },
  {
    value: 'desc',
    icon: 'arrow_downward'
  }
]

export default {
  // props: ['noHover', 'columns', 'table', 'rows', 'title', 'hasDatePicker', 'add_button', 'search_placeholder',
  //   'extra_buttons', 'extra_class', 'multiple', 'hide_buttom', 'hide_header_top_left', 'hide_searchInput', 'hide_header', 'total_rows_number',
  //   'disable_column_search', 'disableTimeFilter', 'initRowsPerPage', 'onReload', 'show_bottom_details', 'bordered',
  //   'extra-input'],
  props: {
    colToRow: {
      type: Boolean,
      default: false
    },
    max_height: {
      type: String,
      default: '90vh'
    },
    sticky_top: {
      type: Boolean,
      default: true
    },
    breakCol: {
      type: Boolean,
      default: true

    },
    row_key: {
      type: String,
      default: 'reserveId'
    },
    fit_width: {
      type: Boolean,
      default: false
    },
    noHover: Boolean,
    columns: Array,
    table: Object,
    rows: Array,
    title: String,
    hasDatePicker: Boolean,
    add_button: Object,
    search_placeholder: String,
    extra_buttons: Array,
    extra_class: String,
    hide_buttom: Boolean,
    hide_header_top_left: Boolean,
    hide_searchInput: { type: Boolean, default: true },
    hide_header: Boolean,
    center_header: {
      type: Boolean,
      default: false
    },
    total_rows_number: Number,
    disable_column_search: Boolean,
    disableTimeFilter: Boolean,
    initRowsPerPage: Number,
    onReload: Function,
    show_bottom_details: Boolean,
    bordered: {
      type: Boolean,
      default: true
    },
    extra_input: Array,
    initVisibleColumns: Array,
    grid: { default: false },
    expansionHideMode: {
      default: 'v-if'
    },
    exportFileName: {
      type: String,
      required: false,
      default: 'صندوق'
    }
  },

  setup (props) {
    const stickyTop = ref(props.sticky_top)
    return {
      stickyTop,
      rowsValue: ref(null),
      totalRecords: ref(null),
      startTime: ref(),
      selectedRow: ref(),
      clickDuration: ref(0),
      pagination: ref(null),
      loaded: ref(true),
      sortItems,
      datePicker: ref({
        placeholderProp: 'انتخاب تاریخ'
      }),
      sort: ref({
        index: 0
      })
    }
  },

  data () {
    const $q = useQuasar()
    this.pagination = {
      rowsPerPage: 100,
      page: 1
    }

    if (this.table != null) {
      this.getRows()
    } else {
      this.totalRecords = this.total_rows_number
      this.rowsValue = this.rows
    }

    this.setPagination()

    let visibleColumns
    // if ($q.screen.md || $q.screen.lg || $q.screen.xl) {
      visibleColumns = this.initVisibleColumns != null ? ref(this.initVisibleColumns) : ref(this.columns.map(item => item.name))
    // } else {
    //   visibleColumns = this.initVisibleColumns != null ? ref(this.initVisibleColumns) : ref(this.columns.map(item => item.name))
    //   visibleColumns.length = Math.min(visibleColumns.length, 5)
    // }
    return {
      filter: ref(''),
      $q,
      item: ref('body'),
      visibleColumns,
      searchColumns: ref(this.columns.map(item => {
        return { field: item.field }
      }))
    }
  },

  methods: {
    filterMethod (rows, temrs, columns, getCellValue) {
      return [this.rowsValue[0]]
    },
    onUpdateVisibleColumns (val) {
      this.$emit('on-change-visible-columns', val)
    },

    onRequest (props) {
      this.pagination = props.pagination
      this.getRows('', props.pagination.rowsPerPage, props.pagination.page, props.pagination.rowsPerPage === 0)
      this.$emit('pagination-update', props)
    },

    exportTable () {
      const exportColumns = this.columns.filter(column => this.visibleColumns.includes(column.name))
      // naive encoding to csv format
      const content = [exportColumns.map(col => wrapCsvValue(col.label))].concat(
        this.rowsValue.map(row => exportColumns.map(col => wrapCsvValue(
          row[col.name],
          col.format,
          row
        )).join(','))
      ).join('\r\n')

      const status = exportFile(
        `خروجی-جدول-${this.exportFileName}.csv`,
        '\ufeff' + content,
        {
          encoding: 'utf-8',
          mimeType: 'text/csv',
          byteOrderMark: '\uFEFF'
        }
      )

      if (status !== true) {
        this.$q.notify({
          timeout: 2000,
          progress: true,
          message: 'خطا در بارگذاری...',
          color: 'negative',
          icon: 'fa-solid fa-triangle-exclamation'
        })
      }
    },

    setPagination () {
      if (this.totalRecords == null) {
        setTimeout(this.setPagination, 100)
      } else {
        this.pagination = {
          rowsNumber: this.totalRecords,
          rowsPerPage: 100,
          page: 1
        }
      }
    },

    setTotalPagination (total = 10) {
      this.pagination.rowsNumber = total
    },

    getRows (filters = '', pageSize = 10, pageNumber = 1, disablePaging = false) {
      if (this.table == null) return
      this.rowsValue = null
      pageSize = this.pagination != null ? this.pagination.rowsPerPage : pageSize
      pageNumber = this.pagination != null ? this.pagination.page : pageNumber
      const query = `/${this.table.url}${this.filter != null && this.filter !== '' ? `/search=${this.filter}` : ''}`
      api.get(query).then(response => {
        const rowsValue = this.table.arrayKey != null ? response.data[this.table.arrayKey] : response.data
        const totalRecords = this.table.totalKey != null ? response.data[this.table.totalKey] : response.data.length
        const extraSummation = this.table.summation != null ? response.data[this.table.summation] : null
        this.rowsValue = rowsValue
        this.pagination.rowsNumber = totalRecords
        this.$emit('after-loaded', this.rowsValue)
        this.$emit('summation-after-loaded', extraSummation)
      })
      // console.log(this.$q.lang.table.rows)
    },

    setRowsValue (rows) {
      this.rowsValue = rows
    },

    onColumnSearch (value) {
      if (this.onReload != null) this.onReload()
      else this.getRows()
    },

    getColumnFilters () {
      let filters = ''
      const columns = this.searchColumns
      if (columns == null) return
      // console.log(columns)
      for (const column of columns) {
        // console.log(column)
        if (column.search != null && column.search.length > 0) filters += `${column.field}=${column.search}&`
      }
      return filters
    },

    getRowsValue () {
      return this.rowsValue
    },

    getPaginationRowsValue () {
      return this.rowsValue.slice((this.pagination.page - 1) * this.pagination.rowsPerPage, this.pagination.page * this.pagination.rowsPerPage)
    },

    onTotalFilter () {
      this.getRows(null)
      this.$emit('on-total-filter', this.filter)
    },

    setSort (col) {
      // const c = this.columns.find(item => item.name === col.name)
      // console.log(this.sort.sort)
      this.sort.index = this.sort.index === sortItems.length - 1 ? 0 : this.sort.index + 1
      this.sort.by = col.sortKey || col.field
      // console.log(this.sort.by)
      this.loaded = false
      setTimeout(() => {
        this.loaded = true
        this.getRows(this.sortBy)
      }, 1)
    },

    rowText (row) {
      let r = row
      if (r != null && r.length != null) {
        r = r.toString()
      }
      return r
    },

    handleRepeat (details) {
      if (details._vts - this.startTime <= 500) {
        this.$emit('row-dblclick', this.selectedRow)
      }
      this.startTime = details._vts
    }
  },

  components: {
    SelectionInput,
    },

  computed: {
    sortBy () {
      // console.log(this.sort.by)
      return this.sort.by != null ? `SortBy=${this.sort.by}&SortDirection=${sortItems[this.sort.index].value}` : ''
    }
  }
}
</script>

<style>
@media screen and (max-width:500px) {
  .q-table-list{
      padding: 0 !important;
      margin-right: -15px;
      margin-left: -15px;
    }
  .download-table-btn{
    max-width: fit-content;
    margin-top: 2px;
    padding: 6px;
    font-size: 12px!important;
    i{
      font-size: 14px!important;
    }
  }
  .extra-buttons{
    width: fit-content;
    padding: 2px;
    font-size: 12px!important;
    i{
      font-size: 15px!important;
    }
  }

  .q-table-list{
      padding: 1px !important;
    }
}
.item .action-btn {
  opacity: 0.9;
  transition: .3s;
}

.item:hover .action-btn {
  opacity: 1;
  transition: .3s;
}
.datepicker-in-table{
  /* padding: 0px!important;
  padding-right:4px!important;
  padding-left:4px!important;
  margin: 0;
   margin-right: 15px!important;
 margin-left: auto!important;
  margin-top: -25px!important;
  max-width: 8px; */
  max-width: 8px;
  position:absolute;
  /* top:5%; */
  right:22%;

}
.filter-in-table{
  /* padding: 0px!important;
  padding-right:4px!important;
  padding-left:4px!important;
  margin: 0;
  margin-right: 3px!important;
  margin-left: auto!important;
  margin-top: -25px!important;*/
  max-width: 8px;
  position:absolute;
  /* top:-5%; */
  margin-top: -10px;
  right:15px;

}
.smaller-icon{
  .q-icon{
    font-size: 1.2em;
  }
}

</style>
