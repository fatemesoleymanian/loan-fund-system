<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class charity extends Model
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
    const FEE_SOURCE = 'از کارمزد';
    const BALANCE_SOURCE = 'از موجودی';
    const NONE = 'از هیچکدام';

    public static function getMoneySource()
    {
        return [
            self::FEE_SOURCE,
            self::BALANCE_SOURCE,
            self::NONE,
        ];
    }
    use HasFactory;
}
