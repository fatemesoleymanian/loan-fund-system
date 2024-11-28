<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
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
    public  function member()
    {
        return $this->belongsTo(Member::class, 'member_id');
    }
    public function loans(){
        return $this->belongsToMany(Loan::class,'loan_account_details');
    }
    public function loan_details(){
        return $this->hasMany(LoanAccountDetail::class);
    }
    public  function monthlyCharges()
    {
        return $this->belongsToMany(MonthlyCharge::class, 'monthly_charge_accounts');
    }
    public static function openAccounts(){

    }
    public static function closedAccounts(){

    }
    public static function splitAccountIds($accoutns){

    }
    use HasFactory;
}
