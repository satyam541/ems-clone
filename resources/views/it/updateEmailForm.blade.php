@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Update Email</li>
            </ol>
        </nav>
    </div>
    <div class="col-12 grid-margin">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Update Email</h4>
                {{ Form::open(['route' => 'updateEmail', 'class' => 'form-sample']) }}
                <div class="form-group row mt-5">

                    {{ Form::label('Department', 'Select Department', ['class' => 'col-sm-2 col-form-label']) }}
                    <div class="col-sm-4">
                        {{ Form::select('department', $departments, null, ['onchange'=>'getEmployees(this.value)','class' => 'form-control selectJS department', 'placeholder' => 'Choose one']) }}
                    </div>
                    
                    {{ Form::label('Employee', 'Select Employee', ['class' => 'col-sm-2 col-form-label']) }}
                    <div class="col-sm-4">
                        {{ Form::select('employee', $employees, null, ['class' => 'form-control selectJS employee', 'id'=>'employees','onchange'=>'getEmail(this.value)','required'=>true, 'placeholder' => 'Choose one']) }}
                    </div>
                    
                    {{ Form::label('Office_email', 'Existing Email', ['class' => 'col-sm-2 col-form-label']) }}
                    <div class="col-sm-4">
                        {!! Form::text('office_email', null, ['class' => 'form-control', 'id'=>'office_email','required'=>true, 'disabled' =>true]) !!}
                    </div>
                    
                    {{ Form::label('email', 'New Email', ['class' => 'col-sm-2 col-form-label']) }}
                    <div class="col-sm-4">
                        {!! Form::text('email', null, ['class' => 'form-control','required'=>true,]) !!}
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mr-2">Submit</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
@endsection
@section('footerScripts')
<script>
    function getEmployees(department) {
        $('#office_email').val('');
        if (department) {
            $.ajax({
                url: "{{route('getEmployees')}}/" + department
                , type: 'get'
                , dataType: 'json'
                , success: function(response) {
                    var options = `<option value=''></option>`;

                    $.each(response, function(id, name) {
                        options += "<option value='" + id + "'>" + name + "</option>";
                    });

                    $('#employees').html(options);
                    $("select").select2({
                        placeholder: "Select an option"
                    });
                }
            })
        }
    }

    function getEmail(employee) {
        console.log(employee);
        if (employee) {
            $.ajax({
                    url: "{{route('getOfficeEmail')}}/" + employee,
                    type: 'get',
                    success: function(response) {

                        $('#office_email').val(response);
                        
                    }
            })
        }
    }

</script>
@endsection
