<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundAccount extends Model
{
    protected $guarded = [];
    public static function current(){
        return self::orderBy('created_at','desc')->first();
    }
    public function getCreatedAtAttribute($val)
    {
        return verta($val)->format('l d %B Y');
    }
    public function getUpdatedAtAttribute($val)
    {
        return verta($val)->format('l d %B Y');
    }
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->balance < 0 || $model->total_balance < 0 || $model->fees < 0 || $model->expenses < 0) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->balance < 0 || $model->total_balance < 0 || $model->fees < 0 || $model->expenses < 0) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->balance < 0 || $model->total_balance < 0 || $model->fees < 0 || $model->expenses < 0) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });
    }
   const STATUS_SETTLEMENT = 'تسویه';
    const STATUS_CREDITOR = 'بستانکار';
    const STATUS_DEBTOR = 'بدهکار';

    public static function getAccountStatus()
    {
        return [
            self::STATUS_SETTLEMENT,
            self::STATUS_CREDITOR,
            self::STATUS_DEBTOR,
        ];
    }

    use HasFactory;
}
