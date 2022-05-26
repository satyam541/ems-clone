@extends('layouts.master')
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employeeView') }}">Employee</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">

                {{ Form::model($employee, ['route' => $submitRoute, 'files' => 'true']) }}
                <div class="card-body">
                    <h4 class="card-title">Employee Onboard Form</h4>
                    {{Form::hidden('id',null)}}
                    {{Form::hidden('onboard_status','onboard')}}

                    <div class="row">

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('name', 'Employee Name', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                 {{ Form::text('name', null, ['class' => 'form-control', 'readonly' => 'readonly', 'placeholder' => 'Employee Name']) }}

                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('phone', 'Contact Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::tel('phone', null, ['class' => 'form-control', 'onkeyup' => 'if(isNaN(this.value)){this.value=""}','required', 'minlength' => '10', 'maxlength' => '10', 'placeholder' => 'Contact Number']) }}

                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('aadhaar_number','Adhaar Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('aadhaar_number',$employee->documents->aadhaar_number ?? null,['class'=>'form-control', 'required','placeholder'=>'XXXX XXXX XXXX'])}}

                                    @error('aadhaar_number')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('aadhaar_file','Adhaar Upload', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        <input type="file" @if(!auth()->user()->hasRole('HR')) required @endif name="aadhaar_file" class="form-control" />
                                    </div>
                                    @if (!empty($employee->documents->aadhaar_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->aadhaar_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @error('aadhaar_file')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('pan_number','Pan Number', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::text('pan_number',$employee->documents->pan_number ?? null,['class'=>'form-control','required', 'placeholder'=>'XXXX XXXX XXXX'])}}

                                    @error('pan_number')
                                    <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('pan_file','Pan Upload', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <div class="col-9 float-left">
                                        <input type="file" name="pan_file" @if(!auth()->user()->hasRole('HR')) required @endif class="form-control" accept="pdf" />
                                    </div>
                                    @if (!empty($employee->documents->pan_file))
                                    <div class="col-3 float-right text-right">
                                        <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->pan_file])}}">
                                            <i class="fa fa-eye text-primary"></i>
                                        </a>
                                    </div>
                                    @endif
                                    @error('pan_file')
                                        <span class="text-danger">{{$message}}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @if($employee->onboard_status=='documents submitted' && auth()->user()->hasRole('HR'))
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('department_id', 'Select Department', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('department_id', $departments, null, ['class' => 'form-control selectJS','required', 'placeholder' => 'Choose one']) }}
                                    @error('department_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('join_date', 'Join Date', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::date('join_date', $employee->join_date, ['class' => 'form-control','required', 'placeholder' => 'choose join date']) }}

                                    @error('join_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($employee->onboard_status=='contract pending' && auth()->user()->hasRole('HR'))
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('status', 'Select Status', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{ Form::select('status', $onboardStatus, null, ['class' => 'form-control selectJS','required', 'placeholder' => 'Choose one']) }}
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="col-md-12 mb-1 bank-form" style="border-top:1px solid #ced4da;">
                            <span class="card-title">Bank Details:</span>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{ Form::label('account_holder', 'Account Holder', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('account_holder', $employee->bankdetail->account_holder ?? '', ['class' => 'form-control','required', 'placeholder' => 'Account Holder']) }}

                                            @error('account_holder')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('bank_name', 'Bank Name', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('bank_name', $employee->bankdetail->bank_name ?? '', ['class' => 'form-control','required', 'placeholder' => 'Bank Name']) }}

                                            @error('bank_name')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('account_no', 'Account Number', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::number('account_no', $employee->bankdetail->account_no ?? '', ['class' => 'form-control','required', 'placeholder' => 'Account Number']) }}

                                            @error('account_no')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group row">

                                        {{ Form::label('ifsc_code', 'IFSC Code', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            {{ Form::text('ifsc_code', $employee->bankdetail->ifsc_code ?? '', ['class' => 'form-control','required', 'placeholder' => 'IFSC Code']) }}

                                            @error('ifsc_code')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        {{Form::label('cheque','Cancel Cheque/ Passbook', ['class' => 'col-sm-3 col-form-label']) }}
                                        <div class="col-sm-9">
                                            <div class="col-9 float-left">
                                                <input type="file" @if(!auth()->user()->hasRole('HR')) required @endif name="cheque" accept="pdf" />
                                            </div>
                                            @if (!empty($employee->documents->cheque))
                                            <div class="col-3 float-right text-right">
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cheque])}}">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a>
                                            </div>
                                            @endif
                                            @error('cheque')
                                                <span class="text-danger">{{$message}}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                        @if($employee->onboard_status=='asked for documents')
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        @elseif(($employee->onboard_status=='documents submitted' || $employee->onboard_status=='contract pending') && auth()->user()->hasRole('HR'))
                        <div class="col-12 mt-3">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                        @endif

                        {{Form::close()}}
                    </div>
                </div>

            </div>



        </div>
    </div>
    <!-- /.row -->

@endsection
