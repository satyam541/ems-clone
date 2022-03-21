@extends('layouts.master')
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Daily Report Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">


                <div class="card-body">
                    <h4 class="card-title">Daily Report Form</h4>
                    {{ Form::model($report, ['route' => 'dailyReport.submit']) }}
                    {{ Form::hidden('id', null) }}


                    <div class="row">
                        <div class="col md-12">
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('report_date', 'Report Date', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        @if ($min == $max && empty($report->report_date))
                                        {{ Form::date('report_date', $max ?? null, ['class' => 'form-control', 'min' => $min, 'max' => $max, 'id' => 'report_date','required'=> true]) }}
                                        @else
                                        {{ Form::date('report_date', null, ['class' => 'form-control', 'min' => $min, 'max' => $max, 'id' => 'report_date','required'=> true]) }}
                                        @endif

                                        @error('report_date')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                    @if(empty($leaves) )
                      @for($i=1;$i<=6;$i++)

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('task'.$i, 'Task'.$i, ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-10">
                                    {{ Form::text('task'.$i, null, ['class' => 'form-control', 'placeholder' => 'Write here...','required'=> true, 'autocomplete' => 'off']) }}
                                    {{-- @error('task'.$i)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror --}}
                                </div>
                            </div>
                        </div>
                     @endfor
                    @else
                     @for($i=1;$i<=3;$i++)
                        <div class="col-md-6">
                            <div class="form-group row">
                                {{ Form::label('task'.$i, 'Task'.$i, ['class' => 'col-sm-2 col-form-label']) }}
                                <div class="col-sm-10">
                                    {{ Form::text('task'.$i, null, ['class' => 'form-control', 'placeholder' => 'Write here...','required'=> true, 'autocomplete' => 'off']) }}
                                    {{-- @error('task'.$i)
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror --}}
                                </div>
                            </div>
                        </div>
                     @endfor
                    @endif
                            {{-- <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task1', 'Task 1', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task1', null, ['class' => 'form-control', 'placeholder' => 'Write here...','required'=> true, 'autocomplete' => 'off']) }}
                                        @error('task1')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task2', 'Task 2', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task2', null, ['class' => 'form-control', 'placeholder' => 'Write here...', 'autocomplete' => 'off']) }}
                                        @error('task2')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task3', 'Task 3', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task3', null, ['class' => 'form-control', 'placeholder' => 'Write here...', 'autocomplete' => 'off']) }}
                                        @error('task3')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task4', 'Task 4', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task4', null, ['class' => 'form-control', 'placeholder' => 'Write here...', 'autocomplete' => 'off']) }}
                                        @error('task4')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task5', 'Task 5', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task5', null, ['class' => 'form-control', 'placeholder' => 'Write here...', 'autocomplete' => 'off']) }}
                                        @error('task5')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{ Form::label('task6', 'Task 6', ['class' => 'col-sm-2 col-form-label']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('task6', null, ['class' => 'form-control', 'placeholder' => 'Write here...', 'autocomplete' => 'off']) }}
                                        @error('task6')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div> --}}



                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </div>
                    {{ Form::close() }}
                </div>

            </div>



        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        // $('#report_date').on('change', function(){
        //     date = $('#report_date').val();
        //     console.log(date);
        // });
    </script>
@endsection
