<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanAccountDetailRequest;
use App\Models\FundAccount;
use App\Models\LoanAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanAccountDetailController extends Controller
{
    public function create(LoanAccountDetailRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();

             LoanAccount::where('loan_id',$request->loan_id)->delete();

            foreach ($request->account_ids as $acc) {
                LoanAccount::create([
                    'loan_id' => $request->loan_id,
                    'account_id' => $acc['value'],
                    'remained_amount' => $request->remained_amount,
                    'paid_amount' => $request->paid_amount,
                    'paid_by_fund' => false,
                ]);
            }
            DB::commit();
            return TransactionController::successResponse('اطلاعات با موفقیت اضافه شد. ',201);

        }catch (\Exception $e){
            DB::rollBack();
            TransactionController::errorResponse($e->getMessage());
        }
    }
    public function update(LoanAccountDetailRequest $request){
        $request->validated();

        $loanAccDetails = LoanAccount::where('id', $request->id)->update([
            'loan_id' => $request->loan_id,
            'account_id' => $request->account_id,
            'remained_amount' => $request->remained_amount,
            'paid_amount' => $request->paid_amount,
            'paid_by_fund' => false,

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
        DB::beginTransaction();
        try {
            $loan = LoanAccount::where('id', $request->id)->first();
            if($loan->paid_amount === 0){
                $fund_account = FundAccount::where('id',$loan->fund_account_id)->first();
                $fund_account->balance += $loan->amount;
                $fund_account->total_balance += $loan->amount;
                $fund_account->fees -= $loan->fee_amount;
                $transaction = Transaction::where('loan_id',$loan->loan_id)->delete();
                $loan->delete();
                DB::commit();
                return response()->json([
                    'msg' => 'وام با موفقیت حذف گردید.',
                    'success' => true
                ]);
            }else{
                return response()->json([
                    'msg' => 'امکان حذف وام وجود ندارد. وام دارای اقساط پرداخت شده است!',
                    'success' => false
                ],400);
            }
        }catch (\Exception $e){
            DB::rollBack();
        }
    }
    public function showAll(){
        $loan_accs = LoanAccount::with(['account','loan'])->get();
        return response()->json([
            'data' => $loan_accs,
            'success' => true
        ]);
    }
    public function showOne($acc_id,$loan_id){
        $loan_acc = LoanAccount::with(['account','loan'])->where('account_id', $acc_id)
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
        $loan_acc = LoanAccount::with(['account','loan'])->where('account_id', $acc_id)->first();
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
        $loan_acc = LoanAccount::with(['account','loan'])->where('loan_id', $loan_id)->first();
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
