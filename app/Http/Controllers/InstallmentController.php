<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallmentRequest;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
//    public function update(InstallmentRequest $request){
//        $request->validated();
//        $installment = Installment::where('id',$request->id)->update([
//            'loan_id' => $request->loan_id,
//            'inst_number' => $request->inst_number,
//            'amount_due' => $request->amount_due,
//            'due_date' => $request->due_date,
//        ]);
//        if ($installment) return response()->json([
//            'msg' => 'قسط با موفقیت آپدیت شد .',
//            'success' => true
//        ],201);
//        else return response()->json([
//            'msg' => 'خطایی در آپدیت قسط رخ داد!',
//            'success' => false
//        ],500);
//    }
//    public function updateGroup(InstallmentRequest $request){
//        $request->validated();
//
//        $installmentsData = $request->input('installments');
//        $updatedInstallments = [];
//        DB::beginTransaction();
//        try {
//            foreach ($installmentsData as $data) {
//                $installment = Installment::where('id', $data['id'])->update([
//                    'loan_id' => $data['loan_id'],
//                    'inst_number' => $data['inst_number'],
//                    'amount_due' => $data['amount_due'],
//                    'due_date' => $data['due_date'],
//                ]);
//                $updatedInstallments[] = $installment;
//            }
//
//            if (count($updatedInstallments) === count($installmentsData)) {
//                DB::commit();
//                return response()->json([
//                    'msg' => 'تمامی اقساط با موفقیت آپدیت شدند.',
//                    'success' => true,
//                    'data' => $updatedInstallments,
//                ], 201);
//            } else {
//                return response()->json([
//                    'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
//                    'success' => false,
//                ], 500);
//            }
//        }catch (\Throwable $e) {
//            DB::rollback();
//            return response()->json([
//                'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
//                'success' => false,
//            ], 500);
//        }
//    }
//
//    public function showOne($id){
//        $installment = Installment::with(['loan'])->where('id', $id)->first();
//        if ($installment) return response()->json([
//            'installment' => $installment,
//            'success' => true
//        ]);
//        else return response()->json([
//            'msg' => 'خطا در پیدا کردن  قسط',
//            'success' => false
//        ]);
//    }
    public function showAll(){
        $installments = Installment::where('delay_days','>',0)->get();
        return response()->json([
            'installments' => $installments,
            'success' => true
        ]);
    }
    public function search(Request $request){
        $id = $request->query('account_id');
        $loan_account_id = $request->query('loan_account_id');
        $account_name = $request->query('account_name');
        $type = $request->query('type');
        $due_date = $request->query('due_date');
        $title = $request->query('title');
        $is_paid = $request->query('is_paid');

        $query = Installment::query();

        if ($id !== null){
            $query->where('account_id', $id);
        }
        if ($loan_account_id !== null){
            $query->where('loan_account_id', $loan_account_id);
        }
        if ($account_name !== null){
            $query->orWhere('account_name', 'LIKE', "%{$account_name}%");
        }
        if ($type !== null){
            $query->orWhere('type', (int)$type);
        }
        if ($due_date !== null){
            $gregorian_due_date = Verta::parse($due_date)->DateTime()->format('Y-m-d');
            $query->orWhere('due_date', $gregorian_due_date);
        }
        if ($title !== null){
            $query->orWhere('title','LIKE', "%{$title}%");
        }
        if ($is_paid !== null){
            $is_paid === 'true' ? $query->orWhere('paid_date','!=',null): $query->orWhere('paid_date',null);
        }

        $installments = $query->get();
        return response()->json([
            'installments' => $installments,
            'success' => true
        ]);
    }

    public function numberOfUnpaidInstallmentsOfAccount($account_id){
        $count = Installment::where('account_id',$account_id)->where('paid_date',null)->count();
        return response()->json([
            'counts' => $count,
            'success' => true
        ]);
    }
    public static function numberOfUnpaidInstallmentsOfAccountt($account_id){
       return Installment::where('account_id',$account_id)->where('paid_date',null)->count();
    }
}
