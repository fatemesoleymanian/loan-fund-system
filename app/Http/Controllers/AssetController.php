<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Account;
use App\Models\Asset;
use App\Models\FundAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function create(AssetRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->handleExpense($request);
            $asset = Asset::create([
                'title' => $request->title,
                'cost' => $request->cost,
                'money_source' => $request->money_source,
//                'accounts' => $request->accounts,
                'description' => $request->description,
            ]);
//            if($request->money_source !== Asset::NONE){
//                $withdrawObj = [
//                    'amount'=>$request->cost,
//                    'account_id'=> null,
//                    'fund_account_id'=> $request->fund_account_id,
//                    'description'=>$request->description.' اثاثیه '
//                ];
//                $withdrawController = new WithdrawController();
//                $withdraw = $withdrawController->createLog($withdrawObj);
//            }
            DB::commit();
            if ($asset) return response()->json([
                'msg' => 'اثاثیه با موفقیت اضافه شد. .',
                'success' => true
            ], 201);
            else return response()->json(
                [
                'msg' => 'خطایی در ایجاد اثاثیه رخ داد!',
                'success' => false
            ], 500);

        }catch (\Exception $e) {
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در ایجاد اثاث رخ داد! ' . $e->getMessage());
        }
    }
    private function handleExpense($request){
        switch ($request->money_source){
            case Asset::NONE:
                $this->handleNone($request);
                break;
            case Asset::BALANCE_SOURCE:
                $this->handleBalanceSource($request);
                break;
            case Asset::FEE_SOURCE:
                $this->updateFundAccountFees($request);
                break;
            default:
                throw new \Exception('منبع هزینه نامعتبر است.');
        }
    }
    private function handleNone($request){
        if ($request->isExpense){
         $fund_acc = FundAccount::where('id',$request->fund_account_id)->first();
         $fund_acc->expenses += $request->cost;
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
        $fund_acc->balance -= $request->cost;
        $fund_acc->total_balance -= $request->cost;
        $fund_acc->save();
    }
    private function updateFundAccountFees($request){
        $fund_acc = FundAccount::where('id',$request->fund_account_id)->first();
        $fund_acc->expenses += $request->cost;
        $fund_acc->fees -= $request->cost;
        $fund_acc->total_balance -= $request->cost;
        $fund_acc->save();

    }
    //BAYAD AZ EXPENSES KAM SHE BE MONEY_SOURCE EZFE SHE
    public function destroy(Request $request){
        DB::beginTransaction();
        try {
            $asset = Asset::where('id', $request->id)->first();
            $fund_account = FundAccount::where('id', $request->fund_account_id)->first();
            switch ($asset->money_source){
                case Asset::NONE:
                    $fund_account->expenses -= $asset->cost;
                    break;
                case Asset::BALANCE_SOURCE:
                    $fund_account->expenses -= $asset->cost;
                    $fund_account->balance += $asset->cost;
                    $fund_account->total_balance += $asset->cost;
                    break;
                case Asset::FEE_SOURCE:
                    $fund_account->expenses -= $asset->cost;
                    $fund_account->total_balance += $asset->cost;
                    $fund_account->fees += $asset->cost;
                    break;
                default:
                    throw new \Exception('منبع هزینه نامعتبر است.');
            }
            $fund_account->save();
            $asset->delete();
            DB::commit();
            return response()->json([
                'msg' => 'اثاثیه با موفقیت حذف گردید.',
                'success' => true
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در ایجاد اثاث رخ داد! ' . $e->getMessage());
        }
    }
    public function showAll(){
        $assets = Asset::all();
        $costs = Asset::sum('cost');
        return response()->json([
            'assets' => $assets,
            'costs'=>$costs,
            'success' => true
        ]);
    }
    public function showOne($id){
        $asset = Asset::where('id', $id)->first();
        if ($asset) return response()->json([
            'asset' => $asset,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن اثاثیه',
            'success' => false
        ],500);
    }
}
