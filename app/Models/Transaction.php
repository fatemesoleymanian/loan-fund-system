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
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->amount < 0 ) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->amount < 0 ) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->amount < 0 ) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
