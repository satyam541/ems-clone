@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Attendance Form</li>
                </ol>
            </nav>
        </div>
        
        <div class="col-12">

            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-2">
                                <h4 class="card-title">Attendance ({{getFormatedDate($min)}})</h4>
                            </div>
                            @if(empty($leave) || $leave->leave_session != 'Full day')
                            
                            <form method="post" action="{{route('submitAttendance')}}">
                                @csrf
                                <input type="hidden" name="type" value="{{$action}}">
                                <input type="hidden" name="id" value="{{$id}}">
                            @endif
                            <div class="table-responsive">
                                <table id="example1" class="table">

                                    <tbody>
                                        <tr>
                                            <th>Employee</th>
                                            <td class="text-right">
                                                {{ $employee->name}}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td class="text-right">{{ $employee->department->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Session</th>
                                            <td class="text-right">{{ optional($employee->leaves)->first()->leave_session ?? 'Present' }}</td>
                                        </tr>
                                        @if(!empty($id))
                                        <tr>
                                            <th>Punch In</th>
                                            <td class="text-right">{{ $employee->attendances->first()->punch_in }}</td>
                                        </tr>
                                        @endif
                                        @if(!empty($employee->attendances->first()->punch_out))
                                        <tr>
                                            <th>Punch Out</th>
                                            <td class="text-right">{{ $employee->attendances->first()->punch_out }}</td>
                                        </tr>
                                        @endif
                                        @if (empty($employee->attendances->first()->location_out))
                                        <tr>
                                            <th>Current Location
                                                <br>
                                                <a href="https://www.google.com/search?q=my+current+location" target="_blank" rel="noopener noreferrer">Need Help?</a>
                                            </th>
                                            <td class="text-right"><textarea name="location" id="" rows="2" style="width: 100%" required></textarea></td>
                                        </tr>
                                        @endif
                                        
                                    </tbody>
                                </table>
                            </div>
                                        
                                @if(empty($leave) || $leave->leave_session != 'Full day')
                                @if(empty($employee->attendances->first()->punch_out))
                                    <div class="text-center">
                                        <button class="btn btn-primary">{{$action}}</button>
                                    </div>
                                    
                                @endif
                                @endif
                        </form>
                            
                           
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('footerScripts')
    <script>

    </script>

@endsection
