@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Asset Form</div>
                    {{ Form::model($asset, ['route' => $submitRoute, 'method' => $method]) }}

                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('type', 'Type', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('sub_type_id', $subTypes, null, ['class' => 'form-control selectJS', 'id' => 'sub-type-id', 'placeholder' => 'Select Type', 'required','readonly', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('barcode', 'Barcode', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{ Form::text('barcode', null, ['class' => 'form-control','placeholder' => 'barcode','disabled' => 'disabled']) }}
                                        </div>
                                    </div>
                                </div>

                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('status', 'Status', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::select('status', $status, null, ['class' => 'form-control selectJS', 'placeholder' => 'Select Type', 'data-placeholder' => 'Select Type']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <div class="form-check form-check-primary">
                                            <label class="form-check-label">Is Exported
                                                <input type="checkbox" name="is_exported" @if($asset->is_exported) checked @endif class="form-check-input">
                                                <i class="input-helper"></i><i class="input-helper"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('Description', 'Description', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::textarea('description',null,['class'=>'form-control','rows'=>'3'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                </div>

                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
