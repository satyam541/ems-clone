@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Remote Attendance</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row  mb-3">
        <div class="col-12">
            <div class="card">

                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">

                        {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{Form::select('department_id',$department, request()->department_id,
                                      ['onchange'=>'getEmployees(this.value)','class' => 'form-control selectJS','placeholder'=>'Select your Department'])}}   
                        </div>


                        {{ Form::label('employee', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{Form::select('employee_id',$employeeNames, request()->employee_id,
                              ['id' => 'employees','class' => 'form-control selectJS','placeholder'=>'Select your Employee Name'])}}
                        </div>

                        
                        {{ Form::label('date', 'Select Date', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            <button  type="button" class="btn btn-sm" style="background-color: #eaeaea"  name="daterange" id="date-btn" value="Select Date">
                                @if(!empty(request()->dateFrom) && !empty(request()->dateTo))
                                <span>
                                {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y')}} - {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y')}} 
                                </span>
                                @else
                                    <span>
                                    <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;
                                    </span>
                                @endif
                                <i class="fa fa-caret-down"></i>
                            </button>
                            {{Form::hidden('dateFrom',request()->dateFrom ?? null, array('id'=>'dateFrom'))}}
                            {{Form::hidden('dateTo', request()->dateTo ?? null, array('id'=>'dateTo'))}}

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::submit('Filter', ['class' => 'btn btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn btn-success">Clear Filter</a>
                            {{ Form::close() }}
                        </div>
                        <div class="col-md-6 float-right">
                            <a href="{{route('exportRemoteAttendance',request()->query())}}" class="btn btn-danger float-right">Download</a>
                           
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <p class="card-title float-left">Attendance List</p>
                    <div class="table-responsive">
                        <table class="table table-striped">

                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Department</th>
                                    <th>Nature</th>
                                    <th>Date</th>
                                    <th>Punch In</th>
                                    <th>Location </th>
                                    <th>Punch Out</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($attendances->isEmpty())
                                <tr>
                                    <td colspan="8">
                                        <marquee behavior="alternate" direction="right"> No data available</marquee>
                                    </td>
                                </tr>
                                @else
                                @foreach ($attendances as $attendance)
                                    <tr>
                                        <td>{{ $attendance->employee->name }}</td>
                                        <td>{{ $attendance->employee->department->name }}</td>
                                        <td>{{ $attendance->getLeaveNature() }}</td>
                                        <td>{{ getFormatedDate($attendance->date)}}</td>
                                        <td>{{ $attendance->punch_in}}</td>
                                        <td><a href="{{ $attendance->location_in}}" target="_blank" rel="noopener noreferrer">View</a></td>
                                        <td>{{ $attendance->punch_out ?? 'N/A'}}</td>
                                        <td>
                                            @if (!empty($attendance->location_out))
                                            <a href="{{ $attendance->location_out}}" target="_blank" rel="noopener noreferrer">View</a></td>
                                            @else
                                            N/A
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-2">
                        <div class="col-sm-6 float-left">
                           <strong>Total Results: </strong> {{$attendances->total()}}
                        </div>
                        <div class="col-sm-6">
                            <div class="float-right">
                                {{$attendances->appends(request()->query())->links()}}
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('footerScripts')
    <script>
function getEmployees(department_id) {
        if (department_id) {
            $.ajax({
                url: "{{route('getEmployees')}}/" + department_id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var options = `<option value=''></option>`;
                    $.each(response, function (id, name) {
                        options += "<option value='" + id + "'>" + name + "</option>";
                    });
                
                    $('#employees').html(options);
                    $("select").select2({
                        placeholder: "Select an option"
                    });
                }
            })
        }
    }
    $('#date-btn').daterangepicker(
        {
            opens: 'left',
            locale: { cancelLabel: 'Clear' },
            ranges   : {
                'Today'       : [moment(), moment()],
                'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 5 Days' : [moment(),moment().subtract(4, 'days')],
                'Last 14 Days':[moment(),moment().subtract(13,'days')],
                'Last 30 Days': [moment(),moment().subtract(29, 'days')],
                'This Month'  : [moment().endOf('month'), moment().startOf('month')],
                'Last Month'  : [moment().subtract(1, 'month').endOf('month'), moment().subtract(1, 'month').startOf('month')]
            },
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        },
        function (start, end) {
            $('#date-btn span').html(end.format('D/ M/ YY')+ ' - ' + start.format('D/ M/ YY') )
            $('#dateTo').val(start.format('YYYY-M-DD'));
            $('#dateFrom').val(end.format('YYYY-M-DD'));
        }
    );
    </script>

@endsection
