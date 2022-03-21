@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-6">
                            <h4 class="">Employee No Dues Requests</h4>
                        </div>
                    </div>
                    <div class="table-responsive col-12">
                        <table class="table table-borderless" style="width: 100%">

                            <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Department</th>
                                    <th>Exit Date</th>
                                    <th>Reason</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr class="border-top">
                                        <td><a target="_blank" href="{{ $employee->image_source }}"><img
                                                    src="{{ $employee->image_source }}" width="42" height="42"></a>
                                        </td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->office_email }}</td>
                                        <td>{{ $employee->department->name ?? null }}</td>
                                        <td>{{ getFormatedDate($employee->employeeExitDetail->exit_date) }}</td>
                                        <td>{{ $employee->employeeExitDetail->reason }}</td>
                                        <td><a href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                                                class="p-2 text-primary fas fa-address-card"
                                                style="font-size:20px;border-radius:5px;"></a>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td colspan="7">
                                            {{ Form::open(['route' => ['noDuesSubmit', $employee->id], 'class' => 'd-flex']) }}
                                                <div class="col-md-3">
                                                    <p>Manager</p>
    
                                                    @if (empty($employee->employeeExitDetail->dept_no_due) && Auth::user()->can('managerNoDuesApprover', new App\Models\Employee()))
                                                        {{ Form::select('dept_no_due', $actions, $employee->employeeExitDetail->dept_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                    @else
                                                        {{ Form::select('dept_no_due', $actions, $employee->employeeExitDetail->dept_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                    @endif

                                                </div>

                                                <div class="col-md-3">
                                                    <p>IT Department</p>
                                                    @if (empty($employee->employeeExitDetail->it_no_due) && Auth::user()->can('itNoDuesApprover', new App\Models\Employee()) && !empty($employee->employeeExitDetail->dept_no_due) && $employee->equipmentAssigned->isEmpty())
                                                        {{ Form::select('it_no_due', $actions, $employee->employeeExitDetail->it_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                    @else
                                                        {{ Form::select('it_no_due', $actions, $employee->employeeExitDetail->it_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                    @endif

                                                </div>

                                                <div class="col-md-3">
                                                    <p class="label">HR Department</p>
                                                       
                                                    @if (empty($employee->employeeExitDetail->hr_no_due) && Auth::user()->can('hrNoDuesApprover', new App\Models\Employee()) && !empty($employee->employeeExitDetail->it_no_due) && !empty($employee->employeeExitDetail->dept_no_due))
                                                        {{ Form::select('hr_no_due', $actions, $employee->employeeExitDetail->hr_no_due ?? null, ['class' => 'selectJS form-control']) }}
                                                    @else
                                                        {{ Form::select('hr_no_due', $actions, $employee->employeeExitDetail->hr_no_due ?? null, ['class' => 'selectJS form-control', 'disabled' => true]) }}
                                                    @endif

                                                </div>

                                                <div class="col-md-3">
                                                    <br>
                                                    <button type="submit" class="btn btn-primary btn-rounded m-3">Submit</button>

                                                </div>

                                            {{ Form::close() }}
                                        </td>

                                    </tr>

                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
