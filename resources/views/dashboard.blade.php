@extends('layouts.master')
@section('headerLinks')
<style>
  table.dataTable thead .sorting_asc
{
background-image:none!important;
}
</style>
@endsection
@section('content')

    {{-- <div class="row">
    <div class="col-md-12 grid-margin">
      <div class="row">
        <div class="col-12 col-xl-8 mb-4 mb-xl-0">
          <h3 class="font-weight-bold">Welcome <span class="text-primary">{{auth()->user()->name}}</span></h3>
        </div>
        <div class="col-12 col-xl-4">
         <div class="justify-content-end d-flex">
          <div class="dropdown flex-md-grow-1 flex-xl-grow-0">
            <button class="btn btn-sm btn-light bg-white dropdown-toggle" type="button" id="dropdownMenuDate2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
             <i class="mdi mdi-calendar"></i> Today (10 Jan 2021)
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuDate2">
              <a class="dropdown-item" href="#">January - March</a>
              <a class="dropdown-item" href="#">March - June</a>
              <a class="dropdown-item" href="#">June - August</a>
              <a class="dropdown-item" href="#">August - November</a>
            </div>
          </div>
         </div>
        </div>
      </div>
    </div>
</div> --}}

    <div class="row">
        <div class="col-md-12 grid-margin transparent">
            <div class="row">
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                          <a style="color: white;" href="{{route('employeeView')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Total Employee</p>
                                <p class="fa-3x mb-2">{{ $hr['employeeCount'] }}</p>
                            </div>
                          </a>
                        </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-danger">
                          <a style="color: white;" href="{{route('hr.adminList')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account"></i> Admin</p>
                                <p class="fa-3x mb-2">{{ $hr['admins'] }}</p>
                            </div>
                          </a>
                        </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                  <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-dark-blue">
                        <a style="color: white;" href="{{route('exitList')}}">
                          <div class="card-body">
                              <p class="mb-4"><i class="mdi mdi-account-multiple"></i> Exit Employee</p>
                              <p class="fa-3x mb-2">{{ $hr['in_active'] }}</p>
                          </div>
                        </a>
                      </div>
                  </div>
                @endcan

                @can('leaveDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent" id="leave">
                        <div class="card card-tale">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-walk"></i> Leaves</p>
                                <p class="fa-3x mb-2">{{count($leaveDashboard['leaves'])}}</p>
                            </div>
                          </a>
                        </div>
                    </div>
                @endcan
                {{-- @can('managerDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-alert-octagon"></i> Pending Entity Request</p>
                                <p class="fa-3x mb-2">{{ $manager['entityRequestCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('itDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-television-guide"></i> Entity</p>
                                <p class="fa-3x mb-2">{{ $it['entityCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-danger">
                          <a style="color: white;" href="{{route('hr.departmentList')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-briefcase"></i> Departments</p>
                                <p class="fa-3x mb-2">{{ $hr['department'] }}</p>

                            </div>
                          </a>
                        </div>
                    </div>
                @endcan
                {{-- @can('hrDashboard', new App\User())
                  <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-dark-blue">
                          <div class="card-body">
                              <p class="mb-4"><i class="mdi mdi-account-multiple"></i> Pending Interviews</p>
                              <p class="fa-3x mb-2">{{ $hr['intervieweeCount'] }}</p>

                          </div>
                      </div>
                  </div>
                @endcan --}}
                {{-- @can('itDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-laptop-windows"></i> Equipment</p>
                                <p class="fa-3x mb-2">{{ $it['equipmentCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}
                {{-- @can('managerDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-light-danger">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-human-greeting"></i> Entity Request</p>
                                <p class="fa-3x mb-2">{{ $manager['entityRequestCount'] }}</p>

                            </div>
                        </div>
                    </div>
                @endcan --}}

                {{-- @can('itDashboard', new App\User())
                  <div class="col-md-3 mb-4 stretch-card transparent">
                      <div class="card card-tale">
                          <div class="card-body">
                              <p class="mb-4"><i class="mdi mdi-account"></i> Entity Request: All</p>
                              <p class="fa-3x mb-2">{{ $it['entityRequestCount'] }}</p>
                          </div>
                      </div>
                  </div>
                @endcan --}}
                {{-- @can('employeeDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-account-multiple"></i> Present in {{$employees['previous_month']}}</p>
                                <p class="fa-3x mb-2">{{$employees['present']}}</p>

                            </div>
                        </div>
                    </div>
                @endcan--}}
                @can('ticketAssign',new App\Models\Ticket())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-tale">
                          <a style="color: white;" href="{{route('itRaiseTicket')}}">
                            <div class="card-body">
                                <p class="mb-4"><i class="mdi mdi-ticket"></i> Opened Tickets</p>
                                <p class="fa-3x mb-2">{{$it['ticketCount']}}</p>

                            </div>
                        </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                    <div class="col-md-3 mb-4 stretch-card transparent">
                        <div class="card card-dark-blue">
                          <a style="color: white;" href="{{route('pendingProfile')}}">
                            <div class="card-body">
                              <p class="mb-4"><i class="mdi mdi-alert-octagon"></i> Pending Employee's Profile</p>
                              <p class="fa-3x mb-2">{{$hr['profilesPendingCount']}}</p>
                            </div>
                          </a>
                      </div>
                    </div>
                @endcan
                @can('hrDashboard', new App\User())
                <div class="col-md-3 mb-4 stretch-card transparent">
                    <div class="card card-dark-blue">
                      <a style="color: white;" href="{{route('recent-joined')}}">
                        <div class="card-body">
                          <p class="mb-4"><i class="fa fa-user-circle"></i> Recently Joined</p>
                          <p class="fa-3x mb-2">{{$recentJoining}}</p>
                        </div>
                      </a>
                  </div>
                </div>
                @endcan

                @if(auth()->user()->hasRole('employee'))
                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-light-blue">
                    <a style="color: white;" href="{{route('leaveList',['dateFrom'=>$start,'dateTo'=>$end])}}">
                      <div class="card-body">
                        <p class="mb-4"><i class="mdi mdi-walk"></i> My Leaves ({{Carbon\Carbon::now()->format('F')}})</p>
                        <p class="fa-3x mb-2">{{$myLeaveDashboard['totalleaves']}} </p>
                      </div>
                    </a>
                  </div>

                </div>

                <div class="col-md-3 mb-4 stretch-card transparent">
                  <div class="card card-dark-blue">
                    <a style="color: white;" href="{{route('myTickets')}}">
                      <div class="card-body">
                        <p class="mb-4"><i class="mdi mdi-ticket"></i> My Opened Tickets </p>
                        <p class="fa-3x mb-2">{{$employees['ticketCount']}} </p>
                      </div>
                    </a>
                  </div>
                </div>
                @endif
          </div>


        </div>
    </div>

    <div class="row">
      {{-- @can('hrDashboard', new App\User()) --}}
      {{-- <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Employees - Departments</h4>
            <canvas id="lineChart"></canvas>
          </div>
        </div>
      </div> --}}
      {{-- @endcan --}}
      {{-- @can('hrDashboard', new App\User()) --}}
      {{-- <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Employees - Departments</h4>
            <canvas id="barChart"></canvas>
          </div>
        </div>
      </div> --}}
      {{-- @endcan
      @can('hrDashboard', new App\User()) --}}
      {{-- <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Recent Employees</h4>

            <div class="table-responsive overflow-auto" style="min-height:400px;max-height:400px">
              <table class="table table-hover">
                <thead>
                </thead>
                <tbody>
                  @foreach($hr['employees'] as $employee)
                  <tr>
                    <td onclick='location.href="{{route('employeeDetail', ['employee' => $employee->id])}}"'>{{ $employee->name }}</td>

                    <td><label class="badge badge-danger">{{ $employee->department->name }}</label></td>
                  </tr>
                  @endforeach

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> --}}

      {{-- <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Mangers List</h4>

            <div class="table-responsive overflow-auto" style="min-height:400px;max-height:400px">
              <table class="table table-hover">
                <thead>
                </thead>
                <tbody>
                  @foreach($hr['managers'] as $manager)
                  <tr><td onclick='location.href="{{route('employeeDetail', ['employee' => $manager->id])}}"'>{{ $manager->name }}</td>

                    <td><label class="badge badge-danger">{{ $manager->department->name }}</label></td>
                  </tr>
                  @endforeach

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> --}}
      {{-- @endcan --}}
      {{-- @can('itDashboard', new App\User()) --}}
        {{-- <div class="col-lg-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Email Assign</h4>

              <div class="table-responsive overflow-auto" style="min-height:400px;max-height:400px">
                <table class="table table-hover">
                  <thead>
                  </thead>
                  <tbody>
                    @foreach($it['emailAssign'] as $user)
                    <tr>
                      <td onclick='location.href="{{route('employeeDetail', ['employee' => $user->employee->id])}}"'>{{ $user->employee->name ?? null }}</td>

                      <td><label class="badge badge-danger">{{ date('d-m-Y g:i:a', strtotime($user->created_at)) }}</label></td>
                    </tr>
                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div> --}}
      {{-- @endcan --}}

      {{-- @can('itDashboard', new App\User()) --}}
      {{-- <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Email Update</h4>

            <div class="table-responsive overflow-auto" style="min-height:400px;max-height:400px">
              <table class="table table-hover">
                <thead>
                </thead>
                <tbody>
                  @foreach($it['emailAssign'] as $user)
                  <tr>
                    <td onclick='location.href="{{route('employeeDetail', ['employee' => $user->employee->id])}}"'>{{ $user->employee->name  ?? null }}</td>

                    <td><label class="badge badge-danger">{{ date('d-m-Y g:i:a', strtotime($user->updated_at)) }}</label></td>
                  </tr>
                  @endforeach

                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div> --}}
      {{-- @endcan
      @can('itDashboard', new App\User()) --}}
      {{-- @if(!$logs->isEmpty())
        <div class="col-lg-6 grid-margin stretch-card">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title">Activity Logs</h4>

              <div class="table-responsive overflow-auto" style="min-height:400px;max-height:400px">
                <table class="table">
                  <thead>

                  </thead>
                  <tbody>
                    @foreach($logs as $log)
                    <tr>
                      <td>{{$log->action}}</td>
                      <td>

                        {{ date('d-m-Y ', strtotime($log->date)) }}
                      </td>

                    </tr>
                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div>
      </div>
      @endif --}}
      {{-- @endcan --}}
    </div>
    @can('leaveDashboard', new App\User())
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card">
          <div class="card-body">
            <div class="row mb-2">
              <div class=" col-md-6 col-sm-6">
                <h4 class="">Employees on leave</h4>
              </div>
            </div>

            {{-- <div class="card-title">
              <h4>Employees on leave</h4>
              <input id="search" type="text" placeholder="Search.." class="float-right">
              <br>
            </div> --}}
            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="display expandable-table" id="leave-list" style="width:100%">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Department</th>
                        <th>Leave Type</th>
                        <th>Timing</th>
                      </tr>
                    </thead>
                    <tbody id="myTable">
                      @forelse($leaveDashboard['leaves'] as $leave)
                      <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>{{$leave->employee->name ?? 'N/A'}}</td>
                        <td>{{$leave->employee->department->name ?? 'N/A'}}</td>
                        <td>{{$leave->leave_type}}</td>
                        <td>{{getFormatedTime($leave->timing)}}</td>
                      </tr>
                      @empty
                      <tr>
                      <td colspan="6"><h4><marquee behavior="alternate" direction="right">No one is on leave today</marquee></h4></td>
                      </tr>
                      @endforelse
                    </tbody>
                </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endcan
    @if(!empty($employeeBirthday) && empty($employeeBirthday->birthday_reminder))
    <div class="swal-overlay swal-overlay--show-modal" id="birthday" tabindex="-1">
      <div class="swal-modal" role="dialog" aria-modal="true">
        <div>
          {{-- <span class="swal-icon--success__line swal-icon--success__line--long"></span>
          <span class="swal-icon--success__line swal-icon--success__line--tip"></span>
          <div class="swal-icon--success__ring"></div>
          <div class="swal-icon--success__hide-corners"></div> --}}
          <img style="max-height:200px;" src="{{url('img/birthday.gif')}}" alt="">
        </div>
        <div class="swal-title" style="">Happy Birthday!</div>
        <div class="swal-text" style="">{{$employeeBirthday->name}}</div>
        <div class="swal-footer">
          <div class="swal-button-container">
            <button class="swal-button swal-button--confirm" onclick="setReadOn('{{$employeeBirthday->id}}')">Close</button>
          </div>
        </div>
      </div>
    </div>
    @endif
@endsection

@section('footerScripts')

{{-- <script src="{{url('skydash/js/chart.js')}}"></script> --}}
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script>
      function setReadOn(id)
      {
        $('#birthday').hide();
        $.ajax({
          url:"{{route('setBirthdayReadOn','')}}"+'/'+id,
          method:'GET'
        });
      }
      $('#leave-list').dataTable();
       $("#leave").click(function(){
         $('html, body').animate({
             scrollTop: $('#leave-list').offset().top
        }, 'slow');
    });
      /* ChartJS
   * -------
   * Data and config for chartjs
   */
  //  @can('hrDashboard', new App\User())
          // 'use strict';
          // var data = {
          //   labels: [  @foreach($hr['department_count'] as $key=> $count) "{{$key}} ({{$count}})", @endforeach],
          //   datasets: [{
          //     label: 'Employees per Department',
          //     data: [ @foreach($hr['department_count'] as $key=> $count) "{{$count}}", @endforeach],
          //     backgroundColor: [
          //       'rgba(255, 99, 132, 0.2)',
          //       'rgba(54, 162, 235, 0.2)',
          //       'rgba(255, 206, 86, 0.2)',
          //       'rgba(75, 192, 192, 0.2)',
          //       'rgba(153, 102, 255, 0.2)',
          //       'rgba(255, 159, 64, 0.2)'
          //     ],
          //     borderColor: [
          //       'rgba(255,99,132,1)',
          //       'rgba(54, 162, 235, 1)',
          //       'rgba(255, 206, 86, 1)',
          //       'rgba(75, 192, 192, 1)',
          //       'rgba(153, 102, 255, 1)',
          //       'rgba(255, 159, 64, 1)'
          //     ],
          //     borderWidth: 1,
          //     fill: false
          //   }],
          //   options: {
          //     responsive: true,
          //   }
          // };
// @endcan

        // @can('employeeDashboard', new App\User())
            // var ctx = document.getElementById("barChart");
            // var myChart = new Chart(ctx, {
            // type: 'bar',
            // data: {
            // labels: [ @php $x = 0; @endphp @foreach ($employees['attendanceYearWise'] as $attentance)
            //     "{{ $attentance['month'][$x++] }} (Present {{ $attentance['count'] }} days)", @endforeach],
            // datasets: [{
            // label: 'Attendance Graph {{ date('Y') }}',
            // data: [@foreach ($employees['attendanceYearWise'] as $attentance)
            //     {{ $attentance['count'] }}, @endforeach ],
            // backgroundColor: '#2593f0'
            // }]
            // },
            // options: {
            // responsive: true,
            // }
            // });
        // @endcan

        // @can('hrDashboard', new App\User())
            // var ctx = document.getElementById("myChart3");
            // var myChart = new Chart(ctx, {
            // type: 'bar',
            // data: {
            // labels: [ @foreach ($hr['department_count'] as $key => $count) "{{ $key }}
            //     ({{ $count }})", @endforeach],
            // datasets: [{
            // label: 'Employees per Department',
            // data: [ @foreach ($hr['department_count'] as $key => $count) "{{ $count }}",
            // @endforeach],
            // backgroundColor: '#2593f0'
            // }]
            // },
            // options: {
            // responsive: true,
            // }
            // });
        // @endcan
    </script>
@endsection

