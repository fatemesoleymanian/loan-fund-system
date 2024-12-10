<?php

namespace App\Http\Controllers;

use App\Http\Requests\CharityRequest;
use App\Models\Account;
use App\Models\Charity;
use App\Models\FundAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CharityController extends Controller
{
    public function create(CharityRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->handleExpense($request);
            $charity = Charity::create([
                'amount' => $request->amount,
                'money_source' => $request->money_source,
//                'accounts' => $request->accounts,
                'description' => $request->description,
            ]);
//            $withdrawObj = [
//                'amount'=>$request->amount,
//                'account_id'=> null,
//                'description'=>$request->description
//            ];
//            $withdrawController = new WithdrawController();
//            $withdraw = $withdrawController->createLog($withdrawObj);
            DB::commit();
            if ($charity) return response()->json([
                'msg' => ' با موفقیت اضافه شد.',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در ایجادد  رخ داد!',
                'success' => false
            ], 500);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در ایجاد رخ داد! ' . $e->getMessage());
        }
    }
    private function handleExpense($request){
        switch ($request->money_source){
            case Charity::NONE:
                $this->handleNone($request);
                break;
            case Charity::BALANCE_SOURCE:
                $this->handleBalanceSource($request);
                break;
            case Charity::FEE_SOURCE:
                $this->updateFundAccountFees($request);
                break;
            default:
                throw new \Exception('منبع هزینه نامعتبر است.');
        }
    }
    private function handleNone($request){
        if ($request->isExpense){
            $fund_acc = FundAccount::where('id',$request->fund_account_id)->first();
            $fund_acc->expenses += $request->amount;
            $fund_acc->save();
        }
    }
    private function handleBalanceSource($request){
        $this->handleNone($request);
//        $this->updateAccountBalance($request);
        $this->updateFundAccBalance($request);
    }
    private function updateFundAccBalance($request){
        $fund_acc = FundAccount::where('id',$request->fund_account_id)->first();
        $fund_acc->balance -= $request->amount;
        $fund_acc->total_balance -= $request->amount;
        $fund_acc->save();
    }

    private function updateFundAccountFees($request){
        $fund_acc = FundAccount::where('id',$request->fund_account_id)->first();
        $fund_acc->expenses += $request->amount;
        $fund_acc->fees -= $request->amount;
        $fund_acc->total_balance -= $request->amount;
        $fund_acc->save();
    }

    //BAYAD AZ EXPENSES KAM SHE BE MONEY_SOURCE EZFE SHE
    public function destroy(Request $request){
        DB::beginTransaction();
        try {
            $charity = Charity::where('id', $request->id)->first();
            $fund_account = FundAccount::where('id', $request->fund_account_id)->first();
            switch ($charity->money_source){
                case Charity::NONE:
                    $fund_account->expenses -= $charity->amount;
                    break;
                case Charity::BALANCE_SOURCE:
                    $fund_account->expenses -= $charity->amount;
                    $fund_account->balance += $charity->amount;
                    $fund_account->total_balance += $charity->amount;
                    break;
                case Charity::FEE_SOURCE:
                    $fund_account->expenses -= $charity->amount;
                    $fund_account->total_balance += $charity->amount;
                    $fund_account->fees += $charity->amount;
                    break;
                default:
                    throw new \Exception('منبع هزینه نامعتبر است.');
            }
            $fund_account->save();
            $charity->delete();
            DB::commit();
            return response()->json([
                'msg' => ' با موفقیت حذف گردید.',
                'success' => true
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در ایجاد  رخ داد! ' . $e->getMessage());
        }
    }
    public function showAll(){
        $charities = Charity::all();
        $amounts = Charity::sum('amount');
        return response()->json([
            'charities' => $charities,
            'amounts'=>$amounts,
            'success' => true
        ]);
    }
    public function showOne($id){
        $charity = Charity::where('id', $id)->first();
        if ($charity) return response()->json([
            'charity' => $charity,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن ',
            'success' => false
        ],500);
    }
}
