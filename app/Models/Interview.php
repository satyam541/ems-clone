<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $table ='interviews';
    protected $guarded =['id'];


    public function addedBy()
    {
        return $this->belongsTo(User::class,'added_by');
    }
}
