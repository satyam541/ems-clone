<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->

    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Employee Management System</title>
    <link rel="icon" href="{{ url('img/favicon.ico') }}" sizes="16x16">
    <!-- plugins:css -->
    <link rel="stylesheet" href="{{ url('skydash/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ url('css/tail.select.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ url('skydash/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ url('skydash/vendors/select2/select2.min.css') }}">
    <link rel="stylesheet" href="{{ url('skydash/vendors/select2-bootstrap-theme/select2-bootstrap.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ url('skydash/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <!-- Datatables -->
    <link rel="stylesheet" href="{{ url('skydash/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link type="text/css" rel="stylesheet" href="{{ url('js/jsgrid/jsgrid.min.css') }}" />
    <link type="text/css" rel="stylesheet" href="{{ url('js/jsgrid/jsgrid-theme.min.css') }}" />
    <link rel="stylesheet" href="{{ url('js/toastr/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">

    <style>
        a:hover {
            text-decoration: none;
        }

        .hidden {
            display: none;
        }

        .notification-bell {
            position: absolute;
            left: 57%;
            width: 27px;
            height: 23px;
            border-radius: 100%;
            background: #4B49AC;
            top: -3px;
            border: 1px solid #ffffff;
            color: blanchedalmond;
            font-size: small;
        }

        .carousel-item {
            height: 50px;
        }

        .settings-panel {
            right: -380px;
            width: 380px !important;
        }

    </style>
    @yield('headerLinks')
</head>

