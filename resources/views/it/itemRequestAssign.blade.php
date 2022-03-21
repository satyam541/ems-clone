@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item Request Assign List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Item Request Assign List</p>
                            <div class="table table-responsive">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Requested By</th>
                                            <th>Requested For</th>
                                            <th>Approved By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemRequests as $itemRequest)
                                            <tr>
                                                <td>{{ ucfirst($itemRequest->item->name) ?? null}}</td>
                                                <td>{{$itemRequest->requestedByEmployee->name ?? null}}</td>
                                                <td>{{$itemRequest->requestedForEmployee->name ?? null}}</td>
                                                <td>{{$itemRequest->actionByEmployee->name ?? null}}</td>
                                                <td><a href="{{route('itemRequestAssignForm',['item_request_id'=>$itemRequest->id])}}" class="btn btn-primary btn-lg p-3">Check Availability</a></td>
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
        $('#example1').DataTable({
        ordering: false,
        "scrollX": true,

        columns: [{
                "name": "Name"
                },
                {
                "name": "Requested By"
                },
                {
                "name": "Requested For"
                },
                {
                "name": "Approved By",
                searching: false,
                sorting: false,
                },
                {
                "name": "Action",
                searching: false,
                sorting: false,
                },
            

        ],
    });

    </script>

@endsection
