<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanRequest;
use App\Models\Loan;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function create(LoanRequest $request){
        $request->validated();
        $loan = Loan::create([
            'principal_amount' => $request->principal_amount,
            'type' => $request->type,
            'number_of_installments' => $request->number_of_installments,
            'status' => $request->status,
            'year' => $request->year,



            'due_date' => $request->due_date,
            'issue_date' => $request->issue_date,
            'end_date' => $request->end_date,
        ]);
        if ($loan) return response()->json([
            'msg' => 'وام با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد وام رخ داد!',
            'success' => false
        ],500);
    }
    public function update(LoanRequest $request){
        $request->validated();
        $loan = Loan::where('id',$request->id)->update([
            'principal_amount' => $request->principal_amount,
            'type' => $request->type,
            'number_of_installments' => $request->number_of_installments,
            'status' => $request->status,
            'year' => $request->year,
            'due_date' => $request->due_date,
            'issue_date' => $request->issue_date,
            'end_date' => $request->end_date,
        ]);
        if ($loan) return response()->json([
            'msg' => 'وام با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت وام رخ داد!',
            'success' => false
        ],500);
    }
    public function destroy(Request $request){
        $loan = Loan::where('id', $request->id)->delete();
        return response()->json([
            'msg' => ' وام با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showOne($id){
        $loan = Loan::with(['installments'])->where('id', $id)->first();
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
}
