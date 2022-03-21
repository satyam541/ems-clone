<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class EquipmentRequests extends Model
{
    protected $table='entity_requests';
    protected $guarded=['id'];

    
    public function entity() 
    {
        return $this->belongsTo('App\Models\Entity','entity_id');
    }
    public function manager()
    {
        return $this->belongsTo('App\Models\Employee','requested_by');
    }
    public function employee()
    {
        return $this->belongsTo('App\Models\Employee','employee_id');
    }
    public function assigner()
    {
        return $this->belongsTo('App\Models\Employee','action_taken_by');
    }

    public function activity()
    {
       return  $this->morphOne('App\Models\ActivityLog','module');
    }

}
