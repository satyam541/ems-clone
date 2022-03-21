<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table='stocks';
    protected $guarded=['id'];
    function item()
    {
        return $this->belongsTo('App\Models\Item','item_id','id');
    }
    function purchasedByEmployee()
    {
        return $this->belongsTo('App\Models\Employee','purchased_by','id');
    }
    function stockDetails()
    {
        return $this->hasMany('App\Models\StockDetails','stock_id','id');
    }

}
