<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockDetails extends Model
{
    protected $table='stock_item_details';
    protected $guarded=['id'];
    protected $appends=['equipment_label','equipment_type'];
    function stock()
    {
        return $this->belongsTo('App\Models\Stock','stock_id','id');
    }
    function EquipmentAssign()
    {
        return $this->hasOne('App\Models\EquipmentAssign','stock_item_id');
    }
    function getEquipmentLabelAttribute()
    {
        return strtoupper($this->manufacturer) .' ('.$this->label.')';
    }
    function getEquipmentTypeAttribute()
    {
        return ucfirst($this->stock->item->name);
    }
}
