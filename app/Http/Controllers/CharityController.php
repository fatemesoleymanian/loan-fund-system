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
                'accounts' => $request->accounts,
                'description' => $request->description,
            ]);
            $withdrawObj = [
                'amount'=>$request->amount,
                'account_id'=> null,
                'description'=>$request->description
            ];
            $withdrawController = new WithdrawController();
            $withdraw = $withdrawController->createLog($withdrawObj);
            DB::commit();
            if ($charity) return response()->json([
                'msg' => ' با موفقیت اضافه شد.',
                'success' => true
            ], 201);
            else return response()->json([
                'msg' => 'خطایی در ایجاد  رخ داد!',
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
            $fund_acc = FundAccount::where('id',$request->fund_acc_id)->first();
            $fund_acc->expenses += $request->amount;
            $fund_acc->save();
        }
    }
    private function handleBalanceSource($request){
        $this->handleExpense($request);
        $this->updateAccountBalance($request);
        $this->updateFundAccBalance($request);
    }
    private function updateFundAccBalance($request){
        $fund_acc = FundAccount::where('id',$request->fund_acc_id)->first();
        $fund_acc->balance -= $request->amount;
        $fund_acc->total_balance -= $request->amount;
        $fund_acc->save();
    }
    private function updateAccountBalance($request){
        $list_of_accounts = Account::splitAccountIds($request->accounts);
        $decrease = round((int) $request->amount / (int) $list_of_accounts['count']);
        $accounts = $list_of_accounts['formattedIds'];
        foreach ($accounts as $id){
            $account = Account::find($id);
            if($account){
                $account->balance -= $decrease;
                $account->save();
            }
        }
    }
    private function updateFundAccountFees($request){
        $fund_acc = FundAccount::where('id',$request->fund_acc_id)->first();
        $fund_acc->expenses += $request->amount;
        $fund_acc->fees -= $request->amount;
        $fund_acc->save();
    }


//    public function update(Request $request){
//        if (!$request->id) return response()->json([
//            'msg' => ' را انتخاب کنید.'
//        ],400);
//        $charity = Charity::where('id', $request->id)->update([
//            'description' => $request->description,
//        ]);
//
//        if ($charity) return response()->json([
//            'msg' => ' با موفقیت آپدیت شد. .',
//            'success' => true
//        ],201);
//        else return response()->json([
//            'msg' => 'خطایی در آپدیت  رخ داد!',
//            'success' => false
//        ],500);
//
//    }
    //BAYAD AZ EXPENSES KAM SHE BE MONEY_SOURCE EZFE SHE
//    public function destroy(Request $request){
//        $charity = Charity::where('id', $request->id)->delete();
//        return response()->json([
//            'msg' => ' با موفقیت حذف گردید.',
//            'success' => true
//        ]);
//    }
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
