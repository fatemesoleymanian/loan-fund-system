<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function closure(Request $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->updateFundAccBalance($request);
            $this->updateAccountBalance($request);
            $withdraw = Withdraw::create([
                'amount'=>$request->amount,
                'account_id'=>$request->account_id,
                'description'=>$request->description
            ]);
            DB::commit();
            return response()->json([
                'msg' => ' با موفقیت برداشت شد.',
                'success' => true
            ], 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در برداشت رخ داد! ' . $e->getMessage());
        }
    }
    private function updateFundAccBalance($request){
        $fund_account = FundAccount::latest()->first();
        $fund_account->balance -= $request->amount;
        $fund_account->total_balance -= $request->amount;
        $fund_account->save();
    }
    private function updateAccountBalance($request){
        $account = Account::where('id', $request->account_id)->first();
        $account->balance -= $request->amount;
        if ($request->close) $account->is_open = false;
        $account->save();
    }
    public function createLog(Request $request){
        $withdraw = Withdraw::create([
            'amount'=>$request->amount,
//            'account_id'=>$request->account_id,
            'description'=>$request->description
        ]);
        DB::commit();
        return response()->json([
            'msg' => ' با موفقیت برداشت شد.',
            'success' => true
        ], 201);
    }
}
