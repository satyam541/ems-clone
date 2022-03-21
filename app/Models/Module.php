<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model 
{
    protected $table = 'module';
    protected $guarded = ['id'];
    public static $cache = array();
    use SoftDeletes;

    public function permissions()
    {
        return $this->hasMany(Permission::class); 
    }

    public function activity()
    {  
       return  $this->morphMany('App\Models\ActivityLog','module');
    }
    
}
