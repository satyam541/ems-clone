<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Specification extends Model
{
    protected $table = 'equipment_specifications';
    protected $guarded = 'id';

    public function activity()
    {
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
}
