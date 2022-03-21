<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class ActivityLog extends Model
{
    use SoftDeletes;
    protected $table='activity_log';
    protected $appends = ['date'];

    public function module()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    
    public function getDateAttribute()
    {
        return $this->created_at->format('d-m-Y h:i:s');
    }
}
