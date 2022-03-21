@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item Request List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
    
                {{ Form::open(['route'=>$submitRoute])}}
                <div class="card-body">
                    <h4 class="card-title">Item Request Form</h4>
    
                    <div class="row">
                        <div class="col-sm-4">
                            {{Form::select('item_id', $items,null,['class' => 'form-control selectJS','required'])}}
                            {{-- @error('item_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror --}}
                        </div>
                        <div class="col-sm-4">
                            {{Form::select('employee_id', $employees,null,['class' => 'form-control selectJS','required'])}}

                            {{-- @error('employee_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror --}}
                        </div>
    
                        <div class="col-sm-4">
                            <button type="submit" class="btn btn-success">Add new Request</button>
                        </div>
    
                        {{Form::close()}}
                    </div>
                </div>
    
            </div>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Item Request List</p>
                            <div class="table table-responsive">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Requested By</th>
                                            <th>Requested For</th>
                                            <th>Approved</th>
                                            <th>Remarks</th>
                                            <th>Action By</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemRequests as $itemRequest)
                                            <tr>
                                                <td>{{ $itemRequest->item->name ?? null}}</td>
                                                <td>{{$itemRequest->requestedByEmployee->name ?? null}}</td>
                                                <td>{{$itemRequest->requestedForEmployee->name ?? null}}</td>
                                                @if(is_null($itemRequest->is_approved))
                                                <td>Pending</td>
                                                @else
                                                <td>{{($itemRequest->is_approved) ? 'Yes' : 'NO'}}</td>
                                                @endif
                                                <td>{{$itemRequest->remarks}}</td>
                                                <td>{{$itemRequest->actionByEmployee->name ?? null}}</td>
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
                "name": "Approved",
                searching: false,
                sorting: false,
                },
                {
                "name": "Remarks"
                },
                {
                "name": "Action By"
                },
            

        ],
    });
    </script>

@endsection
