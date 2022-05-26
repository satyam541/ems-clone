@extends('layouts.master')
@section('content')
    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Pre Details Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">


                <div class="card-body">
                    <h4 class="card-title">Pre Details Form</h4>
                    {{ Form::model($object, ['route' => 'predetails.store', 'files' => 'true']) }}
                    <div class="row">
                        <div class="col md-12">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('name', 'Name', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-8">
                                        {{ Form::text('name', auth()->user()->name, ['class' => 'form-control','disabled' => 'disabled','placeholder' => 'Write here...','required' => true,'autocomplete' => 'off']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('contact_number', 'Contact Number', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-8">
                                        {{ Form::number('contact_number', null, ['class' => 'form-control','placeholder' => 'Write here...','autocomplete' => 'off']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('linked_in', 'LinkedIn', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-8">
                                        {{ Form::text('linked_in', null, ['class' => 'form-control','placeholder' => 'Write here...','autocomplete' => 'off']) }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('cv','CV', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-9">
                                        <div class="float-left">
                                            <input type="file" name="cv" class="form-control" accept="pdf" />
                                        </div>
                                        @if (!empty($object->cv))
                                        <div class="col-2 float-right">
                                            <a target="_blank"
                                                    href="{{ route('downloadCommonDocument', ['folder'=>'employeePreDetails','reference' => $object->cv]) }}">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                        </div>
                                        @endif
                                        @error('cv')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>

            </div>



        </div>
    </div>
@endsection
