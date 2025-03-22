<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepositRequest;
use App\Models\Account;
use App\Models\Deposit;
use App\Models\FundAccount;
use App\Models\Member;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepositController extends Controller
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

            $deposit = Deposit::create([
               'amount'=>$request->amount,
               'account_id'=>$request->account_id,
               'fund_account_id'=>$fund_account->id,
               'description'=>$request->description
           ]);
            DB::commit();

            $sms = $this->sendSms($request->amount, $acc->member_name, $acc->balance, $acc->member->mobile_number);
            return response()->json([
                'deposit'=>$deposit,
                'msg' => ' با موفقیت واریز شد.',
                'success' => true,
                'sms' => $sms
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
        $account = Account::with('member')->where('id', $request->account_id)->first();
        $account->balance += $request->amount;
        $account->save();
        return $account;
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
            $acc = Account::with('member')->where('id',$request['account_id'])->first();
            $sms = $this->sendSms($request['amount'], $acc->member_name, $acc->balance, $acc->member->mobile_number);
            return [
                'deposit'=>$deposit,
                'msg' => ' با موفقیت واریز شد.',
                'success' => true,
                'sms' => $sms
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
    public function showLatestDepositsForAccount($id){
        $deposits = Deposit::where('account_id',$id)->latest()->take(10)->get();
        return response()->json([
            'deposits' => $deposits,
            'success' => true
        ]);
    }
    public function search(Request $request){
        $startSolarDate = $request->query('from');
        $endSolarDate = $request->query('to');

        $query = Deposit::query();
        if ($startSolarDate !== null && $endSolarDate !== null) {
            // Convert solar dates to Gregorian
            $startDate = Verta::parse($startSolarDate)->setTime(0, 0, 0)->toCarbon();
            $endDate = Verta::parse($endSolarDate)->setTime(23, 59, 59)->toCarbon();


            $deposits = $query->whereBetween('created_at', [$startDate, $endDate])->get();
            $amounts = $query->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        }else{
            $deposits = $query->get();
            $amounts = $query->sum('amount');
        }
        return response()->json([
            'amounts'=>$amounts,
            'deposits' => $deposits,
            'success' => true
        ]);
    }
    private function sendSms($amount , $account_name, $balance, $mobile_number){
        $amount = number_format((int)$amount);
        $balance = number_format((int)$balance);
        return $this->smsController->
        sendTemplateSms(
            [  'type' => 1,
                'param1' => (string)$amount,
                'param2' => (string)$account_name,
                'param3' => (string)$balance,
                'receptor' => (string)$mobile_number,
                'template' => 'deposit'
            ]);

    }
}
