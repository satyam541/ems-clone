@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12 grid-margin">
            <div class="card">
                <div class="card-body">
                    <div class="card-title">My Balance
                        <span class="float-right text-right">
                            <form action="" method="GET">
                                <input type="month" name="month" onchange="this.form.submit()"
                                    value="{{ request()->month }}">
                            </form>
                        </span>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">

                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('name',$myBalance->user->name ?? '', ['class' => 'form-control',  'placeholder' => 'Select Type','readonly'=>'readonly', 'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('month', 'Month', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('month',getFormatedDate($myBalance->month), ['class' => 'form-control',  'placeholder' => 'Select Type','readonly'=>'readonly', 'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('balance', 'Balance', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('balance', $myBalance->balance, ['class' => 'form-control',  'placeholder' => 'Select Type','readonly'=>'readonly',  'data-placeholder' => 'Enter Balance']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('absent', 'Absent', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{ Form::text('absent', $myBalance->absent, ['class' => 'form-control','placeholder' => 'Absent','readonly'=>'readonly']) }}
                                        </div>
                                    </div>
                                </div>

                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('taken_leaves', 'Taken Leaves This Month', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('taken_leaves',  $myBalance->taken_leaves, ['class' => 'form-control', 'placeholder' => 'Taken Leaves','readonly'=>'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('after_cut_off', 'Taken Leaves After Cutoff', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('after_cut_off',  $myBalance->leaves_after_cut_off, ['class' => 'form-control','disabled' => 'disabled','readonly'=>'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('sundays', 'Total Sundays', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {!! Form::text('sundays',  $myBalance->total_sundays, ['class' => 'form-control','disabled' => 'disabled','readonly'=>'readonly']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('deduction', 'Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('deduction',$myBalance->deduction,['class'=>'form-control','placeholder' => 'Deduction','readonly'=>'readonly'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('prev_month_deduction', Carbon\Carbon::createFromFormat('Y-m-d',$myBalance->month)->subMonth()->format('F').' Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('prev_month_deduction',null,['class'=>'form-control','placeholder' => 'Prev Month Deduction','readonly'=>'readonly'])}}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group row">
                                        {!! Form::label('next_month_deduction', Carbon\Carbon::createFromFormat('Y-m-d',$myBalance->month)->addMonth()->format('F').' Deduction', ['class' => 'col-sm-3 col-form-label']) !!}
                                        <div class="col-sm-9">
                                            {{Form::text('next_month_deduction',null,['class'=>'form-control','placeholder' => 'Next Month Deduction','readonly'=>'readonly'])}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
