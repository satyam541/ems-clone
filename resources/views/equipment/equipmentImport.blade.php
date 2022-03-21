@extends('layouts.master')
@section('content')

    <div class="card">
        <div class="card-body">
            <div class="row float-right">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Upload Equipment</li>
                    </ol>
                </nav>
            </div>
            @can('it', new App\Models\Equipment())
                <h4 class="card-title">Upload Equipment</h4>
                <p class="card-description">
                    Upload Equipment
                </p>

                {{ Form::model(['route' => 'equipmentImport', 'class' => 'form-group', 'enctype' => 'multipart/form-data']) }}
                <div class="form-group">
                    @csrf
                    {{ Form::file('file', null, ['class' => 'form-control', 'required']) }}
                </div>

                <button type="submit" class="btn btn-primary mr-2">Import Equipment</button>
                {{ Form::close() }}
            </div>
        </div>
    @endcan
@endsection
