<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallmentRequest;
use App\Models\Installment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
    public function create(InstallmentRequest $request){
        $request->validated();
        $installment = Installment::create([
            'loan_id' => $request->loan_id,
            'inst_number' => $request->inst_number,
            'amount_due' => $request->amount_due,
            'due_date' => $request->due_date,
        ]);
        if ($installment) return response()->json([
            'msg' => 'قسط با موفقیت اضافه شد. ',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد قسط رخ داد!',
            'success' => false
        ],500);
    }
    public function createGroup(InstallmentRequest $request)
{
    $request->validated();

    $installmentsData = $request->input('installments');
    $createdInstallments = [];
    DB::beginTransaction();
    try {
        foreach ($installmentsData as $data) {
            $installment = Installment::create([
                'loan_id' => $data['loan_id'],
                'inst_number' => $data['inst_number'],
                'amount_due' => $data['amount_due'],
                'due_date' => $data['due_date'],
            ]);
            $createdInstallments[] = $installment;
        }

        if (count($createdInstallments) === count($installmentsData)) {
            DB::commit();
            return response()->json([
                'msg' => 'تمامی اقساط با موفقیت اضافه شدند.',
                'success' => true,
                'data' => $createdInstallments,
            ], 201);
        } else {
            return response()->json([
                'msg' => 'خطایی در ایجاد برخی از اقساط رخ داد!',
                'success' => false,
            ], 500);
        }
    }catch (\Throwable $e) {
        DB::rollback();
        return response()->json([
            'msg' => 'خطایی در ایجاد برخی از اقساط رخ داد!',
            'success' => false,
        ], 500);
    }
}

    public function update(InstallmentRequest $request){
        $request->validated();
        $installment = Installment::where('id',$request->id)->update([
            'loan_id' => $request->loan_id,
            'inst_number' => $request->inst_number,
            'amount_due' => $request->amount_due,
            'due_date' => $request->due_date,
        ]);
        if ($installment) return response()->json([
            'msg' => 'قسط با موفقیت آپدیت شد .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت قسط رخ داد!',
            'success' => false
        ],500);
    }
    public function updateGroup(InstallmentRequest $request){
        $request->validated();

        $installmentsData = $request->input('installments');
        $updatedInstallments = [];
        DB::beginTransaction();
        try {
            foreach ($installmentsData as $data) {
                $installment = Installment::where('id', $data['id'])->update([
                    'loan_id' => $data['loan_id'],
                    'inst_number' => $data['inst_number'],
                    'amount_due' => $data['amount_due'],
                    'due_date' => $data['due_date'],
                ]);
                $updatedInstallments[] = $installment;
            }

            if (count($updatedInstallments) === count($installmentsData)) {
                DB::commit();
                return response()->json([
                    'msg' => 'تمامی اقساط با موفقیت آپدیت شدند.',
                    'success' => true,
                    'data' => $updatedInstallments,
                ], 201);
            } else {
                return response()->json([
                    'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
                    'success' => false,
                ], 500);
            }
        }catch (\Throwable $e) {
            DB::rollback();
            return response()->json([
                'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
                'success' => false,
            ], 500);
        }
    }

    public function destroy(Request $request){
        $installment = Installment::where('id', $request->id)->delete();
        return response()->json([
            'msg' => ' قسط با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function destroyGroup(Request $request){

        $installmentsData = $request->input('ids');
        $removedInstallments = [];
        DB::beginTransaction();
        try {
        foreach ($installmentsData as $data) {
            $installment = Installment::where('id',$data['id'])->delete();
            $removedInstallments[] = $installment;
        }

        if (count($removedInstallments) === count($installmentsData)) {
            DB::commit();
            return response()->json([
                'msg' => 'تمامی اقساط با موفقیت حذف شدند.',
                'success' => true,
                'data' => $removedInstallments,
            ], 201);
        } else {
            return response()->json([
                'msg' => 'خطایی در حذف برخی از اقساط رخ داد!',
                'success' => false,
            ], 500);
        }
        }catch (\Throwable $e) {

            DB::rollback();
            return response()->json([
                'msg' => 'خطایی در حذف برخی از اقساط رخ داد!',
                'success' => false,
            ], 500);
        }
    }

    public function showOne($id){
        $installment = Installment::with(['loan'])->where('id', $id)->first();
        if ($installment) return response()->json([
            'installment' => $installment,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن  قسط',
            'success' => false
        ]);
    }
    public function showAll(){
        $installments = Installment::with(['loan'])->get();
        return response()->json([
            'installment' => $installments,
            'success' => true
        ]);
    }
}