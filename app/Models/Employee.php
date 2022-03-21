<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
// use App\Traits\Encryptable;

class Employee extends Model
{
    use SoftDeletes;
    // use Encryptable;
    protected   $table='employee';
    protected $appends = ['image_source','pending_fields'];
    protected $guarded = ['id'];
    // protected $encryptable = [ "phone","personal_email","birth_date","pf_no"];
    // protected $appends =['department_name'];
    public $image_path = "upload/employeeimage/";
    public $additional_attributes =['pending_fields'];
   

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('is_active', function (Builder $builder) {
            $builder->where('employee.is_active', '=', 1);
        });
        static::addGlobalScope('guest', function (Builder $builder) {
            $builder->where('employee.is_active', '=', 1)->whereHas('user.roles',function($query){
                $query->where('name','<>','Guest');
            });
        });
    }
    public function toArray()
    {
        $output = parent::toArray();
        $output['personal_email'] = $this->personal_email;
        $output['birth_date'] = $this->birth_date;
        $output['phone'] = $this->phone;
        return $output;
    }
    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function attendances()
    {
        return $this->hasMany('App\Models\RemoteAttendance','employee_id','id');
    }
    public function department()
    {
        return $this->belongsTo('App\Models\Department', 'department_id');
    }
    public function managerDepartments()
    {
        return $this->hasMany('App\Models\Department','manager_id','id');
    }
    public function profileReminder()
    {
        return $this->hasMany('App\Models\PendingProfileReminder');
    }
    /**
     * getters can slow down page as database query is being used / should not be used in a loop
     */
    // public function getTotalAbsentAttribute()
    // {
      
    //  $this->attendances()->whereYear('attendance_date',Carbon::now()->year)->whereMonth('attendance_date',Carbon::now()->month)->where('status','absent')->count();
    // }

    public function equipments()
    {
        return $this->hasMany('App\Models\Equipment','employee_id');
    }

    public function itemRequestAssign()
    {
        return $this->hasMany('App\Models\ItemRequestAssign','assigned_to');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Ticket','employee_id');
    }

    // public function getTotalPresentAttribute()
    // {
      
    //     return $this->attendances->where('attendance_date',Carbon::now()->year)->where('attendance_date',Carbon::now()->month)->where('status','present')->count();
    // }
    public function qualification()
    {
        return $this->belongsTo('App\Models\Qualification','qualification_id');
    }

    public function getImagePath()
    {// check file exist then return default image.
        $imageLink = url($this->image_path.$this->profile_pic);
        if ($this->hasImage()) {
            return $imageLink;
        } else {
          
             return url('/img/user.jpg');
        }
    }

    public function hasImage()
    {
        
        if(empty($this->profile_pic)) return FALSE;
        if (file_exists(public_path($this->image_path.$this->profile_pic))) 
        {
            return TRUE;
        }
        return FALSE;
    }

    public function documents()
    {
        return $this->hasOne('App\Models\Document', 'employee_id');
    }
    public function bankdetail()
    {
        return $this->hasOne('App\Models\BankDetail','employee_id');
    }
    public function activity()
    {  
       return  $this->morphOne('App\Models\ActivityLog','module');
    }
    public function designation()
    {
        return $this->belongsTo('App\Models\Designation')->withDefault();
    }
    public function delete()
    {
        if($this->user)
        {
         $this->user->delete();
        }
     return parent::delete();
    }
    public function restore()
    {
        $this->user()->restore();
        return parent::restore();
    }
    public function getImageSourceAttribute() {
        return $this->getImagePath();
    }
 
    public function draftProfiles()
    {
        return $this->hasMany('App\Models\EmployeeProfileDraft');
    }


    public function getPendingFieldsAttribute()
    {
      
        
       if(!empty($this->documents))
       {
        $attributes = $this->documents->getAttributes();
        $pending    = [];
        foreach($attributes as $key=>$value)
        {
                // 
                if(in_array($key, ['pan_number', 'pan_file', 'deleted_at']) )
                {
                continue;
                }
                if($value==null)
                {

                $pending[]= ucwords(str_replace('_', ' ', $key));
                }
            
            
        }
        if(empty($this->profile_pic))
        {
            $pending[]='Profile Picture';
        }
        if(empty($this->employeeEmergencyContact))
        {
            $pending[]='Emergancy Contact Details';
        }
        $pending=implode(',',$pending);
        return $pending;
       
      }
       return "All details Pending";
     
    }

    public function employeeExitDetail()
    {
        return $this->hasOne('App\Models\EmployeeExitDetail', 'employee_id');
    }

    public function employeeEmergencyContact()
    {
        return $this->hasOne('App\Models\EmployeeEmergencyContact', 'employee_id');
    }

    public function equipmentAssigned()
    {   
        return $this->hasMany('App\Models\EquipmentAssign','assigned_to','id');
    }

    public function matchedAssignedEquipment($availableEquipments)
    {
        $match=array_intersect_key($this->equipmentAssigned->pluck('stock_item_id','stock_item_id')->toArray(),$availableEquipments);
        if(!empty($match))
        {
            return array_key_first($match);
        }
        return null;
    }

    public function workReports()
    {
        return $this->hasMany('App\Models\DailyReport', 'employee_id');
    }

    public function leaves()
    {
        return $this->hasMany('App\Models\Leave', 'employee_id');
    }
}
