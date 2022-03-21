<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected   $table='entity';
    protected $appends = ['total_stock', 'available', 'assigned'];
    public $guarded = ['id'];
    
    public function equipmentDetails()
    {
        return $this->hasMany('App\Models\Equipment', 'entity_id');
    }
    public function getTotalStockAttribute()
    {
        return $this->equipmentDetails->count();
    }
    public function getAssignedAttribute()
    {
        return $this->equipmentDetails()->whereHas('employee')->count();
    }
    public function getAvailableAttribute()
    {
        return $this->equipmentDetails->count() - $this->equipmentDetails()->whereHas('employee')->count();
    }
    public function activity()
    {  
       return  $this->morphOne('App\Models\ActivityLog','module');
    }
}
