@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Interviews</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Interview
                                <div class="float-right">
                                    @can('create',new App\Models\Interview())
                                        <a href="{{ route('interview.create') }}" class="btn btn-sm btn-primary"> <i
                                                class="fa fa-plus"></i> Create </a>
                                    @endcan
                                </div>
                            </div>
                            <div class=" table-responsive">
                                <table id="example1" class="table" style="width: 100%">

                                    <thead>
                                        <tr>
                                            <th>Sr No.</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($interviews as $interview)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{ucfirst($interview->name)}}</td>
                                                <td>{{ucfirst($interview->email)}}</td>
                                                <td><a href="{{route('interview.edit',['interview'=>$interview->id])}}" class="btn btn-warning btn-lg p-3">Detail</a></td>
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
    });
    </script>

@endsection
