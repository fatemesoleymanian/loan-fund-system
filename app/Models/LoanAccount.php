<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAccount extends Model
{
    protected $guarded = [];
    public function getCreatedAtAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function getGrantedAtAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function setGrantedAtAttribute($val)
    {
        $gregorianDate = Verta::parse($val)->DateTime();
        $this->attributes['granted_at'] = $gregorianDate;
    }
    public function getPayBackAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function setPayBackAttribute($val)
    {
        $gregorianDate = Verta::parse($val)->DateTime();
        $this->attributes['payback_at'] = $gregorianDate;
    }

    public  function loan()
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }
    public  function title()
    {
        return $this->belongsTo(Loan::class, 'loan_id')->select('title');
    }
    public  function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public  function installments()
    {
        return $this->hasMany(Installment::class, 'loan_id');
    }
//    public function accounts(){
//        return $this->belongsToMany(Account::class,'loan_accounts');
//    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->amount < 0 || $model->paid_amount < 0 || $model->fee_amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->amount < 0 || $model->paid_amount < 0 || $model->fee_amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->amount < 0 || $model->paid_amount < 0 || $model->fee_amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
