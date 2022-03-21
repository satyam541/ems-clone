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
        <div class="col-12 mb-3">

            <div class="row">
                <div class="col-12">
                    <div class="card">

                        {{ Form::open(['method' => 'GET']) }}
                        <div class="card-body">
                            <p class="card-title">Filter</p>
                            <div class="form-group row">

                                {{ Form::label('Select Name', 'Select Name', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('item_id', $item_name, request()->item_id, ['class' => 'form-control selectJS', 'placeholder' => 'Select Name']) }}
                                </div>

                                {{ Form::label('Select Purchased Source', 'Select Purchased Source', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    {{ Form::select('purchased_source', $purchased_source, request()->purchased_source, ['id' => 'employees','class' => 'form-control selectJS', 'placeholder' => 'Select Employee']) }}
                                </div>

                                {{ Form::label('date', 'Select Date', ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-4">
                                    <button  type="button" class="btn btn-sm" style="background-color: #eaeaea"  name="daterange" id="date-btn" value="Select Date">
                                        @if(!empty(request()->dateFrom) && !empty(request()->dateTo))
                                        <span>
                                        {{ Carbon\Carbon::parse(request()->get('dateFrom'))->format('d/m/Y')}} - {{ Carbon\Carbon::parse(request()->get('dateTo'))->format('d/m/Y')}} 
                                        </span>
                                        @else
                                            <span>
                                            <i class="fa fa-calendar"></i>  &nbsp;Select Date&nbsp;
                                            </span>
                                        @endif
                                        <i class="fa fa-caret-down"></i>
                                    </button>
                                    {{Form::hidden('dateFrom',request()->dateFrom ?? null, array('id'=>'dateFrom'))}}
                                    {{Form::hidden('dateTo', request()->dateTo ?? null, array('id'=>'dateTo'))}}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                    <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                    {{ Form::close() }}
                                </div>                            
                                    <div class="col-md-6">
                                        <a href="{{route('stockCreate')}}" class="btn float-right btn-success">Add new
                                            Stock</a>
                                    </div>                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        
                        <div class="card-body">
                            <p class="card-title">Stock List</p>
                            <div class="">
                                <table id="" style="width: 100%" class="table table-responsive">

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
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($stocks as $stock)
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
                                                <td><a href="{{route('stockEdit',['stock'=>$stock->id])}}"
                                                    class="mdi mdi-table-edit" @if($stock->stockDetails->isNotEmpty()) style="font-size:20px;border-radius:5px;pointer-events: none;" @else style="font-size:20px;border-radius:5px;" @endif></a></td>
                                            </tr>
                                    </tbody>
                                    @empty
                                <tr>
                                    <td colspan="6"><h4><marquee behavior="alternate" direction="right">No data available</marquee></h4></td>
                                    </tr>
                                @endforelse
                                </table>
                                <span style="float: right;" class="mt-3">
                                    {{$stocks->links()}}
                                    </span>
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
        searching:false,

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
                {
                "name": "Currently Available",
                searching: false,
                sorting: false,
                },
                {
                "name": "Stock Details",
                 searching: false,
                 sorting: false,
                },
                {
                "name": "Edit",
                 searching: false,
                 sorting: false,
                },
            

        ],
    });
    $('#date-btn').daterangepicker(
        {
            opens: 'left',
            locale: { cancelLabel: 'Clear' },
            ranges   : {
                'Today'       : [moment(), moment()],
                'Yesterday'   : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 5 Days' : [moment(),moment().subtract(4, 'days')],
                'Last 14 Days': [moment(),moment().subtract(13,'days')],
                'Last 30 Days': [moment(),moment().subtract(29, 'days')],
                'This Month'  : [moment().endOf('month'), moment().startOf('month')],
                'Last Month'  : [moment().subtract(1, 'month').endOf('month'), moment().subtract(1, 'month').startOf('month')]
            },
            // startDate: moment().subtract(29, 'days'),
            //endDate  : moment()
        },
        function (start, end) {
            $('#date-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
            $('#dateFrom').val(start.format('YYYY-M-DD'));
            $('#dateTo').val(end.format('YYYY-M-DD'));
        }
    );
    </script>

@endsection
