@extends('layouts.master')
@section('content')   

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stock Form</li>
                </ol>
            </nav>
        </div>
    
        <div class="col-12 grid-margin">
    
            <div class="card">
    
                {{ Form::model($stock, ['route' => $submitRoute, 'files' => 'true']) }}
                <div class="card-body">
                    <h4 class="card-title">Stock From</h4>
                    {{Form::hidden('id',null)}}
    
                    <div class="row">
                        {{-- dd{{($stock->purchased_source)}} --}}
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('item_id', 'Select Item', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('item_id', $items, null, ['placeholder'=>'select an option','class' => 'form-control selectJS']) }}
                                    @error('item_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('quantity', 'Enter Quantity', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::number('quantity', null, ['id'=>'quantity','class' => 'form-control prc', 'placeholder' => '10*']) }}
    
                                    @error('quantity')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('price_per_item', 'Price Per Item', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::number('price_per_item', null, ['id'=>'price','class' => 'form-control prc', 'minlength' => '1', 'placeholder' => '4000']) }}
    
                                    @error('price_per_item')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('total_price', 'Total Price', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::number('total_price', null, ['id'=>'total_price','class' => 'form-control', 'placeholder' => '40000*']) }}
                                    @error('total_price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('bill','Upload Bill', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        @if (empty($stock->bill))
                                        <input type="file" required name="bill" class="form-control" /> 
                                        @else
                                        <input type="file" name="bill" class="form-control" />
                                        @endif
                                    </div>
                                    @if (!empty($stock->bill))
                                    <br>                              
                                        <a target="_blank" href="{{route('viewBill', ['bill' => $stock->bill])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a> 
                                    @endif
                                    @error('bill')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('purchase_date', 'Purchase Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('purchase_date', null, ['class' => 'form-control', 'placeholder' => 'choose purchase date']) }}
    
                                    @error('purchase_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('purchase_source', 'Select Source', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('purchase_source', $purchaseSource, $stock->purchased_source, ['placeholder'=>'select an option','class' => 'form-control selectJS']) }}
                                    @error('purchase_source')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
        
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
    
    
                        {{Form::close()}}
                    </div>
                </div>
    
            </div>
    
    
    
        </div>
    </div>

    <!-- /.row -->

@endsection
@section('footerScripts')
<script>
   $("#price,#quantity").keyup(function () {
    $('#total_price').val($('#quantity').val() * $('#price').val());
});
    </script>

@endsection