<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallmentPaymentRequest;
use App\Http\Requests\InstallmentRequest;
use App\Models\Account;
use App\Models\FundAccount;
use App\Models\Installment;
use App\Models\LoanAccount;
use App\Models\Transaction;
use Hekmatinasser\Verta\Verta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InstallmentController extends Controller
{
//    public function update(InstallmentRequest $request){
//        $request->validated();
//        $installment = Installment::where('id',$request->id)->update([
//            'loan_id' => $request->loan_id,
//            'inst_number' => $request->inst_number,
//            'amount_due' => $request->amount_due,
//            'due_date' => $request->due_date,
//        ]);
//        if ($installment) return response()->json([
//            'msg' => 'قسط با موفقیت آپدیت شد .',
//            'success' => true
//        ],201);
//        else return response()->json([
//            'msg' => 'خطایی در آپدیت قسط رخ داد!',
//            'success' => false
//        ],500);
//    }
//    public function updateGroup(InstallmentRequest $request){
//        $request->validated();
//
//        $installmentsData = $request->input('installments');
//        $updatedInstallments = [];
//        DB::beginTransaction();
//        try {
//            foreach ($installmentsData as $data) {
//                $installment = Installment::where('id', $data['id'])->update([
//                    'loan_id' => $data['loan_id'],
//                    'inst_number' => $data['inst_number'],
//                    'amount_due' => $data['amount_due'],
//                    'due_date' => $data['due_date'],
//                ]);
//                $updatedInstallments[] = $installment;
//            }
//
//            if (count($updatedInstallments) === count($installmentsData)) {
//                DB::commit();
//                return response()->json([
//                    'msg' => 'تمامی اقساط با موفقیت آپدیت شدند.',
//                    'success' => true,
//                    'data' => $updatedInstallments,
//                ], 201);
//            } else {
//                return response()->json([
//                    'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
//                    'success' => false,
//                ], 500);
//            }
//        }catch (\Throwable $e) {
//            DB::rollback();
//            return response()->json([
//                'msg' => 'خطایی در آپدیت برخی از اقساط رخ داد!',
//                'success' => false,
//            ], 500);
//        }
//    }
//
//    public function showOne($id){
//        $installment = Installment::with(['loan'])->where('id', $id)->first();
//        if ($installment) return response()->json([
//            'installment' => $installment,
//            'success' => true
//        ]);
//        else return response()->json([
//            'msg' => 'خطا در پیدا کردن  قسط',
//            'success' => false
//        ]);
//    }

    protected $smsController;

    public function __construct(){
        $this->smsController = new SMSController();
    }
    public function showAll(){
        $installments = Installment::where('paid_date',null)->orderBy('due_date','asc')->get();
        return response()->json([
            'installments' => $installments,
            'success' => true
        ]);
    }
    public function search(Request $request){
        $id = $request->query('account_id');
        $loan_account_id = $request->query('loan_account_id');
        $account_name = $request->query('account_name');
        $type = $request->query('type');
        $due_date = $request->query('due_date');
        $title = $request->query('title');
        $is_paid = $request->query('is_paid');
        $query = Installment::query();

        if ($id !== null){
            $query->where('account_id', $id);
        }
        if ($loan_account_id !== null){
            $query->where('loan_account_id', $loan_account_id);
        }
        if ($account_name !== null){
            $query->orWhere('account_name', 'LIKE', "%{$account_name}%");
        }
        if ($type !== null){
            $query->where('type', (int)$type);
        }
        if ($due_date !== null){
            // Convert Solar date to Gregorian format
            $gregorian_due_date = Verta::parse($due_date)->DateTime()->format('Y-m-d');
            // Use whereDate for date-only comparison
            $query->whereDate('due_date', '=', $gregorian_due_date);
        }
        if ($title !== null){
            $query->orWhere('title','LIKE', "%{$title}%");
        }
        if ($is_paid !== null){
            $is_paid === 'true' ? $query->where('paid_date','!=',null): $query->where('paid_date',null);
        }

        $installments = $query->get();
        return response()->json([
            'installments' => $installments,
            'success' => true
        ]);
    }
    public function numberOfUnpaidInstallmentsOfAccount($account_id){
        $count = Installment::where('account_id',$account_id)->where('type',2)->where('paid_date',null)->count();
        return response()->json([
            'counts' => $count,
            'success' => true
        ]);
    }
    public static function numberOfUnpaidInstallmentsOfAccountt($account_id){
       return Installment::where('account_id',$account_id)->where('paid_date',null)->count();
    }
    public function showFees(Request $request){
        $startSolarDate = $request->query('from');
        $endSolarDate = $request->query('to');

        $query = Transaction::query()->where('type',Transaction::TYPE_FEE);
        if ($startSolarDate !== null && $endSolarDate !== null) {
            // Convert solar dates to Gregorian
            $startDate = Verta::parse($startSolarDate)->setTime(0, 0, 0)->toCarbon();
            $endDate = Verta::parse($endSolarDate)->setTime(23, 59, 59)->toCarbon();


            $transactions = $query->whereBetween('created_at', [$startDate, $endDate])->get();
            $amounts = $query->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        }else{
            $transactions = $query->get();
            $amounts = $query->sum('amount');
        }
        return response()->json([
            'amounts'=>$amounts,
            'fees' => $transactions,
            'success' => true
        ]);
    }

    public function pay(InstallmentPaymentRequest $request){
        DB::beginTransaction();
        try {
            $account = Account::with('member')->where('id',$request->account_id)->first();
            $installment = Installment::where('id',$request->id)->first();
            $fund_account = FundAccount::where('id',$request->fund_account_id)->first();

            if ((int)$request->type == 1) $this->payCharge($request,$account,$installment,$fund_account);
            else if ((int)$request->type == 2) $this->payLoanInstallment($request,$installment,$fund_account);
            else return TransactionController::errorResponse('نوع قسط صحیح نیست!',400);

            $transaction = $this->logging($request);

            DB::commit();
            $account->status = $this->checkForAccountStatus($account->id);
            $account->save();
            DB::commit();

            $sms = null;

            if ((int)$request->type == 1) $sms = $this->sendSms($request->amount,$request->account_id,$account->balance,
                $account->member->mobile_number,'charge');
            else if ((int)$request->type == 2) $sms = $this->sendSms($installment->inst_number,$request->account_id,$request->amount,
                $account->member->mobile_number,'installment');

            return response()->json([
                'msg' => 'پرداخت انجام شد!',
                'success' => true,
                'sms' => $sms
            ]);
        }catch (\Exception $exception){
            DB::rollBack();
            return TransactionController::errorResponse('خطایی در پرداخت قسط رخ داد!',$exception->getMessage());
        }

    }
    private function payCharge($request,$account,$installment,$fund_account){
        if ($request->amount === $installment->amount) {
            $fund_account->balance += $request->amount;
            $fund_account->total_balance += $request->amount;
            $fund_account->save();

            $account->balance += $request->amount;
            $account->save();

            $installment->paid_date = Verta::now();
            $installment->save();
        }else return TransactionController::errorResponse('مبلغ قسط صحیح نیست!',400);
    }
    private function payLoanInstallment($request,$installment,$fund_account){
        if ($request->amount === $installment->amount) {
            $fund_account->balance += $request->amount;
            $fund_account->total_balance += $request->amount;
            $fund_account->save();

            $loan = LoanAccount::where('id',$installment->loan_account_id)->first();
            $loan->paid_amount += $request->amount;
            $loan->no_of_paid_inst += 1;
            $loan->save();

            $installment->paid_date = Verta::now();
            $installment->save();
        }else return TransactionController::errorResponse('مبلغ قسط صحیح نیست!',400);
    }
    private function checkForAccountStatus($account_id){
       $ownings = Installment::where('account_id',$account_id)->where('paid_date',null)->count();
       if($ownings > 0) return Account::STATUS_DEBTOR;
       else return Account::STATUS_CREDITOR;
}
    private function logging($request){
        $transaction = Transaction::create([
            'account_id' => $request->account_id,
            'loan_account_id' => $request->loan_account_id,
            'amount' => $request->amount,
            'type' => (int)$request->type == 1 ? Transaction::TYPE_MONTHLY_PAYMENT : Transaction::TYPE_INSTALLMENT,
            'description' => (int)$request->type == 1 ? Transaction::TYPE_MONTHLY_PAYMENT : Transaction::TYPE_INSTALLMENT,
            'fund_account_id' => $request->fund_account_id,
            'account_name' => $request->account_name,
            'fund_account_name' => 'صندوق',
        ]);
        return $transaction;
    }

    public static function updateDelayDays()
{
    $today = Verta::now(); // Current date as Verta instance
    $todayFormatted = $today->format('Y/m/d');

    // Fetch installments with due_date and id fields where paid_date is null
    $installments = Installment::whereNull('paid_date')->get(['id', 'due_date']);

    foreach ($installments as $installment) {
        // Parse due_date to Verta instance
        $dueDate = Verta::parse($installment->due_date);

        // Calculate the difference in days
        if($today->greaterThan($dueDate)) $delayDays = $today->diffDays($dueDate); // false to allow negative differences
        else $delayDays = 0;
        // Update delay_days field
        $installment->delay_days = $delayDays;
        $installment->save();
    }

    return response()->json([
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
    public function sendLatencySms(){
        $receptors = [];
        $installments = Installment::where('paid_date',null)
            ->where('delay_days','>','0')->get()->groupBy('account_id');

        $accountIds = $installments->keys();
        $receptorsArray = Account::whereHas('member')
        ->with('member')
        ->whereIn('id', $accountIds)->get()->pluck('member.mobile_number')
            ->filter()->unique()->values()->toArray();

        $messagesArray = [];

        foreach ($installments as $accountId => $accountInstallments) {
            $loans = 0;
            $charges = 0;
            $total = sizeof($accountInstallments);
            foreach ($accountInstallments as $installment) {
                (int)$installment->type == 1 ? $charges++ : $loans++;
            }
            $message = "\n با سلام
        شما $total قسط پرداخت نشده و با تاخیر دارید.
        لطفا برای پرداخت اقدام کنید.
        \n" . ($loans > 0 ? "$loans قسط بابت وام\n" : "" ).
                ($charges > 0 ? "$charges قسط بابت ماهیانه" : "" ).
                "\n
        صندوق خانوادگی سلیمانیان (شهید طایف)
        لغو 11
        ";
//            $message = "لام";
            array_push($messagesArray,urlencode($message));
        }


        $receptors = implode(',', $receptorsArray);
        $message = implode(',', $messagesArray);
        return $this->smsController->sendBulkSms([
            'message' =>$message,
            'receptors' => $receptors
        ]);

//        return response()->json([
//            'installments' => $receptors,
//            'success' => $message
//        ]);
    }
    public function sendReminderSms(){
        $todayJalali = Verta::now()->startDay();
        $cutoffJalali = $todayJalali->copy()->addDays(30)->endDay();
        $todayGregorian = $todayJalali->datetime()->format('Y-m-d');
        $cutoffGregorian = $cutoffJalali->datetime()->format('Y-m-d');

        $installments = Installment::whereNull('paid_date')
            ->whereBetween('due_date', [$todayGregorian, $cutoffGregorian])
            ->get()->groupBy('account_id');
                return response()->json([
            'installments' => $installments,
            'success' => true
        ]);

    }
}
