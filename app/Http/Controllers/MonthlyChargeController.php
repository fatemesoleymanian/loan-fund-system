<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyChargeRequest;
use App\Models\MonthlyCharge;
use Illuminate\Http\Request;

class MonthlyChargeController extends Controller
{
    public function create(MonthlyChargeRequest $request){
        $request->validated();
        $monthlyCharge = MonthlyCharge::create([
            'amount' => $request->amount,
            'year' => $request->year,
            'title' => $request->title,
//            'fund_account_id' => $request->fund_account_id
        ]);
        if ($monthlyCharge) return response()->json([
            'msg' => 'ماهیانه جدیدی با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد ماهیانه رخ داد!',
            'success' => false
        ],500);
    }
    public function update(MonthlyChargeRequest $request){
        $request->validated();
        $monthlyCharge = MonthlyCharge::where('id',$request->id)->update([
            'amount' => $request->amount,
            'year' => $request->year,
            'title' => $request->title,
//            'fund_account_id' => $request->fund_account_id
        ]);
        if ($monthlyCharge) return response()->json([
            'msg' => 'ماهیانه با موفقیت آپدیت شد.',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت ماهیانه رخ داد!',
            'success' => false
        ],500);
    }
    public function destroy(Request $request){
        $monthlyCharge = MonthlyCharge::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'ماهیانه با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showOne($id){
        $monthlyCharge = MonthlyCharge::with(['accounts'])->where('id', $id)->first();
        if ($monthlyCharge) return response()->json([
            'monthly_charge' => $monthlyCharge,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن ماهیانه',
            'success' => false
        ]);
    }
    public function showAll(){
        $monthlyCharges = MonthlyCharge::with(['accounts'])->get();
        return response()->json([
            'monthly_charges' => $monthlyCharges,
            'success' => true
        ]);
    }
    public function showList(){
        $monthlyCharges = MonthlyCharge::all();
        return response()->json([
            'monthly_charges' => $monthlyCharges,
            'success' => true
        ]);
    }
}
