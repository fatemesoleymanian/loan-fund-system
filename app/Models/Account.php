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
    public static function splitAccountIds($accounts)
{
    // Explode the string into an array of IDs
    $ids = explode(',', $accounts);

    // Map each ID into an associative array with 'id' as the key
    $formattedIds = array_map(fn($id) => ['id' => (int) trim($id)], $ids);

    // Get the count of IDs
    $count = count($ids);

    // Return the formatted array and the count
    return [
        'formattedIds' => $formattedIds,
        'count' => $count
    ];
}

    use HasFactory;
}
