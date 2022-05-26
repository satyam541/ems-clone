<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    // use SoftDeletes;
    protected $table='departments';
    protected $guarded=['id'];
    protected $appends = ['manager'];
    public function employees()
    {
        return $this->hasMany('App\Models\Employee', 'department_id');
    }
    public function deptManager()
    {
        return $this->belongsTo('App\Models\Employee','manager_id');
    }
    public function deptTeamLeader()
    {
        return $this->belongsTo('App\Models\Employee','team_leader_id');
    }
    public function activity()
    {
       return  $this->morphOne('App\Models\ActivityLog','module');
    }
    public function getManagerAttribute()
    {
        return $this->managerDetails()->name ?? '';

    }
    public function getTeamLeaderAttribute()
    {
        return $this->teamLeaderDetails()->name ?? '';

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
    // public function teamLeaderDetails()
    // {

    //     $teamLeader= $this->employees()->whereHas('user.roles', function($query){
    //         $query->where('name', 'Team Leader');
    //     })->first();

    //     // if(empty($manager))
    //     // {
    //     //     $department=Department::where('name','IT')->first();
    //     //     $manager=$department->managerDetails();
    //     //     return $manager;
    //     // }

    //     return $teamLeader;
    // }
}
