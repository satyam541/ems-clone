@extends('layouts.master')
@section('content')   

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
    
            <div class="card">
    
                {{ Form::model($item, ['route' => $submitRoute]) }}
                <div class="card-body">
                    <h4 class="card-title">Item Form</h4>
                    {{Form::hidden('id',null)}}
    
                    <div class="row">
    
                        
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('name', 'Item Name', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('name', null, ['class' => 'form-control', 'id' => 'name', 'placeholder' => 'laptop *']) }}
    
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="form-check form-check-flat form-check-primary">
                            <label class="form-check-label">
                              <input type="checkbox" name="assignable" class="form-check-input" @if($item->assignable) checked @endif>
                              Is Assignable
                            <i class="input-helper"></i></label>
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
@endsection

