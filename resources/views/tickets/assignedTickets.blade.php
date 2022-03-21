@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assigned Tickets</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Assigned Tickets </p>
                            <div class="table-responsive">
                                <table id="example1" style="width: 100%" class="table ">

                                    <thead>
                                        <tr>
                                            <th>Ticket No.</th>
                                            <th>Employee</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Subject</th>
                                            <th>Description</th>
                                            <th>Assigned By</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assignedTickets as $assignedTicket)
                                            <tr>
                                                <td>{{$assignedTicket->id}}</td>
                                                <td>{{$assignedTicket->employee->name.' ('.$assignedTicket->employee->department->name.')'}}</td>
                                                <td>{{ucfirst($assignedTicket->ticketCategory->type)}}</td>
                                                <td>{{ucfirst($assignedTicket->ticketCategory->name)}}</td>
                                                <td>{{$assignedTicket->subject}}</td>
                                                <td><textarea name="" id="" cols="20" rows="3" disabled>{{Str::before($assignedTicket->description, ' AnyDesk :')}}</textarea></td>
                                                <td>{{$assignedTicket->ticketLogs->last()->actionBy->name ?? 'N\A'}}</td>
                                                <td><a href="{{route('ticketDetail',['id'=>$assignedTicket->id])}}" class="btn btn-warning btn-lg p-3">Detail</a></td>
                                                {{-- @if($assignedTicket->status == 'Closed')
                                                    <td>Closed</td>
                                                @else
                                                    <td><button  class="btn btn-danger btn-lg p-3" onclick='action("{{$assignedTicket->id}}","Closed")' data-toggle="modal" data-target="#exampleModal">Close Ticket</button></td>
                                                @endif --}}
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
    $('#example1').dataTable({
        ordering: false,
        "scrollX": true,

        columns: [
                {
                "name": "Ticket No."
                },
                {
                "name": "Employee"
                },
                {
                "name": "Type"
                },
                {
                "name": "Category"
                },
                {
                "name": "Subject"
                },
                {
                "name": "Description"
                },
                {
                "name": "Assigned By",
                searching: false,
                sorting: false,
                },
                
                {
                "name": "Detail",
                searching: false,
                sorting: false,
                },
        ],
    });

                
    function action(ticketId,action)
    {
        $('#form_action input[name=id]').val(ticketId);
        $('#form_action input[name=action]').val(action);

    }
    </script>

@endsection
