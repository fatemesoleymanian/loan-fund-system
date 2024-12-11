<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountAndMemberRequest;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Installment;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function createMemberAndAccount(AccountAndMemberRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $fund_account = FundAccount::current();
            $request->merge(['fund_account_id'=>$fund_account->id]);
            $memberController = new MemberController();
            $member = $memberController->create($request);
            $this->updateFundAccBalance($request);
            $account = Account::create([
                'member_id' => $member->id,
                'balance' => $request->balance,
                'member_name' => $member->full_name,
                'stock_units' => $request->stock_units,
                'is_open' => $request->is_open,
                'have_sms' => $request->have_sms,
                'status' => $request->status,
                'description' => $request->description,
            ]);
            if($request->balance >0){
                $deposit = [
                    'description' => 'افتتاح حساب',
                    'account_id' => $account->id,
                    'fund_account_id' => $request->fund_account_id,
                    'amount' => $request->balance,
                ];
                $depositController = new DepositController();
                $deposit = $depositController->createLog($deposit);
            }
            DB::commit();
            if ($account) return response()->json([
                'deposit'=>$deposit ?? null,
                'msg' => ' حساب با موفقیت اضافه شد. .',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در ایجاد حساب رخ داد!',
                'success' => false
            ], 500);
        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در ایجاد رخ داد! ' . $e->getMessage());
        }
    }
    private function updateFundAccBalance($request){
        $fund_account = FundAccount::where('id',$request->fund_account_id)->first();
        $fund_account->balance += $request->balance;
        $fund_account->total_balance += $request->balance;
        $fund_account->save();
    }
    public function update(AccountRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $memberController = new MemberController();
            $member = $memberController->updateWithAccountUpdate($request);
            $account = Account::withoutGlobalScope('is_open')->where('id', $request->id)->update([
                'member_id' => $request->member_id,
//                'balance' => $request->balance,
                'member_name' => $request->full_name,
                'stock_units' => $request->stock_units,
                'is_open' => $request->is_open,
                'have_sms' => $request->have_sms,
                'status' => $request->status,
                'description' => $request->description,
            ]);
            DB::commit();
            if ($account) return response()->json([
                'member' => $member,
                'msg' => ' حساب با موفقیت آپدیت شد. .',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در آپدیت حساب رخ داد!',
                'success' => false
            ], 500);
        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در آپدیت رخ داد! ' . $e->getMessage());
        }

    }
    public function close(Request $request){
        $this->checkForSettelment($request);
    }
    private function checkForSettelment($request){
        $installments = Installment::where('account_id',$request->id)->where('paid_date',null)->count();
        if ($installments > 0){
            return response()->json([
                'msg' => ' این حساب دارای '.$installments.'قسط پرداخت نشده است. بعد از تسویه اقدام به بستن کنید!',
                'success' => false
            ],400);
        }else{
           $account = Account::where('id',$request->id)->first();
           $account->is_open = false;
           $account->save();
          return TransactionController::successResponse('حساب با موفقیت بسته شد!',200);
        }
    }
    public function settlement(Request $request){
        $installments = Installment::where('account_id',$request->id)->where('paid_date',null)->count();
        if ($installments > 0){
            return response()->json([
                'msg' => 'تعداد اقساط پرداخت نشده : '.$installments,
                'success' => true
            ],400);
        }else{
           DB::beginTransaction();
            try {
                $account = Account::where('id',$request->id)->first();
                $fund_account = FundAccount::current();
               if ($account->balance > 0){
                   $withdraw = Withdraw::create([
                       'amount'=>$account->balance,
                       'account_id'=>$request->id,
                       'fund_account_id'=>$fund_account->id,
                       'description'=>'تسویه حساب'
                   ]);
               }
                $fund_account->balance -= $account->balance;
                $fund_account->total_balance -= $account->balance;
                $fund_account->save();
                $account->status = Account::STATUS_SETTLEMENT;
                $account->balance = 0;
                $account->save();
                DB::commit();
                return response()->json([
                    'msg' => 'هیچ قسط پرداخت نشده ای وجود ندارد!',
                    'success' => false
                ],200);
            }catch (\Exception $e){
                DB::rollBack();
                return TransactionController::errorResponse('خطایی رخ داد! ' . $e->getMessage());
            }
        }
    }
    public function activate(Request $request){
        $account = Account::withoutGlobalScope('is_open')->where('id',$request->id)->first();
        $account->status = Account::STATUS_CREDITOR;
        $account->is_open = true;
        $account->save();
        return response()->json([
            'msg'=>'حساب باموفقیت فعال شد!',
            'success'=>true
        ],200);
    }
    public function showOne($id){
//        $account = Account::with(['loans','member','monthlyCharges'])->where('id', $id)->first();
        $account = Account::with(['member','monthlyCharges'])->where('id', $id)->first();
        if ($account) return response()->json([
            'account' => $account,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن شماره حساب',
            'success' => false
        ]);
    }
    public function showOneWithMonthlyCharge($id){
        $account = Account::with(['monthlyCharges'])->where('id', $id)->first();
        if ($account) return response()->json([
            'account' => $account,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن شماره حساب',
            'success' => false
        ]);
    }
    public function showOneWithLoan($id){
        $account = Account::with(['loans','loan_details'])->where('id', $id)->first();
        if ($account) return response()->json([
            'account' => $account,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن شماره حساب',
            'success' => false
        ]);
    }
    //only opened ones
    public function showAllOpened(){
        $accounts = Account::with(['member'])->get();
        return response()->json([
            'accounts' => $accounts,
            'success' => true
        ]);
    }
    //all open and close
    public function showAll(){
        $accounts = Account::withoutGlobalScope('is_open')->with(['member'])->get();
        return response()->json([
            'accounts' => $accounts,
            'success' => true
        ]);
    }
    //only closed ones
//    public function showAllClosed(){
//        $accounts = Account::withoutGlobalScope('is_open')->closedAccounts()->get();
//        return response()->json([
//            'accounts' => $accounts,
//            'success' => true
//        ]);
//    }
    public function showList(){
        $accounts = Account::openAccounts()->get();
        return response()->json([
            'accounts' => $accounts,
            'success' => true
        ]);
    }
    public function search(Request $request){
        $id = $request->query('id');
        $member_name = $request->query('member_name');
        $status = $request->query('status');

        $query = Account::query();

        if ($id !== null){
            $query->where('id', $id);
        }
        if ($member_name !== null){
            $query->orWhere('member_name', 'LIKE', "%{$member_name}%");
        }
        if ($status !== null){
            $query->orWhere('status', $status);
        }
            $accounts = $query->with(['member'])->get();
            return response()->json([
                'accounts' => $accounts,
                'success' => true
            ]);

    }

}
