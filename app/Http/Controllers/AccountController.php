<?php

namespace App\Http\Controllers;

use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Installment;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function create(AccountRequest $request){
        $request->validated();
        $account = Account::create([
            'member_id' => $request->member_id,
            'balance' => $request->balance,
            'account_number' => $request->account_number,
            'member_name' => $request->member_name,
            'status' => $request->status,
            'description' => $request->description,
        ]);
        if ($account) return response()->json([
            'msg' => 'شماره حساب با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد شماره حساب رخ داد!',
            'success' => false
        ],500);
    }
    public function update(AccountRequest $request){
        $request->validated();
        $account = Account::where('id',$request->id)->update([
            'member_id' => $request->member_id,
            'balance' => $request->balance,
            'account_number' => $request->account_number,
            'member_name' => $request->member_name,
            'status' => $request->status,
            'description' => $request->description,
        ]);
        if ($account) return response()->json([
            'msg' => ' با موفقیت آپدیت شد.',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت شماره حساب رخ داد!',
            'success' => false
        ],500);
    }
    public function destroy(Request $request){
        $account = Account::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'شماره حساب با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showOne($id){
        $account = Account::with(['loans','member','monthlyCharges'])->where('id', $id)->first();
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
        $accounts = Account::with(['loans','member','monthlyCharges'])->get();
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
    public function search($str){
        if($str) {
            $accounts = Account::where('account_number', 'LIKE', "%{$str}%")
                ->orWhere('member_name', 'LIKE', "%{$str}%")->with(['loans','member','monthlyCharges'])->get();
            return response()->json([
                'accounts' => $accounts,
                'success' => true
            ]);
        }
    }

}
