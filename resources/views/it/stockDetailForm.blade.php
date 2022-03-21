@extends('layouts.master')
@section('content')   

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stock Detail Form</li>
                </ol>
            </nav>
        </div>
    
        <div class="col-12 grid-margin">
    
            <div class="card">
    
                {{ Form::model($stockDetail, ['route' => $submitRoute, 'files' => 'true','onsubmit'=>'document.getElementById("submit").disabled=true;']) }}
                <div class="card-body">
                    <h4 class="card-title">Stock Detail Form</h4>
                    {{Form::hidden('id',null)}}
                    {{Form::hidden('stock_id',$stock->id)}}
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('item_name', 'Item Name', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('item_name',ucfirst($stock->item->name), ['class' => 'form-control','disabled'=>'disabled']) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('manufacturer', 'Manufacturer', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('manufacturer', null, ['class' => 'form-control','placeholder'=>'Dell*']) }}
                                    @error('manufacturer')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('model_no', 'Enter model No', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('model_no', null, ['class' => 'form-control', 'placeholder' => 'sds98s*']) }}
    
                                    @error('model_no')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if($stock->item->assignable)
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('label', 'Enter label', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                {{Form::text('label',null,['placeholder'=>'M1*','autocomplete'=>'off','class'=>'form-control'])}}
                                    <span class="text-danger duplicateLabel">{{ $errors->first('label') ?? null }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('warranty_from', 'Enter Warranty From', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('warranty_from', null, ['class' => 'form-control', 'placeholder' => '*']) }}
    
                                    @error('warranty_from')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('warranty_till', 'Enter Warranty Till', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('warranty_till', null, ['class' => 'form-control', 'placeholder' => '*']) }}
    
                                    @error('warranty_till')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('status', 'Select Status', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('status', $status, null, ['class' => 'form-control selectJS']) }}
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
        
                        <div class="col-12 mt-3">
                            <button type="submit" id="submit" class="btn btn-primary">Submit</button>
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
    $('input[name="label"]').keyup(function(){
        $(this).val($(this).val().toUpperCase());
        var label=$(this).val();
        var id=$('input[name="id"]').val();
        var link="{{route('checkLabelExists')}}";
        $.ajax({
            url:link,
            type:'GET',
            data:{id:id,label:label},
            success:function(response)
            {
                if(response)
                {
                 $('.duplicateLabel').html('The label has already taken');   
                }
                else{
                    $('.duplicateLabel').html('');
                }
            }
        });
    });
</script>
@endsection
