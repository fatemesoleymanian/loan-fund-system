<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoanAndPartitionRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Installment;
use App\Models\Loan;
use App\Models\LoanAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanAccountController extends Controller
{
    public function createLoanAndPartition(LoanAndPartitionRequest $request){
        DB::beginTransaction();
        try {
            $loan = Loan::where('id',$request->loan_id)->first();

            $loan_account = $this->createLoan($request);

            $account = $this->updateAccount($request);

            $fund_account = $this->updateFundAccount($request);

            $loan_transaction = $this->logging($request,$loan_account->id,$loan,true);
            $fee_transaction = $this->logging($request,$loan_account->id,$loan,false);

            if ($loan->no_need_to_pay && $request->number_of_installments === 0){

            }else{
                $installments = $this->doPartition($request,$loan,$loan_account->id);
            }
            DB::commit();
            $installments = Installment::where('loan_account_id',$loan_account->id)->get();
            return response()->json([
                'installments' => $installments,
                'success' => true
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
        $account = Account::where('id',$request->account_id)->first();
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
        for ($i = 0; $i < $request->number_of_installments; $i++) {
            Installment::create([
                'loan_id' => $loan->id,
                'loan_account_id' => $loan_account_id,
                'account_id' => $request->account_id,
                'account_name' => $request->account_name,
                'inst_number' => $i+1,
                'amount' => $amount,
                'due_date' => $i, //
                'delay_days' => 0,
                'type' => 2,
                'title' => $loan->title,
            ]);
        }
    }
    function generateDates($startDate, $intervalDays, $count) {
    $start = Verta::parse($startDate); // Parse the start date
    $dates = [];
    
    for ($i = 0; $i < $count; $i++) {
        $nextDate = $start->addDays($i * $intervalDays);
        $dates[] = $nextDate->format('Y/n/j'); // Format the date as "Y/n/j" (1403/10/06, 1403/10/11, etc.)
    }

    return $dates;
}

    public function showLoansOfAccount($account_id){
        $loan_accs = LoanAccount::with(['title'])->where('account_id', $account_id)->get();
         return response()->json([
            'loans' => $loan_accs,
            'success' => true
        ]);
    }
    //DONE
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

}
