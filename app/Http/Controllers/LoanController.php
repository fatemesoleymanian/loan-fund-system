<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use App\Models\Installment;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function create(LoanRequest $request){
        $request->validated();
        $loan = Loan::create([
            'title'=>$request->title,
            'static_fee'=>$request->static_fee,
            'fee_percent'=>$request->fee_percent,
            'number_of_installments'=>$request->number_of_installments,
            'installment_interval'=>$request->installment_interval,
            'max_amount'=>$request->max_amount,
            'min_amount'=>$request->min_amount,
            'emergency'=>$request->emergency,
            'no_need_to_pay'=>$request->no_need_to_pay,
        ]);
        if ($loan) return TransactionController::successResponse('وام با موفقیت ساخته شد.', 201);

    }
    private function createLoan($request){
        return Loan::create([
            'principal_amount' => $request['principal_amount'],
            'type' => $request['type'],
            'number_of_installments' => $request['number_of_installments'],
            'status' => $request['status'],
            'year' => $request['year'],
            'due_date' => $request['due_date'],
            'issue_date' => $request['issue_date'],
            'end_date' => $request['end_date'],
        ]);
    }
    private function createInstallments($loan_id,$request){
        foreach ($request as $installment){
            Installment::create([
                'loan_id' => $loan_id,
                'inst_number' => $installment['inst_number'],
                'amount_due' => $installment['amount_due'],
                'due_date' => $installment['due_date'],
            ]);
        }
    }
    public function update(LoanRequest $request){
        DB::beginTransaction();
        try {
            $validated = $request->validated();
            $this->updateLoan($validated,$request->id);
            $this->updateInstallments($request->id,$validated['installments']);

            DB::commit();
            return TransactionController::successResponse('وام با موفقیت آپدیت شد.', 201);
        }catch  (\Exception $e) {
            DB::rollBack();
            return  TransactionController::errorResponse('خطایی در آپدیت وام و اقساط رخ داد! ' . $e->getMessage());
        }
    }
    private function updateLoan($request,$id){
        return Loan::where('id',$id)->update([
            'principal_amount' => $request['principal_amount'],
            'type' => $request['type'],
            'number_of_installments' => $request['number_of_installments'],
            'status' => $request['status'],
            'year' => $request['year'],
            'due_date' => $request['due_date'],
            'issue_date' => $request['issue_date'],
            'end_date' => $request['end_date'],
        ]);
    }
    private function updateInstallments($loan_id,$request){
        Installment::where('loan_id',$loan_id)->delete();
        foreach ($request as $installment){
            Installment::create([
                'loan_id' => $loan_id,
                'inst_number' => $installment['inst_number'],
                'amount_due' => $installment['amount_due'],
                'due_date' => $installment['due_date'],
            ]);
        }
    }

    public function destroy(Request $request){
        $loan = Loan::where('id', $request->id)->delete();
        return response()->json([
            'msg' => ' وام با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showOne($id){
        $loan = Loan::with(['accounts'])->where('id', $id)->first();
        if ($loan) return response()->json([
            'loan' => $loan,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن  وام',
            'success' => false
        ]);
    }
    public function showAll(){
        $loans = Loan::with(['installments'])->get();
        return response()->json([
            'loans' => $loans,
            'success' => true
        ]);
    }
    public function showOneWithInst($loan_id){
        $installments = Installment::where('loan_id',$loan_id)->get();
        return response()->json([
            'installments' => $installments,
            'success' => true
        ]);
    }
}
