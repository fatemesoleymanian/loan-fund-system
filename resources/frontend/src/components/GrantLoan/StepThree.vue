<template>
  <q-page>
<div class="row items-center">
<div class="col-12 text-center">
  <SelectionInput dense
    :option-list="fundAccounts"
    @on-update-model="account.fund_account_id = $event.value"
    label="حساب" />
</div>
      <div class="col-12 row">
        <q-btn v-if="account.fund_account_id != null && installments.length < 1" label=" قسط بندی و ثبت وام" color="primary" @click="askForPartition"/>
      </div>
      <q-separator inset />
                  <div class="col-12 row q-my-md" v-if="installments.length > 0">
                    <div class="table-container q-mb-sm">
                    <table>
                      <thead>
                        <tr>
                          <th>شماره قسط</th>
                          <th> مبلغ </th>
                          <th> تاریخ سررسید </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr  v-for="(d,i) in installments" :key="i">
                          <td>{{ d.inst_number}}</td>
                          <td>{{ formatCurrencyy(d.amount)}}</td>
                          <td>{{ d.due_date}}</td>
                        </tr>
                      </tbody>
                    </table>
                    </div>
                  </div>
                <q-separator inset />
<div class="col-12 items-center q-ml-auto q-mt-md">
  <q-btn
   @click="goPreviousStep" label="قبلی" outline color="primary" v-if="installments.length < 1"/>
  <q-btn @click="this.$emit('on-close')" label="بستن" color="primary" />
</div>
</div>
</q-page>
</template>
<script >
import { ref } from 'vue';
import SelectionInput from '../SelectionInput.vue';
import { api } from 'src/boot/axios';
import { inject } from 'vue';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
export default{
  components:{
    SelectionInput
  },
  props:{
    instance:{
    type: Object,
    required:true
  },
  funds:{
    type:Array,
    required:true
  }
  },
  setup(props){
    const onOkDialog = inject('onOkDialog');
    const account = props.instance
    const fundAccounts = props.funds
    const no_need_to_pay = props.instance.no_need_to_pay !== undefined && props.instance.no_need_to_pay
    return{
    no_need_to_pay,
    installments:ref([]),
    account,
    fundAccounts,
    onOkDialog
    }
  },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    askForPartition(){
      this.onOkDialog({
        message:` آیا از ${this.no_need_to_pay ? 'ثبت' :'قسط بندی'} اطمینان دارید؟`,
        icon: 'info',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          this.saveLoanAndDoPartition()
        },
      });
    },
    async saveLoanAndDoPartition(){
      await api.post('loan_account',this.account).then(res=>{
        this.installments = res.data.installments
        if (this.installments.length < 1) this.$emit('on-close')
        alert(res.data.msg)
      }).catch(error=>{
        alert(error.response.data.msg)
      })
    },
    goPreviousStep(){
      // this.account.step = 2
      this.$emit('back-to-step-two')
    },
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
