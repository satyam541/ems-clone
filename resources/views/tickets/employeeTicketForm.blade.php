@extends('layouts.master')
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">


                <div class="card-body">
                    <h4 class="card-title">Ticket Form <strong class="float-right">Ticket will be raised to HR</strong></h4>
                    
                    {{Form::open(array('route'=>$submitRoute,'onsubmit'=>"myButton.disabled = true; return true;"))}}
                    {{Form::hidden('id',null)}}

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('category','Category', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::select('category', $ticketCategories,null,['class' => 'form-control selectJS ticket-category','placeholder'=>'Select Category','data-placeholder'=>'Select Category','required'=>'required',])}}
                                </div>
                            </div>
                        </div>

                         <div class="col-md-6 hr-subject">
                            <div class="form-group row">
                                {{Form::label('subject','Subject', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-8">
                                    {{Form::text('subject',null,['class'=>'form-control','placeholder'=>'Enter Subject'])}}
                                </div>
                            </div>
                        </div>

                       <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('description','Description', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-8">
                                    <textarea name="description"  rows="3" class="form-control" placeholder="Enter Description" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('priority','Select Priority', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::select('priority', $priority,null,['class' => 'form-control selectJS','required'=>'required','placeholder'=>'Select Priority'])}}

                                </div>
                            </div>
                        </div>
                       
                        <div class="col-12 mt-3">
                            <button type="submit" name="myButton" class="btn btn-primary">Submit</button>
                        </div>

                    </div>
                    {{Form::close()}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        

    </script>
@endsection
