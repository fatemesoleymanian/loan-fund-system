<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssetRequest;
use App\Models\Account;
use App\Models\Asset;
use App\Models\FundAccount;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function create(AssetRequest $request){
        $request->validated();
        $this->handleExpense($request);
        $asset = Asset::create([
            'title' => $request->title,
            'cost' => $request->cost,
            'description' => $request->description,
        ]);
        if ($asset) return response()->json([
            'msg' => 'اثاثیه با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد اثاثیه رخ داد!',
            'success' => false
        ],500);
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
            default:
                throw new \Exception('منبع هزینه نامعتبر است.');
        }
    }
    private function handleNone($request){
        if ($request->isExpense){
         $fund_acc = FundAccount::where('id',$request->fund_acc_id)->first();
         $fund_acc->expenses += $request->cost;
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
        $fund_acc->balance -= $request->cost;
        $fund_acc->total_balance -= $request->cost;
        $fund_acc->save();
    }
    private function updateAccountBalance($request){
        $number_of_accounts = Account::openAccounts()->count();
        $decrease = round((int) $request->cost / (int) $number_of_accounts);
        $accounts = Account::openAccounts()->get();
        foreach ($accounts as $account){
            Account::where('id',$account->id)->update([
                'balance' => ($account->balance - $decrease)
            ]);
        }
    }


    public function update(AssetRequest $request){
        $request->validated();
        if (!$request->id) return response()->json([
            'msg' => 'اثاثیه را انتخاب کنید.'
        ],400);
        $asset = Asset::where('id', $request->id)->update([
            'title' => $request->title,
            'cost' => $request->cost,
            'description' => $request->description,
        ]);

        if ($asset) return response()->json([
            'msg' => 'اثاثیه با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت اثاثیه رخ داد!',
            'success' => false
        ],500);

    }
    public function destroy(Request $request){
        $asset = Asset::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'اثاثیه با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showAll(){
        $assets = Asset::all();
        return response()->json([
            'assets' => $assets,
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
