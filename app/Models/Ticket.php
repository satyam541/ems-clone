<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $table='ticket';
    protected $guarded=['id'];

    // function problemType()
    // {
    //     return $this->morphTo(__FUNCTION__,'module_type','module_id');
    // }
    function employee()
    {
        return $this->belongsTo('App\Models\Employee','employee_id','id');
    }
    function actionBy()
    {
        return $this->belongsTo('App\Models\Employee','action_by','id')->withoutGlobalScope('guest');
    }

    public function ticketCategory()
    {
        return $this->belongsTo('App\Models\TicketCategory','ticket_category_id');
    }

    public function ticketLogs()
    {
        return $this->hasMany('App\Models\TicketLog','ticket_id','id');
    }

    public function assignedBy()
    {
        return $this->ticketLogs->where('action','Assigned')->last()->actionBy->name ?? 'N\A';

    }
    
}
