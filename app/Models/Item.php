<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $guarded=['id'];
    protected $appends=['current_stock_count'];
    function stocks()
    {
        return $this->hasMany('App\Models\Stock');
    }

    function getCurrentStockCountAttribute()
    {
        return array_sum($this->stocks->pluck('quantity')->toArray());
    }
    function getNameAttribute($value)
    {
        return ucfirst($value);
    }
    
}
