@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        {{Form::open(['route'=>'switchlogin', 'method' => 'POST'])}}
                        <div class="card-body">

                            <p class="card-title">User List</p>
                            {{Form::select('id',$users,null,['class'=>'form-group selectJS','style'=>'width: 659.8px'])}}

                                <button class="btn btn-primary mt-4">Submit</button>



                        </div>



                    </div>
                     {{ Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection
