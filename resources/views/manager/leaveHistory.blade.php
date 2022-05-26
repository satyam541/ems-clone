@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title float-left">Leave History List</p>
                            <div>
                                <div class="float-right">
                                    {{Form::open(['method'=>'GET'])}}
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
                            
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    {{Form::close()}}
                                </div>
                            </div>
                                <br><br><br>
                            <div class="">
                                <table id="example1" class="table table-responsive">

                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Session</th>
                                            <th>Name</th>
                                            @if($departmentCount)
                                            <th>Department</th>
                                            @endif
                                            <th>From Date</th>
                                            <th>To Date</th>
                                            <th>Duration</th>
                                            <th>Timing</th>
                                            <th>Reason</th>
                                            <th>Remarks</th>
                                            <th>Status</th>
                                            <th>Attachment</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($leaves as $leave)
                                            <tr class="border-top">
                                                <td>{{$leave->leaveType->name}}</td>
                                                <td>{{$leave->leave_session}}</td>
                                                <td>{{optional($leave->user)->name}}</td>
                                                @if($departmentCount)
                                                <td>{{optional($leave->user->employee)->department->name ?? ""}}</td>
                                                @endif
                                                <td>{{getFormatedDate($leave->from_date)}}</td>
                                                <td>{{getFormatedDate($leave->to_date)}}</td>
                                                <td>{{$leave->duration}} {{Str::plural('Day', $leave->duration)}}</td>
                                                <td>{{getFormatedTime($leave->timing)}}</td>
                                                <td><textarea name="" id="" cols="30" rows="3" disabled>{{$leave->reason}}</textarea></td>
                                                <td>{{$leave->remarks ?? 'N/A'}}</td>
                                                <td>{{ucfirst($leave->status)}}</td>
                                                <td>
                                                    @if($leave->attachment)
                                                    <a target="_blank" href="{{route('viewFile', ['file' => $leave->attachment])}}">
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
       var filterColumns=[0,1];
        @if($departmentCount)
        
            filterColumns=[0,1,3];
        @endif
        $('#date-btn').daterangepicker({
            opens: 'left',
            locale: {
                cancelLabel: 'Clear'
            },
            ranges: {
                'Today'       : [moment(), moment()],
                'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 5 Days' : [moment().subtract(4, 'days'),moment()],
                'Last 7 Days' : [moment().subtract(6, 'days'), moment()],
                'Last 14 Days': [moment().subtract(13,'days'),moment()],
                'Last 30 Days': [moment().subtract(29, 'days'),moment()],
                'This Month'  : [moment().startOf('month'),moment().endOf('month') ],
                'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
            },
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        },
        function(start, end) {
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
            // "dom": '<"top"ifl<"clear">>rt<"bottom"ip<"clear">>',

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
                    "name": "Name"
                },
                @if($departmentCount)
                {
                    "name": "Department"
                },
                @endif
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

            ],
            initComplete: function() {
                var data = this;
                this.api().columns(filterColumns).every(function() {
                    var column = this;
                    var columnName = $(column.header()).text();
                    var select = $('<select class="selectJS form-control" data-placeholder="' +
                            columnName + '"><option value=""></option></select>')
                        .appendTo($(column.header()).empty())
                        .on('change', function() {
                            var val = $.fn.dataTable.util.escapeRegex(
                                $(this).val()
                            );
                            if (val == "all") {
                                val = "";
                            }
                            column
                                .search(val ? '^' + val + '$' : '', true, true)
                                .draw();
                        });
                    select.append('<option value="all">All</option>')
                    column.data().unique().each(function(d, j) {
                        select.append('<option value="' + d + '">' + d +
                            '</option>')
                    });
                });
            }
        });
    </script>

@endsection
