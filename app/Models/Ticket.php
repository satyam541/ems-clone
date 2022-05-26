<?php

namespace App\Models;

use App\Models\Asset;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table='ticket';
    protected $guarded=['id'];

    // function problemType()
    // {
    //     return $this->morphTo(__FUNCTION__,'module_type','module_id');
    // }
    // function employee()
    // {
    //     return $this->belongsTo('App\Models\Employee','employee_id','id');
    // }
    function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }
    function raisedBy()
    {
        return $this->belongsTo('App\User','raised_by','id');
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

    public function asset()
    {
        return $this->belongsTo(Asset::class,'barcode');
    }
    
}
