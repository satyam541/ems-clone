<?php

namespace App\models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
class QuotationDetails extends Model
{
    protected $table='quotation_details';
    protected $guarded=['id'];
    function getItemDetailAttribute($value)
    {
        return unserialize($value);

    }
    // function getCreatedAtAttribute($value)
    // {
    //     return Carbon::parse($value)->format('d/m/Y');
    // }
    function quotation()
    {
        return $this->belongsTo('App\Models\Quotation','quotation_id');
    }
}
