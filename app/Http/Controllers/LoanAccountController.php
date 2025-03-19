<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanAndPartitionRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanAccount;
use App\Models\Transaction;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanAccountController extends Controller
{
    protected $smsController;

    public function __construct(){
        $this->smsController = new SMSController();
    }
    public function createLoanAndPartition(LoanAndPartitionRequest $request){
        DB::beginTransaction();
        try {
            $loan = Loan::where('id',$request->loan_id)->first();

            $loan_account = $this->createLoan($request);

            $account = $this->updateAccount($request);

            $fund_account = $this->updateFundAccount($request);

            $loan_transaction = $this->logging($request,$loan_account->id,$loan,true);
            $fee_transaction = $this->logging($request,$loan_account->id,$loan,false);

            if (!$loan->no_need_to_pay && $request->number_of_installments != 0){
                $this->doPartition($request,$loan,$loan_account->id);
            }
            DB::commit();
            $sms1 = $this->sendSms($request->amount,$request->account_id,$account->balance,$account->member->mobile_number,'loan');
            $sms2 = $this->sendSms($request->fee_amount,$request->account_id,$account->balance,$account->member->mobile_number,'fee');

            $installments = Installment::where('loan_account_id',$loan_account->id)->get();
            return response()->json([
                'installments' => $installments,
                'msg' => 'با موفقیت اعطا شد!',
                'success' => true,
                'sms' => $sms1,
                'sms2' => $sms2
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return TransactionController::errorResponse('خطایی رخ داد! ' . $e->getMessage());
        }

    }
    private function createLoan($request){
        return LoanAccount::create([
            'loan_id' => $request->loan_id,
            'account_id' => $request->account_id,
            'fund_account_id' => $request->fund_account_id,
            'amount' => $request->amount,
            'paid_amount' => 0,
            'granted_at' => $request->granted_at,
            'payback_at' => $request->payback_at,
            'number_of_installments' => $request->number_of_installments,
            'fee_amount' => $request->fee_amount,
            'description' => $request->description,
            'account_name' => $request->account_name,
            'no_of_paid_inst' => 0,
        ]);
    }
    private function updateAccount($request){
        $account = Account::with('member')->where('id',$request->account_id)->first();
        $account->balance -= $request->fee_amount;
        $account->status = Account::STATUS_DEBTOR;
        $account->save();
        return $account;
    }
    private function updateFundAccount($request){
        $fund_account = FundAccount::where('id',$request->fund_account_id)->first();
        $fund_account->balance -= $request->amount;
        $fund_account->fees += $request->fee_amount;
        $fund_account->total_balance += $request->fee_amount;
        $fund_account->total_balance -= $request->amount;
        $fund_account->save();
        return $fund_account;
    }
    private function logging($request,$loan_account_id,$loan,$is_loan){
        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'loan_account_id' => $loan_account_id,
            'amount' => $is_loan ? $request->amount : $request->fee_amount,
            'type' => $is_loan ? Transaction::TYPE_LOAN_PAYMENT : Transaction::TYPE_FEE,
            'description' => $is_loan ? ' پرداخت وام '.$loan->title : 'پرداخت کارمزد',
            'fund_account_id' => $request->fund_account_id,
            'account_name' => $request->account_name,
            'fund_account_name' => 'صندوق',
        ]);
        return $transaction;
    }
    private function doPartition($request,$loan,$loan_account_id){
        $amount = ceil(((int)$request->amount / (int)$request->number_of_installments) / 1000) * 1000;
        $dates = $this->generateDates($request->payback_at,$loan->installment_interval,$request->number_of_installments);
        forEach ($dates as $date) {
            Installment::create([
                'loan_id' => $loan->id,
                'loan_account_id' => $loan_account_id,
                'account_id' => $request->account_id,
                'account_name' => $request->account_name,
                'inst_number' => $date['index'],
                'amount' => $amount,
                'due_date' => $date['date'],
                'paid_date' => null,
                'delay_days' => 0,
                'type' => 2,
                'title' => $loan->title,
            ]);
        }
    }
    private function generateDates($startDate, $intervalDays, $count) {
    $start = Verta::parse($startDate); // Parse the start date
    $dates = [];

    for ($i = 0; $i < $count; $i++) {
        $start = Verta::parse($startDate);
        $nextDate = $start->addDays($i * $intervalDays);
        $dates[] = [
            'index' => $i+1,
            'date' => $nextDate->format('Y/m/d') // Format the date as "Y/n/j" (1403/10/06, 1403/10/11, etc.)
        ];
    }

    return $dates;
}
    public function search(Request $request){
        $account_id = $request->query('account_id');
        $account_name = $request->query('account_name');
        $loan_account_id = $request->query('loan_account_id');
        $amount = $request->query('amount');
        $title = $request->query('title');
        $is_not_paid = $request->query('is_not_paid');

        $query = LoanAccount::query();

        if ($account_id !== null){
            $query->where('account_id', $account_id);
        }
        if ($account_name !== null){
            $query->orWhere('account_name', 'LIKE', "%{$account_name}%");
        }
        if ($loan_account_id !== null){
            $query->where('id', $loan_account_id);
        }
        if ($amount !== null){
            $query->orWhere('amount', $amount);
        }
        if ($title !== null){
            $query->orWhere('description','LIKE', "%{$title}%");
        }
        if ($is_not_paid !== null){
            if ($is_not_paid === 'true'){
                $query->where('paid_amount','<','amount');
            }else{
                $query->where('paid_amount','>=','amount');
            }
        }

        $loan_accs = $query->get();
        $paid_amounts = $query->sum('paid_amount');
        $amounts = $query->sum('amount');
        return response()->json([
            'amounts' => [$amounts , $amounts - $paid_amounts],
            'loans' => $loan_accs,
            'success' => true
        ]);
    }
    public function destroy(Request $request){
        DB::beginTransaction();
        try {
            $loan_account = LoanAccount::where('id',$request->id)->first();

            $account = Account::where('id',$loan_account->account_id)->first();

            $unpaid_inst = InstallmentController::numberOfUnpaidInstallmentsOfAccountt($account->id);

            $fund_account = FundAccount::where('id',$loan_account->fund_account_id)->first();

            $account->balance += $loan_account->fee_amount;

            $fund_account->fees -= $loan_account->fee_amount;
            $fund_account->balance += $loan_account->amount;
            $fund_account->total_balance += $loan_account->amount;
            $fund_account->total_balance -= $loan_account->fee_amount;


            $loan_account->delete();
            $account->status = $unpaid_inst >0 ?  Account::STATUS_DEBTOR : Account::STATUS_CREDITOR ;
            $fund_account->save();
            $account->save();

            DB::commit();
            return response()->json([
                'msg' => 'وام با موفقیت حذف گردید.',
                'success' => true
            ]);
        }catch (\Exception $e){
            DB::rollBack();
            return TransactionController::errorResponse('خطایی رخ داد! ' . $e->getMessage());
        }

    }
    public function showLoansOfAccount($account_id){
        $loan_accs = LoanAccount::with(['title'])->where('account_id', $account_id)->get();
         return response()->json([
            'loans' => $loan_accs,
            'success' => true
        ]);
    }
    public function showAll(){
        $loan_accs = LoanAccount::get();
        $paid_amounts = LoanAccount::sum('paid_amount');
        $amounts = LoanAccount::sum('amount');
        return response()->json([
            'amounts' => [$amounts , $amounts - $paid_amounts],
            'loans' => $loan_accs,
            'success' => true
        ]);
    }
    private function sendSms($amount , $account_id, $balance, $mobile_number,$template){
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
