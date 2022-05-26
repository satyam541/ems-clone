<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class LiveAttendance extends Model
{
    protected $connection = 'sqlsrv';
    protected $table = 'paralleltable';
    
    protected $appends = [
        'punch_date',
        'punch_time',
        'us_punch_date',
        'us_punch_time'
    ];

    // public function getPunchDateAttribute()
    // {
    //     return getFormattedDate($this->Logdate, 'Y-m-d');
    // }
    // public function getPunchTimeAttribute()
    // {
    //     return getFormattedTime($this->Logtime, 'H:i:s');
    // }

    // public function getUsPunchDateAttribute()
    // {
    //     return convert_IST_to_US_datetime($this->Logdate, 'Y-m-d');
    // }
    // public function getUsPunchTimeAttribute()
    // {
    //     return convert_IST_to_US_datetime($this->Logdate, 'H:i:s');
    // }
    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class, 'Empcode', 'attendance_code');
    // }
}
