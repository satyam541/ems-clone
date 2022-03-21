@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stock List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Stock List</p>
                            <div class="">
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($items as $item)
                                            <tr>
                                                <td>{{$item->name}}</td>
                                                <td>{{$item->current_stock_count}}</td>
                                                <td><a href="{{route('hr.stockDetailList',['item_id'=>$item->id])}}"><i style="font-size:20px;border-radius:5px;" class="mdi mdi-table-edit"></i></a></td>
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
                "name": "Quantity"
                searching: false,
                sorting: false,
                },
                {
                "name": "Details",
                 searching: false,
                 sorting: false,
                },
            

        ],
    });
    </script>

@endsection
