<template>
 <q-page>
<div class="row items-center">
<div class="col-12 text-center">
  <SelectionInput dense
    :option-list="loans"
    @on-update-model="updateLoan"
    label="نوع وام" />
</div>
      <div class="col-12 row">
      <div class="col-6 ">
        <q-input label="تاریخ اعطای وام" v-model="account.granted_at" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            </div>
            <div class="col-6">
              <q-input label="تاریخ شروع بازپرداخت" v-model="account.payback_at" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            </div>
          <div class="col-6 ">
            <q-input type="number" :min="loanInstance.min_amount" :max="loanInstance.max_amount"
             class="style" outlined dense
                  hint="مبلغ وام"
                  placeholder="مبلغ وام"
                   v-model="account.amount"
                   suffix="ریال"
                   @update:model-value="account.fee_amount=Number(Number(loanInstance.static_fee) + ((Number(loanInstance.fee_percent) * $event)/100))"/>
                   {{ formatCurrencyy(account.amount) }}
                   <div v-if="loanInstance.max_amount >0 && Number(loanInstance.max_amount)<=Number(account.amount)" class="text-negative text-center items-center" style="text-decoration: underline;">سقف وام : {{ loanInstance.max_amount }}</div>
          </div>
          <div class="col-6  ">
            <q-input type="number" min="2"
             class="style" outlined dense
                  hint="تعداد اقساط"
                  placeholder="تعداد اقساط"
                   v-model="account.number_of_installments"/>
            </div>
            <div class="col-6 q-pa-sm h4-5 font-medium">
              فاصله دو قسط : {{ loanInstance.installment_interval }} روز
            </div>
            <div class="col-6 q-pa-sm h4 font-bold" v-show="loanInstance.emergency === '1'">
              وام ضروری است.
            </div>
            <div class="col-6 q-pa-sm h3-4 font-bold" v-show="loanInstance.no_need_to_pay === '1'">
              وام بلاعوض است.
            </div>
            <div class="col-6 q-pa-sm h5 font-medium-bold" >
              سود صفر است.
            </div>
            <div class="col-6  ">
          مقدار کارمزدی که حالا از متقاضی اخذ می گردد :
            <q-input type="text"
             class="style" outlined dense
                  hint="مبلغ کارمزد"
                  placeholder="مبلغ کارمزد"
                   v-model="account.fee_amount"
                   suffix="ریال"/>
                   {{ formatCurrencyy(account.fee_amount) }}
                   است.
            </div>
            <div class="col-6 ">
              <q-input dense
                type="textarea"
                class="style"
                outlined
                placeholder="توضیحات"
                hint="توضیحات"
                v-model="account.description"/>
            </div>
      </div>
      <q-separator inset />

<div class="col-12 items-center q-ml-auto q-mt-md">
  <q-btn
   @click="goPreviousStep" label="قبلی" outline color="primary"/>
  <q-btn v-if="account.amount != 0 && account.loan_id != null && account.granted_at != null && account.payback_at != null"
   @click="goNextStep" label="بعدی" color="primary"/>
</div>
</div>
</q-page>
</template>
<script>
import { ref } from 'vue';
import SelectionInput from '../SelectionInput.vue';
import { api } from 'src/boot/axios';
import { addDaysToJalaliDate, getJalaliDate } from 'src/helpers/dateOutputs';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
export default{
  components:{
    SelectionInput
  },
  props:{
    instance:{
    type: Object,
    required:true
  }
  },
  setup(props){
    const loans = ref([])
    const account = props.instance

    return{
      loans,
      account,
      loanInstance:ref({})
        }
  },
  data(){
    this.getLoans()
    this.account.granted_at = getJalaliDate()

  },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    async getLoans(){
      await api.get('loan').then(res=>{
        res.data.loans.forEach(loan=>{
          this.loans.push({label:loan.title ,value:loan.id,data:loan})
        })
      }).catch(error=>{
        alert(error.response.data.msg)
      })
    },
    setLoan(item,loan){
      this.account[item] = loan
    },
    updateLoan(event){
      this.loanInstance = event.data;
      this.account.number_of_installments = this.loanInstance.number_of_installments;
      this.account.description = this.loanInstance.title
      // this.account.fee_amount =  this.loanInstance.static_fee + ((this.loanInstance.fee_percent * this.account.amount)/100);
      this.setLoan('loan_id',event.value)
      this.account.payback_at = addDaysToJalaliDate(getJalaliDate(),parseInt(this.loanInstance.installment_interval))

    },
    goNextStep(){
      this.account.step = 3
      if(this.loanInstance.no_need_to_pay === '1') this.account.no_need_to_pay = true
      this.$emit('ready-for-step-three',this.account)
    },
    goPreviousStep(){
      // this.account.step = 2
      this.$emit('back-to-step-one')
    },
  }
}
</script>
