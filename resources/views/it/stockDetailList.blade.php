@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stock Detail List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-12">
                                <p class="card-title">Stock Detail List
                                @if($stock->quantity!=count($stockDetails))
                                    <a href="{{route('stockDetailForm',['stock_id'=>$stock->id])}}" class="btn float-right btn-success">Add new
                                        Stock Detail</a>
                                </p>
                                @endif
                                </div>
                            </div>
                            <div>
                                <table id="example1" style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Manufacturer</th>
                                            <th>Model No</th>
                                            <th>Label</th>
                                            <th>Warranty From</th>
                                            <th>Warranty Till</th>
                                            <th>Status</th>
                                            <th>Assigned To</th>
                                            @can('update',new App\Models\Stock())
                                            <th>Edit</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($stockDetails as $stockDetail)
                                            <tr>
                                                <td>{{$stockDetail->stock->item->name}}</td>
                                                <td>{{$stockDetail->manufacturer}}</td>
                                                <td>{{$stockDetail->model_no}}</td>
                                                <td>{{$stockDetail->label}}</td>
                                                <td>{{getFormatedDate($stockDetail->warranty_from)}}</td>
                                                <td>{{$stockDetail->warranty_till}}</td>
                                                <td>{{$stockDetail->status}}</td>
                                                <td>
                                                @if(!empty($stockDetail->EquipmentAssign))
                                                {{$stockDetail->EquipmentAssign->assignedToEmployee->name.' ('.
                                                $stockDetail->EquipmentAssign->assignedToEmployee->department->name.')'}}
                                                @else
                                                N/A
                                                @endif
                                                </td>
                                                @can('update',new App\Models\Stock())
                                                <td><a href="{{route('stockDetailForm',['stock_detail_id'=>$stockDetail->id,'stock_id'=>$stock->id])}}"
                                                    class="mdi mdi-table-edit" style="font-size:20px;border-radius:5px;"></a></td>
                                                @endcan
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
                "name": "Status",
                searching: false,
                sorting: false,
                },
                {
                "name": "Assigned To",
                searching: false,
                sorting: false,
                },
                @can('update',new App\Models\Stock())
                {
                "name": "Edit",
                 searching: false,
                 sorting: false,
                },
                @endcan
            

        ],
    });
    </script>

@endsection
