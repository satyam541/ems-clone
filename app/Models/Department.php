<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use SoftDeletes;
    protected $table='departments';
    protected $fillable=['name','description'];
    protected $appends = ['manager'];
    public function employees()
    {
        return $this->hasMany('App\Models\Employee', 'department_id');
    }
    public function deptManager()
    {
        return $this->belongsTo('App\Models\Employee','manager_id');
    }
    public function activity()
    {  
       return  $this->morphOne('App\Models\ActivityLog','module');
    }
    public function getManagerAttribute()
    {
        return $this->managerDetails()->name ?? '';

    }

    public function managerDetails()
    {
       
        $manager= $this->employees()->whereHas('user.roles', function($query){
            $query->where('name', 'Manager');
        })->first();
     
        // if(empty($manager))
        // {
        //     $department=Department::where('name','IT')->first();
        //     $manager=$department->managerDetails();
        //     return $manager;
        // }
      
        return $manager;
    }
}
