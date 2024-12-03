<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundAccount extends Model
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
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->balance < 0 || $model->total_balance || $model->fees || $model->expenses) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->balance < 0 || $model->total_balance || $model->fees || $model->expenses) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->balance < 0 || $model->total_balance || $model->fees || $model->expenses) {
                throw new \Exception('موجودی نمیتواند منفی شود!');
            }
        });
    }

    use HasFactory;
}
