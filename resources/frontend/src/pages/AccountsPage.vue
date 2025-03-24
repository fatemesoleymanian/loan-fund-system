<template>
  <q-page class="style">
    <div class="row justify-center text-black h2">حساب ها</div>

   <div class="row text-center items-center" >
      <div class="col-3">
        <q-input label="شماره حساب" type="text" class="style" outlined v-model="filter.id" dense/>
      </div>
      <div class="col-3">
    <q-input type="text" class="style" outlined label="نام حساب" v-model="filter.member_name" dense/>
    </div>
    <div class="col-4">
    <q-option-group
      v-model="filter.status"
      :options="accountStatus"
      color="primary"
      class="h5"
      dense
      inline size="md" keep-color/>
      </div>
      <div class="col-2">
    <q-btn label="جستجو" @click="search"  color="primary" class="sm-style" outline/>
    </div>
   </div>
   <q-checkbox label="نمایش حساب های بسته شده"  v-model="filter.is_open" @update:model-value="reloadTable"/>

      <CustomeTable
        ref="table"
        @after-loaded="onAfterLoaded"
        :table="{
          url:`${filter.is_open ?'account/all':'account' }${this.searchQuery}`,
          arrayKey: 'accounts'
          }"
              :add_button="
              {
                  label: 'افتتاح حساب ',
                  icon: 'add'
              }"
          @on-add-button="resetInstance();accountInfoDialog = true"
          :columns="columns"
          :extra_buttons="[
            {
              label: 'ارسال پیامک به اعضا',
             emit: 'on-send-sms-with-custom-message'
            }]"
            @on-send-sms-with-custom-message="smsObject={receptors:[],message:''};sendSmsWithCustomMessageDialog=true;">
              <template v-slot:row-created_at="{ row }">
                <div class="h5">{{row.created_at }}</div>
              </template>
              <template v-slot:row-balance="{ row }">
                <div class="h4">{{formatCurrencyy(row.balance) }}</div>
              </template>
              <template v-slot:row-is_open="{ row }">
                <div class="h4">{{row.is_open === '1' ? 'باز':' بسته شده' }}</div>
              </template>
              <template v-slot:row-stock_units="{ row }">
                <div v-if="row.stock_units > 0">
                  {{row.stock_units }} سهم
                </div>
                <div v-else>
                  ندارد
                </div>
              </template>
              <template v-slot:row-action="{ row }">
                <q-btn icon="more_horiz" class="action-btn" size="xs" rounded dense outline unelevated
                  color="primary" >
                  <q-menu  class="font-demi-bold h4-5">
                      <q-list v-if="row.is_open == 1">
                        <q-item clickable @click="fillInstance(row);accountInfoDialog = true" >
                          <q-item-section>مشاهده / ویرایش</q-item-section>
                        </q-item>
                        <q-item clickable @click="prepareDepositDialog(row)" >
                          <q-item-section>واریز</q-item-section>
                        </q-item>
                        <q-item clickable @click="prepareWithdrawDialog(row)" >
                          <q-item-section>برداشت</q-item-section>
                        </q-item>
                        <q-item clickable @click="showInstallments(row)" >
                          <q-item-section>اقساط و ماهیانه ها</q-item-section>
                        </q-item>
                        <q-item clickable @click="settlement(row)" >
                          <q-item-section>تسویه حساب</q-item-section>
                        </q-item>
                        <q-item clickable @click="closure(row)" >
                          <q-item-section>بستن حساب</q-item-section>
                        </q-item>
                      </q-list>
                      <q-list v-else>
                        <q-item clickable @click="fillInstance(row);accountInfoDialog = true" >
                          <q-item-section>مشاهده / ویرایش</q-item-section>
                        </q-item>
                        <q-item clickable @click="activate(row)" >
                          <q-item-section>فعالسازی حساب</q-item-section>
                        </q-item>
                      </q-list>
                      </q-menu>
                  </q-btn>
              </template>
            </CustomeTable>

      <q-dialog v-model="accountInfoDialog" :persistent="true">
        <card-panel ref="accountInfoDialogRef" :title="accountInstance.id == null ? 'افتتاح حساب ':'ویرایش اطلاعات حساب'"
         size="85%"
         @on-submit="accountInstance.id == null ? addaccount() : updateaccount()"
         :disableNotify="false"
        @on-success="this.$refs.table.getRows();accountInfoDialog=false;">

          <template #body>
            <div class="row items-center">
                <div class="col-12 text-center h3-4 text-dark q-pa-lg font-bold">اطلاعات عضو</div>
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="نام و نام خانوادگی"
                  placeholder="نام و نام خانوادگی"
                   v-model="accountInstance.full_name" />
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                type="text"
                class="style"
                outlined
                placeholder="شماره تلفن همراه"
                hint="شماره تلفن همراه"
                v-model="accountInstance.mobile_number"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="شماره تلفن ثابت"
                  placeholder="شماره تلفن ثابت"
                   v-model="accountInstance.telephone_number"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                type="text"
                class="style"
                outlined
                placeholder="نام پدر"
                hint="نام پدر"
                v-model="accountInstance.father_name"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="فکس"
                  placeholder="فکس"
                   v-model="accountInstance.fax"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                type="textarea"
                class="style"
                outlined
                placeholder="آدرس"
                hint="آدرس"
                v-model="accountInstance.address"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-checkbox v-model="accountInstance.have_sms" label="پیامک دارد"/>
                  </div>
                </div>
                <q-separator inset />

                <div class="row items-center">
                  <div class="col-12 text-center h3-4 text-dark q-pa-lg font-bold">اطلاعات حساب</div>

                <div class="col-12 col-sm-6">
                  <q-input type="number" min="0" class="style" outlined dense hint="نوع عضویت"
                  placeholder="نوع عضویت"
                   v-model="accountInstance.stock_units"
                   suffix="سهم"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense  v-if="accountInstance.id == null"
                    type="number"
                    min="0"
                    class="style"
                    outlined
                    placeholder="موجودی"
                    hint="موجودی"
                    v-model="accountInstance.balance"/>
                    <div class="row items-center text-center" v-else>
                      <div class="col-6 h4">موجودی : {{ accountInstance.balance }}</div>
                      <div class="col-6 h4-5">وضعیت حساب : {{ accountInstance.status }}</div>
                    </div>
                    {{formatCurrencyy(accountInstance.balance)}}

                </div>

                <div class="col-12 col-sm-6">
                  <q-input dense
                    type="textarea"
                    class="style"
                    outlined
                    placeholder="توضیحات"
                    hint="توضیحات"
                    v-model="accountInstance.description"/>
                </div>

            </div>
          </template>
        </card-panel>
      </q-dialog>

      <q-dialog v-model="createDepositDialog" :persistent="true">
        <card-panel ref="createDepositDialogRef"
        title="واریز" size="50%"
         @on-submit="createDeposit"
         :disableNotify="false"
        @on-success="this.$refs.table.getRows();createDepositDialog=false;">
          <template #body>
            <div class="row items-center">
              <div class="col-6 ">
                 شماره حساب : {{accountInstance.id}}
                </div>
                <div class="col-6 ">
                 نام حساب : {{accountInstance.full_name}}
                </div>
              <div class="col-6 font-demi-bold">
                موجودی : {{ formatCurrencyy(transactionInstance.balance) }}
              </div>
              <div class="col-6 ">
                 عضویت : {{accountInstance.stock_units}} سهمی
                </div>
                <div class="col-6 ">
                 وضعیت حساب : {{accountInstance.status}}
                </div>
                </div>
                <q-separator inset />
                  <div class="row q-my-md">
                    <div class="table-container q-mb-sm">
                    <table>
                      <thead>
                        <tr>
                          <th>تاریخ </th>
                          <th>حساب </th>
                          <th>مبلغ </th>
                          <th>توضیحات</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr  v-for="(d,i) in accountInstance.deposits" :key="i">
                          <td>{{ d.created_at}}</td>
                          <td>صندوق </td>
                          <td>{{ formatCurrencyy(d.amount) }}</td>
                          <td>{{ d.description }}</td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                  </div>
                <q-separator inset />
                <div class="row items-center">
                <div class="col-12 ">
                  <q-input type="number" min="0" class="style" outlined dense hint="مبلغ"
                  placeholder="مبلغ"
                   v-model="transactionInstance.amount"/>
                   {{ formatCurrencyy(transactionInstance.amount) }}
                </div>
                <div class="col-12 ">
                  <q-input type="textarea" class="style" outlined dense hint="توضیح"
                  placeholder="توضیح"
                   v-model="transactionInstance.description"/>
                </div>
            </div>
          </template>
        </card-panel>
      </q-dialog>
      <q-dialog v-model="createWithdrawDialog" :persistent="true">
        <card-panel ref="createWithdrawDialogRef"
        title="برداشت" size="50%"
         @on-submit="createWithdraw"
         :disableNotify="false"
        @on-success="this.$refs.table.getRows();createWithdrawDialog=false;">
          <template #body>
            <div class="row items-center">
              <div class="col-6 ">
                 شماره حساب : {{accountInstance.id}}
                </div>
                <div class="col-6 ">
                 نام حساب : {{accountInstance.full_name}}
                </div>
              <div class="col-6 font-bold">
                موجودی : {{ formatCurrencyy(transactionInstance.balance) }}
              </div>
              <div class="col-6 ">
                 عضویت : {{accountInstance.stock_units}} سهمی
                </div>
                <div class="col-6 ">
                 وضعیت حساب : {{accountInstance.status}}
                </div>
                </div>

                <q-separator inset />
                  <div class="row q-my-md">
                    <div class="table-container q-mb-sm">
                    <table>
                      <thead>
                        <tr>
                          <th>تاریخ </th>
                          <th>حساب </th>
                          <th>مبلغ </th>
                          <th>توضیحات</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr  v-for="(d,i) in accountInstance.withdraws" :key="i">
                          <td>{{ d.created_at}}</td>
                          <td>صندوق </td>
                          <td>{{ formatCurrencyy(d.amount) }}</td>
                          <td>{{ d.description }}</td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                  </div>
                <q-separator inset />
                <div class="row items-center">
                <div class="col-12 ">
                  <q-input type="number" min="0" class="style" outlined dense hint="مبلغ"
                  placeholder="مبلغ"
                   v-model="transactionInstance.amount"/>
                   {{formatCurrencyy(transactionInstance.amount)}}
                </div>
                <div class="col-12 ">
                  <q-input type="textarea" class="style" outlined dense hint="توضیح"
                  placeholder="توضیح"
                   v-model="transactionInstance.description"/>
                </div>
            </div>
          </template>
        </card-panel>
      </q-dialog>
      <q-dialog v-model="sendSmsWithCustomMessageDialog" :persistent="true">
        <card-panel ref="sendSmsWithCustomMessageDialogRef"
        title="ارسال پیامک به اعضا" size="50%"
         @on-submit="sendSmsWithCustomMessage"
         :disableNotify="false"
        @on-success="sendSmsWithCustomMessageDialog=false;">
          <template #body>

                <div class="row items-center">
                  <div class="col-12 ">
                    <SelectionInput dense
                    :option-list="accountsList"
                    :multiple="true"
                    :multible="true"
                    @on-update-model="addMemberTosms"
                    label="حساب" />
                  </div>
                <div class="col-12 ">
                  <q-input type="textarea" class="style" outlined dense hint="متن پیامک"
                  placeholder="متن پیامک" @blur="addExtraTextToSms"
                   v-model="smsObject.message"/>
                </div>
            </div>
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
import { accountStatus } from 'src/helpers/statics';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
import SelectionInput from 'src/components/SelectionInput.vue';
const columns = [
  {
    name: 'id',
    label: 'شماره حساب',
    field: 'id',
    disable_search: true,
  },
  {
    name: 'member_name',
    label: 'نام',
    field: 'member_name',
    disable_search: true,
  },
  {
    name: 'stock_units',
    label: 'ماهیانه',
    field: 'stock_units',
    disable_search: true,
  },
  {
    name: 'status',
    label: 'وضعیت',
    field: 'status',
    disable_search: true,
  },
  {
    name: 'balance',
    label: 'موجودی',
    field: 'balance',
    disable_search: true,
  },
  {
    name: 'is_open',
    label: ' باز / بسته',
    field: 'is_open',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ ایجاد حساب',
    field: 'created_at',
    disable_search: true,
  },
  {
    name: 'action',
    field: 'action',
    disable_search: true,
    label: 'عملیات'
  }
]
export default {

  setup () {
    const {year , month , day} = getJalaliDateSeperately()
    return {
      accountsList:ref([]),
      smsObject:ref({receptors:[],message:''}),
      sendSmsWithCustomMessageDialog:ref(false),
      accountStatus,
      filter:ref({
        id:null,
        member_name:null,
        status:4,
        is_open:false
      }),
      accountInstance: ref({
        id:null,
        full_name: '',
        mobile_number: '',
        telephone_number: '',
        father_name: '',
        fax: '',
        stock_units: '',
        address: '',
        have_sms:false,
        account_id: '',
        balance: 0,
        status: 'بستانکار',
        is_open: true,
        member_id:null,
        member_name:'',
        description:'',
      }),
      monthlyChargeInstance:ref({
        account_id:null,
        monthly_charge_id:null,
        year:year
      }),
      transactionInstance:ref({
        account_id:null,
        amount:null,
        description:'',
        balance:0
      }),
      accountInfoDialog: ref(false),
      createDepositDialog: ref(false),
      createWithdrawDialog: ref(false),
      closureDialog: ref(false),
      columns,
      searchQuery:ref('')
    }
  },
  data(){
    return{
    }
    },
  methods:{
    addMemberTosms(member){
      this.smsObject.receptors = []
      this.smsObject.receptors = member
    },
    async sendSmsWithCustomMessage(){

      const receptors = this.smsObject.receptors.map(item => item.value).join(',')

      await api.post('sms/custom_message',{
        message: this.smsObject.message,
        receptors
      }).then(res=>{
          alert('ارسال شد!')
      }).catch(error=>{
        alert(error.response.data.msg)
      })
    },
    addExtraTextToSms(){
      this.smsObject.message += "\n صندوق خانوادگی سلیمانیان (شهید طایف)\nلغو 11"
    },
    formatCurrencyy(num){
      return formatCurrency(num)
    },
  reloadTable(){
    setTimeout(()=>{this.$refs.table.getRows()},1)
  },
    resetInstance(){
      this.accountInstance = {
        id:null,
        full_name: '',
        mobile_number: '',
        telephone_number: '',
        father_name: '',
        fax: '',
        stock_units: '',
        address: '',
        have_sms:false,
        account_id: '',
        balance: 0,
        status: 'بستانکار',
        is_open: true,
        member_id:null,
        member_name:'',
        description:'',
        // loans:[],
        monthly_charges:[{title:'',value:0}]
      }
    },
    fillInstance(instance){
      this.accountInstance = {
        id:instance.id,
        full_name: instance.member_name,
        mobile_number: instance.member.mobile_number,
        telephone_number: instance.member.telephone_number,
        father_name: instance.member.father_name,
        fax: instance.member.fax,
        stock_units: instance.stock_units,
        address: instance.member.address,
        have_sms:instance.have_sms,
        account_id: instance.id,
        balance: instance.balance,
        status: instance.status,
        is_open: instance.is_open,
        member_id:instance.member.id,
        member_name:instance.member.full_name,
        description:instance.description,
        monthly_charges:instance.monthly_charges ?[{title:'',value:0}]:[]
      }
    },
    async onAfterLoaded(rows){
      rows.forEach((row) => {
        row.class = row.is_open === '1' ? '':'opt-5'
        row.label = row.member_name + "-"+row.id
        row.value = row.member.mobile_number
      })
      this.$refs.table.setRowsValue(rows)
      this.accountsList = rows
    },
    addaccount(){
      this.$refs.accountInfoDialogRef.submit({
        url: 'account',
        value : this.accountInstance
      })
    },
    updateaccount(){
      this.$refs.accountInfoDialogRef.submit({
        url: 'account',
        value : this.accountInstance
      },'put')
    },
    async prepareWithdrawDialog(row){
      this.fillInstance(row)
      this.transactionInstance.account_id=row.id
      this.transactionInstance.balance=row.balance
      this.transactionInstance.description='برداشت از حساب'
      await api.get(`withdraw/account/latest/${row.id}`).then(res=>{
        this.accountInstance.withdraws = res.data.withdraws
        this.createWithdrawDialog=true
      }).catch(error=>{
        alert(error.response.data.msg)
      })

    },
    async prepareDepositDialog(row){
      this.fillInstance(row)
      this.transactionInstance.account_id=row.id
      this.transactionInstance.balance=row.balance
      this.transactionInstance.description='واریز حساب'
      await api.get(`deposit/account/latest/${row.id}`).then(res=>{
        this.accountInstance.deposits = res.data.deposits
        this.createDepositDialog=true
      }).catch(error=>{
        alert(error.response.data.msg)
      })

    },
    createDeposit(){
      this.$refs.createDepositDialogRef.submit({
        url: 'deposit',
        value : this.transactionInstance
      })
    },
      createWithdraw(){
      this.$refs.createWithdrawDialogRef.submit({
        url: 'withdraw',
        value : this.transactionInstance
      })
    },
      closure(account){
        this.$emit('on-ok-dialog', {
        message: `آیا از بستن حساب اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.put('account/closure',{id:account.id}).then(res=>{
        this.$refs.table.getRows()
      }).catch(error=>{
        if(error.status == 400){
          alert(error.response.data.msg)
          // this.$router.push(`/transactions?account_id=${account.id}`)
        }else alert(error.response.data.msg)
      })
        }
      })
      },
      settlement(account){
        this.$emit('on-ok-dialog', {
        message: `آیا از تسویه حساب اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.put('account/settlement',{id:account.id}).then(res=>{
        this.$refs.table.getRows()
      }).catch(error=>{
        if(error.status == 400){
          alert(error.response.data.msg)
          this.showInstallments(account)
        }else alert(error.response.data.msg)
      })
        }
      })
      },
      activate(account){
        this.$emit('on-ok-dialog', {
        message: `آیا از فعالسازی حساب اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.put('account/activate',{id:account.id}).then(res=>{
        this.$refs.table.getRows()
      }).catch(error=>{
       alert(error.response.data.msg)
      })
        }
      })
      },
      showInstallments(account){
        this.$router.push(`/installments?account_id=${account.id}`)
      },
      search(){
        this.searchQuery = ''
        this.searchQuery = '/search?'
        if (this.filter.id != null && this.filter.id !== '') this.searchQuery += `id=${this.filter.id}&`
        if (this.filter.member_name != null && this.filter.member_name !== '') this.searchQuery += `member_name=${this.filter.member_name}&`
        if (this.filter.status != null && this.filter.status !== 4) this.searchQuery += `status=${this.filter.status}`
        // this.searchQuery = `/search?id=${this.filter.id}&member_name=${this.filter.member_name}&status=${this.filter.status}`
        this.reloadTable()
      }
  },

  components:{
    CustomeTable,
    SelectionInput,
    CardPanel
    }
}
</script>
<style scoped>
.table-container {
  width: 100%;
  overflow-x: auto;
  margin-top: 18px;
}

table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
  margin: 0 auto;
}

th, td {
  text-align: center;
  border: 1px solid #ddd;
  padding: 8px;
}

thead tr {
  background-color: #f4f4f4; /* Apply background color only to thead row */
}

/*tbody tr:nth-child(even) {
  background-color: #f9f9f9;  Optional alternating row colors
}*/
</style>
