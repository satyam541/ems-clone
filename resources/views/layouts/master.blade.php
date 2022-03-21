<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags -->

  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Employee Management System</title>
  <link rel="icon" href="{{url('img/favicon.ico')}}" sizes="16x16">
  <!-- plugins:css -->
  <link rel="stylesheet" href="{{url('skydash/vendors/feather/feather.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/css/vendor.bundle.base.css')}}">
  <!-- endinject -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/ti-icons/css/themify-icons.css')}}">
  <link rel="stylesheet" type="text/css" href="{{url('skydash/js/select.dataTables.min.css')}}">
  <!-- End plugin css for this page -->
  <!-- Plugin css for this page -->
  <link rel="stylesheet" href="{{url('skydash/vendors/select2/select2.min.css')}}">
  <link rel="stylesheet" href="{{url('skydash/vendors/select2-bootstrap-theme/select2-bootstrap.min.css')}}">
  <!-- End plugin css for this page -->
  <!-- inject:css -->
  <link rel="stylesheet" href="{{url('skydash/css/vertical-layout-light/style.css')}}">
  <!-- endinject -->
  <!-- Datatables -->
  <link rel="stylesheet" href="{{url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.css')}}">
  <!-- Datatables -->
  <link rel="stylesheet" href="{{url('skydash/vendors/mdi/css/materialdesignicons.min.css')}}">
  <link type="text/css" rel="stylesheet" href="{{url('js/jsgrid/jsgrid.min.css')}}" />
  <link type="text/css" rel="stylesheet" href="{{url('js/jsgrid/jsgrid-theme.min.css')}}" />
  <link rel="stylesheet" href="{{url('js/toastr/toastr.min.css')}}">
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <style>
    a:hover {
      text-decoration: none;
    }
    .hidden{
      display: none;
    }

   .notification-bell{
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
  </style>
  @yield('headerLinks')
</head>
<body>
  @php $commonCount=commonCount() @endphp
  @if(url('') == 'http://ems.tka-in.com' || url('') == 'https://ems.tka-in.com')
    @php $hidden = 'hidden'; @endphp
  @else
      @php $hidden = ''; @endphp
  @endif
  <div class="container-scroller">
    <!-- partial:partials/_navbar.html -->
    <nav id="header" class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
        <a class="navbar-brand brand-logo mr-5" href="{{route('dashboard')}}">EMS
        <a class="navbar-brand brand-logo-mini" href="{{route('dashboard')}}">EMS</a>
      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
        <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
          <span class="icon-menu"></span>
        </button>
        {{-- <ul class="navbar-nav mr-lg-2">
          <li class="nav-item nav-search d-none d-lg-block">
            <div class="input-group">
              <div class="input-group-prepend hover-cursor" id="navbar-search-icon">
                <span class="input-group-text" id="search">
                  <i class="icon-search"></i>
                </span>
              </div>
              <input type="text" class="form-control" id="navbar-search-input" placeholder="Search now" aria-label="search" aria-describedby="search">
            </div>
          </li>
        </ul> --}}

        <ul class="navbar-nav navbar-nav-right">
            <li class="nav-item dropdown">
                @if (!Session::has('orig_user'))
                 @if(in_array(strtolower(auth()->user()->email), App\User::$developers))
                        <li class="nav-item">
                            <a href="{{route('switchUser')}}" class="nav-link">Switch User</a>
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
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="fas fa-running mx-0"></i>
            </a>
            <span class="badge badge-primary mb-3" id="leave-count">0</span>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="leave-notification" style="min-width: 290px;" aria-labelledby="notificationDropdown">

            </div>
          </li>

          <li class="nav-item dropdown">
            <a class="nav-link count-indicator dropdown-toggle" id="notificationDropdown" href="#" data-toggle="dropdown">
              <i class="icon-bell mx-0"></i>
              <span class="notification-bell " id="count"></span>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown preview-list" id="notification" style="min-width: 290px;" aria-labelledby="notificationDropdown">
              {{-- <p class="mb-0 font-weight-normal float-left dropdown-header">Notifications</p> --}}



            </div>
          </li>
          <li class="nav-item nav-profile dropdown">
            <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
              <img src="{{auth()->user()->employee->getImagePath()}}" alt="{{auth()->user()->name}}"/>
            </a>
            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown">
              <a class="dropdown-item" href="{{route('changePassword')}}">
                <i class="ti-settings text-primary"></i>
                Change Password
              </a>

              <form action="{{route('logout')}}" method="post">
                @csrf
              <button type="submit" class="dropdown-item">
                  <i class="ti-power-off text-primary"></i>
                  Logout

              </button>

            </form>
            </div>
          </li>
          {{-- <li class="nav-item nav-settings d-none d-lg-flex">
            <a class="nav-link" href="#">
              <i class="icon-ellipsis"></i>
            </a>
          </li> --}}
        </ul>
        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
          <span class="icon-menu"></span>
        </button>
      </div>
    </nav>
    <!-- partial -->
    <div class="container-fluid page-body-wrapper">
      <!-- partial:partials/_settings-panel.html -->
       <div class="theme-setting-wrapper">
        <div id="settings-trigger"><i class="ti-settings"></i></div>
        <div id="theme-settings" class="settings-panel">
          <i class="settings-close ti-close"></i>
          <p class="settings-heading">SIDEBAR SKINS</p>
          <div class="sidebar-bg-options selected" id="sidebar-light-theme" onclick="themeColor('light','sidebar')"><div class="img-ss rounded-circle bg-light border mr-3"></div>Light</div>
          <div class="sidebar-bg-options" id="sidebar-dark-theme" onclick="themeColor('dark','sidebar')"><div class="img-ss rounded-circle bg-dark border mr-3"></div>Dark</div>
          <p class="settings-heading mt-2">HEADER SKINS</p>
          <div class="color-tiles mx-0 px-4">
            <div class="tiles success" onclick="themeColor('success','header')"></div>
            <div class="tiles warning" onclick="themeColor('warning','header')"></div>
            <div class="tiles danger"  onclick="themeColor('danger','header')"></div>
            <div class="tiles info"    onclick="themeColor('info','header')"></div>
            <div class="tiles dark"    onclick="themeColor('dark','header')"></div>
            <div class="tiles default" onclick="themeColor('default','header')"></div>
          </div>
        </div>
      </div>
      {{--<div id="right-sidebar" class="settings-panel">
        <i class="settings-close ti-close"></i>
        <ul class="nav nav-tabs border-top" id="setting-panel" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="todo-tab" data-toggle="tab" href="#todo-section" role="tab" aria-controls="todo-section" aria-expanded="true">TO DO LIST</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="chats-tab" data-toggle="tab" href="#chats-section" role="tab" aria-controls="chats-section">CHATS</a>
          </li>
        </ul>
        <div class="tab-content" id="setting-content">
          <div class="tab-pane fade show active scroll-wrapper" id="todo-section" role="tabpanel" aria-labelledby="todo-section">
            <div class="add-items d-flex px-3 mb-0">
              <form class="form w-100">
                <div class="form-group d-flex">
                  <input type="text" class="form-control todo-list-input" placeholder="Add To-do">
                  <button type="submit" class="add btn btn-primary todo-list-add-btn" id="add-task">Add</button>
                </div>
              </form>
            </div>
            <div class="list-wrapper px-3">
              <ul class="d-flex flex-column-reverse todo-list">
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Team review meeting at 3.00 PM
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Prepare for presentation
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li>
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox">
                      Resolve all the low priority tickets due today
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Schedule meeting for next week
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
                <li class="completed">
                  <div class="form-check">
                    <label class="form-check-label">
                      <input class="checkbox" type="checkbox" checked>
                      Project review
                    </label>
                  </div>
                  <i class="remove ti-close"></i>
                </li>
              </ul>
            </div>
            <h4 class="px-3 text-muted mt-5 font-weight-light mb-0">Events</h4>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 11 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Creating component page build a js</p>
              <p class="text-gray mb-0">The total number of sessions</p>
            </div>
            <div class="events pt-4 px-3">
              <div class="wrapper d-flex mb-2">
                <i class="ti-control-record text-primary mr-2"></i>
                <span>Feb 7 2018</span>
              </div>
              <p class="mb-0 font-weight-thin text-gray">Meeting with Alisa</p>
              <p class="text-gray mb-0 ">Call Sarah Graves</p>
            </div>
          </div>
          <!-- To do section tab ends -->
          <div class="tab-pane fade" id="chats-section" role="tabpanel" aria-labelledby="chats-section">
            <div class="d-flex align-items-center justify-content-between border-bottom">
              <p class="settings-heading border-top-0 mb-3 pl-3 pt-0 border-bottom-0 pb-0">Friends</p>
              <small class="settings-heading border-top-0 mb-3 pt-0 border-bottom-0 pb-0 pr-3 font-weight-normal">See All</small>
            </div>
            <ul class="chat-list">
              <li class="list active">
                <div class="profile"><img src="{{url('skydash/images/faces/face1.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Thomas Douglas</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">19 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{url('skydash/images/faces/face2.jpg')}}" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <div class="wrapper d-flex">
                    <p>Catherine</p>
                  </div>
                  <p>Away</p>
                </div>
                <div class="badge badge-success badge-pill my-auto mx-2">4</div>
                <small class="text-muted my-auto">23 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{url('skydash/images/faces/face3.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Daniel Russell</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">14 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{url('skydash/images/faces/face4.jpg')}}" alt="image"><span class="offline"></span></div>
                <div class="info">
                  <p>James Richardson</p>
                  <p>Away</p>
                </div>
                <small class="text-muted my-auto">2 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{url('skydash/images/faces/face5.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Madeline Kennedy</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">5 min</small>
              </li>
              <li class="list">
                <div class="profile"><img src="{{url('skydash/images/faces/face6.jpg')}}" alt="image"><span class="online"></span></div>
                <div class="info">
                  <p>Sarah Graves</p>
                  <p>Available</p>
                </div>
                <small class="text-muted my-auto">47 min</small>
              </li>
            </ul>
          </div>
          <!-- chat tab ends -->
        </div>
      </div> --}}
      <!-- partial -->
      <!-- partial:partials/_sidebar.html -->
      <nav class="sidebar sidebar-offcanvas" id="sidebar">
        <ul class="nav">
          <li class="nav-item">
            <a style="cursor: default;" href="javascript:void(0)" class="nav-link">
              <i ><img src="{{auth()->user()->employee->getImagePath()}}" alt="{{auth()->user()->name}}" width="40" height="40" style="border-radius: 100%;"/></i>
              <span class="menu-title" style="padding-left:10px;">{{ucfirst(auth()->user()->name)}}</span>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="{{route('dashboard')}}">
              <i class="icon-grid menu-icon"></i>
              <span class="menu-title">Dashboard</span>
            </a>
          </li>
          @if (Auth::user()->can('view', new App\User()) || Auth::user()->can('view', new App\Models\Role()) || Auth::user()->can('view', new App\Models\Permission()) || Auth::user()->can('view', new App\Models\Module())  || Auth::user()->can('view', new App\Models\Department()))
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#admin" aria-expanded="false" aria-controls="admin">
              <i class="icon-head menu-icon"></i>
              <span class="menu-title">Admin</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="admin">
              <ul class="nav flex-column sub-menu">
                @can('view', new App\User())
                <li class="nav-item"> <a class="nav-link" href="{{route('userView')}}">User</a></li>
                @endcan
                @can('view', new App\Models\Role())
                <li class="nav-item"> <a class="nav-link" href="{{route('roleView')}}">Role</a></li>
                @endcan
                @can('view', new App\Models\Permission())
                <li class="nav-item"> <a class="nav-link" href="{{route('permissionView')}}">Permission</a></li>
                @endcan
                @can('view', new App\Models\Module())
                <li class="nav-item"> <a class="nav-link" href="{{route('moduleView')}}">Module</a></li>
                @endcan
                @can('view', new App\Models\Qualification())
                <li class="nav-item"> <a class="nav-link" href="{{route('qualificationView')}}">Qualification</a></li>
                @endcan

                @can('view', new App\Models\Department())
                <li class="nav-item"> <a class="nav-link" href="{{route('departmentView')}}">Department</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('designation.index')}}">Designation</a></li>
                @endcan
              </ul>
            </div>
          </li>
          @endcan
          @if (Auth::user()->can('hrEmployeeList', new App\Models\Employee()) || Auth::user()->can('hrNoDuesApprover',new App\Models\Employee()) || Auth::user()->can('interviewee', new App\Models\Interviewee()) || Auth::user()->can('hrView', new App\Models\Attendance())  || Auth::user()->can('import', new App\Models\Attendance()))
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#hr" aria-expanded="false" aria-controls="hr">
              <i class="icon-columns menu-icon"></i>
              <span class="menu-title">HR</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="hr">
              <ul class="nav flex-column sub-menu">

                 @can('hrEmployeeList', new App\Models\Employee())
                 <li class="nav-item"><a class="nav-link" href="{{route('employeeView')}}">Employee List</a></li>
                 <li class="nav-item"><a class="nav-link" href="{{route('exitList')}}">Exit Employee List</a></li>
                 @endcan
                 {{-- <li class="nav-item  {{$hidden}}"><a class="nav-link" href="{{route('createJobLink')}}">Interviewee Register</a></li> --}}
                {{-- <li class="nav-item  {{$hidden}}"><a class="nav-link" href="{{route('intervieweeView')}}">Interviewee All</a></li> --}}
                {{-- <li class="nav-item  {{$hidden}}"><a class="nav-link" href="{{route('attendanceView')}}">Attendance List</a></li> --}}
                {{-- <li class="nav-item  {{$hidden}}"><a class="nav-link" href="{{route('attendanceUpload')}}">Upload Attendance</a></li> --}}
                @can('pendingProfile', new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('pendingProfile')}}">Pending Profiles @if($commonCount['pendingProfiles']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0" style="width:18px; height:18px; font-size:10px;">{{$commonCount['pendingProfiles']}}</span>@endif</a></li>
                @endcan
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('draftList')}}">Draft List @if($commonCount['drafts']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3" style="width:18px; height:18px; font-size:10px;">{{$commonCount['drafts']}}</span>@endif</a></li>
                @endcan
                {{-- @can('hrNoDuesApprover',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('noDuesRequests')}}">No Dues Requests</a></li>
                @endcan --}}
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('updateEmployeeDepartment')}}">Update Department</a></li>
                @endcan
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"> <a class="nav-link" href="{{route('officeEmailView')}}">Email Assign</a></li>
                 @endcan
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('hrLeaveList')}}">Leave Requests @if($commonCount['managerLeaves']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0" style="width:18px; height:18px; font-size:10px;">{{$commonCount['managerLeaves']}}</span>@endif</a></li>
                @endcan
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('forwardedLeaveList')}}">Forwarded Leaves @if($commonCount['forwardedLeaves']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center ml-1" style="width:18px; height:18px; font-size:10px;">{{$commonCount['forwardedLeaves']}}</span>@endif</a></li>
                @endcan
                @can('hrUpdateEmployee',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('hrLeaveHistory')}}">Leave History</a></li>
                @endcan



                {{-- @can('hr',auth()->user())
                <li class="nav-item {{$hidden}}"><a class="nav-link" href="{{route('hr.stockList')}}">Stocks</a></li>
                @endcan
                @can('hr',auth()->user())
                <li class="nav-item {{$hidden}}"><a class="nav-link" href="{{route('hrQuotationList')}}">Quotations<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3" style="width:18px; height:18px; font-size:10px;">{{$commonCount['quotations']}}</span></a></li>
                @endcan --}}
              </ul>
            </div>
          </li>
          @endcan
          @if (Auth::user()->can('itEntityRequestList', new App\Models\EquipmentRequests()) || Auth::user()->can('itNoDuesApprover',new App\Models\Employee()) || Auth::user()->can('it', new App\Models\Equipment()) )
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#it" aria-expanded="false" aria-controls="it">
              <i class="icon-bar-graph menu-icon"></i>
              <span class="menu-title">IT</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="it">
              <ul class="nav flex-column sub-menu">
                {{-- <li class="nav-item"> <a class="nav-link" href="{{route('entityView')}}">Entity List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewEntityRequest')}}">Requested Equipments</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('equipmentView')}}">Equipment List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('createEquipment')}}">Equipment Add</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('equipmentImportView')}}">Equipment Import</a></li> --}}
                {{-- <li class="nav-item  {{$hidden}}"> <a class="nav-link" href="{{route('itemRequestAssign')}}">Equipment Assign</a></li> --}}
                {{-- @can('view',new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('stockList')}}">Stocks</a></li>
                @endcan --}}
                @can('it', new App\Models\Equipment())
                <li class="nav-item"> <a class="nav-link" href="{{route('officeEmailView')}}">Email Assign</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('editEmail')}}">Update Email</a></li>
                @endcan
                {{-- @can('view', new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('itemList')}}">Equipment Type</a></li>
                @endcan
                @can('equipmentAssign', new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('employeeEquipmentList')}}">Equipment Assign</a></li>
                @endcan --}}
                {{-- @can('itNoDuesApprover', new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('noDuesRequests')}}">No Dues Requests</a></li>
                @endcan --}}
                {{-- @can('quotationView',new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('quotationList')}}">My Quotations</a></li>
                @endcan --}}
                {{-- <li class="nav-item"> <a class="nav-link" href="{{route('AllotedOfficeEmail')}}">Email Update</a></li> --}}
              </ul>
            </div>
          </li>
          @endif
          @if(auth()->user()->hasRole('stockManager') || auth()->user()->can('hr',new App\User()))
          <li class="nav-item {{$hidden}}">
            <a class="nav-link" data-toggle="collapse" href="#stock" aria-expanded="false" aria-controls="stocks">
              <i class="mdi mdi-dropbox menu-icon"></i>
              <span class="menu-title">Stocks</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="stock">
              <ul class="nav flex-column sub-menu">
                @can('hr',auth()->user())
                <li class="nav-item"> <a class="nav-link" href="{{route('hr.stockList')}}">HR Stocks</a></li>
                @endcan
                @can('hr',auth()->user())
                <li class="nav-item {{$hidden}}"><a class="nav-link" href="{{route('hr.quotationList')}}">Quotations @if($commonCount['quotations']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3" style="width:18px; height:18px; font-size:10px;">{{$commonCount['quotations']}}</span>@endif</a></li>
                @endcan
                @can('view',new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('stockList')}}">IT Stocks</a></li>
                @endcan
                @can('view', new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('itemList')}}">Equipment Type</a></li>
                @endcan
                @can('equipmentAssign', new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('employeeEquipmentList')}}">Equipment Assign</a></li>
                @endcan
                @can('quotationView',new App\Models\Stock())
                <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('quotationList')}}">My Quotations</a></li>
                @endcan
              </ul>
            </div>
          </li>
          @endif
          @if (Auth::user()->can('managerLeaveList', new App\Models\Leave()) || Auth::user()->can('managerNoDuesApprover',new App\Models\Employee()))
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#manager" aria-expanded="false" aria-controls="manager">
              <i class="icon-grid-2 menu-icon"></i>
              <span class="menu-title">Manager</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="manager">
              <ul class="nav flex-column sub-menu">
                @can('managerLeaveList',new App\Models\Leave())
                <li class="nav-item"> <a class="nav-link" href="{{route('managerLeaveList')}}">Leave Requests @if($commonCount['departmentLeaves']!=0)<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0" style="width:18px; height:18px; font-size:10px;">{{$commonCount['departmentLeaves']}}</span>@endif</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('managerLeaveHistory')}}">Leave History</a></li>
                @endcan
                {{-- <li class="nav-item  {{$hidden}}"> <a class="nav-link" href="{{route('managerItemRequests')}}">Equipment Requests</a></li> --}}
                <li class="nav-item"> <a class="nav-link" href="{{route('managerEmployeeView')}}">Employee List</a></li>

                {{-- <li class="nav-item {{$hidden}}"> <a class="nav-link" href="{{route('managerAttendanceView')}}">Attendance List</a></li> --}}
                {{-- <li class="nav-item"> <a class="nav-link" href="{{route('managerDepartmentEquipment')}}">Equipment List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('requestEquipment')}}">Equipment Requests</a></li> --}}
                {{-- @can('managerNoDuesApprover',new App\Models\Employee())
                <li class="nav-item"><a class="nav-link" href="{{route('noDuesRequests')}}">No Dues Requests</a></li>
                @endcan --}}


               </ul>
            </div>
          </li>
          @endif
          @canany(['hrNoDuesApprover','itNoDuesApprover','managerNoDuesApprover'], new App\Models\Employee())
          <li class="nav-item">
            <a class="nav-link" href="{{route('noDuesRequests')}}">
              <i class="mdi mdi-clock-alert menu-icon fa-lg"></i>
              <span class="menu-title">No Dues Requests</span>
            </a>
          </li>
          @endcanany
          @if(auth()->user()->hasRole('employee') || auth()->user()->hasRole('admin'))
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#employee" aria-expanded="false" aria-controls="manager">
              <i class="mdi mdi-account-settings menu-icon"></i>
              <span class="menu-title">My Profile</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="employee">
              <ul class="nav flex-column sub-menu">
                {{-- <li class="nav-item  {{$hidden}}"> <a class="nav-link" href="{{route('employeeAttendance',['employee'=>Auth::user()->employee->id])}}">Attendance Record</a></li> --}}
                <li class="nav-item"> <a class="nav-link" href="{{route('employeeDetail',['employee'=>Auth::user()->employee->id])}}">Profile</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('editProfile',['employee'=>Auth::user()->employee->id])}}">Edit Profile</a></li>
                <li class="nav-item "> <a class="nav-link" href="{{route('leaveList')}}">Leave List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('createLeave')}}">Apply Leave</a></li>
              </ul>
            </div>
          </li>
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#ticket" aria-expanded="false" aria-controls="ticket">
              <i class="mdi mdi-ticket menu-icon"></i>
              <span class="menu-title">Tickets</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ticket">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{route('ticketRaiseForm')}}">Open a Ticket</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('myTickets')}}">My Tickets</a></li>
                @can('ticketAssign',new App\Models\Ticket())
                <li class="nav-item"> <a class="nav-link" style="margin-right: 8%;" href="{{route('itRaiseTicket')}}">Opened<span class="badge badge-light text-dark d-flex justify-content-center align-items-center p-0 ml-3" style="width:18px; height:18px; font-size:10px;">{{$commonCount['openedTickets']}}</span></a></li>
                @endcan
                @can('ticketSolver',new App\Models\Ticket())
                <li class="nav-item"> <a class="nav-link" href="{{route('assignedTicket')}}">Assigned</a></li>
                @endcan
                @can('ticketHistory',new App\Models\Ticket())
                <li class="nav-item"><a class="nav-link" href="{{route('ticketHistory')}}">Ticket History</a></li>
                @endcan
                @if(auth()->user()->hasRole('admin'))
                <li class="nav-item"><a class="nav-link" href="{{route('categoryView')}}">Ticket Category</a></li>
                @endif
              </ul>
            </div>
          </li>

          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#daily-report" aria-expanded="false" aria-controls="daily-report">
              <i class="mdi mdi-book-open-page-variant  menu-icon"></i>
              <span class="menu-title">Daily Report</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="daily-report">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{route('dailyReport.form')}}">Submit Report</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('dailyReport.myList')}}">My Reports</a></li>
                @if(auth()->user()->can('hrDashboard', new App\User()) || auth()->user()->can('managerDashboard', new App\User()))
                <li class="nav-item"> <a class="nav-link" href="{{route('dailyReport.departmentReports')}}">Department Reports</a></li>
                @endif
              </ul>
            </div>
          </li>
          @canany(['submit','view'],new App\Models\RemoteAttendance())
          <li class="nav-item">
            <a class="nav-link" data-toggle="collapse" href="#attendance" aria-expanded="false" aria-controls="attendance">
              <i class="mdi mdi-account-check  menu-icon"></i>
              <span class="menu-title">Attendance</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="attendance">
              <ul class="nav flex-column sub-menu">
                @can('submit',new App\Models\RemoteAttendance())
                <li class="nav-item"><a class="nav-link" href="{{route('attendanceForm')}}">Submit Attendance</a></li>
                @endcan
                @can('view',new App\Models\RemoteAttendance())
                <li class="nav-item"><a class="nav-link" href="{{route('attendanceView')}}">Attendance List</a></li>
                @endcan
              </ul>
            </div>
          </li>
          @endcanany
        @endif


          {{-- <li class="nav-item {{$hidden}}">
            <a class="nav-link" data-toggle="collapse" href="#trash" aria-expanded="false" aria-controls="trash">
              <i class="mdi mdi-delete-forever menu-icon"></i>
              <span class="menu-title">Trash</span>
              <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="trash">
              <ul class="nav flex-column sub-menu">
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashEmployee')}}">Employee:Trash List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashEquipment')}}">Equipment:Trash List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashDepartment')}}">Department:Trash List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashRole')}}">Role:Trash List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashPermission')}}">Permission:Trash List</a></li>
                <li class="nav-item"> <a class="nav-link" href="{{route('viewTrashModule')}}">Module:Trash List</a></li>
              </ul>
            </div>
          </li> --}}
          @can('view', new App\Models\ActivityLog())
          <li class="nav-item">
            <a class="nav-link" href="{{route('activityLogView')}}">
              <i class="mdi mdi-bullseye menu-icon fa-lg"></i>
              <span class="menu-title">Activity Logs</span>
            </a>
          </li>
          @endcan
          @if(in_array(strtolower(auth()->user()->email), App\User::$developers))
          <li class="nav-item">
            <a class="nav-link" href="{{route('laravelLogs')}}">
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
        @yield('content')
        </div>
        <!-- content-wrapper ends -->
        <!-- partial:partials/_footer.html -->
        <footer class="footer">
          <div class="d-sm-flex justify-content-center justify-content-sm-between">
            <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">EMS developed in July,2021.</span>

          </div>
        </footer>
        <!-- partial -->
      </div>
      <!-- main-panel ends -->
    </div>
    <!-- page-body-wrapper ends -->
  </div>
  <!-- container-scroller -->
  <!-- JQuery -->
  <script src="{{url('js/jquery-3.6.0.min.js')}}"></script>
  <!-- JQuery -->
  <script>

  $(document).ready(function(){

    $('.selectJS').select2({
      placeholder: "Select an option",
      allowClear: false,
      width: '93%'
    });
    if(sessionStorage.getItem("sidebar"))
    {
      if(sessionStorage.getItem("sidebar")=='dark')
      {
        $('#sidebar-light-theme').removeClass('selected');
        $('body').addClass('sidebar-dark');
      }
      else{
        $('#sidebar-dark-theme').removeClass('selected');
        $('body').addClass('sidebar-light');
      }
     $('#sidebar-'+sessionStorage.getItem("sidebar")+'-theme').addClass('selected');
    }
    if(sessionStorage.getItem("header"))
    {
      $('#header').addClass('navbar-'+sessionStorage.getItem("header"));
    }

    setInterval(function checkSession() {
                $.getJSON('/check-session', function(data) {
                    if (data.guest) {
                        location.reload(true);
                    }
                });
            }, 60000);
  });
  function themeColor(color,section)
  {
      sessionStorage.setItem(section, color);

  }
  </script>

  <!-- plugins:js -->
  <script src="{{url('skydash/vendors/js/vendor.bundle.base.js')}}"></script>
  <!-- endinject -->
  <!-- Plugin js for this page -->
  <script src="{{url('skydash/vendors/chart.js/Chart.min.js')}}"></script>
  <script src="{{url('skydash/vendors/datatables.net/jquery.dataTables.js')}}"></script>
  <script src="{{url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js')}}"></script>
  <script src="{{url('skydash/js/dataTables.select.min.js')}}"></script>

  <!-- End plugin js for this page -->
  <!-- inject:js -->
  <script src="{{url('skydash/js/off-canvas.js')}}"></script>
  <script src="{{url('skydash/js/hoverable-collapse.js')}}"></script>
  <script src="{{url('skydash/js/template.js')}}"></script>
  <script src="{{url('skydash/js/settings.js')}}"></script>
  <script src="{{url('skydash/js/todolist.js')}}"></script>
  <script src="{{url('js/bootstrap-datepicker.js')}}"></script>
  <!-- datatables -->
  <script src="{{url('skydash/vendors/datatables.net-bs4/dataTables.bootstrap4.js')}}"></script>
  <!-- datatables -->
  <!-- endinject -->
  <!-- Custom js for this page-->
  <script src="{{url('skydash/js/dashboard.js')}}"></script>
  <script src="{{url('skydash/js/Chart.roundedBarCharts.js')}}"></script>
  <!-- End custom js for this page-->
  <!-- Plugin js for this page -->
  <script src="{{url('skydash/vendors/typeahead.js/typeahead.bundle.min.js')}}"></script>
  <script src="{{url('skydash/vendors/select2/select2.min.js')}}"></script>
  <script type="text/javascript" src="{{url('js/jsgrid/jsgrid.min.js')}}"></script>
  <!-- common.js -->
  <script type="text/javascript" src="{{url('js/common.js')}}"></script>
  <script src="{{url('js/toastr/toastr.min.js')}}"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script>
  $(function () {
    @if($message = Session::get('success'))
    toastr.success('{{$message}}');
    @endif
    @if($message = Session::get('failure'))
    toastr.warning('{{$message}}');
    @endif
    @if($errors -> any())
    @foreach($errors -> all() as $error)
    toastr.warning('{{$error}}');
    @endforeach
    @endif
});

        var clearNotification = "{{route('clearNotification')}}";
        var notification = setInterval(notifications, 20000);
        notifications();
        function notifications() {
            $.ajax({
                url: "{{route('getNotification')}}",
                type: 'get',
                dataType: 'json',
                success: function (response) {

                  var html="";
                  var leaveNotifications="";
                  var leaveCount=0;
                  var notificationCount=0;
                  if (response.count == 0)
                  {
                    html+=`<li class="list text-center">
                    <h6>No Notification</h6>
                    </li>`
                    leaveNotifications=html;
                  }



                  if (response.count >= 1) {
                      html += `<div class="overflow-auto" style="max-height:250px">`;
                      leaveNotifications += `<div class="overflow-auto" style="max-height:250px">`;
                        $.each(response.notifications, function (index, notification) {
                            if(notification.type!='leave')
                            {
                              notificationCount=notificationCount + parseInt(1);
                            html+=`<div onclick="setReadAt('`+notification.id+`','`+notification.link+`')" class="dropdown-item preview-item border-bottom">
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
                          else
                          {
                            leaveCount=leaveCount + parseInt(1);
                            leaveNotifications+=`<div onclick="setReadAt('`+notification.id+`','`+notification.link+`')" class="dropdown-item preview-item border-bottom">
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
                        if(notificationCount!=0)
                        {
                          if(notificationCount>1)
                          {
                            html=`<center>You have ${notificationCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>`+html;
                          }
                          else{
                            html=`<center>You have ${notificationCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications()">X</i>`+html;
                          }
                          html += `</div>`;
                        }
                        else{
                          html+=`<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`;
                        }
                        if(leaveCount!=0)
                        {
                          if(leaveCount>1)
                          {
                            leaveNotifications=`<center>You have ${leaveCount} new notifications</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>`+leaveNotifications;
                          }
                          else
                          {
                            leaveNotifications=`<center>You have ${leaveCount} new notification</center><i class="btn btn-primary btn-xs" style="position:relative;left:89%;bottom:30px" onclick="clearNotifications('leave')">X</i>`+leaveNotifications;
                          }
                          leaveNotifications += `</div>`;
                        }
                        else{
                          leaveNotifications+=`<li class="list text-center">
                          <h6>No Notification</h6>
                          </li>`
                        }
                    }
                        $('#notification').html(html);
                        $('#leave-notification').html(leaveNotifications);
                        $('#count').html(notificationCount);
                        $('#leave-count').html(leaveCount);


                },
                error: function () {
                    clearInterval(notification);
                },
            });
        }
        function clearNotifications(type=null)
        {
          console.log(type);
          $.ajax({
                url: clearNotification,
                type: 'get',
                data:{type:type},
                success: function (response) {
                  location.reload();
                },
                error: function (error) {
                    console.log(error);
                },
            });
        }
        function setReadAt(notification_id,link)
        {

          $.ajax({
            url:"{{route('notificationReadStatus', '')}}"+"/"+notification_id,
            type:'get',
            success:function(response) {
              window.location.href=link;
            },

          })
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
$("form[method='GET']").submit(function(){
    $("input,select").each(function(index, input){
        if($(input).val() == "") {
            $(input).attr("name", '');
        }
    });
});
</script>

  <!-- End plugin js for this page -->
</body>

</html>

