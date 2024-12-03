<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountAndMemberRequest;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function createMemberAndAccount(AccountAndMemberRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $memberController = new MemberController();
            $member = $memberController->create($request);
            $this->updateFundAccBalance($request);
            $account = Account::create([
                'member_id' => $member->id,
                'balance' => $request->balance,
                'is_open' => $request->is_open,
                'member_name' => $member->full_name,
                'status' => $request->status,
                'stock_units' => $request->stock_units,
                'description' => $request->description,
            ]);
            $deposit = [
                'description' => $request->description,
                'account_id' => $account->id,
                'amount' => $request->balance,
                ];
            $depositController = new DepositController();
            $depositController->createLog($deposit);
            DB::commit();
            if ($account) return response()->json([
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
        $fund_account = FundAccount::latest()->first();
        $fund_account->balance += $request->balance;
        $fund_account->total_balance += $request->balance;
        $fund_account->save();

    }
    public function update(AccountRequest $request){
            $request->validated();
            $memberController = new MemberController();
            $member = $memberController->updateWithAccountUpdate($request);
            $account = Account::where('id',$request->id)->update([
                'member_id' => $request->member_id,
//                'balance' => $request->balance,
                'is_open' => $request->is_open,
                'member_name' => $request->member_name,
                'status' => $request->status,
                'stock_units' => $request->stock_units,
                'description' => $request->description,
            ]);
            if ($account) return response()->json([
                'member'=>$member,
                'msg' => ' حساب با موفقیت آپدیت شد. .',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در آپدیت حساب رخ داد!',
                'success' => false
            ], 500);

    }
//    public function destroy(Request $request){
//        $account = Account::where('id', $request->id)->delete();
//        return response()->json([
//            'msg' => 'شماره حساب با موفقیت حذف گردید.',
//            'success' => true
//        ]);
//    }
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
    public function showAll(){
        $accounts = Account::with(['monthlyCharges','member'])->get();
        return response()->json([
            'accounts' => $accounts,
            'success' => true
        ]);
    }
    public function showList(){
        $accounts = Account::all();
        return response()->json([
            'accounts' => $accounts,
            'success' => true
        ]);
    }
    public function updateStocks(Request $request){
        foreach ($request->account_ids as $id){
            Account::where('id',$id)->update(['stock_units' => $request->stock_units]);
        }
        return response()->json([
            'msg' => 'آپدیت با موفقیت انجام شد.',
            'success' => true
        ]);
    }
    public function search($str){
        if($str) {
            $accounts = Account::where('id', 'LIKE', "%{$str}%")
                ->orWhere('member_name', 'LIKE', "%{$str}%")->with(['loans','member','monthlyCharges'])->get();
            return response()->json([
                'accounts' => $accounts,
                'success' => true
            ]);
        }
    }

}
