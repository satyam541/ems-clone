@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">Leave Balance Form

                        <span class="float-right text-right col-sm-3">
                            <form action="" method="GET">
                                <input type="hidden" name="month" value="{{$leaveBalance->month}}">
                                <select style='width:100%;' name="user_id" data-placeholder="select an option"
                                        placeholder="select an option" class='selectJS' onchange="this.form.submit();">
                                        @foreach ($employeeDepartments as $department => $employees)
                                            <optgroup label="{{ $department }}">
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->id }}"
                                                        @if ($employee->id == request()->user_id || $employee->id == $leaveBalance->user_id) selected @endif>
                                                        {{ $employee->name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                            </form>
                        </span>

                    </div>
                    {{ Form::model($leaveBalance, ['route' => $submitRoute, 'method' => $method]) }}
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('name',$user, ['class' => 'form-control',  'placeholder' => 'Select Type','readonly'=>'readonly', 'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('month', 'Month', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('month',null, ['class' => 'form-control',  'placeholder' => 'Select Type','readonly'=>'readonly', 'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('balance', 'Balance', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('balance', null, ['class' => 'form-control',  'placeholder' => 'Select Type',  'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('absent', 'Absent', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{ Form::text('absent', null, ['class' => 'form-control','placeholder' => 'Absent']) }}
                                        </div>
                                    </div>
                                </div>

                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('taken_leaves', 'Taken Leaves This Month', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('taken_leaves',  null, ['class' => 'form-control', 'placeholder' => 'Taken Leaves']) !!}
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('after_cut_off', 'Taken Leaves After Cutoff', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('after_cut_off',  $leaveBalance->leaves_after_cut_off, ['class' => 'form-control','disabled' => 'disabled']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('sundays', 'Total Sundays', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('sundays',  $leaveBalance->total_sundays, ['class' => 'form-control','disabled' => 'disabled']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('deduction', 'Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('deduction',null,['class'=>'form-control','placeholder' => 'Deduction'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('prev_month_deduction', Carbon\Carbon::createFromFormat('Y-m-d',$leaveBalance->month)->subMonth()->format('F').' Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('prev_month_deduction',null,['class'=>'form-control','placeholder' => 'Prev Month Deduction'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('next_month_deduction', Carbon\Carbon::createFromFormat('Y-m-d',$leaveBalance->month)->addMonth()->format('F').' Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('next_month_deduction',null,['class'=>'form-control','placeholder' => 'Next Month Deduction'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary me-2">Submit</button>
                                </div>

                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
