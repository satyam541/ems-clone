<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Leave'              => 'App\Policies\LeavePolicy',
        'App\Models\Role'               => 'App\Policies\RolePolicy',
        'App\Models\Permission'         => 'App\Policies\PermissionPolicy',
        'App\Models\Module'             => 'App\Policies\ModulePolicy',
        'App\Models\Department'         => 'App\Policies\DepartmentPolicy',
        'App\Models\Attendance'         => 'App\Policies\AttendancePolicy',
        'App\Models\Qualification'      => 'App\Policies\QualificationPolicy',
        'App\Models\ActivityLog'        => 'App\Policies\ActivityLogPolicy',
        'App\Models\Interviewee'        => 'App\Policies\IntervieweePolicy',
        'App\Models\EquipmentRequests'  => 'App\Policies\EquipmentRequestPolicy',
        'App\Models\Employee'           => 'App\Policies\EmployeePolicy',
        'App\Models\Equipment'          => 'App\Policies\EquipmentPolicy',
        'App\Models\Ticket'             => 'App\Policies\TicketPolicy',
        'App\Models\Stock'              => 'App\Policies\StockPolicy',
        'App\Models\RemoteAttendance'   => 'App\Policies\RemoteAttendancePolicy',
        'App\Models\Asset'              => 'App\Policies\AssetPolicy',
        'App\Models\AssetCategory'      => 'App\Policies\AssetCategoryPolicy',
        'App\Models\AssetDetails'       => 'App\Policies\AssetDetailsPolicy',
        'App\Models\AssetSubType'       => 'App\Policies\AssetSubTypePolicy',
        'App\Models\AssetType'          => 'App\Policies\AssetTypePolicy',
        'App\Models\Interview'          => 'App\Policies\InterviewPolicy',
        'App\Models\LeaveType'          => 'App\Policies\LeaveTypePolicy',
        'App\Models\Attendance'         => 'App\Policies\AttendancePolicy',
        'App\Models\Badge'              => 'App\Policies\BadgePolicy',
        'App\Models\Announcement'              => 'App\Policies\AnnouncementPolicy',
        ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