<body @if(auth()->user()->id==4) class="sidebar-dark" @else class="sidebar-light" @endif>
    @php $commonCount=commonCount() @endphp
    @if (url('') == 'http://ems.tka-in.com' || url('') == 'https://ems.tka-in.com')
        @php $hidden = 'hidden'; @endphp
    @else
        @php $hidden = ''; @endphp
    @endif
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav id="header" class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row  @if(auth()->user()->id==4) navbar-info @endif ">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo mr-5" href="{{ route('dashboard') }}">EMS
                    <a class="navbar-brand brand-logo-mini" href="{{ route('dashboard') }}">EMS</a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>

                <ul class="navbar-nav navbar-nav-right">

                    @php
                        $annoucements = getAnnouncements();
                    @endphp
                    @if($annoucements->isNotEmpty())
                    <li class="nav-item nav-profile"><span class="btn btn-sm btn-warning mt-1"
                            id="annoucement">Annoucements</span></li>
                    @endif
                    <li class="nav-item dropdown">
                        @if (!Session::has('orig_user'))
                            @if (in_array(strtolower(auth()->user()->email), App\User::$developers))
                    <li class="nav-item">
                        <a href="{{ route('switchUser') }}" class="nav-link">Switch User</a>
                    </li>
                    @endif
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('swithLogout') }}">
                            <span class="">Return Back
                            </span>
                        </a>
                    </li>
                    @endif
                    </li>
                    <li class="nav-item dropdown mt-1">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="fas fa-running mx-0"></i>
                        </a>
                        <span class="badge badge-primary mb-3" id="leave-count">0</span>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list"
                            id="leave-notification" style="min-width: 290px;" aria-labelledby="notificationDropdown">

                        </div>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#"
                            data-toggle="dropdown">
                            <i class="icon-bell mx-0"></i>
                            <span class="notification-bell " id="count"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="notification"
                            style="min-width: 290px;" aria-labelledby="notificationDropdown">



                        </div>
                    </li>
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            @if (!empty(auth()->user()->employee))
                                <img src="{{ auth()->user()->employee->getImagePath() }}"
                                    alt="{{ auth()->user()->name }}" />
                            @else
                                <img src="404" alt="">
                            @endif
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a class="dropdown-item" href="{{ route('changePassword') }}">
                                <i class="ti-settings text-primary"></i>
                                Change Password
                            </a>

                            <form action="{{ route('logout') }}" method="post">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="ti-power-off text-primary"></i>
                                    Logout

                                </button>

                            </form>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>
        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            @include('layouts.annoucement')
            <!-- partial:partials/_settings-panel.html -->
            {{-- <div class="theme-setting-wrapper">
                <div id="annoucements-trigger"><i class="ti-settings"></i></div>
                <div id="theme-settings" class="settings-panel">
                    <i class="settings-close ti-close"></i>
                    <p class="settings-heading">SIDEBAR SKINS</p>
                    <div class="sidebar-bg-options selected" id="sidebar-light-theme"
                        onclick="themeColor('light','sidebar')">
                        <div class="img-ss rounded-circle bg-light border mr-3"></div>Light
                    </div>
                    <div class="sidebar-bg-options" id="sidebar-dark-theme" onclick="themeColor('dark','sidebar')">
                        <div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark
                    </div>
                    <p class="settings-heading mt-2">HEADER SKINS</p>
                    <div class="color-tiles mx-0 px-4">
                        <div class="tiles success" onclick="themeColor('success','header')"></div>
                        <div class="tiles warning" onclick="themeColor('warning','header')"></div>
                        <div class="tiles danger" onclick="themeColor('danger','header')"></div>
                        <div class="tiles info" onclick="themeColor('info','header')"></div>
                        <div class="tiles dark" onclick="themeColor('dark','header')"></div>
                        <div class="tiles default" onclick="themeColor('default','header')"></div>
                    </div>
                </div>
            </div> --}}
            <!-- partial -->
            <!-- partial:partials/_sidebar.html -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">
                    <li class="nav-item">
                        <a style="cursor: default;" href="javascript:void(0)" class="nav-link">
                            <i>
                                @if (!empty(auth()->user()->employee))
                                    <img src="{{ auth()->user()->employee->getImagePath() }}"
                                        alt="{{ auth()->user()->name }}" width="40" height="40"
                                        style="border-radius: 100%;" />
                                @else
                                    <img src="404" alt="" width="40" height="40" style="border-radius: 100%;">
                                @endif
                            </i>
                            <span class="menu-title"
                                style="padding-left:10px;">{{ ucfirst(auth()->user()->name) }}
                                @if (!empty(auth()->user()->employee) && auth()->user()->employee->is_power_user)
                                <i title="Power User" class="fa fa-bolt ml-3 text-warning" style="font-size:18px;"></i>
                                @endif
                            </span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    {{-- Admin Panel --}}
                    @if (Auth::user()->can('view', new App\User()) || Auth::user()->can('view', new App\Models\Role()) || Auth::user()->can('view', new App\Models\Permission()) || Auth::user()->can('view', new App\Models\Module()))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#admin" aria-expanded="false"
                                aria-controls="admin">
                                <i class="icon-head menu-icon"></i>
                                <span class="menu-title">Admin</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="admin">
                                <ul class="nav flex-column sub-menu">
                                    @can('view', new App\User())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('userView') }}">User</a></li>
                                    @endcan
                                    @can('view', new App\Models\Role())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('roleView') }}">Role</a></li>
                                    @endcan
                                    @can('view', new App\Models\Permission())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('permissionView') }}">Permission</a></li>
                                    @endcan
                                    @can('view', new App\Models\Module())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('moduleView') }}">Module</a></li>
                                    @endcan
                                    @can('view', new App\Models\Role())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('assignRoles') }}">Assign Role</a></li>
                                        <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('bulkAssignRole') }}">Bulk Assign Role</a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- HR Panel --}}
                    @if (Auth::user()->can('hrEmployeeList', new App\Models\Employee()) || Auth::user()->can('pendingProfile', new App\Models\Employee()) || Auth::user()->can('hrUpdateEmployee', new App\Models\Employee()))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#hr" aria-expanded="false"
                                aria-controls="hr">
                                <i class="icon-columns menu-icon"></i>
                                <span class="menu-title">HR</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="hr">
                                <ul class="nav flex-column sub-menu">

                                    @can('hrEmployeeList', new App\Models\Employee())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('employeeView') }}">Employee List</a></li>
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('employeeDashboard') }}">Employee Dashboard</a></li>


                                            <li class="nav-item"><a class="nav-link"
                                                    href="{{ route('performanceDashboard') }}">Performance Dashboard</a>
                                            </li>

                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('exitList') }}">Exit Employee List</a></li>
                                    @endcan
                                    @can('pendingProfile', new App\Models\Employee())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('pendingProfile') }}">Pending Profiles @if ($commonCount['pendingProfiles'] != 0)<span
                                                        class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                                        style="width:18px; height:18px; font-size:10px;">{{ $commonCount['pendingProfiles'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                    @can('hrUpdateEmployee', new App\Models\Employee())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('draftList') }}">Draft List @if ($commonCount['drafts'] != 0)<span
                                                        class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3"
                                                        style="width:18px; height:18px; font-size:10px;">{{ $commonCount['drafts'] }}</span>
                                                @endif
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- Leave Panel --}}
                    @if (Auth::user()->can('hrEmployeeList', new App\Models\Employee()))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#leave-tab" aria-expanded="false"
                                aria-controls="leave-tab">
                                <i class="icon-clock menu-icon"></i>
                                <span class="menu-title">Leave</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="leave-tab">
                                <ul class="nav flex-column sub-menu">
                                    {{-- @can('hrEmployeeList', new App\Models\Employee()) --}}
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('manual-leave.index') }}">Manual Leave</a></li>
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('hrLeaveList') }}">Leave Requests @if ($commonCount['managerLeaves'] != 0)<span
                                                    class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                                    style="width:18px; height:18px; font-size:10px;">{{ $commonCount['managerLeaves'] }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('leaveBalanceDashboard') }}">Balance Dashboard</a></li>
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('forwardedLeaveList') }}">Forwarded Leaves
                                            @if ($commonCount['forwardedLeaves'] != 0)
                                                <span
                                                    class="badge badge-light text-dark d-flex justify-content-center align-items-center ml-1"
                                                    style="width:18px; height:18px; font-size:10px;">{{ $commonCount['forwardedLeaves'] }}</span>
                                            @endif
                                        </a>
                                    </li>
                                    <li class="nav-item"><a class="nav-link"
                                            href="{{ route('hrLeaveHistory') }}">Leave History</a></li>
                                    {{-- @endcan --}}
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- Attendance Panel --}}
                    @if (Auth::user()->can('create', new App\Models\Attendance()) || Auth::user()->can('dashboard', new App\Models\Attendance()))

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#attendance" aria-expanded="false"
                                aria-controls="attendance">
                                <i class="icon-align-justify menu-icon"></i>
                                <span class="menu-title">Attendance</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="attendance">
                                <ul class="nav flex-column sub-menu">
                                    @can('dashboard', new App\Models\Attendance())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('attendanceDashboard') }}">Dashboard</a></li>

                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('lateAttendanceDashboard') }}">Late
                                                Dashboard</a></li>
                                    @endcan
                                    @can('create', new App\Models\Attendance())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('manual-attendance.create') }}">Manual Attendance
                                            </a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- Onboard Panel --}}
                    @if (Auth::user()->can('view', new App\Models\Interview()) || Auth::user()->can('hrUpdateEmployee', new App\Models\Employee()))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#onboard" aria-expanded="false"
                                aria-controls="onboard">
                                <i class="mdi mdi-onedrive  menu-icon"></i>
                                <span class="menu-title">Onboard</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="onboard">
                                <ul class="nav flex-column sub-menu">
                                    @can('view', new App\Models\Interview())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('interview.index') }}">Interview </a>
                                        </li>
                                    @endcan
                                    @can('hrUpdateEmployee', new App\Models\Employee())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('onboardDashboard') }}">Onboard Dashboard</a>
                                        </li>
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('predetails.index') }}">Pre Details</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcan

                    {{-- Asset Panel --}}
                    @can('assetPermission', new App\User())
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#it" aria-expanded="false"
                                aria-controls="it">
                                <i class="icon-bar-graph menu-icon"></i>
                                <span class="menu-title">Asset</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="it">
                                <ul class="nav flex-column sub-menu">
                                    @can('dashboard', new App\Models\Asset())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('assetDashboard') }}">Asset Dashboard</a>
                                        </li>
                                    @endcan
                                    @can('view', new App\Models\Asset())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('asset.index') }}">Asset</a>
                                        </li>
                                    @endcan
                                    @can('view', new App\Models\AssetCategory())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('asset-category.index') }}">Asset
                                                Category</a>
                                        </li>
                                    @endcan
                                    @can('view', new App\Models\AssetType())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('asset-type.index') }}">Asset Type</a>
                                        </li>
                                    @endcan
                                    @can('view', new App\Models\AssetSubType())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('asset-subtype.index') }}">Asset Sub
                                                Type</a>
                                        </li>
                                    @endcan
                                    @can('assignmentList', new App\Models\Asset())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('assignmentList') }}">Assignment List</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    @endcan

                    {{-- Manager Panel --}}
                    @if (Auth::user()->can('managerLeaveList', new App\Models\Leave()))
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#manager" aria-expanded="false"
                                aria-controls="manager">
                                <i class="icon-grid-2 menu-icon"></i>
                                <span class="menu-title">Manager</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="manager">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('employeeManagerDashboard') }}">Employee Dashboard</a>
                                    </li>
                                    {{-- @can('managerLeaveList', new App\Models\Leave()) --}}
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('managerLeaveList') }}">Leave Requests
                                            @if ($commonCount['departmentLeaves'] != 0)
                                                <span
                                                    class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0"
                                                    style="width:18px; height:18px; font-size:10px;">{{ $commonCount['departmentLeaves'] }}</span>
                                            @endif
                                        </a></li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('managerLeaveHistory') }}">Leave History</a>
                                    </li>
                                    {{-- @endcan --}}

                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('managerEmployeeView') }}">Employee List</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endif

                    {{-- No Dues Requests Panel --}}
                    @canany(['hrNoDuesApprover', 'itNoDuesApprover', 'managerNoDuesApprover'], new
                        App\Models\Employee())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('noDuesRequests') }}">
                                <i class="mdi mdi-clock-alert menu-icon fa-lg"></i>
                                <span class="menu-title">No Dues Requests</span>
                            </a>
                        </li>
                    @endcanany

                    {{-- Employees Panels --}}
                    @if (auth()->user()->hasRole('employee') ||
                        auth()->user()->hasRole('admin') || auth()->user()->hasRole('powerUser'))
                        {{-- My Profile Panel --}}
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#employee" aria-expanded="false"
                                aria-controls="manager">
                                <i class="mdi mdi-account-settings menu-icon"></i>
                                <span class="menu-title">My Profile</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="employee">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('employeeDetail', ['employee' => Auth::user()->employee->id]) }}">Profile</a>
                                    </li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('editProfile', ['employee' => Auth::user()->employee->id]) }}">Edit
                                            Profile</a></li>
                                    <li class="nav-item "> <a class="nav-link"
                                            href="{{ route('leaveList') }}">Leave List</a></li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('createLeave') }}">Apply Leave</a></li>
                                            @if(auth()->user()->hasRole('admin'))
                                    <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('myBalance') }}">My Balance</a></li>
                                                @endif
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('myAttendance') }}">My Attendance</a></li>
                                </ul>
                            </div>
                        </li>

                        {{-- Tickets Panel --}}
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#ticket" aria-expanded="false"
                                aria-controls="ticket">
                                <i class="mdi mdi-ticket menu-icon"></i>
                                <span class="menu-title">Tickets</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="ticket">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('ticketRaiseForm') }}">Open a Ticket</a>
                                    </li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('myTickets') }}">My Tickets</a></li>
                                    @can('ticketAssign', new App\Models\Ticket())
                                        <li class="nav-item"> <a class="nav-link"
                                                style="margin-right: 8%;"
                                                href="{{ route('itRaiseTicket') }}">Opened<span
                                                    class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3"
                                                    style="width:18px; height:18px; font-size:10px;">{{ $commonCount['openedTickets'] }}</span></a>
                                        </li>
                                    @endcan
                                    @can('ticketSolver', new App\Models\Ticket())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('assignedTicket') }}">Assigned</a></li>
                                    @endcan
                                    @can('ticketHistory', new App\Models\Ticket())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('ticketHistory') }}">Ticket History</a></li>
                                    @endcan
                                    @if (auth()->user()->hasRole('admin'))
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('categoryView') }}">Ticket Category</a>
                                        </li>
                                    @endif

                                    @can('powerUser', new App\User())
                                        <li class="nav-item"><a class="nav-link"
                                            href="{{ route('departmentTickets') }}">Department Tickets</a>
                                        </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        {{-- Daily Report Panel --}}
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#daily-report"
                                aria-expanded="false" aria-controls="daily-report">
                                <i class="mdi mdi-book-open-page-variant  menu-icon"></i>
                                <span class="menu-title">Daily Report</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="daily-report">
                                <ul class="nav flex-column sub-menu">
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('dailyReport.form') }}">Submit Report</a>
                                    </li>
                                    <li class="nav-item"> <a class="nav-link"
                                            href="{{ route('dailyReport.myList') }}">My Reports</a>
                                    </li>
                                    @if (auth()->user()->can('hrDashboard', new App\User()) ||
                                        auth()->user()->can('managerDashboard', new App\User()))
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('dailyReport.departmentReports') }}">Department
                                                Reports</a></li>
                                    @endif
                                </ul>
                            </div>
                        </li>

                    @endif

                    {{-- Settings Panel --}}
                    @if(auth()->user()->can('checkPermission', new App\User()) || auth()->user()->can('barcodeListView', new App\Models\Employee()) )
                    {{-- @can('checkPermission', new App\User()) --}}
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="collapse" href="#settings" aria-expanded="false"
                                aria-controls="settings">
                                <i class="icon-cog menu-icon"></i>
                                <span class="menu-title">Settings</span>
                                <i class="menu-arrow"></i>
                            </a>
                            <div class="collapse" id="settings">
                                <ul class="nav flex-column sub-menu">

                                    @can('hrEmployeeList', new App\Models\Employee())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('shift-type.index') }}">Shift Type</a></li>
                                    @endcan
                                    @can('hrUpdateEmployee', new App\Models\Employee())
                                        <li class="nav-item"><a class="nav-link"
                                                href="{{ route('updateEmployeeDepartment') }}">Update
                                                Department</a></li>
                                    @endcan
                                    @can('view', new App\Models\Qualification())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('qualificationView') }}">Qualification</a>
                                        </li>
                                    @endcan

                                    @can('view', new App\Models\Department())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('departmentView') }}">Department</a></li>
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('designation.index') }}">Designation</a>
                                        </li>
                                    @endcan

                                    @can('view', new App\Models\LeaveType())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('leave-type.index') }}">Leave Type</a></li>
                                    @endcan

                                    @can('view', new App\Models\Badge())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('badge.index') }}">Badge</a></li>
                                    @endcan

                                    @can('view', new App\Models\Announcement())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('announcement.index') }}">Announcement</a></li>
                                    @endcan

                                    @can('barcodeListView', new App\Models\Employee())
                                        <li class="nav-item"> <a class="nav-link"
                                                href="{{ route('barcodeList') }}">Barcode List</a></li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                    {{-- @endcan --}}
                    @endif

                    {{-- Activity Logs Panel --}}
                    @can('view', new App\Models\ActivityLog())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('activityLogView') }}">
                                <i class="mdi mdi-bullseye menu-icon fa-lg"></i>
                                <span class="menu-title">Activity Logs</span>
                            </a>
                        </li>
                    @endcan

                    {{-- Laravel Logs Panel --}}
                    @if (in_array(strtolower(auth()->user()->email), App\User::$developers))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('laravelLogs') }}">
                                <i class="mdi mdi-bullseye menu-icon fa-lg"></i>
                                <span class="menu-title">Laravel Logs </span>
                            </a>
                        </li>
                    @endif
            </ul>
        </nav>
        <!-- partial -->
        <div class="main-panel">
            <div class="content-wrapper">

                <div class="container-scroller">
                    <div id="carouselExampleIndicators" class="carousel slide m-2" data-bs-ride="carousel">
                        <div class="carousel-inner" style="border-radius: 5px;">
                            <div class="carousel-itemactive">
                                @if (todayAttendance() &&
                                    !auth()->user()->hasRole('admin'))
                                    <marquee behavior="alternate" scrollAmount="12" style="font-size:28px">
                                        {{ ucwords(todayAttendance()) }}</marquee>
                                @endif
                                @if (departmentAttendance() &&
                                    Route::currentRouteName() != 'lateAttendanceDashboard' &&
                                    auth()->user()->hasRole('manager'))

                                    <marquee behavior="alternate" scrollAmount="12" style="font-size:28px"
                                        class="text-danger">
                                        <a href="{{ route('lateAttendanceDashboard') }}" style="color:red">
                                            {{ departmentAttendance() }}
                                        </a>
                                    </marquee>

                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                @yield('content')
            </div>
            <!-- content-wrapper ends -->
            <!-- partial:partials/_footer.html -->
            <footer class="footer">
                <div class="d-sm-flex justify-content-center justify-content-sm-between">
                    <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">EMS developed in
                        July,2021.</span>

                </div>
            </footer>
            <!-- partial -->
        </div>
        <div class="modal hide fade" id="modal-default" aria-labelledby="ModalLabel" aria-hidden="true"
            role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <h5 class="modal-title" id="chart-heading"></h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div id="modal-data"></div>
                    </form>
                </div>
            </div>
        </div>
        <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
