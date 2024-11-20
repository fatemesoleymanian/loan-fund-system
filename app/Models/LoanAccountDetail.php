<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAccountDetail extends Model
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
    use HasFactory;
}
