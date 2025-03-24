<template>
<q-page>
<div class="row items-center">
<div class="col-12 text-center">
  <SelectionInput dense
    :option-list="accounts"
    @on-update-model="checkForUnpaidInstallments"
    label="حساب" />
</div>
      <div class="col-12 row">
      <div class="col-6 q-pa-sm h4">
            شماره حساب : {{selectedAccount.id}}
            </div>
            <div class="col-6 q-pa-sm h4">
            نام حساب : {{selectedAccount.member_name}}
            </div>
          <div class="col-6 q-pa-sm h4 font-bold">
            موجودی : {{ formatCurrencyy(selectedAccount.balance) }}
          </div>
          <div class="col-6 q-pa-sm h4 ">
            عضویت : {{selectedAccount.stock_units}} سهمی
            </div>
            <div class="col-6 q-pa-sm h4 ">
            وضعیت حساب : {{selectedAccount.status}}
            </div>
      </div>
      <q-separator inset />
                  <div class="col-12 row q-my-md" v-if="account.recent_loans.length > 0">
                    <div class="table-container q-mb-sm">
                    <table>
                      <thead>
                        <tr>
                          <th>شماره وام </th>
                          <th>تاریخ وام </th>
                          <th>ت شروع بازپرداخت </th>
                          <th>مبلغ</th>
                          <th>تعداد اقساط</th>
                          <th>تعداد پرداخت شده</th>
                          <th>مانده وام</th>
                          <th>نوع وام </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr  v-for="(d,i) in account.recent_loans" :key="i">
                          <td>{{ d.id}}</td>
                          <td>{{ d.created_at}}</td>
                          <td>{{ d.payback_at}}</td>
                          <td>{{ formatCurrencyy(d.amount)}}</td>
                          <td>{{ d.number_of_installments}}</td>
                          <td>{{ d.no_of_paid_inst}}</td>
                          <td>{{ d.amount - d.paid_amount}}</td>
                          <td>{{ d.title}}</td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                  </div>
                <q-separator inset />
<div class="col-12 items-center q-ml-auto q-mt-md">
  <q-btn v-if="!noUnpaidInst && account.account_id != null" @click="goNextStep" label="بعدی" color="primary"/>
</div>
</div>
</q-page>
</template>

<script>
import { inject, ref } from 'vue'
import SelectionInput from '../SelectionInput.vue';
import { api } from 'src/boot/axios';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
export default {
props: {
  instance:{
    type: Object,
    required:true
  },
  accounts:{
    type:Array,
    required:true
  }
},
components:{
  SelectionInput
},
setup (props) {
const account = props.instance
const onOkDialog = inject('onOkDialog');

return {
  account,
  noUnpaidInst:ref(true),
  selectedAccount:ref({}),
  onOkDialog
}
},
methods:{
  formatCurrencyy(num){
      return formatCurrency(num)
    },
  async checkForUnpaidInstallments(account){
    this.account.account_id = account.value
    this.account.account_name = account.data.member_name
    this.selectedAccount = account.data
    await api.get(`loan_account/${account.value}`).then(async res=>{
      this.account.recent_loans = res.data.loans
      await api.get(`installment/count/${account.value}`).then(res=>{
      this.allowToGrant(res.data.counts)
    }).catch(error=>{
      alert(error.response.data.msg)
    })
    }).catch(error=>{
      alert(error.response.data.msg)
    })

  },
  allowToGrant(count){
    if(count > 0){
      this.onOkDialog({
        message: `این حساب ${ count} قسط پرداخت نشده دارد ، آیا از اعطای وام اطمینان دارید؟`,
        icon: 'info',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          this.noUnpaidInst = false
        }
      })
    }else this.noUnpaidInst = false

  },
  goNextStep(){
    this.account.step = 2
    this.$emit('ready-for-step-two',this.account)
  }
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
