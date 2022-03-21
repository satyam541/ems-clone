@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket Detail</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <h4 class="card-title">Tickets Detail</h4>
                                </div>

                                <div class="col-md-3">
                                    @can('ticketSolver', new App\Models\Ticket())
                                        @if ($ticketDetail->status != 'Closed' && $ticketDetail->status!='Sorted')
                                            <a class="btn btn-primary float-right px-5" data-toggle="modal" id="action"
                                                data-target="#exampleModal">Action</a>
                                        @endif
                                    @endcan
                                </div>
                                <div class="col-md-3">
                                    @if ($ticketDetail->status == 'Closed' && $ticketDetail->employee_id == auth()->user()->employee->id)
                                        <button onclick='action("{{ $ticketDetail->id }}","Reopen")'
                                            class="btn btn-primary btn-lg p-3" data-toggle="modal"
                                            data-target="#closeTicket">Reopen Ticket</button>
                                    @else
                                        @if ($ticketDetail->status != 'Closed')
                                            <button class="btn btn-danger"
                                                onclick='action("{{ $ticketDetail->id }}","Closed")' data-toggle="modal"
                                                data-target="#closeTicket">Close Ticket</button>
                                        @endif
                                    @endif
                                </div>

                            </div>
                            <div class="">
                                <table id="example1" style="width:100%" class="table table-responsive ">

                                    <tbody>
                                        <tr>
                                            <th>Type</th>
                                            <td class="col-md-4 pull-right">
                                                {{ ucfirst($ticketDetail->ticketCategory->type) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Category</th>
                                            <td>{{ ucfirst($ticketDetail->ticketCategory->name) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Employee</th>
                                            <td>{{ $ticketDetail->employee->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $ticketDetail->employee->department->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Subject</th>
                                            <td>
                                                {{ $ticketDetail->subject }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>
                                                {{ $ticketDetail->description }}
                                            </td>
                                        </tr>
                                        @if(!empty($ticketDetail->remote_id))
                                        <tr>
                                            <th>AnyDesk</th>
                                            <td>{{$ticketDetail->remote_id}}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <th>Priority</th>
                                            <td>{{ $ticketDetail->priority }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>{{ $ticketDetail->status }}</td>
                                        </tr>
                                        <tr>
                                            <th>Opened At</th>
                                            <td>{{ getFormatedDateTime($ticketDetail->created_at) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Assigned By</th>
                                            <td>{{ $ticketDetail->assignedBy() }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Updates</h4>
                            <ul class="bullet-line-list">
                                
                                @foreach ($ticketLogs as $log)
                                    <li>
                                        <h6>{{ $log->action }}</h6>

                                        @if (!empty($log->remarks))
                                            <p><strong>Remarks: </strong>{{ $log->remarks }}</p>
                                        @endif

                                        @if ($log->action == 'Assigned' || $log->action == 'Forward')
                                            <p>{{ $log->action }} to {{ $log->assignedTo->name ?? '' }}</p>
                                        @else
                                            <p>{{ $log->action }} by {{ $log->actionBy->name ?? '' }}</p>
                                        @endif

                                        <p class="text-muted mb-4">
                                            <i class="ti-time"></i>
                                            {{ getFormatedDateTime($log->created_at) }}
                                        </p>
                                    </li>
                                @endforeach
                                <li>
                                    <h6>Ticket Opened</h6>
                                    <p>Ticket opened by {{ $ticketDetail->employee->name }}</p>
                                    <p class="text-muted mb-4">
                                        <i class="ti-time"></i>
                                        {{ getFormatedDateTime($ticketDetail->created_at) }}
                                    </p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ticket Action: <span></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
        
                {{ Form::open(['route' => 'raiseTicketAction']) }}
                <div class="modal-body">
                    {{ Form::hidden('id', $ticketDetail->id) }}
                    <div class="form-group row">
                        {{ Form::label('action', 'Action :', ['class' => 'col-sm-4 col-form-label']) }}
                        <div class="col-sm-8">
                            {{ Form::select('action', $status, null, ['class' => 'form-control col-sm-11 selectJS', 'placeholder' => 'Select an option', 'id' => 'status', 'required' => 'required']) }}
                        </div>

                    </div>
                    <div class="forward">
                        <div class="form-group row">
                            {{ Form::label('assigned_to', 'Forward To:', ['class' => 'col-sm-4 col-form-label']) }}
                            <div class="col-sm-8">
                                <select name="assigned_to" class="col-sm-10 form-control selectJS" id="assignedTo"
                                    title="Select Employee" placeholder="Select Employee"
                                    data-placeholder="Select Employee">
                                    @foreach ($employees as $employee)
                                        <option value="{{ $employee->id }}">{{ $employee->name }}
                                            ({{ $employee->department->name }})</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="form-group row">
                            {{ Form::label('remarks', 'Remarks :', ['class' => 'col-sm-4 col-form-label']) }}
                            <div class="col-sm-8">
                                {{ Form::text('remarks', null, ['id' => 'remarks', 'class' => 'form-control']) }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Action</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="modal fade" id="closeTicket" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{-- <h5 class="modal-title" id="exampleModalLabel">Ticket Action: <span></span></h5> --}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                {{ Form::open(['route' => 'raiseTicketAction', 'id' => 'form_action']) }}
                <div class="modal-body">
                    {{ Form::hidden('id', null) }}
                    {{ Form::hidden('action', null) }}
                    <div class="form-group row">
                        {{ Form::label('remarks', 'Remarks:', ['class' => 'col-sm-3 col-form-label']) }}
                        {{ Form::text('remarks', null, ['class' => 'col-7 form-control', 'placeholder' => 'Enter Remarks', 'required']) }}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>
        $(document).ready(function() {
            $('.forward').hide();
        });
        $(document).on('change', "select[name='action']", function() {
            if ($(this).val() == 'Forward') {
                $('.forward').show();
                $('#remarks,#assignedTo').attr('required', true);

            } else {
                $('.forward').hide();
                $('#remarks,#assignedTo').attr('required', false);

            }


        });

        function action(ticketId, action) {
            $('#form_action input[name=id]').val(ticketId);
            $('#form_action input[name=action]').val(action);

        }
    </script>

@endsection
