<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
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
    public function getDueDateAttribute($val)
    {
        return verta($val)->format('Y/m/d');
    }
    public  function loan()
    {
        return $this->hasOne(Loan::class, 'loan_id');
    }
    use HasFactory;
}
