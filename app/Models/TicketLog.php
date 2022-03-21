<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketLog extends Model
{
    protected $table='ticket_logs';
    
    public function actionBy()
    {
        return $this->belongsTo('App\Models\Employee','action_by','id')->withoutGlobalScope('guest');
    }

    public function assignedTo()
    {
        return $this->belongsTo('App\Models\Employee','assigned_to','id');
    }
    public function ticket()
    {
        return $this->belongsTo('App\Models\TIcket');
    }
}
