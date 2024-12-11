<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClosureRequest;
use App\Http\Requests\DepositRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Withdraw;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    public function create(DepositRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->updateFundAccBalance($request);
            $this->updateAccountBalance($request);
            $fund_account = FundAccount::current();
            $withdraw = Withdraw::create([
                'amount'=>$request->amount,
                'account_id'=>$request->account_id,
                'fund_account_id'=>$fund_account->id,
                'description'=>$request->description
            ]);
            DB::commit();
            return response()->json([
                'withdraw'=>$withdraw,
                'msg' => ' با موفقیت برداشت شد.',
                'success' => true
            ], 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در برداشت رخ داد! ' . $e->getMessage());
        }
    }
    /**TODO */
    public function closure(DepositRequest $request){
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
            $account = Account::where('id',$request->account_id)->update([
                'is_open' =>false
            ]);
            DB::commit();
            return response()->json([
                'account'=>$account,
                'msg' => ' با موفقیت برداشت شد.',
                'success' => true
            ], 201);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در برداشت رخ داد! ' . $e->getMessage());
        }
    }
    private function updateFundAccBalance($request){
        $fund_account = FundAccount::current();
        $fund_account->balance -= $request->amount;
        $fund_account->total_balance -= $request->amount;
        $fund_account->save();
    }
    private function updateAccountBalance($request){
        $account = Account::where('id', $request->account_id)->first();
        $account->balance -= $request->amount;
//        if ($request->close) $account->is_open = false;
        $account->save();
    }
    public function createLog($request){
        $withdraw = Withdraw::create([
            'amount'=>$request['amount'],
            'account_id'=>$request['account_id'] ?? null,
            'fund_account_id'=>$request['fund_account_id'],
            'description'=>$request['description']
        ]);
        return response()->json([
            'msg' => ' با موفقیت برداشت شد.',
            'success' => true
        ], 201);
    }
    public function showAll(){
        $withdraws = Withdraw::all();
        $amounts = Withdraw::sum('amount');
        return response()->json([
            'amounts'=>$amounts,
            'withdraws' => $withdraws,
            'success' => true
        ]);
    }
    public function showLatestWithdrawsForAccount($id){
        $withdraws = Withdraw::where('account_id',$id)->latest()->take(10)->get();
        return response()->json([
            'withdraws' => $withdraws,
            'success' => true
        ]);
    }
}
