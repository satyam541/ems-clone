<?php

namespace App;

use App\Models\Module;
use App\User as Users;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;

class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];
    protected $table = 'users';
    protected $with = ['employee', 'notifications'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    private $permissionsCache; // related user permissions cache
    private $rolesCache;       // related user role cache
    public static $developers = ['satyam.suri@themsptraining.com', 'konica.arora@themsptraining.com'];
    public function employee()
    {
        return $this->hasOne('App\Models\Employee', 'user_id')->withoutGlobalScope('guest');
    }

    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'role_user', 'user_id', 'role_id')->orderBy('display_order');
    }

    public function hasRole($roleName)
    {
        //mysql (utf8mb4_unicode_ci) is case insensitive
        if (empty($this->rolesCache)) {
            $this->rolesCache = $this->roles->map(function ($item, $key) {
                $item->name = strtolower($item->name);
                return $item;
            });
        }
        $roleName = strtolower($roleName);
        return $this->rolesCache->where("name", $roleName)->isNotEmpty();
    }

    public function hasPermission($moduleName, $access = null)
    {
        // do not check for permission if the user is admin
        if ($this->hasRole('admin')) {
            return TRUE;
        }
        if (empty(Module::$cache)) {
            Module::$cache = Module::all()->map(function ($item, $key) {
                $item->name = strtolower($item->name);
                return $item;
            });
        }
        $module = Module::$cache->where('name', strtolower($moduleName))->first();
        $module_id = empty($module) ? null : $module->id;

        $permissions = $this->permissions();
        if (empty($module_id) || $permissions->isEmpty()) {
            return FALSE;
        }
        $result = $permissions->where('module_id', $module_id);
        if (!empty($access)) {
            $result = $result->where('access', strtolower($access));
        }

        if (!$result->isEmpty()) {
            return TRUE;
        }

        return FALSE;
    }

    private function permissions()
    {
        // using actions method only once per page
        //testing required here .............................................................
        if (empty($this->permissionsCache)) {
            $this->permissionsCache = $this->roles->load("permissions")->pluck("permissions")
                ->collapse()->map(function ($item, $key) {
                    $item->access = strtolower($item->access);
                    return $item;
                });
        }
        return $this->permissionsCache;
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notifications', 'user_id')->where('read_on', null)->orderBy('created_at','desc');
    }

    public function activity()
    {
        return  $this->morphOne('App\Models\ActivityLog', 'module');
    }
    public function activities()
    {
        return $this->hasMany('App\Models\ActivityLog', 'user_id');
    }
    public static function havingRole($role)
    {
        $role = Arr::wrap($role);
        $users = User::wherehas('roles', function ($query) use ($role) {
            $query->whereIn('name', $role);
        });
        return $users->pluck('id')->toArray();
    }
}