<!-- container-scroller -->
<!-- JQuery -->

<script src="{{ url('js/jquery-3.6.0.min.js') }}"></script>
{{-- <script
      src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
      integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
      crossorigin="anonymous"
    ></script> --}}
<!-- JQuery -->
<script>
    $(document).ready(function() {
        $('#annoucement').on('click', function() {
            $('#settings-trigger').trigger('click');
        });
        $('.selectJS').select2({
            placeholder: "Select an option",
            allowClear: true,
            width: '93%'
        });
        if (sessionStorage.getItem("sidebar")) {
            if (sessionStorage.getItem("sidebar") == 'dark') {
                $('#sidebar-light-theme').removeClass('selected');
                $('body').addClass('sidebar-dark');
            } else {
                $('#sidebar-dark-theme').removeClass('selected');
                $('body').addClass('sidebar-light');
            }
            $('#sidebar-' + sessionStorage.getItem("sidebar") + '-theme').addClass('selected');
        }
        if (sessionStorage.getItem("header")) {
            $('#header').addClass('navbar-' + sessionStorage.getItem("header"));
        }

        setInterval(function checkSession() {
            $.getJSON('/check-session', function(data) {
                if (data.guest) {
                    location.reload(true);
                }
            });
        }, 60000);
    });

    function themeColor(color, section) {
        sessionStorage.setItem(section, color);

    }
    function copyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);

        toastr.success('Link Copied');
    }
