<div class="modal-body">
{{ Form::open(['route' => 'onboardStatusUpdate','method' => 'post']) }}
{{ Form::hidden('type', $type, ['id' => 'tutor_id']) }}
{{Form::hidden('id',$employee->id,['id'=>'course_training_id'])}}
<div class="form-group col-sm-12 row">
    {{ Form::label('name', 'Name', ['class' => 'control-label col']) }}
    {{ Form::text('name', $employee->name, ['class' => 'form-control col','readonly']) }}
</div>
@if($type=='interview')
@php $email = $employee->email @endphp
@else
@php $email = $employee->office_email @endphp
@endif
<div class="form-group col-sm-12 row">
    {{ Form::label('email', 'Email', ['class' => 'control-label col']) }}
    {{ Form::text('email', $email, ['class' => 'form-control col','readonly']) }}
</div>
@if($type=='interview')
<div class="form-group col-sm-12 row">
    {{ Form::label('is_selected', 'Select Action', ['class' => 'control-label']) }}
    {{ Form::select('is_selected', $action, null, ['class' => 'selectJS ', 'placeholder' => 'select an option', 'data-placeholder' => 'select an option', 'id' => 'course-select', 'required']) }}

</div>
@endif
@if($type=='selected')
<div class="form-group col-sm-12 row">


</div>
@endif
<div class="modal-footer">
    @if($type=='selected')
    <a href="{{ route('sendDocumentLink',['id'=>$employee->id]) }} " class="btn btn-primary"> Send Document Link</a>
    @elseif($type=='document submitted')
    <button type="submit" class="btn btn-success text-white">Move To Onboard</button>
    @else
    <button type="submit" class="btn btn-success text-white">Submit</button>
    @endif
</div>
{{ Form::close() }}
</div>


