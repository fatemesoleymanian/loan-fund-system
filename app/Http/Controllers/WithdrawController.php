<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClosureRequest;
use App\Http\Requests\DepositRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Withdraw;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    protected $smsController;

    public function __construct(){
        $this->smsController = new SMSController();
    }

    public function create(DepositRequest $request){
        DB::beginTransaction();
        try {
            $request->validated();
            $this->updateFundAccBalance($request);
            $acc = $this->updateAccountBalance($request);
            $fund_account = FundAccount::current();
            $withdraw = Withdraw::create([
                'amount'=>$request->amount,
                'account_id'=>$request->account_id,
                'fund_account_id'=>$fund_account->id,
                'description'=>$request->description
            ]);
            DB::commit();
            $sms = $this->sendSms($request->amount, $request->account_id, $acc->balance, $acc->member->mobile_number,'withdraw');

            return response()->json([
                'withdraw'=>$withdraw,
                'msg' => ' با موفقیت برداشت شد.',
                'success' => true,
                'sms' => $sms
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
            $acc = $this->updateAccountBalance($request);
            $withdraw = Withdraw::create([
                'amount'=>$request->amount,
                'account_id'=>$request->account_id,
                'description'=>$request->description
            ]);
            $account = Account::where('id',$request->account_id)->update([
                'is_open' =>false
            ]);
            DB::commit();
            $sms = $this->sendSms($request->account_id,null, null, $acc->member->mobile_number,'closure');
            return response()->json([
                'account'=>$account,
                'msg' => ' با موفقیت برداشت شد.',
                'success' => true,
                'sms' => $sms
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
        $account = Account::with('member')->where('id', $request->account_id)->first();
        $account->balance -= $request->amount;
//        if ($request->close) $account->is_open = false;
        $account->save();
        return $account;
    }
    public function createLog($request){
        $withdraw = Withdraw::create([
            'amount'=>$request['amount'],
            'account_id'=>$request['account_id'] ?? null,
            'fund_account_id'=>$request['fund_account_id'],
            'description'=>$request['description']
        ]);
        if ($request['account_id'] != null) {
            $acc = Account::with('member')->where('id', $request['account_id'])->first();
            $sms = $this->sendSms($request['amount'], $request['account_id'],$acc->balance, $acc->member->mobile_number,'withdraw');
        }
        return response()->json([
            'msg' => ' با موفقیت برداشت شد.',
            'success' => true,
            'sms' => $sms ?? null
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

    public function search(Request $request){
        $startSolarDate = $request->query('from');
        $endSolarDate = $request->query('to');

        $query = Withdraw::query();
        if ($startSolarDate !== null && $endSolarDate !== null) {
            // Convert solar dates to Gregorian
            $startDate = Verta::parse($startSolarDate)->setTime(0, 0, 0)->toCarbon();
            $endDate = Verta::parse($endSolarDate)->setTime(23, 59, 59)->toCarbon();


            // Query withdraws within the date range
            $withdraws = $query->whereBetween('created_at', [$startDate, $endDate])->get();
            $amounts = $query->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        }else{
            $withdraws = $query->get();
            $amounts = $query->sum('amount');
        }
        return response()->json([
            'amounts'=>$amounts,
            'withdraws' => $withdraws,
            'success' => true
        ]);
    }
    private function sendSms($amount , $account_id, $balance, $mobile_number,$template='withdraw'){
        if($template !== 'closure'){
            $amount = number_format((int)$amount);
            $balance = number_format((int)$balance);
        }
        return $this->smsController->
        sendTemplateSms(
            [  'type' => 1,
                'param1' => (string)$amount,
                'param2' => (string)$account_id,
                'param3' => (string)$balance,
                'receptor' => (string)$mobile_number,
                'template' => $template
            ]);

    }
}
