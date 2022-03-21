<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Observers\DepartmentObserver;
use App\Models\Department;
use App\Observers\EmployeeObserver;
use App\Observers\IntervieweeObserver;
use App\Observers\DocumentObserver;
use App\Models\Document;
use App\Models\Interviewee;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Attendance;
use App\Models\EmployeeProfileDraft;
use App\Models\Leave;
use App\Models\Equipment;
use App\Models\Repair;
use App\Models\Specification;
use App\Models\Entity;
use App\Models\EquipmentRequests;
use App\Models\Module;
use App\User;
use App\Observers\PermissionObserver;
use App\Observers\UserObserver;
use App\Observers\RoleObserver;
use App\Observers\AttendanceObserver;
use App\Observers\LeaveObserver;
use App\Observers\EquipmentObserver;
use App\Observers\EmployeeEquipmentObserver;
use App\Observers\EmployeeProfileDraftObserver;
use App\Observers\SpecificationObserver;
use App\Observers\RepairObserver;
use App\Observers\EntityObserver;
use App\Observers\EquipmentRequestObserver;
use App\Observers\ModuleObserver;
use Illuminate\Database\Eloquent\Relations\Relation;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        
        Relation::morphMap([

            'employee'             => 'App\Models\Employee',
            'department'           => 'App\Models\Department',
            'attendance'           => 'App\Models\Attendance',
            'user'                 => 'App\User',
            'equipment'            => 'App\Models\Equipment',
            'entity'               => 'App\Models\Entity',
            'role'                 => 'App\Models\Role',
            'permission'           => 'App\Models\Permission',
            'module'               => 'App\Models\Module',
            'specification'        => 'App\Models\Specification',
            'interviewee'          =>  'App\Models\Interviewee',
            'document'             =>  'App\Models\Document',
            'leave'                =>  'App\Models\Leave',
            'equipment'            =>  'App\Models\Equipment',
            'specification'        =>  'App\Models\Specification',
            'repair'               =>  'App\Models\Repair',
            'equipment request'    =>  'App\Models\EquipmentRequests',
            'software'             =>   'App\Models\Software',
            'hardware'             =>   'App\Models\ItemRequestAssign',     


        ]);
        
        Department::observe(DepartmentObserver::class);
        Employee::observe(EmployeeObserver::class);
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);
        Module::observe(ModuleObserver::class);
        Interviewee::observe(IntervieweeObserver::class);
        Document::observe(DocumentObserver::class);
        User::observe(UserObserver::class);
        Attendance::observe(AttendanceObserver::class);
        Leave::observe(LeaveObserver::class);
        Equipment::observe(EquipmentObserver::class);
        Specification::observe(SpecificationObserver::class);
        Repair::observe(RepairObserver::class);
        Entity::observe(EntityObserver::class);
        EquipmentRequests::observe(EquipmentRequestObserver::class);
        EmployeeProfileDraft::observe(EmployeeProfileDraftObserver::class);

    }
}
