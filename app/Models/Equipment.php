<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Equipment extends Model
{
    use SoftDeletes;
    protected $table='equipment';
    protected $guarded = ['id'];

    public function entity()
    {
        return $this->belongsTo('App\Models\Entity','entity_id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee','employee_id');
    }

    public function specifications()
    {
        return $this->hasMany("App\Models\Specification", 'equipment_id','id');
    }
    public function repairs()
    {
        return $this->hasMany("App\Models\Repair", 'equipment_id','id');
    }
    public function activity()
    {
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
    public function problems()
    {
        return $this->hasMany("App\Models\EquipmentProblem", 'equipment_alloted_no','alloted_no');
    }

}
