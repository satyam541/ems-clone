@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Tickets</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">My Tickets </p>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th>Ticket No.</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th>Subject</th>
                                            <th>Status</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($tickets as $ticket)
                                            <tr>
                                                <td>{{$ticket->id}}</td>
                                                <td>{{ucfirst($ticket->ticketCategory->type)}}</td>
                                                <td>{{ucfirst($ticket->ticketCategory->name)}}</td>
                                                <td>{{$ticket->subject}}</td>
                                                <td>{{$ticket->status}}</td>
                                                <td><a href="{{route('ticketDetail',['id'=>$ticket->id])}}" class="btn btn-warning btn-lg p-3">Detail</a></td>
                                                {{-- <td>
                                                @if($ticket->status == 'Closed')
                                                    <a href="" onclick='action("{{$ticket->id}}","Reopen")' class="btn btn-primary btn-lg p-3" data-toggle="modal" data-target="#exampleModal">Reopen Ticket</a>
                                                @else
                                                    <a href="" onclick='action("{{$ticket->id}}","Closed")' class="btn btn-danger btn-lg p-3" data-toggle="modal" data-target="#exampleModal">Close Ticket</a>
                                                @endif
                                                </td> --}}
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
                "name": "Type"
                },
                {
                "name": "Category"
                },
                {
                "name": "Subject"
                },
                {
                "name": "Status"
                },
                {
                'name':"Detail"
                },
        ],
    });
    $('tbody').on('click', 'button', function() {
                    var button = this;
                    $(button).attr('disabled', true).html('Please wait...').append(
                        '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
                    var id = $(button).attr('id');
                    var link = "{{ route('cancelEquipmentProblem', '') }}" + '/' + id;
                    $.ajax({
                        url: link,
                        method: 'GET',
                        success: function() {
                            toastr.success('Ticket Closed');
                            location.reload();
                        },
                    });
                });

    function action(ticketId,action)
    {
        $('#form_action input[name=id]').val(ticketId);
        $('#form_action input[name=action]').val(action);

    }
    </script>

@endsection
