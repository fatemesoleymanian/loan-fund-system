<template>
  <div class="row items-center">
    <div>
      اطلاعات حساب
      <div class="col-12 row">
      <div class="col-6 q-pa-sm h4">
            شماره حساب : {{account.id}}
            </div>
            <div class="col-6 q-pa-sm h4">
            نام حساب : {{account.member_name}}
            </div>
          <div class="col-6 q-pa-sm h4 font-bold">
            موجودی : {{ formatCurrencyy(account.balance) }}
          </div>
          <div class="col-6 q-pa-sm h4 ">
            عضویت : {{account.stock_units}} سهمی
            </div>
            <div class="col-6 q-pa-sm h4 ">
            وضعیت حساب : {{account.status}}
            </div>
      </div>
    </div>
    <div class="col-6 ">
        <q-input label="شماره قسط" v-model="payment.inst_number" class="style" outlined dense disable />
            </div>
            <div class="col-6 ">
            <q-input type="text"
             class="style" outlined dense
                  hint="مبلغ قسط " disable
                  placeholder="مبلغ قسط "
                   v-model="payment.amount"
                   suffix="ریال"/>
                   {{formatCurrencyy(payment.amount)}}
          </div>
          <div class="col-6 ">
            <q-input type="text"
             class="style" outlined dense
                  hint="تاریخ سررسید " disable
                  placeholder="تاریخ سررسید "
                   v-model="payment.due_date"/>
          </div>
          <div class="col-6 q-pa-sm h4">
            تاخیر : {{ payment.delay_days }}
          </div>

        <div class="col-6 " v-if="payment.type == 2">
          <q-input label="شماره وام" v-model="payment.loan_account_id" class="style" outlined dense />
        </div>

    <div class="col-12 text-center">
      <SelectionInput dense
        :option-list="fundAccounts"
        @on-update-model="payment.fund_account_id = $event.value"
        label="حساب" />
    </div>
    <div class="text-center items-center justify-center">
      <q-btn label="پرداخت" unelevated color="primary" class="col-6 style col-sm-3" style="max-width: 200px;"
       @click="doPayment"
      v-if="payment.fund_account_id != null"/>
      <q-btn unelevated  label="لغو" outline color="negative"
      class="col-6 style font-demi-bold col-sm-3" style="max-width: 200px;" v-close-popup/>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import SelectionInput from './SelectionInput.vue';
import { api } from 'src/boot/axios';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';

export default{
  props:{
    instance:{
      type:Object,
      required:true
    },
    funds:{
    type:Array,
    required:true
  }
  },
  setup(props){
    const fundAccounts = props.funds
    const payment = ref(props.instance)
    return{
      payment,
      fundAccounts,
      account:ref({})
    }
  },
  data(){
    this.setAccount()
  },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    doPayment(){
      this.$emit('on-pay',this.payment)
    },
    async setAccount(){
      api.get(`account/${this.payment.account_id}`).then(res=>{
          this.account = res.data.account
      })
    }
  },
  components:{
    SelectionInput
  }
}
</script>
