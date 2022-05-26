@extends('layouts.master')
@section('headerLinks')
<style>
.table td, .table th{
    padding: 5px;
}
</style>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Manual Leave List</li>
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
                                    {{ Form::select('department_id', $departments, request()->department_id, ['class' => 'form-control selectJS','placeholder' => 'Select Department']) }}
                                </div>

                                {{ Form::label('employee', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('employee', $employees, request()->employee, ['class' => 'form-control selectJS','placeholder' => 'Select Employee']) }}
                                </div>

                                {{ Form::label('leave_type_id', 'Select Leave Type', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('leave_type_id', $leaveTypes, request()->leave_type_id, ['class' => 'form-control selectJS','placeholder' => 'Select Leave Type']) }}
                                </div>

                                {{ Form::label('leave_session', 'Select Session', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('leave_session', $sessions, request()->leave_session, ['class' => 'form-control selectJS','placeholder' => 'Select Session']) }}
                                </div>
                                <div class="col-sm-4">
                                <button type="button" class="btn btn-sm btn-primary" name="daterange" id="date-btn"
                                    value="Select Date">
                                    @if (request()->has('dateFrom') && request()->has('dateTo'))
                                        <span>
                                            {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y') }} -
                                            {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y') }}
                                        </span>
                                    @else
                                        <span>
                                            <i class="fa fa-calendar"></i> &nbsp;Filter Date&nbsp;
                                        </span>
                                    @endif
                                    <i class="fa fa-caret-down"></i>
                                </button>
                                    {{ Form::hidden('dateFrom', request()->dateFrom ?? null, ['id' => 'dateFrom']) }}
                                    {{ Form::hidden('dateTo', request()->dateTo ?? null, ['id' => 'dateTo']) }}
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


                                        <a href="{{ route('exportEmployee', request()->query()) }}"
                                            class="btn m-2 float-right btn-primary">Export</a>
                                        <a href="{{ route('manual-leave.create') }}" class="btn m-2 float-right btn-success">Add
                                            new
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
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title float-left">Manual Leave List</p>
                            
                            <div class="table-responsive">
                                <table id="example1" class="table ">

                                    <thead>
                                        <tr>
                                            <th>Session</th>
                                            <th>Type</th>
                                            <th>Name</th>
                                            <th>Department</th>
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Duration</th>
                                            <th>Timing</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Attachment</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaves as $leave)
                                            <tr class="border-top">
                                                <td>{{ $leave->leave_session }}</td>
                                                <td>{{ $leave->leaveType->name ?? '' }}</td>
                                                <td>{{ $leave->user->name ?? '' }}</td>
                                                <td>
                                                    @if (!empty($leave->user->employee->department))
                                                        {{ optional($leave->user->employee)->department->name ?? '' }}
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ getFormatedDate($leave->from_date) }}</td>
                                                <td>{{ getFormatedDate($leave->to_date) }}</td>
                                                <td>{{ $leave->duration }} {{ Str::plural('Day', $leave->duration) }}
                                                </td>
                                                <td>{{ getFormatedTime($leave->timing) }}</td>
                                                <td>
                                                    <textarea name="" id="" cols="20" rows="3" disabled>{{ $leave->reason }}</textarea>
                                                </td>
                                                {{-- <td>{{$leave->remarks ?? 'N/A'}}</td> --}}
                                                <td>{{ ucfirst($leave->status) }}</td>
                                                <td>
                                                    @if ($leave->attachment)
                                                        <a target="_blank"
                                                            href="{{ route('viewFile', ['file' => $leave->attachment]) }}">
                                                            <i class="fa fa-eye text-primary"></i>
                                                        </a>
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>

                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
        var filterColumns = [0, 1];
        $('#date-btn').daterangepicker({
                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 5 Days': [moment().subtract(4, 'days'), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(13, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf(
                        'month')],
                },
                startDate: moment().subtract(5, 'days'),
                endDate  : moment()
            },
            function(start, end) {
                $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
            }
        );

        $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('date-btn', 'date');
        });

        function clearDateFilters(id, inputId) {
            $('#' + id + ' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
            $('#' + inputId + 'From').val('');
            $('#' + inputId + 'To').val('');
        }

        $('#example1').dataTable({
            ordering: false,
            fixedColumns: true,
            // "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

            columnsDefs: [{
                    "name": "Session",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Type",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Name"
                },
                {
                    "name": "Department"
                },
                {
                    "name": "From Date",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "To Date",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Duration",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Timing",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Reason",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Status",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Attachment",
                    sorting: false,
                    searching: false
                },

            ]
        });
    </script>
@endsection
