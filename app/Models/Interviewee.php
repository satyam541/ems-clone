<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interviewee extends Model
{
    protected $table='interviewee';

    public function activity()
    {  
       return  $this->morphOne('App\Models\ActivityLog','module');
    }
    
    public function qualification()
    {
        return $this->belongsTo('App\Models\Qualification','qualification_id');
    }
}
