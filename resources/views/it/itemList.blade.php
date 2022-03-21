@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="col-md-12 mt-1">
                            <a href="{{route('itemCreate')}}" class="btn float-right btn-success">Add new
                                Item</a>
                        </div>
                        <div class="card-body">
                            <p class="card-title">Item List</p>
                            <div class="">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td><a href="{{route('itemEdit',['item'=>$item->id])}}" class="p-2 text-primary mdi mdi-table-edit"
                                                    style="font-size:20px;border-radius:5px;"></a></td>
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
                "name": "Action",
                searching: false,
                sorting: false,
                },
            

        ],
    });
    </script>

@endsection
