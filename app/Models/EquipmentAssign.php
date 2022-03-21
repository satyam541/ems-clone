<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentAssign extends Model
{
    protected $table='equipment_assign';
    protected $guarded=['id'];
    function stockItemDetail()
    {
        return $this->belongsTo('App\Models\StockDetails','stock_item_id','id');
    }
    function assignedToEmployee()
    {
        return $this->belongsTo('App\Models\Employee','assigned_to','id');
    }
}