</script>

<!-- plugins:js -->
<script src="{{ url('skydash/vendors/js/vendor.bundle.base.js') }}"></script>
<!-- endinject -->
<!-- Plugin js for this page -->
<script src="{{ url('skydash/vendors/chart.js/Chart.min.js') }}"></script>
<script src="{{ url('skydash/vendors/datatables.net/jquery.dataTables.js') }}"></script>
<script src="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<script src="{{ url('skydash/js/dataTables.select.min.js') }}"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{ url('skydash/js/off-canvas.js') }}"></script>
<script src="{{ url('skydash/js/hoverable-collapse.js') }}"></script>
<script src="{{ url('skydash/js/template.js') }}"></script>
<script src="{{ url('skydash/js/settings.js') }}"></script>
<script src="{{ url('skydash/js/todolist.js') }}"></script>
<script src="{{ url('js/bootstrap-datepicker.js') }}"></script>
<script src="{{ url('js/tail.select.js') }}"></script>
<!-- datatables -->
<script src="{{ url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
<!-- datatables -->
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{ url('skydash/js/dashboard.js') }}"></script>
<script src="{{ url('skydash/js/Chart.roundedBarCharts.js') }}"></script>
<!-- End custom js for this page-->
<!-- Plugin js for this page -->
<script src="{{ url('skydash/vendors/typeahead.js/typeahead.bundle.min.js') }}"></script>
<script src="{{ url('skydash/vendors/select2/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ url('js/jsgrid/jsgrid.min.js') }}"></script>
<!-- common.js -->
<script type="text/javascript" src="{{ url('js/common.js') }}"></script>
<script src="{{ url('js/toastr/toastr.min.js') }}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>
<script>
    $(function() {
        @if ($message = Session::get('success'))
            toastr.success('{{ $message }}');
        @endif
        @if ($message = Session::get('failure'))
            toastr.warning('{{ $message }}');
        @endif
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.warning('{{ $error }}');
            @endforeach
        @endif
    });
    tail.select(".tail-select", {
            search: true,
            multiShowCount: true,
            multiSelectAll: true,
            multiPinSelected: false,
            width: '350px',
            placeholder: 'select an option'
        });
    var clearNotification = "{{ route('clearNotification') }}";
    var notification = setInterval(notifications, 20000);
    notifications();

    function notifications() {
        $.ajax({
            url: "{{ route('getNotification') }}",
            type: 'get',
            dataType: 'json',
            success: function(response) {

                var html = "";
                var leaveNotifications = "";
                var leaveCount = 0;
                var notificationCount = 0;
                if (response.count == 0) {
                    html += `<li class="list text-center">
                    <h6>No Notification</h6>
                    </li>`
                    leaveNotifications = html;
                }



                if (response.count >= 1) {
                    html += `<div class="overflow-auto" style="max-height:250px">`;
                    leaveNotifications += `<div class="overflow-auto" style="max-height:250px">`;
                    $.each(response.notifications, function(index, notification) {
                        if (notification.type != 'leave') {
                            notificationCount = notificationCount + parseInt(1);
                            html += `<div onclick="setReadAt('` + notification.id + `','` +
                                notification.link + `')" class="dropdown-item preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div>

                              </div>
                            </div>
                              <div class="preview-item-content">
                              <h6 class="preview-subject font-weight-normal">${notification.message}</h6>
                              <p class="font-weight-light small-text mb-0 text-muted">
                              <small class="pull-right"><i class="mdi mdi-clock"></i> ${notification.time}</small>
                              </p>
                              </div>

                          </div>`
                        } else {
                            leaveCount = leaveCount + parseInt(1);
                            leaveNotifications += `<div onclick="setReadAt('` + notification.id +
                                `','` + notification.link + `')" class="dropdown-item preview-item border-bottom">
                            <div class="preview-thumbnail">
                              <div>

                              </div>
                            </div>
                              <div class="preview-item-content">
                              <h6 class="preview-subject font-weight-normal">${notification.message}</h6>
                              <p class="font-weight-light small-text mb-0 text-muted">
                              <small class="pull-right"><i class="mdi mdi-clock"></i> ${notification.time}</small>
                              </p>
                              </div>

                          </div>`
                        }
                    });
                    if (notificationCount != 0) {
                        if (notificationCount > 1) {
                            html =
                                `<center>You have ${notificationCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>` +
                                html;
                        } else {
                            html =
                                `<center>You have ${notificationCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>` +
                                html;
                        }
                        html += `</div>`;
                    } else {
                        html += `<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`;
                    }
                    if (leaveCount != 0) {
                        if (leaveCount > 1) {
                            leaveNotifications =
                                `<center>You have ${leaveCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>` +
                                leaveNotifications;
                        } else {
                            leaveNotifications =
                                `<center>You have ${leaveCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>` +
                                leaveNotifications;
                        }
                        leaveNotifications += `</div>`;
                    } else {
                        leaveNotifications += `<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`
                    }
                }
                $('#notification').html(html);
                $('#leave-notification').html(leaveNotifications);
                $('#count').html(notificationCount);
                $('#leave-count').html(leaveCount);


            },
            error: function() {
                clearInterval(notification);
            },
        });
    }

    function copyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);

        toastr.success('Link Copied');
    }
    function clearNotifications(type = null) {
        console.log(type);
        $.ajax({
            url: clearNotification,
            type: 'get',
            data: {
                type: type
            },
            success: function(response) {
                location.reload();
            },
            error: function(error) {
                console.log(error);
            },
        });
    }

    function setReadAt(notification_id, link) {

        $.ajax({
            url: "{{ route('notificationReadStatus', '') }}" + "/" + notification_id,
            type: 'get',
            success: function(response) {
                window.location.href = link;
            },

        })
    }

    function deleteItem(path) {
        var sure = confirm('Are you sure?');
        if (!sure) {
            return false;
        }
        $.ajax({
            url: path,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                location.reload();
                toastr.success('Successfully Deleted.');
            },
            error: function(response) {
                if (response.status == '404') {
                    alert("Item not found");
                } else {
                    alert(response.statusText);
                }
            }
        });
        return true;
    }
</script>
@yield('footerScripts')

<script>
    $("#jsGrid").jsGrid({
        pageButtonCount: 4,
        pagerFormat: "Pages: {first} {prev} {pages} {next} {last}    {pageIndex} of {pageCount}",
        pagePrevText: "Prev",
        pageNextText: "Next",
        pageFirstText: "First",
        pageLastText: "Last",
        pageNavigatorNextText: "...",
        pageNavigatorPrevText: "..."
    });

    /* clean submit query by
     * removing empty inputs from url */
    $("form[method='GET']").submit(function() {
        $("input,select").each(function(index, input) {
            if ($(input).val() == "") {
                $(input).attr("name", '');
            }
        });
    });
</script>

<!-- End plugin js for this page -->
</body>

</html>
