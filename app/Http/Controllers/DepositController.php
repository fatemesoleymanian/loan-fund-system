<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\FundAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
{

    public function create(DepositRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->updateFundAccBalance($request);
           $this->updateAccountBalance($request);
            $fund_account = FundAccount::current();

            $deposit = Deposit::create([
               'amount'=>$request->amount,
               'account_id'=>$request->account_id,
               'fund_account_id'=>$fund_account->id,
               'description'=>$request->description
           ]);
            DB::commit();
            return response()->json([
                'deposit'=>$deposit,
                'msg' => ' با موفقیت واریز شد.',
                'success' => true
            ], 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در واریز رخ داد! ' . $e->getMessage());
        }
    }
    private function updateFundAccBalance($request){
        $fund_account = FundAccount::current();
        $fund_account->balance += $request->amount;
        $fund_account->total_balance += $request->amount;
        $fund_account->save();
    }
    private function updateAccountBalance($request){
        $account = Account::where('id', $request->account_id)->first();
        $account->balance += $request->amount;
        $account->save();
    }
    public function createLog($request){
        DB::beginTransaction();
        try {
            $deposit = Deposit::create([
                'amount'=>$request['amount'],
                'account_id'=>$request['account_id'],
                'fund_account_id'=>$request['fund_account_id'],
                'description'=>$request['description']
            ]);
            DB::commit();
            return [
                'deposit'=>$deposit,
                'msg' => ' با موفقیت واریز شد.',
                'success' => true
            ];

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در واریز رخ داد! ' . $e->getMessage());
        }
    }
    public function showAll(){
        $deposits = Deposit::all();
        $amounts = Deposit::sum('amount');
        return response()->json([
            'amounts'=>$amounts,
            'deposits' => $deposits,
            'success' => true
        ]);
    }

}
