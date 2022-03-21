@extends('layouts.master')
@section('headerLinks')
<style>
.select:invalid { color: gray; }
.select2-selection__rendered {
    line-height: 15px !important;
}

</style>
@endsection
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Opened Tickets</li>
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
                                {{ Form::label('ticket_category_id', 'Select Type', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('ticket_category_id', $ticketTypes,request()->ticket_category_id,
                                    ['class' => 'form-control selectJS','placeholder'=>'Select your issue'])}}
                                </div>

                                {{ Form::label('priority', 'Select Priority', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{Form::select('priority', $priority,request()->priority,
                                    ['class' => 'form-control selectJS','placeholder'=>'Select your Priority'])}}
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
                                   
                                    {{Form::hidden('dateTo', request()->dateTo ?? null, array('id'=>'dateTo'))}}
                                    {{Form::hidden('dateFrom',request()->dateFrom ?? null, array('id'=>'dateFrom'))}}

                                </div>
                            </div>
                            {{Form::submit('Filter',['class'=>'btn btn-primary','style'=>'float:right;'])}}
                            {{Form::close()}}
                            <a href="{{request()->url()}}" class="btn btn-success">Clear Filter</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body table-responsive">
                            <p class="card-title">Opened Tickets</p>
                                <table style="width: 100%" class="table table-striped table-hover">

                                    <thead>
                                        <tr>
                                            <th style="white-space: normal;">Ticket No.</th>
                                            <th style="white-space: normal;">Employee</th>
                                            <th style="white-space: normal;">Type</th>
                                            <th style="white-space: normal;">Category</th>
                                            <th style="white-space: normal;">Subject</th>
                                            <th style="white-space: normal;">Description</th>
                                            <th style="white-space: normal;">Priority</th>
                                            <th style="white-space: normal;">Status</th>
                                            <th style="white-space: normal;">Opened At</th>
                                            @can('ticketAssign',  new App\Models\Ticket())
                                            <th style="white-space: normal;">Assign</th>
                                            @endcan
                                            <th style="white-space: normal;">Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $departmentName='' @endphp;
                                        @forelse($tickets as $ticket)
                                            @if(!empty($ticket->employee))
                                            @php $departmentName=$ticket->employee->department->name @endphp
                                            @endif
                                            <tr>                           
                                                <td style="white-space: normal;">{{$ticket->id}}</td>
                                                
                                                <td style="white-space: normal;">{{$ticket->employee->name ?? ''.' ('.$departmentName.')'}}</td>
                                                <td style="white-space: normal;">{{ucfirst($ticket->ticketCategory->type ?? '')}}</td>
                                                <td style="white-space: normal;">{{ucfirst($ticket->ticketCategory->name ?? '')}}</td>
                                                <td style="white-space: normal;">{{$ticket->subject}}</td>
                                                <td style="white-space: normal;"><textarea name="" id="" cols="30" rows="3" disabled>{{Str::before($ticket->description, ' AnyDesk :')}}</textarea></td>
                                                <td style="white-space: normal;">{{$ticket->priority}}</td>
                                                <td style="white-space: normal;">{{$ticket->status}}</td>
                                                <td style="white-space: normal;">{{getFormatedDateTime($ticket->created_at)}}</td>
                                                @can('ticketAssign',  new App\Models\Ticket())
                                                @if($ticket->status=='Assigned')
                                                <td style="white-space: normal;">{{$ticket->ticketLogs->last()->assignedTo->name ?? ''}}</td>
                                                @else
                                                <td style="white-space: normal;"><button class="btn btn-primary" onclick='action("{{$ticket->id}}")' data-toggle="modal" data-target="#exampleModal"><i class="fas fa-user-plus" style="font-size: 13px"></i> Assign</button></td>
                                                @endif
                                                @endcan
                                                <td style="white-space: normal;"><a href="{{route('ticketDetail',['id'=>$ticket->id])}}" class="btn btn-warning">Detail</a></td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="11"><h4><marquee behavior="alternate" direction="right">No tickets available</marquee></h4></td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                                <span style="float: right;" class="mt-3">
                                {{$tickets->appends(request()->input())->links()}}
                                </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ticket Assignment<span></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ Form::open(['route'=>'raiseTicketAction']) }}
                <div class="modal-body">
                    {{Form::hidden('id',null, ['id'=> 'problemId'])}}
                    {{Form::hidden('action','Assigned')}}
                    <div class="form-group row">
                        {{ Form::label('assigned_to', 'Assign To:', ['class' => 'col-sm-3 col-form-label']) }}
                        <select name="assigned_to" required class="col-md-5 form-control selectJS" placeholder="Select an Employee">
                            @foreach($employees as $employee)
                            <option value="{{$employee->id}}">{{$employee->name}} ({{$employee->department->name}})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Assign</button>
                    </div>
                {{ Form::close() }}
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
                'This Month'  : [moment().startOf('month'),moment().endOf('month') ],
                'Last Month'  : [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        },
        function (start, end) {
            $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
          
            $('#dateTo').val(start.format('YYYY-M-DD'));
            $('#dateFrom').val(end.format('YYYY-M-DD'));
        
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
    function action(problemId)
    {
        $('#problemId').val(problemId);
    }

    </script>

@endsection
