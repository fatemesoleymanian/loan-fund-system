import { api } from "src/boot/axios";

export async function membersList (){
  const listt = []
 await api.get('member/list').then(res=>{

  res.data.members.forEach(member=>{
    listt.push({label:member.full_name , value:member.id})
  })

 }).catch(error=>{
  if (error.response != null ) {
    alert(error.response.data.message)
  }
 })
 return listt
}
export async function accountsList (){
  const listt = []
 await api.get('account/list').then(res=>{

  res.data.accounts.forEach(account=>{
    listt.push({label:account.member_name +' - '+ account.id , value:account.id, data:account})
  })

 }).catch(error=>{
  if (error.response != null ) {
    alert(error.response.data.message)
  }
 })
 return listt
}
export async function monthlyChargeList (){
  const listt = []
 await api.get('monthly_charge/list').then(res=>{

  res.data.monthly_charges.forEach(monthly=>{
    listt.push({label:monthly.title , value:monthly.id})
  })

 }).catch(error=>{
  if (error.response != null ) {
    alert(error.response.data.message)
  }
 })
 return listt
}
export const fundAccTypeList = [
  {
    label:'قرض الحسنه',
    value:0
  },
  {
    label:'عادی',
    value:1
  }
]
export const transactionTypes =[
  {
    label:'واریز',
    value:0
  },
  {
    label:'برداشت',
    value:1
  },
  {
    label:'پرداخت کارمزد',
    value:2
  },
  {
    label:'پرداخت جریمه',
    value:3
  },
  {
    label:'پرداخت قسط',
    value:4
  },
  {
    label:'پرداخت وام',
    value:5
  },
  {
    label:'پرداخت ماهیانه',
    value:6
  }
]
export async function fundAccountList(){
  const listt = []
await api.get('fund_account').then(res=>{

  res.data.fund_accounts.forEach(acc=>{
    listt.push({label:acc.name , value:acc.id, data:acc})
  })

 }).catch(error=>{
  if (error.response != null ) {
    alert(error.response.data.message)
  }
 })
 return listt
}
export const accountStatus = [
  {
    label: 'بدهکار',
    value: 'بدهکار'
  },
  {
    label: 'تسویه',
    value:  'تسویه'
  },
  {
    label: 'بستانکار',
    value: 'بستانکار'
  },
  {
    label: 'همه',
    value: 4
  }
]
