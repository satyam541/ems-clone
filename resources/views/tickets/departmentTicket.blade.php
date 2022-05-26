@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Department Tickets</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Department Tickets </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th># </th>
                                            <th>Name </th>
                                            <th>Ticket No.</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Raised By</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$ticket->user->name}}</td>
                                                <td>{{$ticket->id}}</td>
                                                <td>{{ucfirst($ticket->ticketCategory->type)}}</td>
                                                <td>{{ucfirst($ticket->ticketCategory->name)}}</td>
                                                <td>{{$ticket->subject}}</td>
                                                <td>{{$ticket->status}}</td>
                                                <td>{{$ticket->raisedBy->name ?? ''}}</td>
                                                <td><a href="{{route('ticketDetail',['id'=>$ticket->id])}}" class="btn btn-warning btn-lg p-3">Detail</a></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- <div class="row mt-4 float-right">
                        <div class="col-md-12">
                            {{ $tickets->appends(request()->query())->links() }}
                        </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
   
@endsection


@section('footerScripts')
    <script>
    $('#example1').dataTable({
        ordering: false,
        
    });
    
    </script>

@endsection
