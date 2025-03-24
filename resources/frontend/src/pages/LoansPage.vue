<template>
  <q-page class="style">
    <div class="row justify-center text-black h2">وام ها</div>
    <div class="row text-center items-center">
      <q-input label="شماره حساب" type="text" class="style" outlined v-model="filter.account_id" dense/>
    <q-input label="نام حساب" v-model="filter.account_name" type="text" class="style" outlined dense/>
    <q-input label="شماره وام" v-model="filter.loan_account_id" type="number" class="style" outlined dense/>
    <q-input label="نوع وام" v-model="filter.title" type="text" class="style" outlined dense/>
    <q-input label="مبلغ وام" v-model="filter.amount" type="number" class="style" outlined dense/>
    <q-checkbox label="دارای اقساط پرداخت نشده"  v-model="filter.is_not_paid"/>
    <q-btn label="جستجو" @click="search" class="sm-style" color="primary" outline/>
   </div>
      <CustomeTable
        ref="table"
        @after-loaded="onAfterLoaded"
        :table="{
          url: `loan_account${searchQuery}`,
          arrayKey: 'loans',
          summation: 'amounts'
          }"
          :extra_buttons="[
            {
              label: 'تعریف انواع وام ',
             emit: 'on-init-loan'
            },
            {
              label: 'اعطای وام',
             emit: 'on-grant'
            },
          ]"
              @summation-after-loaded="loanSummation = $event"
              @on-init-loan="initLoan"
              @on-grant="this.grantingLoanInstance = {step:1,
              id:null,
              loan_id:null,
              account_id:null,
              amount:0,
              paid_amount:0,
              granted_at:null,
              payback_at:null,
              number_of_installments:0,
              no_of_paid_inst:0,
              fee_amount:0,
              description:'',
              account_name:null,
              created_at:null,
              recent_loans:[],
              fund_account_id:null,
              installments:[]};grantLoanDialog=true"
              @on-delete-loan="deleteloan"
              @on-edit-installments="onEditInstallments"
              @on-show-installments="showInstallments"
              :columns="columns">
              <template v-slot:row-created_at="{ row }">
                <div class="h4-5">{{row.created_at }}</div>
              </template>
              <template v-slot:row-amount="{ row }">
                <div class="h4">{{formatCurrencyy(row.amount) }}</div>
              </template>
              <template v-slot:row-paid_amount="{ row }">
                <div class="h4">{{formatCurrencyy(row.paid_amount) }}</div>
              </template>
              <template v-slot:row-granted_at="{ row }">
                <div>{{formatCurrencyy(row.amount - row.paid_amount) }}</div>
              </template>
            </CustomeTable>
            <div class="q-px-md q-py-sm font-demi-bold row">
              <div class="col-6">
              مجموع مانده وام : {{ loanSummation[1] }}
            </div>
            <div class="col-6">
              مجموع مبلغ وام: {{ formatCurrencyy(loanSummation[0]) }}
              </div>
          </div>

      <q-dialog v-model="loanInfoDialog" :persistent="true">
        <card-panel ref="loanInfoDialogRef"  title="تعریف وام جدید" size="50%"
         @on-submit="addLoan"
         :disableNotify="false"
        @on-success="loanInfoDialog=false;">

          <template #body>
            <div class="row items-center">
                  <div class="col-12">
                  <q-input type="text" class="style" outlined dense
                  hint="عنوان"
                  placeholder="عنوان"
                   v-model="loanInstance.title"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="تعداد اقساط"
                  placeholder="تعداد اقساط"
                   v-model="loanInstance.number_of_installments"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="درصد کارمزد"
                  placeholder="درصد کارمزد"
                   v-model="loanInstance.fee_percent"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="فاصله اقساط"
                  placeholder="فاصله اقساط"
                   v-model="loanInstance.installment_interval"
                   suffix="روز"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="کارمزد ثابت"
                  placeholder="کارمزد ثابت"
                   v-model="loanInstance.static_fee"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="سقف وام"
                  placeholder="سقف وام"
                   v-model="loanInstance.max_amount"/>
                </div>
                <div class="col-6">
                  <q-input type="number" min="0" class="style" outlined dense
                  hint="حداقل وام"
                  placeholder="حداقل وام"
                   v-model="loanInstance.min_amount"/>
                </div>
              </div>
              <div class="row">
                <div class="col-12 col-sm-6">
                  <q-checkbox label="وام ضروری"  v-model="loanInstance.emergency"/>
                  <q-checkbox label="وام بلاعوض"  v-model="loanInstance.no_need_to_pay"
                   @update:model-value="$event ? loanInstance.number_of_installments = 0:loanInstance.number_of_installments=5"/>
                </div>
              </div>
          </template>
        </card-panel>
        <!-- <q-input class="style" outlined dense
                  hint="تاریخ پایان"
                  placeholder="تاریخ پایان"
                   fill-mask="#" disable
                   mask="####/##/##"
                   v-model="loanInstance.end_date"/> -->
      </q-dialog>
      <q-dialog v-model="grantLoanDialog">
        <card-panel ref="grantLoanDialogRef" title="اعطای وام" size="70%" :hide_actions="true">
          <template #card-sections>
        <q-card-section class="row items-center q-pb-none" >
          <q-space />
          <q-btn icon="close" size="md" flat round dense v-close-popup style="margin-top: -60px!important;" />
        </q-card-section>
        </template>
          <template #body>
            <div class="row" v-if="grantingLoanInstance.step == 1" >
              <StepOne :instance="grantingLoanInstance" :accounts="accountsList" @ready-for-step-two="grantingLoanInstance = $event;"/>
            </div>
            <div class="row" v-if="grantingLoanInstance.step == 2">
              <StepTwo :instance="grantingLoanInstance" @back-to-step-one="grantingLoanInstance.step=1"
               @ready-for-step-three="grantingLoanInstance = $event;" />
            </div>
            <div class="row" v-if="grantingLoanInstance.step == 3">
              <StepThree :instance="grantingLoanInstance" :funds="fundAccountsList" @back-to-step-two="grantingLoanInstance.step = 2"
              @on-close="this.$refs.table.getRows();grantLoanDialog=false"/>
            </div>
          </template>
        </card-panel>
      </q-dialog>
  </q-page>
