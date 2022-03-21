@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket History</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Ticket History</p>
                            <div class="">
                                <table id="example1" class="table table-responsive">

                                    <thead>
                                        <tr>
                                            <th style="white-space: normal;">Ticket No.</th>
                                            <th style="white-space: normal;">Department</th>
                                            <th style="white-space: normal;">Employee</th>
                                            <th style="white-space: normal;">Type</th>
                                            <th style="white-space: normal;">Category</th>
                                            <th style="white-space: normal;">Subject</th>
                                            <th style="white-space: normal;">Description</th>
                                            <th style="white-space: normal;">AnyDesk</th>
                                            <th style="white-space: normal;">Priority</th>
                                            <th style="white-space: normal;">Opened At</th>
                                            <th style="white-space: normal;">Status</th>
                                            <th style="white-space: normal;">Action By</th>
                                            {{-- <th>Closed At</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            <tr>
                                                <td style="white-space: normal;">{{$ticket->id}}</td>
                                                <td style="white-space: normal;">{{$ticket->employee->department->name ?? ''}}</td>
                                                <td style="white-space: normal;">{{$ticket->employee->name ?? ''}}</td>
                                                <td style="white-space: normal;">{{ucfirst($ticket->ticketCategory->type)}}</td>
                                                <td style="white-space: normal;">{{ucfirst($ticket->ticketCategory->name)}}</td>
                                                <td style="white-space: normal;">{{$ticket->subject}}</td>
                                                <td style="white-space: normal;"><textarea name="" id="" cols="30" rows="3" disabled>{{Str::before($ticket->description, ' AnyDesk :')}}</textarea></td>
                                                <td style="white-space: normal;">{{$ticket->anydesk_id}}</td>
                                                <td style="white-space: normal;">{{$ticket->priority}}</td>
                                                <td style="white-space: normal;">{{getFormatedDateTime($ticket->created_at)}}</td>
                                                <td style="white-space: normal;">{{$ticket->status}}</td>
                                                <td style="white-space: normal;">{{$ticket->assignedBy()}}</td>
                                                {{-- <td>{{getFormatedDateTime($ticket->closing_time)}}</td> --}}
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

        columns: [
                {
                "name": "Ticket No."
                },
                {
                "name": "Department"
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
                    "name": "AnyDesk",
                    searching: false,
                    sorting: false,
                },
                {
                    "name": "Priority"
                },
                {
                    "name": "Opened At",
                    searching: false,
                    sorting: false,
                },
                {
                    "name": "Status"
                },
                {
                    "name": "Action By"
                },
        ],
    });
    </script>

@endsection
