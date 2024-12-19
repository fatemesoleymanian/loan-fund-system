<?php

namespace App\Http\Controllers;

use App\Http\Requests\MonthlyChargeRequest;
use App\Models\Account;
use App\Models\Installment;
use App\Models\MonthlyCharge;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        DB::beginTransaction();
        try {
            $monthlyCharge = MonthlyCharge::where('id', $request->id)->first();
            Installment::where('monthly_charge_id',$monthlyCharge->id)->where('paid_date',null)->delete();
            $monthlyCharge->delete();
            DB::commit();
            return response()->json([
                'msg' => 'ماهیانه با موفقیت حذف گردید.',
                'success' => true
            ]);
        }catch  (\Exception $e) {
            DB::rollBack();
            return  TransactionController::errorResponse('خطایی رخ داد! ' . $e->getMessage());
        }

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
        $monthlyCharges = MonthlyCharge::all();
        return response()->json([
            'monthly_charges' => $monthlyCharges,
            'success' => true
        ]);
    }
    public function checkBeforeApply(Request $request){
        $names = '';
        foreach($request->accounts as $account){
            $acc = Installment::where('account_id' , $account)->where('type',1)->where('year',$request->year)->first();
            if ($acc) $names .= $acc->account_name.', ';
        }
       return TransactionController::successResponse($names !== '' ? $names.' دارای ماهیانه در سال انتخابی هستند. آیا از تنظیم ماهیانه برای این حساب ها مطمئن هستید؟ با زدن دکمه ثبت ماهیانه پیشین لغو میگردد.!':' آیا از تنظیم ماهیانه برای این حساب ها مطمئن هستید؟ ',200);
    }
    public function applyChargeForAccounts(Request $request){
        $accounts = [];
        foreach ($request->accounts as $account){
            $acc = Account::where('id',$account)->first();
            array_push($accounts,[
                'account_id' => $acc->id ,
                'account_name'=>$acc->member_name ,
                'stock_units' => $acc->stock_units
        ]);
        }
        $charge = MonthlyCharge::where('id',$request->monthly_charge_id)->first();
        //amount // title  // year // from // to

    }
    function generatePeriods($startDate, $endDate, $intervalDays = 29)
{
    $start = Verta::parse($startDate);
    $end = Verta::parse($endDate);
    
    $periods = [];
    $currentDate = $start;

    while ($currentDate->lte($end)) {
        $periods[] = $currentDate->format('Y/m/d');
        $currentDate->addDays($intervalDays);
    }

    return $periods;
}

}
