<template>
  <q-page class="style">
    <div class="row justify-center text-black h2">قسط و ماهیانه ها</div>
    <div class="row q-pa-md text-center justify-between">
    <q-input label="شماره حساب" v-model="filter.account_id"  dense  type="text" class="style" outlined/>
    <q-input label="شماره وام" v-model="filter.loan_account_id"  dense  type="text" class="style" outlined/>
    <q-input label="نام حساب" v-model="filter.account_name"  dense  type="text" class="style" outlined/>
    <q-input label="تاریخ سررسید" dense v-model="filter.due_date" class="style" outlined fill-mask="#" mask="####/##/##"/>
    <q-input label="نوع وام یا ماهانه" v-model="filter.title" dense  type="text" class="style" outlined/>
    <!-- <q-input label="نوع ماهانه " v-model="filter.member_name"/> -->
    <q-radio v-model="filter.isPaid" val="true" label="پرداخت شده" />
    <q-radio v-model="filter.isPaid" val="false" label="پرداخت نشده"/>
    <q-checkbox v-model="filter.typeM" label="ماهیانه"/>
    <q-checkbox v-model="filter.typeI" label="قسط"/>
    <q-btn label="جستجو" @click="search" color="primary" size="sm"/>
   </div>
      <CustomeTable
        ref="table"
        @after-loaded="onAfterLoaded"
        :table="{
          url: `installment${this.searchQuery}`,
          arrayKey: 'installments'
          }"
           :extra_buttons="[
            {
              label: 'تعریف ماهیانه',
             emit: 'on-init-charge'
            },
            {
              label: 'تنظیم ماهیانه',
             emit: 'on-apply-charge'
            },
            {
              label: 'کارمزد',
             emit: 'on-show-fees'
            },
            {
              label: 'ارسال پیام دیرکرد ماهیانه',
             emit: 'on-send-latency-sms1'
            },
            {
              label: 'ارسال پیام دیرکرد قسط',
             emit: 'on-send-latency-sms2'
            },
            {
              label: 'ارسال پیام یادآوری قسط',
             emit: 'on-send-reminder-sms'
            },
          ]"
          @on-init-charge="monthlyChargeInstance={ id: null,amount: 0,year: year,title: '',accounts:[]};monthlyChargeInfoDialog = true"
          @on-apply-charge="getCharges();onAfterLoaded([]);applyChargeDialog=true;"
          @on-show-fees="showFees"
          @on-send-latency-sms1="sendLatencySms1"
          @on-send-latency-sms2="sendLatencySms2"
          @on-send-reminder-sms="sendReminderSms"
          :columns="columns">
          <template v-slot:row-created_at="{ row }">
                <div class="h5">{{row.created_at }}</div>
              </template>
              <template v-slot:row-type="{ row }">
                <div class="h4-5">{{row.type === '1' ? 'ماهیانه' : 'وام'}}</div>
              </template>
              <template v-slot:row-delay_days="{ row }">
                <div class="h4-5" :style="`${row.delay_days > 0 ? 'color:#C10015;':''}`">{{ row.delay_days}}</div>
              </template>
              <template v-slot:row-amount="{ row }">
                <div class="h4">{{formatCurrencyy(row.amount)}}</div>
              </template>
              <template v-slot:row-actions="{ row }">
                <q-btn icon="more_horiz" class="action-btn" size="xs" rounded dense outline unelevated
                  color="primary" v-if="row.is_paid == null">
                  <q-menu  class="font-demi-bold h4-5">
                      <q-list>
                        <q-item clickable @click="payInstallment(row)" >
                          <q-item-section>پرداخت {{ row.type === '1' ? 'ماهیانه' : 'قسط وام' }}</q-item-section>
                        </q-item>
                      </q-list>
                      </q-menu>
                  </q-btn>
              </template>
          </CustomeTable>

          <!-- edit charge -->
          <q-dialog v-model="chargeEditionDialog" :persistent="true">
        <card-panel ref="chargeEditionDialogRef" title="ویرایش ماهیانه " size="50%"
         @on-submit="updatemonthlyCharge"
         :disableNotify="false">
         <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <div class="row items-center">
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="عنوان"
                  placeholder="عنوان"
                   v-model="monthlyChargeInstance.title"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                    type="number"
                    min="0"
                    class="style"
                    outlined
                    placeholder="مبلغ"
                    hint="مبلغ"
                    v-model="monthlyChargeInstance.amount"/>
                    {{ formatCurrencyy(monthlyChargeInstance.amount) }}
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                    type="number"
                    min="0"
                    class="style"
                    outlined
                    placeholder="سال"
                    hint="سال"
                    v-model="monthlyChargeInstance.year"/>
                </div>

              </div>
            </template>
            </card-panel>
            </q-dialog>

            <!-- add charge -->
      <q-dialog v-model="monthlyChargeInfoDialog" :persistent="true">
        <card-panel ref="monthlyChargeInfoDialogRef" title="تعریف ماهیانه " size="50%"
         @on-submit="addmonthlyCharge"
         :disableNotify="false"
        @on-success="monthlyChargeInfoDialog=false;">
        <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <div class="row items-center">
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="عنوان"
                  placeholder="عنوان"
                   v-model="monthlyChargeInstance.title"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                    type="number"
                    min="0"
                    class="style"
                    outlined
                    placeholder="مبلغ"
                    hint="مبلغ"
                    v-model="monthlyChargeInstance.amount"/>
                    {{ formatCurrencyy(monthlyChargeInstance.amount) }}
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                    type="number"
                    min="0"
                    class="style"
                    outlined
                    placeholder="سال"
                    hint="سال"
                    v-model="monthlyChargeInstance.year"/>
                </div>

              </div>
              <q-separator inset style="background-color: black;"/>

              <div class="row items-center text-center">
                <CustomeTable
                style="width: 100%;margin: 0!important;"
                ref="table2"
                :table="{
                  url: `monthly_charge`,
                  arrayKey: 'monthly_charges'
                  }"
                @on-edit-charge="onEditCharge"
                @on-delete-charge="deletemonthlyCharge"
                :columns="chargeColumns"/>
              </div>
          </template>
        </card-panel>
      </q-dialog>

      <!-- apply charge -->
      <q-dialog v-model="applyChargeDialog" >
        <card-panel ref="monthlyChargeAccountsRef" title="تنظیم ماهیانه برای حساب ها" size="60%"
        @on-submit="checkCharges"
         :disableNotify="false" >
        <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <div class="row items-center">
              <div class="col-12 q-pa-md q-pb-sm">حساب ها :</div>
              <br>
              <q-checkbox v-for="acc in accountsList" :key="acc" v-model="acc.value" @update:model-value="addAccount(acc)" :label="acc.label"/>
              </div>
              <div class="row items-center">
                <div class="col-12 q-pa-md q-pb-sm">ماهیانه :</div>
                <br>
                <div class="col-4">
                  <q-input label="از تاریخ" v-model="monthlyChargeInstance.from" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
                </div>
                <div class="col-4">
                  <q-input label="تا تاریخ" v-model="monthlyChargeInstance.to" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
                </div>
                <div class="col-4">
                  <SelectionInput dense
                  :option-list="charges"
                  @on-update-model="setCharge"
                  label="ماهیانه" />
                </div>
              </div>
          </template>
        </card-panel>
      </q-dialog>

      <!-- show fees  -->
      <q-dialog v-model="showFeesDialog" >
        <card-panel ref="showFeesDialogRef" title="گزارش کارمزد" size="60%" :hide_actions="true">
        <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <div class="row items-center text-center">
              <q-input label="از تاریخ" v-model="feeFilter.from" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-input label="تا تاریخ" v-model="feeFilter.to" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-btn label="نمایش" @click="this.$refs.table3.getRows()"  color="primary" class="sm-style" outline/>
            </div>
            <CustomeTable
            v-if="feeFilter.from != null && feeFilter.to != null"
            ref="table3"
            :table="{
              url: `installment/fee_report?from=${feeFilter.from}&to=${feeFilter.to}`,
              arrayKey: 'fees',
              summation: 'amounts'
              }"
              @summation-after-loaded="feesSummation = $event"
              :columns="feesColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-12">مجموع مبالغ : {{ feesSummation }} </div>
          </div>
          </template>
        </card-panel>
      </q-dialog>

      <!-- payment  -->
      <q-dialog v-model="paymentDialog" >
        <card-panel ref="paymentDialogRef" :title="` پرداخت  ${paymentInstance.type === '1' ? 'ماهیانه' : 'قسط'}`" size="60%"
        :hide_actions="true" >
        <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <InstallmentPayment :instance="paymentInstance" @on-pay="this.paymentInstance=$event;doPayment();"
            :funds="fundAccountsList"/>
            </template>
            </card-panel>
            </q-dialog>

  </q-page>
