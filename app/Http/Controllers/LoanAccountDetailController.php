<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanAccountDetailRequest;
use App\Models\LoanAccountDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanAccountDetailController extends Controller
{
    public function create(LoanAccountDetailRequest $request){
        DB::beginTransaction();

        try {
            $request->validated();
            LoanAccountDetail::where('loan_id',$request->loan_id)->delete();
            foreach ($request->account_ids as $acc) {
                LoanAccountDetail::create([
                    'loan_id' => $request->loan_id,
                    'account_id' => $acc['value'],
                    'remained_amount' => $request->remained_amount,
                    'paid_amount' => $request->paid_amount
                ]);
            }
            DB::commit();
            return response()->json([
                'msg' => 'اطلاعات با موفقیت اضافه شد. .',
                'success' => true
            ], 201);
        }catch (\Exception $e){
            DB::rollBack();
            TransactionController::errorResponse($e->getMessage());
        }
    }
    public function update(LoanAccountDetailRequest $request){
        $request->validated();

        $loanAccDetails = LoanAccountDetail::where('id', $request->id)->update([
            'loan_id' => $request->loan_id,
            'account_id' => $request->account_id,
            'remained_amount' => $request->remained_amount,
            'paid_amount' => $request->paid_amount
        ]);

        if ($loanAccDetails) return response()->json([
            'msg' => 'اطلاعات با موفقیت آپدیت شد .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت اطلاعات رخ داد!',
            'success' => false
        ],500);

    }
    public function destroy(Request $request){
        $loan_acc = LoanAccountDetail::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'اطلاعات با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showAll(){
        $loan_accs = LoanAccountDetail::with(['account','loan'])->get();
        return response()->json([
            'data' => $loan_accs,
            'success' => true
        ]);
    }
    public function showOne($acc_id,$loan_id){
        $loan_acc = LoanAccountDetail::with(['account','loan'])->where('account_id', $acc_id)
        ->where('loan_id',$loan_id)->first();
        if ($loan_acc) return response()->json([
            'data' => $loan_acc,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اطلاعات',
            'success' => false
        ],500);
    }
    public function showOneByAccount($acc_id){
        $loan_acc = LoanAccountDetail::with(['account','loan'])->where('account_id', $acc_id)->first();
        if ($loan_acc) return response()->json([
            'data' => $loan_acc,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اطلاعات',
            'success' => false
        ],500);
    }
    public function showOneByLoan($loan_id){
        $loan_acc = LoanAccountDetail::with(['account','loan'])->where('loan_id', $loan_id)->first();
        if ($loan_acc) return response()->json([
            'data' => $loan_acc,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اطلاعات',
            'success' => false
        ],500);
    }
}