</template>

<script>
import { ref } from 'vue';
import StepOne from 'src/components/GrantLoan/StepOne.vue';
import CustomeTable from 'src/components/CustomeTable.vue';
import CardPanel from 'src/components/CardPanel.vue';
import { getJalaliDateSeperately } from 'src/helpers/dateOutputs';
import { accountsList, fundAccountList } from 'src/helpers/statics';
import StepTwo from 'src/components/GrantLoan/StepTwo.vue';
import StepThree from 'src/components/GrantLoan/StepThree.vue';
import { api } from 'src/boot/axios';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
const columns = [
  {
    name: 'id',
    label: 'شماره وام',
    field: 'id',
    disable_search: true,
  },
  {
    name: 'account_id',
    label: 'شماره حساب',
    field: 'account_id',
    disable_search: true,
  },
  {
    name: 'account_name',
    label: ' نام',
    field: 'account_name',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ وام',
    field: 'created_at',
    disable_search: true,
  },
  {
    name: 'granted_at',
    label: 'مانده وام',
    field: 'granted_at',
    disable_search: true,
  },
  {
    name: 'amount',
    label: 'مبلغ وام',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'number_of_installments',
    label: 'تعداد اقساط',
    field: 'number_of_installments',
    disable_search: true,
  },
  {
    name: 'no_of_paid_inst',
    label: 'پرداخت شده',
    field: 'no_of_paid_inst',
    disable_search: true,
  },
  {
    name: 'description',
    label: 'توضیحات',
    field: 'description',
    disable_search: true,
  },
  {
    name: 'paid_amount',
    label: 'مبلغ پرداخت شده',
    field: 'paid_amount',
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
              title: 'اقساط این وام',
              icon_name: 'info',
              icon_color: 'primary',
              emit: 'on-show-installments'
            },
            // {
            //   title: 'ویرایش اقساط',
            //   icon_name: 'delete',
            //   icon_color: 'primary',
            //   emit: 'on-edit-installments'
            // },
            // {
            //   title: 'ویرایش وام',
            //   icon_name: 'delete',
            //   icon_color: 'primary',
            //   emit: 'on-edit-installments'
            // },
            {
              title: 'حذف وام',
              icon_name: 'delete',
              icon_color: 'primary',
              emit: 'on-delete-loan'
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
export default {

  setup () {
    const {year , month , day} = getJalaliDateSeperately()
    return {
        loanSummation:ref([]),
        filter:ref({
          account_id:null,
          account_name:null,
          loan_account_id:null,
          amount:null,
          title:'',
          is_not_paid:null
        }),
        searchQuery:ref(''),
        loanInstance: ref({
        id:null,
        title:'',
        static_fee:0,
        fee_percent:4,
        number_of_installments:5,
        installment_interval:30,
        max_amount:0,
        min_amount:0,
        emergency:false,
        no_need_to_pay:false
      }),
      grantingLoanInstance:ref({
        step:1,
        id:null,
        loan_id:null,
        account_id:null,
        amount:0,
        paid_amount:0,
        granted_at:null,
        payback_at:null,
        number_of_installments:0,
        no_of_paid_inst:0,
        fee_amount:null,
        description:'',
        account_name:null,
        created_at:null,
        recent_loans:[],
        fund_account_id:null,
        installments:[]
      }),
      year,
      loanInfoDialog: ref(false),
      grantLoanDialog:ref(false),
      columns,
      accountsList:ref([]),
      fundAccountsList:ref([])
    }
  },
  data(){

    },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    showInstallments(loan){
        this.$router.push(`/installments?loan_account_id=${loan.id}`)
      },
      async deleteloan(loan){
        await api.get(`installment/count/${loan.account_id}`).then(res=>{
      this.$emit('on-ok-dialog', {
        message: `آیا از حذف وام اطمینان دارید؟`+`این وام دارای ${res.data.counts} قسط پرداخت نشده است.`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.post('loan_account/delete',{id:loan.id}).then(res=>{
        this.$refs.table.getRows()
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
    }).catch(error=>{
      alert(error.response.data.msg)
    })

      },
    search(){
      this.searchQuery = ''
        this.searchQuery = '/search?'
        if (this.filter.account_id != null && this.filter.account_id !== '') this.searchQuery += `account_id=${this.filter.account_id}&`
        if (this.filter.account_name != null && this.filter.account_name !== '') this.searchQuery += `account_name=${this.filter.account_name}&`
        if (this.filter.loan_account_id != null && this.filter.loan_account_id !== '') this.searchQuery += `loan_account_id=${this.filter.loan_account_id}&`
        if (this.filter.amount != null && this.filter.amount !== '') this.searchQuery += `amount=${this.filter.amount}&`
        if (this.filter.title != null && this.filter.title !== '') this.searchQuery += `title=${this.filter.title}&`
        if (this.filter.is_not_paid != null && this.filter.is_not_paid) this.searchQuery += `is_not_paid=true&`
        if (this.filter.is_not_paid != null && !this.filter.is_not_paid) this.searchQuery += `is_not_paid=false&`
        this.reloadTable()
    },
    reloadTable(){
    setTimeout(()=>{this.$refs.table.getRows()},1)
  },
    async onAfterLoaded(rows){
      this.accountsList = await accountsList()
      this.fundAccountsList = await fundAccountList()
    },
    initLoan(){
      this.loanInstance = {
        id:null,
        title:'',
        static_fee:0,
        fee_percent:4,
        number_of_installments:5,
        installment_interval:30,
        max_amount:0,
        min_amount:0,
        emergency:false,
        no_need_to_pay:false
      }
      this.loanInfoDialog = true
    },
    addLoan(){
      this.$refs.loanInfoDialogRef.submit({
        url: 'loan',
        value : this.loanInstance
      })
    }
  },
  components:{
    CustomeTable,
    CardPanel,
    StepOne,
    StepTwo,
    StepThree
  }
}
</script>
