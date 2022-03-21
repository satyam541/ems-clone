@extends('layouts.master')
@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css" />

{{-- <style>
  .text-danger {
    display: none;
  
</style> --}}



<div class="row">
    <div class="col-12 grid-margin">
        <!-- general form elements -->
        <div class="card">
            {{ Form::model($leave, ['route'=>$submitRoute,'id' => 'leaveForm', 'class' => 'form-sample']) }}
            <div class="card-body">
                <div class="float-lg-right row">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashbpard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('leaveApprovalView') }}">Approval List</a>
                            </li>
                            <li class="breadcrumb-item active">Approval Form</li>
                        </ol>
                    </nav>
                </div>
                <h4 class="card-title">
                    Leave Approval
                </h4>
                <p class="card-description">
                    Leave Form
                </p>
                <!-- form start -->
                {{ Form::hidden('id', null) }}
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('name', 'Name', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::text('name', $leave->employee->user->name ?? '', ['class' => 'form-control', 'disabled' => 'disabled']) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('department', 'Department', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::text('department', $leave->employee->department->name ?? '', ['class' => 'form-control', 'disabled' => 'disabled']) }}

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('type', 'Leave Type', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::select('type', $leaveTypes, null, ['class' => 'form-control selectJS', 'required' => 'required', 'onChange' => 'getType(this)', 'id' => 'type','disabled' => 'disabled']) }}

                                <span class="text-danger" id="type_error"></span>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('reason', 'Reason', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::textArea('reason', null, ['class' => 'form-control', 'required' => 'required', 'rows' => '2', 'placeholder' => 'Enter Reason','disabled' => 'disabled']) }}

                                <span class="text-danger" id="comment_error"></span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('from_date', 'Date From', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::date('from_date', null, ['class' => ' form-control', 'required' => 'required', 'disabled' => 'disabled']) }}

                                <span class="text-danger" id="from_date_error"></span>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('to_date', 'Date To', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::date('to_date', null, ['class' => ' form-control', 'required' => 'required', 'disabled' => 'disabled']) }}

                                <span class="text-danger" id="to_date_error"></span>

                            </div>
                        </div>
                    </div>

                    {{-- <div class="col-md-6">
                      <div class="form-group row">
                        <div id="durationField">
                          {{ Form::label('duration', 'Duration Period', ['class' => 'col-sm-3 col-form-label']) }}
                    <div class="col-sm-9">
                        @if (!empty($leave->id))
                        {{Form::select('duration', $duration, null,['class'=>'form-control', 
                                           'required' => 'required', 'disabled' => 'disabled' ])}}
                        @else
                        {!! $duration !!}
                        @endif
                        <span class="text-danger" id="duration_error"></span>
                    </div>
                </div>
            </div> --}}

            <div class="col-md-6">
                <div class="form-group row">
                    {{ Form::label('status', 'Status', ['class' => 'col-sm-3 col-form-label']) }}
                    <div class="col-sm-9">
                        {{ Form::select('status', $status, null, ['class' => 'form-control selectJS','onchange' => 'statusChanged(this)']) }}

                        @error('status')
                          <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="form-group @if ($leave->status != 'Rejected') d-none @endif" id="reason_rejected">
                            {{ Form::label('reason_rejected', 'Reason for Rejected') }}
                            {{ Form::textArea('reason_rejected', null, ['class' => 'form-control', 'rows' => 2, 'placeholder' => 'Enter Reason for rejected']) }}

                            @error('reason_rejected')
                              <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <div class="form-check form-check-primary col-sm-6">
                        {{ Form::checkbox('emergency', null, null, ['disabled' => 'disabled']) }}
                        {{ Form::label('emergency', 'Emergency', ['class' => 'ml-2']) }}
                    </div>
                    <div class="form-check form-check-primary col-sm-6">
                        @if ($leave->forwarded)
                        {{ Form::checkbox('forwarded', null, null, ['disabled' => 'disabled']) }}
                        @else
                        {{ Form::checkbox('forwarded', null) }}
                        @endif
                        {{ Form::label('forwarded', 'Forward to HR', ['class' => 'ml-2']) }}
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary mr-2">Submit</button>
        </div>
        {{ Form::close() }}
        <!-- /.card-body -->
    </div>
</div>

@endsection

@section('footerScripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.js"></script>
<script>
    function getType(sel) {
        search_term = $(sel).val();
        $.ajax({
            url: "{{ route('leaveType') }}",
            type: "GET",
            dataType: "JSON",
            data: {
                'search_term': search_term,
                'id': '{{ $leave->id }}',
                'disabled': true
            },
            success: function (data) {
                $('#durationField').html(data.duration);
                if (search_term == 'Short Leave') {
                    $('#dates').hide();
                    $('.leave_duration').timepicker({
                        'scrollDefault': 'now',
                        'minTime': '9:00am',
                        'maxTime': '6:00pm',
                    });
                    times = data.durationValue.split(';');
                    $('#from_time').val(times[0]);
                    $('#to_time').val(times[1]);
                } else {
                    $('#dates').show();
                    if (data.durationValue != '') {
                        $('#duration').val(data.durationValue);
                    }
                }
            },
        });
    }

    $(document).ready(function () {

        $('#type').trigger('change');

        /* $('#leaveForm').on('submit', function(e) {
          e.preventDefault();

          //hide form error messages if any from prevous failure.
          $('.text-danger').val('').hide();

          $.ajax({
              url : $(this).prop('action'),
              type : 'POST',
              dataType : 'JSON',
              data : $(this).serialize(),
              success : function(data) {
                  location.reload(true);
              },
              error : function(data) {

              errors = data.responseJSON.errors;
              $.each(errors, function(key, value) {
                  
                  item = $('#' + key + '_error');
                  item.html(value);
                  item.show();

              });

              },
          });
        }); */

    });

    function statusChanged(item) {
        status = $(item).val();
        if (status == 'Rejected') {
            $('#reason_rejected').removeClass('d-none');
        } else {
            $('#reason_rejected').addClass('d-none');
        }
    }

</script>
@endsection
