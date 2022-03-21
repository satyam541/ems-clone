@extends('layouts.master')
@section('content')
    <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb" class="float-right">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Upload Attendance</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Upload Attendance</h4>
                        <p class="card-description">
                        Upload Attendance
                        </p>

                        {{ Form::model(['route' => 'importAttendance', 'class' => 'form-group', 'enctype' => 'multipart/form-data']) }}
                        <div class="form-group">
                            @csrf
                            {{ Form::file('file', null, ['class' => 'form-control', 'required']) }}
                        </div>

                        <button type="submit" class="btn btn-primary btn-rounded btn-fw mr-2">Import Attendance</button>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
    </div>
@endsection
