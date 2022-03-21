<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Repair extends Model
{
    protected $table = 'equipment_repair';
    protected $guarded = 'id';


    public function activity()
    {
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
}
