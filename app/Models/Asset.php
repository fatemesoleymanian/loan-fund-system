<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
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
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->cost < 0) {
                throw new \Exception('هزینه نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->cost < 0) {
                throw new \Exception('هزینه نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->cost < 0) {
                throw new \Exception('هزینه نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
