<?php

namespace App\Models;

use Hekmatinasser\Verta\Verta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    protected $guarded = [];
    public function getCreatedAtAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function getPaidDateAttribute($val)
    {
       return $val == null ? null :verta($val)->format('Y/m/d');
    }
    public function setPaidDateAttribute($val)
    {
        if($val != null){
            $gregorianDate = Verta::parse($val)->DateTime();
            $this->attributes['paid_date'] = $gregorianDate;
        }
    }
    public function getDueDateAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function setDueDateAttribute($val)
    {
        $gregorianDate = Verta::parse($val)->DateTime();
        $this->attributes['due_date'] = $gregorianDate;
    }
    public  function loan()
    {
        return $this->hasOne(Loan::class, 'loan_id');
    }
    public  function ccount()
    {
        return $this->hasOne(Account::class, 'account_id');
    }
    public  function charge()
    {
        return $this->hasOne(MonthlyCharge::class, 'monthly_charge_id');
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->amount < 0) {
                throw new \Exception('مبلغ نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
