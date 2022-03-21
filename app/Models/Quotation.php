<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class Quotation extends Model
{
    protected $table='quotations';
    protected $fillable=['id'];
    protected $with=['quotationDetails'];
    function quotationDetails()
    {
        return $this->hasMany('App\Models\QuotationDetails','quotation_id','id');
    }
    function quotationStatus()
    {
        $status=$this->quotationDetails->pluck('is_approved')->toArray();
        if(in_array(1,$status))
        {
            
            $status=count(array_filter($status));
        }
        elseif(in_array(0,$status,true))
        {
            $status='rejected';
        }
        else
        {
            $status=null;
        }
        return $status;
    }
    function employee()
    {
        return $this->belongsTo('App\Models\Employee','employee_id','id');
    }
    // function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d/m/Y');
    // }

}
