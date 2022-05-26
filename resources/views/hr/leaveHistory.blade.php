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
                    <li class="breadcrumb-item active" aria-current="page">Leave History</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            {{Form::open(['method'=>'GET'])}}
                            <div class="form-group row">

                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('department_id',$department, request()->department_id,
                                      ['class' => 'form-control selectJS','placeholder'=>'Select your Department'])}}
                                </div>

                                {{ Form::label('user_id', 'Select Name', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('user_id',$users, request()->user_id,
                                      ['class' => 'form-control selectJS','placeholder'=>'Select your Employee Name'])}}
                                </div>

                                {{ Form::label('leave_type_id', 'Select Leave Type ', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                     {{Form::select('leave_type_id', $leave_types, request()->leave_type_id,
                                     ['class' => 'form-control selectJS','placeholder'=>'Select your Leave Type'])}}
                                </div>

                                {{ Form::label('leave_session', 'Select Leave Session', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('leave_session', $leave_session, request()->leave_session,
                                     ['class' => 'form-control selectJS','placeholder'=>'Select your Leave Type'])}}
                                </div>
                                {{ Form::label('Is Pre Approved', 'Is Pre Approved', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{-- {{Form::checkbox('pre_approved',null,['class' => 'selectJS','placeholder'=>'Select your Leave Type'])}} --}}
                                    @if (empty(request()->pre_approved))
                                    <input type="checkbox" name="pre_approved" id="" >
                                    @else
                                    <input type="checkbox" name="without_office_date" id="" checked>
                                    @endif
                                </div>


                                    <button  type="button" class="btn btn-sm btn-light bg-white"  name="daterange" id="date-btn" value="Select Date">
                                        @if(request()->has('dateFrom') && request()->has('dateTo'))
                                        <span>
                                        {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y')}} - {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y')}}
                                        </span>
                                        @else
                                           <span>
                                           <i class="fa fa-calendar"></i>  &nbsp;Filter Date&nbsp;
                                           </span>
                                        @endif
                                       <i class="fa fa-caret-down"></i>
                                    </button>
                                    {{Form::hidden('dateFrom',request()->dateFrom ?? null, array('id'=>'dateFrom'))}}
                                    {{Form::hidden('dateTo', request()->dateTo ?? null, array('id'=>'dateTo'))}}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                    <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    {{ Form::close() }}
                                </div>
                                @can('hrUpdateEmployee', new App\Models\Employee())
                                    <div class="col-md-6">

                                        <a href="{{ route('exportLeave',request()->query()) }}" class="btn m-2 float-right btn-primary">Export</a>

                                    </div>
                                @endcan
                            </div>
                            {{-- <div class="col-md-6">
                            {{Form::submit('Filter',['class'=>'btn btn-primary','style'=>'float:right;'])}}
                            </div>
                            {{Form::close()}}
                            <div class="col-md-6">

                             <a href="{{ route('exportLeave',request()->query()) }}" class="btn m-2 float-right btn-primary">Export</a>
                            <a href="{{request()->url()}}" class="btn btn-success">Clear Filter</a> --}}

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <p class="card-title float-left">Leave History</p>
                    <div class="col-md-8 float-right text-right">
                        <b>Total Results: </b>{{ $leaves->total() }}
                    </div>
                        <br><br>
                        <div class="table-responsive">
                        <table id="" class="table  table-hover">

                            <thead>
                                <tr>
                                    <th style="white-space: normal">Type</th>
                                    <th  style="white-space: normal">Session</th>
                                    <th  style="white-space: normal">Department</th>
                                    <th  style="white-space: normal">Name</th>
                                    <th  style="white-space: normal">From Date</th>
                                    <th  style="white-space: normal">To Date</th>
                                    <th  style="white-space: normal">Duration</th>
                                    <th  style="white-space: normal">Applied At</th>
                                    <th  style="white-space: normal">Reason</th>
                                    <th  style="white-space: normal">Remarks</th>
                                    <th  style="white-space: normal">Status</th>
                                    <th  style="white-space: normal">Attachment</th>
                                    <th  style="white-space: normal">Action</th>
                                </tr>
                            </thead>
                            <tbody>

                                @forelse ($leaves as $leave)

                                    <tr class="{{$leave->status=="Pre Approved" ? 'table-success':'table-danger'}}">
                                        <td  style="white-space: normal">{{$leave->leaveType->name}}</td>
                                        <td  style="white-space: normal">{{$leave->leave_session}}</td>
                                        <td  style="white-space: normal">{{optional($leave->user->employee)->department->name ?? ""}}</td>
                                        <td  style="white-space: normal">{{optional($leave->user)->name}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->from_date)}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->to_date)}}</td>
                                        <td  style="white-space: normal">{{$leave->duration}} {{Str::plural('Day', $leave->duration)}}</td>
                                        <td  style="white-space: normal">{{getFormatedDate($leave->created_at)}}</td>
                                        <td  style="white-space: normal"><textarea name="" id="" cols="30" rows="3" disabled>{{$leave->reason}}</textarea></td>
                                        <td  style="white-space: normal">{{$leave->remarks ?? 'N/A'}}</td>
                                        <td  style="white-space: normal">{{ucfirst($leave->status)}}</td>
                                        <td  style="white-space: normal">
                                        @if($leave->attachment)
                                        <a target="_blank" href="{{route('viewFile', ['file' => $leave->attachment])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                        @else
                                        N/A
                                        @endif
                                        </td>
                                         
                                        @if(Carbon\Carbon::parse($leave->from_date)->format('M') == Carbon\Carbon::now()->format('M'))
                                        <td  style="white-space: normal">
                                            {{ Form::open(['route' => $submitRoute]) }}
                                                {{ Form::hidden('action','cancel') }}
                                                {{ Form::hidden('id', $leave->id) }}
                                                    <button type="submit" onclick="return confirm('Are you sure?');"
                                                    class="btn btn-danger btn-xl p-2 leave-cancel"
                                                    @if($leave->status == 'Cancelled')
                                                        disabled
                                                    @endif >Cancel</button>
                                            {{ Form::close() }}
                                        </td>
                                        @else
                                        <td></td>
                                        @endif
                                    </tr>

                                @empty
                                <tr>
                                    <td colspan="6"><h4><marquee behavior="alternate" direction="right">No data available</marquee></h4></td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-4 float-right">
                <div class="col-md-12 float-right">
                    {{ $leaves->appends(request()->query())->links() }}
                </div>
        </div>

        </div>
    </div>
@endsection


@section('footerScripts')
    <script>


    $('#date-btn').daterangepicker(
        {
            opens: 'left',
            locale: { cancelLabel: 'Clear' },
            ranges   : {
                'Today'       : [moment(), moment()],
                'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 5 Days' : [moment().subtract(4, 'days'),moment()],
                'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                'Last 14 Days': [moment().subtract(13,'days'),moment()],
                'Last 30 Days': [moment().subtract(29, 'days'),moment()],
                'This Month'  : [moment().startOf('month'), moment().endOf('month')],
                'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            }
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        },
        function (start, end) {
            $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
            $('#dateFrom').val(start.format('YYYY-M-DD'));
            $('#dateTo').val(end.format('YYYY-M-DD'));
        }
    );

    $('#date-btn').on('cancel.daterangepicker', function(ev, picker) {
        clearDateFilters('date-btn','date');
    });

    function clearDateFilters(id, inputId){
        $('#'+id+' span').html('<span> <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;</span>')
        $('#'+inputId+'From').val('');
        $('#'+inputId+'To').val('');
    }

        $('#example1').dataTable({
            ordering: false,
            fixedColumns: true,
            searching:false,
            "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

            columnsDefs: [
                {
                    "name": "Nature",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Type",
                    sorting: false,
                    searching: false
                },
                {
                    "name": "Department"
                },
                {
                    "name": "Name"
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
                    "name": "Remarks",
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
                {
                    "name": "Action",
                    sorting: false,
                    searching: false
                },

            ],

        });
    </script>

@endsection
