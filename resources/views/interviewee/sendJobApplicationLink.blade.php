@extends('layouts.master')
@section('content')
<div class="row">
  
    <div class="col-sm-12">
      <nav aria-label="breadcrumb" class="float-right">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active" aria-current="page">Register</li>
        </ol>
      </nav>
    </div>
    <div class="col-md-12">
      <div class="card">
        <div class="card-body">
            <h4 class="card-title">Register</h4>

            {{ Form::model($interviewee, ['route' => $submitRoute, 'class' => 'form-group']) }}
            <div class="form-group row">
              {{Form::label('email','Register Email',['class' => 'col-sm-3 col-form-label'])}}
              <div class="col-md-6">
                {{ Form::text('email', null, ['class' => 'form-control', 'required', 'placeholder' => 'Enter Email Id']) }}

                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
              </div>
            </div>
            <div class="col-md-4">
              <button type="submit" class="btn btn-primary float-right">Send</button>
              {{ Form::close() }}
            </div>
        </div>
      </div>
    </div>
</div>
   

@endsection
