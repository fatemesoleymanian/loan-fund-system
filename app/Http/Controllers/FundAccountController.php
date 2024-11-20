<?php

namespace App\Http\Controllers;

use App\Http\Requests\FundAccountRequest;
use App\Models\FundAccount;
use Illuminate\Http\Request;

class FundAccountController extends Controller
{
    public function create(FundAccountRequest $request){
        $request->validated();
        $fundAccount = FundAccount::create([
            'account_number' => $request->account_number,
            'name' => $request->name,
            'balance' => $request->balance,
            'type' => $request->type
        ]);
        if ($fundAccount) return response()->json([
            'msg' => 'صندوق جدیدی با موفقیت اضافه شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در ایجاد صندوق رخ داد!',
            'success' => false
        ],500);
    }
    public function update(FundAccountRequest $request){
        $request->validated();
        if (!$request->id) return response()->json([
            'msg' => 'صندوق را انتخاب کنید.'
        ],400);
            $fundAccount = FundAccount::where('id', $request->id)->update([
                'account_number' => $request->account_number,
                'name' => $request->name,
                'balance' => $request->balance,
                'type' => $request->type
            ]);

        if ($fundAccount) return response()->json([
            'msg' => 'صندوق با موفقیت آپدیت شد. .',
            'success' => true
        ],201);
        else return response()->json([
            'msg' => 'خطایی در آپدیت صندوق رخ داد!',
            'success' => false
        ],500);

    }
    public function destroy(Request $request){
        $fund_acc = FundAccount::where('id', $request->id)->delete();
        return response()->json([
            'msg' => 'صندوق با موفقیت حذف گردید.',
            'success' => true
        ]);
    }
    public function showAll(){
        $fund_accs = FundAccount::all();
        return response()->json([
            'fund_accounts' => $fund_accs,
            'success' => true
        ]);
    }
    public function showOne($id){
        $fund_acc = FundAccount::where('id', $id)->first();
        if ($fund_acc) return response()->json([
            'member' => $fund_acc,
            'success' => true
        ]);
        else return response()->json([
            'msg' => 'خطا در پیدا کردن صندوق',
            'success' => false
        ],500);
    }
}
