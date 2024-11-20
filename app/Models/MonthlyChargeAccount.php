<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonthlyChargeAccount extends Model
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
    public  function monthlyCharge()
    {
        return $this->belongsTo(MonthlyCharge::class, 'monthly_charge_id');
    }
    use HasFactory;
}
