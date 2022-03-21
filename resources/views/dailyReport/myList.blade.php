@extends('layouts.master')
@section('content')
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 15px;
        }

        td {
            vertical-align: top;
        }

    </style>

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Daily Reports</li>
                </ol>
            </nav>
        </div>
        
    </div>
    <div class="row">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <p class="card-title float-left">My Daily Reports</p>
                  
                        <div class="float-right">
                         
                            <div class="form-group">
                                <form>
                                    {{ Form::select('month',['current'=>'This Month','last_month'=>'Previous Month'],request()->month ?? '',['class'=>'form-control','onchange'=>"this.form.submit()"])}}
                              
                            
                            </div>
                            
                            </form>
                        </div>
                   

                    <div class="table-responsive">
                        <table class="table table-striped">

                            <thead>
                                <tr class="">
                                    <th class="">Date</th>
                                    <th>Leave Nature</th>
                                    <th class="">Task 1</th>
                                    <th class="">Task 2</th>
                                    <th class="">Task 3</th>
                                    <th class="">Task 4</th>
                                    <th class="">Task 5</th>
                                    <th class="">Task 6</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($reports as $report)
                                    @if(Carbon\Carbon::parse($report->report_date)->addDays(1)->format('l')=='Sunday' && $report->report_date!=Carbon\Carbon::now()->format('Y-m-d'))
                                    <tr>
                                        <td colspan="8" class="text-center"><strong>Sunday<strong></td>
                                    </tr> 
                                    @endif
                                    <tr>
                                        <td class="">{{ getFormatedDate($report->report_date) }}</td>
                                        <td>{{$report->employeeLeaveNature()}}</td>
                                        <td style="white-space: normal;">{{ $report->task1 }}</td>
                                        <td style="white-space: normal;">{{ $report->task2 }}</td>
                                        <td style="white-space: normal;">{{ $report->task3 }}</td>
                                        <td style="white-space: normal;">{{ $report->task4 }}</td>
                                        <td style="white-space: normal;">{{ $report->task5 }}</td>
                                        <td style="white-space: normal;">{{ $report->task6 }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>

    </div>
    <div class="col-sm-12">
        <div class="float-right">
            {{$reports->appends(request()->query())->links()}}
        </div>
        
    </div>
@endsection


@section('footerScripts')
    <script>
    </script>

@endsection
