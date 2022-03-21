<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Software extends Model
{
    use SoftDeletes;
    protected $table = 'software';
    protected $guarded = ['id'];

    function equipmentProblem()
    {
        return $this->morphMany('App\Models\EquipmentProblem','problemType','module_type','module_id');
    }
}