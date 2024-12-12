<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
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
            if ($model->max_amount < 0 || $model->min_amount < 0 || $model->static_fee < 0 || $model->fee_percent < 0) {
                throw new \Exception('سقف نمیتواند منفی شود!');
            }
        });

        // Alternatively, for strict control during creation or updates
        static::creating(function ($model) {
            if ($model->max_amount < 0 || $model->min_amount < 0 || $model->static_fee < 0 || $model->fee_percent < 0) {
                throw new \Exception('سقف نمیتواند منفی شود!');
            }
        });

        static::updating(function ($model) {
            if ($model->max_amount < 0 || $model->min_amount < 0 || $model->static_fee < 0 || $model->fee_percent < 0) {
                throw new \Exception('سقف نمیتواند منفی شود!');
            }
        });
    }
    use HasFactory;
}
