@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item Assign Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Item Assign Form</p>
                            <div class="table table-responsive">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Manufacturer</th>
                                            <th>Model No</th>
                                            <th>Label</th>
                                            <th>Warranty From</th>
                                            <th>Warranty Till</th>
                                            <th>Assign</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($availableItems as $availableItem)
                                            <tr>
                                                <td>{{ ucfirst($availableItem->manufacturer)}}</td>
                                                <td>{{$availableItem->model_no}}</td>
                                                <td>{{$availableItem->label}}</td>
                                                <td>{{$availableItem->warranty_from}}</td>
                                                <td>{{$availableItem->warranty_till}}</td>
                                                {{Form::open(['route'=>$submitRoute,'method'=>'POST'])}}
                                                {{Form::hidden('stock_item_id',$availableItem->id)}}
                                                {{Form::hidden('item_request_id',$itemRequest->id)}}
                                                <td><button type="submit" class="btn btn-primary btn-lg p-3">Assign</button></td>
                                                {{Form::close()}}
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
                "name": "Manufacturer"
                },
                {
                "name": "Model No"
                },
                {
                "name": "Label"
                },
                {
                "name": "Warranty From",
                searching: false,
                sorting: false,
                },
                {
                "name": "Warranty Till",
                searching: false,
                sorting: false,
                },
                {
                "name": "Assign",
                searching: false,
                sorting: false,
                },
            

        ],
    });

    </script>

@endsection
