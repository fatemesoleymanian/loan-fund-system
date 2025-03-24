<template>
  <q-page class="style">

    <div class="row justify-center text-black h2">مدیریت حساب های صندوق</div>
    <div class="row">
      <div class="col-8">
      <q-btn label="ایجاد حساب" v-if="current_acc == null" color="primary" outline @click="accountInstance={  id: null,
        balance: 0,
        total_balance: 0,
        fees: 0,
        expenses:0,
        name: 'صندوق قرض الحسنه'};fundAccountInfoDialog = true"/>
      </div>
      <div class="col-4">
        <q-select outlined dense v-model="current_acc" :value="fundAccountsList[0]" :options="fundAccountsList" label="صندوق" />
      </div>
    </div>
    <div v-if="current_acc != null">
    <q-splitter
      v-model="splitterModel"
      style="height: fit-content;">
      <template v-slot:before>
        <q-tabs
          v-model="tab"
        switch-indicator
          vertical
          class="text-primary bg-white"
          @update:model-value="tabUpdated">
          <q-tab name="info" icon="mail" label="حساب صندوق"/>
          <q-tab name="assets" icon="alarm" label="اثاثیه" />
          <q-tab name="charity" icon="movie" label="هزینه" />
          <q-tab name="todayReport" icon="movie" label="دفتر روزانه" />
        </q-tabs>
      </template>
      <template v-slot:after>
        <q-tab-panels
          v-model="tab"
          animated
          swipeable
          vertical
          transition-prev="jump-up"
          transition-next="jump-up">
          <q-tab-panel name="info">
            <div class="text-h6 q-mb-md">اطلاعات کلی این حساب</div>
            <div class="row">
              <div class="col-12 q-pa-sm">
                عنوان : {{ current_acc.label }}
               <div class="row">
                <q-input type="text" outlined dense
                   v-model="accountInstance.name" class="col-7 q-mx-sm"/>
                   <q-btn label="ویرایش نام صندوق" @click="updatefundAccount" color="primary" />
               </div>

              </div>
              <div class="col-12 q-pa-sm">
            موجودی : {{ formatCurrencyy(current_acc.data.balance) }}
            </div>
            <div class="col-12 q-pa-sm">
           مجموع کارمزد (درآمد) : {{ formatCurrencyy(current_acc.data.fees) }}
            </div>
            <div class="col-12 q-pa-sm">
           هزینه : {{ formatCurrencyy(current_acc.data.expenses) }}
            </div>
            <div class="col-12 q-pa-sm">
           موجودی کل : {{ formatCurrencyy(current_acc.data.total_balance) }}
            </div>
            <div class="col-12 q-pa-sm">
           وضعیت : {{ current_acc.data.status }}
            </div>
            </div>
          </q-tab-panel>
          <q-tab-panel name="assets">
            <CustomeTable
            ref="table"
            :table="{
              url: 'asset',
              arrayKey: 'assets',
              summation: 'costs'
              }"
                :add_button="
                  {
                      label: 'افزودن اثاثیه',
                      icon: 'add'
                  }"
              @summation-after-loaded="costs = $event"
              @on-add-button="assetInstance={
               id:null,
              title:'',
              cost:0,
              isExpense:true,
              fund_account_id:null,
              description:'',
              money_source:'از کارمزد',
              };assestInfoDialog=true;"
              @on-delete-asset="deleteAsses"
              @on-edit-asset="assetInstance=$event;assestInfoDialog=true;"
              :columns="assestColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
                  <template v-slot:row-cost="{ row }">
                    <div class="h4">{{formatCurrencyy(row.cost) }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-6">مجموع هزینه اثاثیه : {{ formatCurrencyy(costs) }} </div>
            <div class="col-6">مجموع هزینه ها  : {{ formatCurrencyy(current_acc.data.expenses) }} </div>
          </div>
          </q-tab-panel>
          <q-tab-panel name="charity">
                <CustomeTable
            ref="table"
            :table="{
              url: 'charity',
              arrayKey: 'charities',
              summation: 'amounts'
              }"
                :add_button="
                  {
                      label: 'افزودن ',
                      icon: 'add'
                  }"
              @summation-after-loaded="costs = $event"
              @on-add-button="charityInstance={
               id:null,
              amount:0,
              isExpense:true,
              fund_account_id:null,
              description:'',
              money_source:'از کارمزد',
              };charityInfoDialog=true;"
              @on-delete-charity="deleteCharity"
              @on-edit-charity="charityInstance=$event;charityInfoDialog=true;"
              :columns="charityColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
                  <template v-slot:row-amount="{ row }">
                    <div class="h4">{{formatCurrencyy(row.amount) }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-6">مجموع هزینه هزینه : {{ formatCurrencyy(costs) }} </div>
            <div class="col-6">مجموع هزینه ها  : {{ formatCurrencyy(current_acc.data.expenses) }} </div>
          </div>
          </q-tab-panel>
          <q-tab-panel name="todayReport">
            <q-tabs v-model="todayTab" dense class="text-grey"  active-color="primary" indicator-color="primary" align="justify"
          narrow-indicator @update:model-value="tabUpdated">
        <q-tab name="deposits" icon="alarm" label="واریزی ها" />
        <q-tab name="withdraws" icon="movie" label="برداشت ها" />
        <q-tab name="transactions" icon="movie" label="گزارش تراکنش ها" />
             </q-tabs>
             <q-separator />

        <q-tab-panels v-model="todayTab" animated>
          <q-tab-panel name="deposits">
            <div class="row items-center text-center">
              <q-input label="از تاریخ" v-model="filter.from" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-input label="تا تاریخ" v-model="filter.to" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-btn label="نمایش" @click="search"  color="primary" class="sm-style" outline/>
            </div>
            <CustomeTable
            ref="table"
            :table="{
              url: `deposit${searchQuery}`,
              arrayKey: 'deposits',
              summation: 'amounts'
              }"

              @summation-after-loaded="depositsSummation = $event"
              :columns="depositAndWithdrawColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
                  <template v-slot:row-amount="{ row }">
                    <div class="h4">{{formatCurrencyy(row.amount) }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-12">مجموع واریزی ها : {{ formatCurrencyy(depositsSummation) }} </div>
          </div>
          </q-tab-panel>
          <q-tab-panel name="withdraws">
            <div class="row items-center text-center">
              <q-input label="از تاریخ" v-model="filter.from" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-input label="تا تاریخ" v-model="filter.to" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-btn label="نمایش" @click="search"  color="primary" class="sm-style" outline/>
            </div>
            <CustomeTable
            ref="table"
            :table="{
              url: `withdraw${searchQuery}`,
              arrayKey: 'withdraws',
              summation: 'amounts'
              }"

              @summation-after-loaded="withdrawsSummation = $event"
              :columns="depositAndWithdrawColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
                  <template v-slot:row-amount="{ row }">
                    <div class="h4">{{formatCurrencyy(row.amount) }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-12">مجموع برداشت ها : {{ formatCurrencyy(withdrawsSummation) }} </div>
          </div>
          </q-tab-panel>
          <q-tab-panel name="transactions">
            <div class="row items-center text-center">
              <q-input label="از تاریخ" v-model="filter.from" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-input label="تا تاریخ" v-model="filter.to" class="style" outlined dense fill-mask="#" mask="####/##/##"/>
            <q-btn label="نمایش" @click="search"  color="primary" class="sm-style" outline/>
            </div>
            <CustomeTable
            ref="table"
            :table="{
              url: `transaction${searchQuery}`,
              arrayKey: 'transactions',
              summation: 'amounts'
              }"

              @summation-after-loaded="transactionsSummation = $event"
              :columns="transactionsColumns">
              <template v-slot:row-created_at="{ row }">
                    <div class="h5">{{row.created_at }}</div>
                  </template>
                  <template v-slot:row-amount="{ row }">
                    <div class="h4">{{formatCurrencyy(row.amount) }}</div>
                  </template>
                  <!-- <template v-slot:row-fund_account_id="{ row }">
                    <div class="h4-5">{{row.type === 'پرداخت وام' || row.type === 'پرداخت قسط' ? 'صندوق':row.account_id }}</div>
                  </template> -->
                  <template v-slot:row-account_id="{ row }">
                    <div class="h4-5">{{row.account_id }} - {{ row.account_name }}</div>
                  </template>
                  <template v-slot:row-type="{ row }">
                    <div class="h4">{{row.type }}</div>
                  </template>
          </CustomeTable>
          <div class="row q-pa-sm justify-center">
            <div class="col-12">مجموع مبالغ : {{ formatCurrencyy(transactionsSummation) }} </div>
          </div>
          </q-tab-panel>
          </q-tab-panels>

          </q-tab-panel>
        </q-tab-panels>
      </template>
    </q-splitter>
  </div>
      <q-dialog v-model="fundAccountInfoDialog" :persistent="true">
        <card-panel ref="fundAccountInfoDialogRef" title=" حساب صندوق جدید" size="50%"
         @on-submit="addfundAccount()"
         :disableNotify="false"
        @on-success="reloadPage">

          <template #body>
            <div class="row items-center">
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="نام حساب"
                  placeholder="نام حساب"
                   v-model="accountInstance.name"/>
                </div>
                <div class="col-12 col-sm-6">
                  موجودی : {{ formatCurrencyy(accountInstance.balance) }}<br>
                  مجموع کارمزد (درآمد) : {{ formatCurrencyy(accountInstance.fees)}}<br>
                  موجودی کل : {{ formatCurrencyy(accountInstance.total_balance) }}<br>
                  هزینه : {{ formatCurrencyy(accountInstance.expenses) }}

                </div>
              </div>
          </template>
        </card-panel>
      </q-dialog>
      <q-dialog v-model="assestInfoDialog" :persistent="true">
        <card-panel ref="assestInfoDialogRef" :title="assetInstance.id == null ? 'افزودن اثاثیه جدید':'ویرایش اثاثیه'" size="50%"
         @on-submit="assetInstance.id == null ? addAsset() : updateAsset()"
         :disableNotify="false"
        @on-success="reloadPage">

          <template #body>
            <div class="row items-center">
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="نام "
                  placeholder="نام "
                   v-model="assetInstance.title"/>
                </div>
                <div class="col-12 col-sm-6">
                  <q-input dense
                type="number"
                min="0"
                class="style"
                outlined
                placeholder="هزینه"
                hint="هزینه"
                v-model="assetInstance.cost"/>
                {{ formatCurrencyy(assetInstance.cost) }}
                </div>
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="توضیحات اضافی"
                  placeholder="توضیحات اضافی"
                   v-model="assetInstance.description"/>
                </div>
                <div class="col-12 ">
                <q-option-group
                  v-model="assetInstance.money_source"
                  :options="money_sources"
                  color="primary"
                  inline
                  dense
                />
                <q-checkbox v-if="assetInstance.money_source === 'از هیچکدام'" left-label v-model="assetInstance.isExpense"
                 label="بعنوان هزینه محاسبه شود." />
              </div>
              <!-- <div class="col-12 col-sm-6" v-if="assetInstance.money_source === 'از موجودی'">
                <SelectionInput dense
                    :multible="true"
                    :option-list="member_accounts"
                    @on-update-model="makeAccountsField"
                    label="انتخاب حساب ها" />
              </div> -->
              </div>
          </template>
        </card-panel>
      </q-dialog>
      <q-dialog v-model="charityInfoDialog" :persistent="true">
        <card-panel ref="charityInfoDialogRef" :title="charityInstance.id == null ? 'افزودن  ':'ویرایش '" size="50%"
         @on-submit="charityInstance.id == null ? addCharity() : updateCharity()"
         :disableNotify="false"
        @on-success="reloadPage">

          <template #body>
            <div class="row items-center">

                <div class="col-12 col-sm-6">
                  <q-input dense
                type="number"
                min="0"
                class="style"
                outlined
                placeholder="هزینه"
                hint="هزینه"
                v-model="charityInstance.amount"/>
                {{ formatCurrencyy(charityInstance.amount) }}
                </div>
                <div class="col-12 col-sm-6">
                  <q-input type="text" class="style" outlined dense hint="توضیحات اضافی"
                  placeholder="توضیحات اضافی"
                   v-model="charityInstance.description"/>
                </div>
                <div class="col-12 ">
                <q-option-group
                  v-model="charityInstance.money_source"
                  :options="money_sources"
                  color="primary"
                  inline
                  dense
                />
                <q-checkbox v-if="charityInstance.money_source === 'از هیچکدام'" left-label v-model="charityInstance.isExpense"
                 label="بعنوان هزینه محاسبه شود." />
              </div>
              <!-- <div class="col-12 col-sm-6" v-if="charityInstance.money_source === 'از موجودی'">
                <SelectionInput dense
                 :multible="true"
                    :option-list="member_accounts"
                    @on-update-model="makeAccountsField"
                    label="انتخاب حساب ها" />
              </div> -->
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
import { accountsList, fundAccountList, fundAccTypeList } from 'src/helpers/statics';
import { formatCurrency } from 'src/functions/tripleSplitterForNumbers';
const assestColumns = [
{
    name: 'id',
    label: 'شناسه',
    field: 'id',
    disable_search: true,
  },
  {
    name: 'title',
    label: 'نام',
    field: 'title',
    disable_search: true,
  },
  {
    name: 'money_source',
    label: 'برداشت هزینه',
    field: 'money_source',
    disable_search: true,
  },
  {
    name: 'cost',
    label: 'هزینه',
    field: 'cost',
    disable_search: true,
  },
  {
    name: 'description',
    label: 'توضیح',
    field: 'description',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ خرید ',
    field: 'created_at',
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
            // {
            //   title: 'مشاهده/ویرایش',
            //   icon_name: 'info',
            //   icon_color: 'primary',
            //   emit: 'on-edit-asset'
            // },
            {
              title: 'حذف',
              icon_name: 'delete',
              icon_color: 'primary',
              emit: 'on-delete-asset'
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
const charityColumns = [
{
    name: 'id',
    label: 'شناسه',
    field: 'id',
    disable_search: true,
  },
  {
    name: 'money_source',
    label: 'برداشت هزینه',
    field: 'money_source',
    disable_search: true,
  },
  {
    name: 'amount',
    label: 'هزینه',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'description',
    label: 'توضیح',
    field: 'description',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ ',
    field: 'created_at',
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
            // {
            //   title: 'مشاهده/ویرایش',
            //   icon_name: 'info',
            //   icon_color: 'primary',
            //   emit: 'on-edit-asset'
            // },
            {
              title: 'حذف',
              icon_name: 'delete',
              icon_color: 'primary',
              emit: 'on-delete-charity'
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
const depositAndWithdrawColumns = [
{
    name: 'id',
    label: 'شناسه',
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
    name: 'amount',
    label: 'مبلغ',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'description',
    label: 'توضیح',
    field: 'description',
    disable_search: true,
  },
  {
    name: 'created_at',
    label: 'تاریخ ',
    field: 'created_at',
    disable_search: true,
  }
]
const transactionsColumns = [
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
    name: 'amount',
    label: 'مبلغ',
    field: 'amount',
    disable_search: true,
  },
  {
    name: 'type',
    label: 'علت',
    field: 'type',
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
    return {
      tab: ref('info'),
      todayTab:ref('deposits'),
      money_sources:[
        {
          label: 'برداشت از کارمزد',
          value: 'از کارمزد'
        },
        {
          label: 'برداشت از موجودی',
          value: 'از موجودی'
        },
        {
          label: 'برداشت از هیچکدام',
          value: 'از هیچکدام'
        },
      ],
      splitterModel: ref(20),
      accountInstance: ref({
        id: null,
        balance: 0,
        total_balance: 0,
        fees: 0,
        expenses:0,
        name: 'صندوق قرض الحسنه ',
      }),
      assetInstance:ref({
        id:null,
        title:'',
        cost:0,
        isExpense:true,
        fund_account_id:null,
        description:'',
        money_source:'از کارمزد',
      }),
      charityInstance:ref({
         id:null,
        amount:0,
        isExpense:true,
        fund_account_id:null,
        description:'',
        money_source:'از کارمزد',
      }),
      costs:ref(0),
      depositsSummation:ref(0),
      withdrawsSummation:ref(0),
      transactionsSummation:ref(0),
      fundAccountInfoDialog: ref(false),
      assestInfoDialog: ref(false),
      charityInfoDialog: ref(false),
      assestColumns,
      charityColumns,
      depositAndWithdrawColumns,
      transactionsColumns,
      balance_change:ref(0),
      fundAccountsList:ref([]),
      current_acc:ref(null),
      member_accounts:ref([]),
      tmpAccounts:ref([]),
      filter:ref({
        from:'',
        to:''
      }),
      searchQuery:ref('')
    }
  },
  data(){
    this.onAfterLoaded()
    },
  methods:{
    formatCurrencyy(num){
      return formatCurrency(num)
    },
    tabUpdated(tab){
      // console.log(tab)
      this.searchQuery = ''
      this.filter.from = ''
      this.filter.to = ''
    },
    search(){
        this.searchQuery = ''
        this.searchQuery = '/search?'
        if (this.filter.from != null && this.filter.from !== '') this.searchQuery += `from=${this.filter.from}&`
        if (this.filter.to != null && this.filter.to !== '') this.searchQuery += `to=${this.filter.to}&`
        this.reloadTable()
      },
      reloadTable(){
    setTimeout(()=>{this.$refs.table.getRows()},1)
  },
    reloadPage(){
     window.location.reload()
    },
    async onAfterLoaded(){
      this.fundAccountsList = await fundAccountList()
      if (this.fundAccountsList.length <1) {
        this.accountInstance={  id: null,
        balance: 0,
        total_balance: 0,
        fees: 0,
        expenses:0,
        name: 'صندوق قرض الحسنه '}
        this.fundAccountInfoDialog = true
      }else {
        // this.member_accounts = await accountsList()
        this.current_acc = this.fundAccountsList[0]
      }
    },
    addfundAccount(){
      this.$refs.fundAccountInfoDialogRef.submit({
        url: 'fund_account',
        value : this.accountInstance
      })
    },
    async updatefundAccount(){
      this.accountInstance.balance = this.current_acc.data.balance
      this.accountInstance.total_balance = this.current_acc.data.total_balance
      this.accountInstance.fees = this.current_acc.data.fees
      this.accountInstance.expenses = this.current_acc.data.expenses
      this.accountInstance.id = this.current_acc.value
      await api.put('fund_account',this.accountInstance).then(res=>{
        this.$emit('on-notify',res.data.msg)
      }).catch(error=>{
        if (error.response != null ) {
    alert(error.response.data.message || error.response.data.msg)
  }
      })

    },
    async deletefundAccount(){
      this.$emit('on-ok-dialog', {
        message: `آیا از حذف حساب صندوق اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.post('fund_account/delete',{id:this.current_acc.value}).then(res=>{
        this.reloadPage()
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
    },
    async deleteAsses(asset){
      this.$emit('on-ok-dialog', {
        message: `آیا از حذف اثاثیه اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.post('asset/delete',{id:asset.id,fund_account_id:this.current_acc.value}).then(res=>{
        this.reloadPage();
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
    },
    async deleteCharity(charity){
      this.$emit('on-ok-dialog', {
        message: `آیا از حذف هزینه اطمینان دارید؟`,
        icon: 'delete',
        color: 'negative',
        textColor: 'white',
        onOk: async () => {
          await api.post('charity/delete',{id:charity.id,fund_account_id:this.current_acc.value}).then(res=>{
        this.reloadPage();
      }).catch(error=>{
        alert(error.response.data.message)
      })
        }
      })
    },
    addAsset(){
      //  this.assetInstance.accounts = this.splitAccountsForCharityAndAsset()
      this.assetInstance.fund_account_id = this.current_acc.value
      this.$refs.assestInfoDialogRef.submit({
        url: 'asset',
        value : this.assetInstance
      })
    },
    updateAsset(){
      this.$refs.assestInfoDialogRef.submit({
        url: 'asset',
        value : this.assetInstance
      },'put')
    },
    makeAccountsField(val){
      this.tmpAccounts = val
    },
    addCharity(){
      // this.charityInstance.accounts = this.splitAccountsForCharityAndAsset()
      this.charityInstance.fund_account_id = this.current_acc.value
      this.$refs.charityInfoDialogRef.submit({
        url: 'charity',
        value : this.charityInstance
      })
    },
    splitAccountsForCharityAndAsset(){
      let accs = ''
      if(this.tmpAccounts == null) return null
      else {
        this.tmpAccounts.forEach((acc,indx)=>{
        if (indx != 0) accs += ','
        accs += acc.value
      })
      return accs;
    }
    }
  },
  components:{
    CustomeTable,
    CardPanel  }
}
</script>
