@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('hr.stockList') }}">Stock List</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stock Detail List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Stock Detail List</p>
                            <div class="">
                                <table id="example1" style="width: 100%" class="table table-responsive">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Quantity</th>
                                            <th>Price Per Item</th>
                                            <th>Total Price</th>
                                            <th>Purchased Date</th>
                                            <th>Purchased Source</th>
                                            <th>Purchased By</th>
                                            <th>Bill</th>
                                            <th>Currently Available</th>
                                            <th>Stock Details</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stocks as $stock)
                                            <tr>
                                                <td>{{$stock->item->name ?? null}}</td>
                                                <td>{{$stock->quantity}}</td>
                                                <td>{{$stock->price_per_item}}</td>
                                                <td>{{$stock->total_price}}</td>
                                                <td>{{getFormatedDate($stock->purchase_date)}}</td>
                                                <td>{{$stock->purchased_source}}</td>
                                                <td>{{$stock->purchasedByEmployee->name ?? null}}</td>
                                                <td><a href="{{route('viewBill', ['bill' => $stock->bill])}}" target="_blank"><i class="fa fa-eye"></i></a></td>
                                                <td>{{$stock->currently_assigned_count}}</td>
                                                <td><a href="{{route('stockDetailList',['stock_id'=>$stock->id])}}"><i style="font-size:20px;border-radius:5px;" class="mdi mdi-table-edit"></i></a></td>
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
                },
                {
                "name": "Price Per Item"
                },
                {
                "name": "Total Price"
                },
                {
                "name": "Purchase Date",
                searching: false,
                sorting: false,
                },
                {
                "name": "Purchase Source"
                },
                {
                "name": "Purchase By"
                },
                {
                "name": "Bill",
                searching: false,
                sorting: false,
                },
            

        ],
    });
    </script>

@endsection
