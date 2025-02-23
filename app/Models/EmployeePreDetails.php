<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class EmployeePreDetails extends Model
{
    protected $table ='employee_pre_details';
    protected $guarded = ['id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
