<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

   protected $guarded = [];
    public function getCreatedAtAttribute($val)
    {
        return verta($val)->format('l d %B Y');
    }
    public function getUpdatedAtAttribute($val)
    {
        return verta($val)->format('l d %B Y');
    }
    public  function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public  function fundAccount()
    {
        return $this->belongsTo(FundAccount::class, 'fund_account_id');
    }
    public  function monthlyCharge()
    {
        return $this->belongsTo(MonthlyCharge::class, 'monthly_charge_id');
    }
    public  function installment()
    {
        return $this->belongsTo(Installment::class, 'installment_id');
    }
    const TYPE_MONTHLY_PAYMENT = 'پرداخت ماهیانه';
    const TYPE_INSTALLMENT = 'پرداخت قسط';
    const TYPE_LOAN_PAYMENT = 'پرداخت وام';
    const TYPE_PENALTY = 'پرداخت جریمه';
    const TYPE_FEE = 'پرداخت کارمزد';
    const TYPE_WITHDRAW = 'برداشت';
    const TYPE_DEPOSIT = 'واریز';

    public static function getTransactionTypes()
    {
        return [
            self::TYPE_MONTHLY_PAYMENT,
            self::TYPE_INSTALLMENT,
            self::TYPE_LOAN_PAYMENT,
            self::TYPE_PENALTY,
            self::TYPE_FEE,
            self::TYPE_WITHDRAW,
            self::TYPE_DEPOSIT
        ];
    }

    use HasFactory;
}
