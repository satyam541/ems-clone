@extends('layouts.master')
@section('content')   

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Leave Form</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
    
            <div class="card">
    
                {{ Form::model($model, ['route' => $submitRoute,'files'=>'true','onsubmit'=>"myButton.disabled = true; return true;"]) }}
                <div class="card-body">
                    <h4 class="card-title">Apply Leave</h4>    
                    <div class="row">
    
                        
    
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('leave_nature', 'Select leave nature', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('leave_nature',$leaveNature, null, ['class' => 'form-control selectJS', 'placeholder'=>'Select an option','required'=>'required']) }}
    
                                    @error('leave_nature')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="form-group row">
                                {{-- {{Form::label('leave_nature','select leave nature',['class'=>'form-control'])}} --}}
                                <div class="form-check ml-3 hidden">
                                    <label class="form-check-label">
                                      <input type="radio" id="short-leave-type" class="form-check-input" name="leave_type" value="Short leave" required>
                                      Short Leave
                                    <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                      <input type="radio" id="halfDayType" class="form-check-input" name="leave_type" value="Half day">
                                      Half Day
                                    <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                      <input type="radio" class="form-check-input" name="leave_type" value="Full day">
                                      Full Day
                                    <i class="input-helper"></i></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="col-6" id="short-leave-timing" style="display: none;">
                                <div class="form-group row">
                                    {{ Form::label('timing', 'Select short leave timing', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{ Form::select('timing',$shortLeaveTimings, null, ['class' => 'form-control selectJS']) }}
        
                                        @error('timing')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" id="leave-type" style="display: none;">
                                {{-- {{Form::label('leave_nature','select leave nature',['class'=>'form-control'])}} --}}
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                      <input type="radio" class="form-check-input" name="halfDayType" value="First half">
                                      First half
                                    <i class="input-helper"></i></label>
                                </div>
                                <div class="form-check ml-3">
                                    <label class="form-check-label">
                                      <input type="radio" class="form-check-input" name="halfDayType" value="Second half">
                                      Second Half
                                    <i class="input-helper"></i></label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('from_date', 'From Date', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::date('from_date', null, ['class' => 'form-control date','min'=>$today, 'placeholder' => 'choose from date','required'=>'required']) }}
            
                                            @error('from_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('to_date', 'To Date', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::date('to_date', null, ['class' => 'form-control date','min'=>$today, 'placeholder' => 'choose to date','required'=>'required']) }}
            
                                            @error('to_date')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('reason', 'Enter Reason', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::textarea('reason', null, ['class' => 'form-control','rows'=>'4','cols'=>'4', 'placeholder' => 'Enter Reason','required'=>'required']) }}
            
                                            @error('reason')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{Form::label('attachment','Attach document', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9 float-left">
                                            <input type="file" name="attachment" class="form-control" /> 
                                        </div>
                                        @error('attachment')
                                            <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                        <div class="form-check form-check-flat form-check-primary">
                            <label class="form-check-label">
                              <input type="checkbox" class="form-check-input" required>
                              I hereby declare that I have not any pending task related company & can contact me anytime if they required * 
                            <i class="input-helper"></i></label>
                        </div>
                        </div>
                        <div class="col-12 mt-3">
                            <button type="submit" name="myButton" class="btn btn-primary">Apply</button>
                        </div>
    
                        {{Form::close()}}
                    </div>
                </div>
    
            </div>
    
    
    
        </div>

    
    </div>
@endsection
@section('footerScripts')
    <script>
        $('input:radio').change(function(){
            if($('#halfDayType').is(':checked'))
            {
                $('#leave-type').show();
            }
            else{
                
                $('#leave-type').hide().find('input:radio').prop('checked',false);
            }
            if($('#short-leave-type').is(':checked'))
            {
                $('#short-leave-timing').show();
                $('.selectJS').select2({
                placeholder: "Select an option",
                allowClear: true
                });
                $('#leave-type').hide().find('input:radio').prop('checked',false);
            }
            else{
                
                $('#short-leave-timing').hide();
            }
            
        });
    </script>
@endsection

