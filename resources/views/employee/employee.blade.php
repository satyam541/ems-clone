@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        {{ Form::open(['method' => 'GET']) }}
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            <div class="form-group row">

                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('department_id', $department_id, request()->department_id, ['onchange'=>'getEmployees(this.value)','class' => 'form-control selectJS', 'placeholder' => 'Select your department']) }}
                                </div>

                                {{ Form::label('user_id', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    <select style='width:100%;' name="user_id" data-placeholder="select an option" id="employees"
                                        placeholder="select an option" class='form-control selectJS'>
                                        <option value="" disabled selected>Select your option</option>
                                        @foreach ($employeeDepartments as $department => $employee)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employee as $user)
                                                <option value="{{$user->user_id}}" @if($user->user_id == request()->user_id) selected @endif>{{$user->name.' ('.$user->biometric_id.')'}}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>
    

                                {{ Form::label('office_email', 'Select Email', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('office_email', $office_emails, request()->office_email, ['class' => 'form-control selectJS','id'=>'emails' ,'placeholder' => 'Select your email']) }}
                                </div>

                                {{ Form::label('status', 'Select Status', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('status', ['active'=> 'Active', 'exit' => 'Exit'], request()->status, ['class' => 'form-control selectJS', 'placeholder' => 'Select your email']) }}
                                </div>

                                {{ Form::label('shift_type', 'Select Shift Type', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('shift_type', $shift_types, request()->shift_type, ['class' => 'form-control selectJS', 'placeholder' => 'Select Shift Type']) }}
                                </div>

                                {{ Form::label('user_type', 'Select User Type', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('user_type[]', $userTypes, request()->user_type, ['class' => 'form-control selectJS','multiple'=>'multiple', 'dataPlaceholder' => 'Select User Type']) }}
                                </div>
                                {{ Form::label('gender', 'Select Gender', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('gender', $gender, request()->gender, ['class' => 'form-control selectJS', 'placeholder' => 'Select Gender']) }}
                                </div>
                                {{ Form::label('shift_time', 'Select Shift Time', ['class' => 'col-sm-2 col-form-label']) }}
                            
                                <div class="col-sm-4">
                                    <select class="form-control selectJS " name="shift_time" value="option_select" >
                                        <option value="" readonly> Select </option>
                                        @foreach($shiftTypes as $shiftType)
                                        <option value="{{$shiftType->id}}" @if(request()->shift_time == $shiftType->id) selected @endif>{{ $shiftType->name.' ('. $shiftType->start_time.'-'.$shiftType->end_time.')'}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                {{ Form::label('Biometric Id', 'Biometric Id', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::text('biometric_id', request()->biometric_id, ['class' => 'form-control selectJS', 'placeholder' => 'Enter Biometric Id']) }}
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">Is Power User
                                                <input type="checkbox" name="is_power_user" @if(request()->is_power_user) checked @endif class="form-check-input">
                                                <i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                    <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    {{ Form::close() }}
                                </div>
                                @can('hrUpdateEmployee', new App\Models\Employee())
                                    <div class="col-md-6">


                                        <a href="{{ route('exportEmployee',request()->query()) }}" class="btn m-2 float-right btn-primary">Export</a>
                                        <a href="{{ route('createEmployee') }}" class="btn m-2 float-right btn-success">Add new
                                            Record <i class=""></i></a>
                                    </div>
                                @endcan
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="col-md-8 float-right text-right">
                                <b>Total Results: </b>{{ $employees->total() }}
                            </div>

                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12 table-responsive ">
                            <table id="" class="table table-hover">

                                <thead>
                                    <tr>
                                        <th>Picture</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Department</th>
                                        <th>Biometric</th>
                                        <th>Employee Type</th>
                                        @can('status', new App\Models\Employee())
                                            <th>Status</th>
                                        @endcan
                                        {{-- @can('hrView', new App\Models\Attendance())
                                            <th class="hidden">Attendance</th>
                                        @endcan --}}
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($employees as $employee)
                                        <tr>
                                            <td><a target="_blank" href="{{ $employee->image_source }}"><img
                                                        src="{{ $employee->image_source }}" width="42" height="42"></a>
                                            </td>
                                            <td>{{ $employee->name }}</td>
                                            <td>{{ $employee->office_email }}</td>
                                            <td>{{ $employee->department->name ?? null }}</td>
                                            <td>{{ $employee->biometric_id ?? null }}</td>
                                            <td>{{ $employee->user->user_type}}</td>
                                            <td>
                                                @if ($employee->is_active == 1)
                                                    Active
                                                @else
                                                    Exit
                                                @endif
                                            </td>
                                            {{-- <td class="hidden"><a
                                                    href="{{ route('employeeAttendance', ['employee' => $employee->id]) }}"
                                                    class="p-2 text-primary fas fa-user-clock"
                                                    style="font-size:20px;border-radius:5px;"></a></td> --}}
                                            <td><a href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                                                    class="p-2 text-primary fas fa-address-card"
                                                    style="font-size:20px;border-radius:5px;"></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row mt-4 float-right">
                        <div class="col-md-12">
                            {{ $employees->appends(request()->query())->links() }}
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
                url: "{{route('getUsers')}}/" + department_id,
                type: 'get',
                dataType: 'json',
                success: function (response) {
                    var options = `<option value=''></option>`;
                    var option = `<option value=''></option>`;

                    $.each(response, function (data,name,office_email,biometric_id) {
                        console.log(response[data].user_id);
                        option += "<option value='" + response[data].user_id + "'>" + response[data].name+'('+response[data].biometric_id+')' + "</option>";
                        options += "<option value='" + response[data].office_email + "'>" + response[data].office_email + "</option>";

                    });

                    $('#employees').html(option);
                    $('#emails').html(options);
                    $("select").select2({
                        placeholder: "Select an option",
                        allowClear: true,
                    });
                }
            })
        }
    }
        $('#example1').dataTable({
            ordering: false,
            fixedColumns: true,
            'searching': false,

            columnsDefs: [{
                    "name": "Picture",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Name"
                },
                {
                    "name": "Email"
                },
                {
                    "name": "Department"
                },
                {
                    "name": "Status",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Attendance",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Details",
                    sorting: false,
                    searching: false
                },

            ],

        });
    </script>

@endsection
