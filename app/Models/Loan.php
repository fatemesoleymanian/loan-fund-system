<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $guarded = [];
    const TYPE_CHARITY = 'وام قرض الحسنه';

//    public function getCreatedAtAttribute($val)
//    {
//        return verta($val)->format('l d %B Y');
//    }
//    public function getUpdatedAtAttribute($val)
//    {
//        return verta($val)->format('l d %B Y');
//    }
    public function getDueDateAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function getIssueDateAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public function getEndDateAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }

     public static function getLoanTypes()
    {
        return [
            self::TYPE_CHARITY
        ];
    }
    public  function installments()
    {
        return $this->hasMany(Installment::class, 'loan_id');
    }
    public function accounts(){
        return $this->belongsToMany(Account::class,'loan_account_details');
    }
    use HasFactory;
}
