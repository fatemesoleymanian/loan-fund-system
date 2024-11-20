<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyChargeAccountRequest;
use App\Models\MonthlyChargeAccount;
use Illuminate\Http\Request;

class MonthlyChargeAccountController extends Controller
{
    public function create(MonthlyChargeAccountRequest $request){
        $request->validated();
        $member = MonthlyChargeAccount::create([
            'account_id' => $request->account_id,
            'monthly_charge_id' => $request->monthly_charge_id
        ]);
        if ($member) return response()->json([
            'msg' => ' با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی رخ داد!',
            'success' => false
        ],500);
    }
    public function update(MonthlyChargeAccountRequest $request){
        $request->validated();
        $member = MonthlyChargeAccount::where('id',$request->id)->update([
            'account_id' => $request->account_id,
            'monthly_charge_id' => $request->monthly_charge_id
        ]);
        if ($member) return response()->json([
            'msg' => 'حساب با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت حساب رخ داد!',
            'success' => false
        ],500);
    }
    public function destroy(Request $request){
        $account = MonthlyChargeAccount::where('account_id', $request->account_id)
            ->where('monthly_charge_id',$request->monthly_charge_id)->delete();
        return response()->json([
            'msg' => 'حساب با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showOneByMember($account_id){
        $charges = MonthlyChargeAccount::with(['member','monthlyCharge'])->where('account_id', $account_id)->first();
        if ($charges) return response()->json([
            'charges' => $charges,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن ماهیانه',
            'success' => false
        ]);
    }
    public function showAllByCharge($charge_id){
        $members = MonthlyChargeAccount::with(['member','monthlyCharge'])->where('monthly_charge_id', $charge_id)->get();
        if ($members) return response()->json([
            'members' => $members,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اعضا',
            'success' => false
        ]);
    }
    public function showAll(){
        $membersAndCharges = MonthlyChargeAccount::with(['member','monthlyCharge'])->get();
        return response()->json([
            'members_charges' => $membersAndCharges,
            'success' => true
        ]);
    }
}