</template>

<script>
import { ref } from 'vue';

import CustomeTable from 'src/components/CustomeTable.vue';
import { api } from 'src/boot/axios';
import CardPanel from 'src/components/CardPanel.vue';
import {  getJalaliDateSeperately } from 'src/helpers/dateOutputs';
import { useRoute } from 'vue-router';
import { accountsList, fundAccountList } from 'src/helpers/statics';
import SelectionInput from 'src/components/SelectionInput.vue';
import InstallmentPayment from 'src/components/InstallmentPayment.vue';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
const columns = [
  {
    name: 'inst_number',
    label: 'شماره قسط',
    field: 'inst_number',
    disable_search: true,
  },
  {
    name: 'account_id',
    label: 'شماره حساب ',
    field: 'account_id',
    disable_search: true,
  },
  {
    name: 'account_name',
    label: 'عنوان حساب',
    field: 'account_name',
    disable_search: true,
  },
  {
    name: 'loan_account_id',
    label: 'شماره وام',
    field: 'loan_account_id',
    disable_search: true,
  },
  {
    name: 'amount',
    label: 'مبلغ',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'due_date',
    label: 'سررسید',
    field: 'due_date',
    disable_search: true,
  },
  {
    name: 'delay_days',
    label: 'تاخیر',
    field: 'delay_days',
    disable_search: true,
  },
  {
    name: 'type',
    label: 'نوع',
    field: 'type',
    disable_search: true,
  },
  {
    name: 'paid_date',
    label: 'تاریخ پرداخت',
    field: 'paid_date',
    disable_search: true,
  },
  {
    name: 'title',
    label: 'نوع قسط و ماهیانه',
    field: 'title',
    disable_search: true,
  },
  {
    name: 'actions',
    field: 'actions',
    disable_search: true
  }
]
const chargeColumns =[
{
    name: 'year',
    label: 'سال',
    field: 'year',
    disable_search: true,
  },
  {
    name: 'amount',
    label: 'مبلغ',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'actions',
    field: 'actions',
    disable_search: true,
    label: 'عملیات',
    tools: [
      {
        'q-btn': {
          menu: [
            {
              title: 'ویرایش',
              icon_name: 'info',
              icon_color: 'primary',
              emit: 'on-edit-charge'
            },
            {
              title: 'حذف',
              icon_name: 'delete',
              icon_color: 'primary',
              emit: 'on-delete-charge'
            },
          ],
          color: 'primary',
          size: 'xs',
          icon: 'more_horiz',
          round: true,
          outline: true
        }

      }
    ]
  }
]
const feesColumns =[
{
    name: 'id',
    label: 'شناسه',
    field: 'id',
    disable_search: true,
  },
  {
    name: 'fund_account_id',
    label: 'حساب صندوق',
    field: 'fund_account_id',
    disable_search: true,
  },
  {
    name: 'account_id',
    label: 'شماره حساب ',
    field: 'account_id',
    disable_search: true,
  },
  {
    name: 'loan_account_id',
    label: 'شماره وام ',
    field: 'loan_account_id',
    disable_search: true,
  },
  {
    name: 'amount',
    label: 'مبلغ',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ ',
    field: 'created_at',
    disable_search: true,
  }
]
export default {
  setup () {
    const {year , month , day} = getJalaliDateSeperately()
    const route = useRoute()
    const filter = ref({
      account_id : route.query.account_id || null,
      loan_account_id : route.query.loan_account_id || null,
      account_name : null,
      typeM:null,
      typeI:null,
      due_date:null,
      title:null,
      isPaid:false,
    })
    let searchQuery = ref('')
     searchQuery.value = `${route.query.account_id === undefined && route.query.loan_account_id === undefined ? '' : '/search?'}${route.query.account_id === undefined ? '': `account_id=${route.query.account_id}&`}${route.query.loan_account_id  === undefined ? '': `loan_account_id=${route.query.loan_account_id}&`}`
     return {
      allAccounts:ref(false),
      filter,
      searchQuery,
      monthlyChargeInstance: ref({
        id: null,
        amount: 0,
        title: null,
        year:year ,
        accounts:[]
      }),
      feeFilter:ref({
        from:null,
        to:null
      }),
      paymentInstance:ref({
        id:null,
        loan_id:null,
        loan_account_id:null,
        monthly_charge_id:null,
        year:0,
        account_id:null,
        account_name:null,
        inst_number:null,
        amount:0,
        due_date:null,
        paid_date:null,
        delay_days:0,
        type:0,
        title:'',
        fund_account_id:null
      }),
      feesSummation:ref(0),
      chargeColumns,
      year,
      monthlyChargeInfoDialog: ref(false),
      applyChargeDialog: ref(false),
      chargeEditionDialog:ref(false),
      showFeesDialog:ref(false),
      paymentDialog:ref(false),
      columns,
      feesColumns,
      accountsList:ref([]),
      charges:ref([]),
      fundAccountsList:ref([])
    }
  },
  data(){
    return{
      accountsTable:{
        rows:[],
        columns:[
          {
            label: 'نام حساب'
          },
          {
            label: 'شماره حساب'
          },
          {
            label: 'موجودی حساب'
          },
          {
            label: 'وضعیت حساب'
          },
          {
            label: 'تاریخ عضویت حساب'
          }
        ]
      }
    }

    },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    reloadPage(){
     window.location.reload()
    },
    async onAfterLoaded(rows){
      rows.forEach((row, index) => {
        row.class = row.delay_days > 0 ? 'bg-lighten-pink':''
      })
      this.fundAccountsList = await fundAccountList()
      this.accountsList = []
      this.accountsList = await accountsList()
      this.monthlyChargeInstance.accounts = []
      this.accountsList.forEach(acc=>{
        acc.value = true
        this.monthlyChargeInstance.accounts.push(acc.data.id)
      })
    },
    addmonthlyCharge(){
      this.$refs.monthlyChargeInfoDialogRef.submit({
        url: 'monthly_charge',
        value : this.monthlyChargeInstance
      })
    },
    updatemonthlyCharge(){
      this.$refs.monthlyChargeInfoDialogRef.submit({
        url: 'monthly_charge',
        value : this.monthlyChargeInstance
      },'put')
      this.monthlyChargeInfoDialog = false
      this.chargeEditionDialog = false
    },
    onEditCharge(charge){
      this.monthlyChargeInstance = charge
      this.chargeEditionDialog = true
    },
    async deletemonthlyCharge(monthlyCharge){
      this.$emit('on-ok-dialog', {
        message: `با حذف ماهیانه تمام قسط های موجود از بین می روند.آیا از حذف ماهیانه اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.post('monthly_charge/delete',{id:monthlyCharge.id}).then(res=>{
        this.$refs.table2.getRows()
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
    },
    async getCharges(){
      this.charges = []
        await api.get('monthly_charge').then(res=>{
            res.data.monthly_charges.forEach(charge=>{
              this.charges.push({value:charge.id,label:charge.title})
            })
        })
    },
    search(){
        this.searchQuery = ''
        this.searchQuery = '/search?'
        if (this.filter.account_id != null && this.filter.account_id !== '') this.searchQuery += `account_id=${this.filter.account_id}&`
        if (this.filter.loan_account_id != null && this.filter.loan_account_id !== '') this.searchQuery += `loan_account_id=${this.filter.loan_account_id}&`
        if (this.filter.account_name != null && this.filter.account_name !== '') this.searchQuery += `account_name=${this.filter.account_name}&`
        if (this.filter.typeI != null && this.filter.typeI) this.searchQuery += `type=2&`
        if (this.filter.typeM != null && this.filter.typeM) this.searchQuery += `type=1&`
        if (this.filter.due_date != null && this.filter.due_date !== '') this.searchQuery += `due_date=${this.filter.due_date}&`
        if (this.filter.title != null && this.filter.title !== '') this.searchQuery += `title=${this.filter.title}&`
        if (this.filter.isPaid != null) this.searchQuery += `is_paid=${this.filter.isPaid}&`
        this.reloadTable()
      },
      reloadTable(){
    setTimeout(()=>{this.$refs.table.getRows()},1)
  },
  payInstallment(row){
    this.paymentInstance = {
      id:null,
        loan_id:null,
        loan_account_id:null,
        monthly_charge_id:null,
        year:0,
        account_id:null,
        account_name:null,
        inst_number:null,
        amount:0,
        due_date:null,
        paid_date:null,
        delay_days:0,
        type:0,
        title:''
    }
    this.paymentInstance = row
    this.paymentDialog = true
  },
  async doPayment(){
    api.post('installment/pay',this.paymentInstance).then(res=>{
      this.paymentDialog = false
      this.$emit('on-notify',res.data.msg)
      this.$refs.table.getRows()
    }).catch(error=>{
      alert(error.response.data.message)
    })
  },
  showFees(){
    this.showFeesDialog = true
  },
  sendReminderSms(){
    this.$emit('on-ok-dialog', {
        message: `آیا میخواهید برای عضوهایی که بزودی سررسید قسط دارند پیامی ارسال کنید؟`,
        icon: 'warning',
        color: 'warning',
        textColor: 'white',
        onOk: async () => {
          await api.get('installment/reminder_sms').then(res=>{
        alert('با موفقیت ارسال شد.')
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
  },
  sendLatencySms1(){
    this.$emit('on-ok-dialog', {
        message: `آیا میخواهید برای عضوهایی که در پرداخت ماهیانه تاخیر داشته اند پیامی ارسال کنید؟`,
        icon: 'warning',
        color: 'warning',
        textColor: 'white',
        onOk: async () => {
          await api.get('installment/latency_sms1').then(res=>{
        alert('با موفقیت ارسال شد.')
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
  },
  sendLatencySms2(){
    this.$emit('on-ok-dialog', {
        message: `آیا میخواهید برای عضوهایی که در پرداخت قسط تاخیر داشته اند پیامی ارسال کنید؟`,
        icon: 'warning',
        color: 'warning',
        textColor: 'white',
        onOk: async () => {
          await api.get('installment/latency_sms2').then(res=>{
        alert('با موفقیت ارسال شد.')
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
  },
  checkCharges(){
    this.monthlyChargeInstance.accounts = [...new Set(this.monthlyChargeInstance.accounts)]
    api.post('monthly_charge/check_before_apply',this.monthlyChargeInstance).then(res=>{
      this.$emit('on-ok-dialog', {
        message: res.data.msg,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          this.applyChargeAndCreateInstallments()
        }
      })
    }).catch(error=>{
        alert(error.response.data.message)
      })
  },
  applyChargeAndCreateInstallments(){
    api.post('monthly_charge/apply_to_accounts',this.monthlyChargeInstance).then(res=>{
    console.log(res.data)
      this.$emit('on-notify',res.data.msg)
      this.applyChargeDialog = false
      this.$refs.table.getRows()

    }).catch(error=>{
        alert(error.response.data.message)
      })

  },
  addAccount(acc){
    if (acc.value) this.monthlyChargeInstance.accounts.push(acc.data.id)
    else this.monthlyChargeInstance.accounts = this.monthlyChargeInstance.accounts.filter(id => id !== acc.data.id);
  },
  setCharge(charge){
    this.monthlyChargeInstance.monthly_charge_id = charge.value
  }
  },
  components:{
    CustomeTable,
    CardPanel,
    SelectionInput,
    InstallmentPayment
  }
}
</script>
