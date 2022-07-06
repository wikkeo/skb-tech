<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['slug', 'name', 'description', 'createdDate', 'active'];

    protected $dates = ['createdDate'];

    public $timestamps = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->createdDate = $model->freshTimestamp();
        });
    }
}
