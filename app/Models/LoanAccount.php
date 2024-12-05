<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAccount extends Model
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
    public  function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public  function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public  function installments()
    {
        return $this->hasMany(Installment::class, 'loan_id');
    }
    public function accounts(){
        return $this->belongsToMany(Account::class,'loan_accounts');
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->amount < 0 || $model->paid_amount || $model->fee_amount) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->amount < 0 || $model->paid_amount || $model->fee_amount) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->amount < 0 || $model->paid_amount || $model->fee_amount) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
