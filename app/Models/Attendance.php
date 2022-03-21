<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;


class Attendance extends Model
{
    protected $table='attendance';
    use SoftDeletes;
    protected $guarded = ['id'];
 
    

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('employee', function (Builder $builder) {
            $builder->has('employee');
        });
        static::addGlobalScope('attendance_month',function(Builder $builder){
            $builder->selectRaw("*,MONTH(attendance_date) as attendance_month");
        });
      
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Employee','employee_id','id');
    }
    
    public function activity()
    {  
       return  $this->morphOne('App\Models\ActivityLog','module');
    }

   
}
