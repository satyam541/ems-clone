@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12 grid-margin">
        @if(empty(request()->route('request_id')))
        <!-- general form elements -->
        <div class="card">
            {{ Form::model($entity_request, ['route' => $submitRoute, 'class' => 'form-sample']) }}
            {{ Form::hidden('requested_by',auth()->user()->employee->id) }}
            {{ Form::hidden('request_id', $requestnumber) }}


            <div class="card-body">
                <h4 class="card-title">Equipment Request</h4>

                <p class="card-description">
                    Equipment Request : {{$requestnumber}}
                </p>

                <!-- form start -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('entity_id', 'Select Entity', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::select('entity_id', $entity,null,['class'=>'form-control selectJS','placeholder'=>'Select Entity','data-placeholder'=>'Select Entity',
                             'data-placeholder'=>'Select Status','id'=>'entity','onchange'=>'getEmployees(this.value)'])}}
                                @error('entity_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('employee_id', 'Select Employee', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::select('employee_id[]',array(),null,['class'=>'form-control selectJS', 'placeholder'=>'Select Employee','id'=>'employees' ])}}

                                @error('employee_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('comment', 'Manager\'s Comment', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{ Form::textarea('comment', null, ['class'=>'form-control','rows'=>'2']) }}

                                @error('comment')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(!empty($entity_request->id))
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('status', 'Status', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::select('status', ['pending'=>'Pending', 'approved'=>"Approved", 'rejected'=>'Rejected'], null, ['class'=>'form-control selectJS',
                            'data-placeholder'=>'Select Status','placeholder'=>'Select Status'])}}

                                @error('status')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    {{Form::hidden('action_taken_by', auth()->user()->id)}}
                    <div class="col-md-6">
                        <div class="form-group row">
                            {{ Form::label('remarks', 'Remarks', ['class' => 'col-sm-3 col-form-label']) }}
                            <div class="col-sm-9">
                                {{Form::textarea('remarks', null, ['class'=>'form-control','rows'=>'2'])}}
                                @error('remarks')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @endif
                    <!--  -->
                </div>
                <button type="submit" class="btn btn-primary mr-2">Request</button>
                {{-- </div> --}}

                <!-- /.card-body -->
            </div>
        </div>
    </div>

    @else
    @foreach($equipment_requests as $request_id => $equipmentrequests)
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Department : {{$department}}</h4>
            <p class="card-description">Request Id : {{$request_id}}</p>
            @foreach($equipmentrequests as $equipmentrequest)

            <div class="template-demo">
                <div class="loading-ico"
                    style="position:absolute;left: 0;top: 0;right: 0;bottom: 0;display: none;height: auto;width: auto;">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1699/xlink"
                        style="margin:auto;background:rgb(255 255 255 / 80%);display:block;z-index:9" width="320px"
                        height="380px" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid">
                        <path d="M40 50A10 10 0 0 0 60 50A10 11 0 0 1 40 50" fill="#0a69aa" stroke="none">
                            <animateTransform attributeName="transform" type="rotate" dur="1s" repeatCount="indefinite"
                                keyTimes="0;1" values="0 50 51.1;360 50 51.1"></animateTransform>
                        </path>
                    </svg>
                </div>
                {{Form::model($equipmentrequest,['method'=>'GET','id'=>'editable-form','class' => 'editable-form'])}}
                {{Form::hidden('id', null)}}
                <div class="form-group row ">
                    {{Form::label('entity_id', 'Requested Entity',['class'=>'col-6 col-lg-4 col-form-label'])}}
                    <span class="col-6 col-lg-8 d-flex align-items-center">{{$equipmentrequest->entity->name}}</span>
                </div>
                <div class="form-group row">
                    {{Form::label('requested_by', 'Requested By',['class'=>'col-6 col-lg-4 col-form-label'])}}
                    <span class="col-6 col-lg-8 d-flex align-items-center">{{$equipmentrequest->manager->name}}</span>
                </div>
                <div class="form-group row">
                    {{Form::label('requested_for', 'Requested For',['class'=>'col-6 col-lg-4 col-form-label'])}}
                    <span class="col-6 col-lg-8 d-flex align-items-center">{{$equipmentrequest->employee->name}}</span>
                </div>
                <div class="form-group row">
                    {{Form::label('alloted_id', 'Equipment Available',['class'=>'col-6 col-lg-4 col-form-label'])}}
                    {{Form::select('alloted_id', $equipmentAvailable,null,['class'=>'col-6  d-flex align-items-center selectJS','placeholder'=>'Select '.$equipmentrequest->entity->name, 'data-placeholder'=>'Select', 'style'=>'display:inline-block;'])}}
                </div>
                <div class="form-group row">
                    <button type="submit" class="btn btn-info alot-equipment">Allot Equipment</button>

                </div>

                @endforeach
            </div>
        </div>
    </div>
  
</div>

</div>
</div>
@endforeach
@endif
</div>
@endsection
@section('footerScripts')
<script>
  
    @if(empty(request()->route('request_id')))
    $('#employees').select2();

      function getEmployees(entity_id) {
          if (entity_id) {
              $.ajax({
                  url: "{{route('employeesToAssign')}}/" + entity_id,
                  type: 'get',
                  dataType: 'json',
                  success: function (response) {
                      var options = "";
                      $.each(response, function (id, name) {
                          options += "<option value='" + id + "'>" + name + "</option>";
                      });
                   
                        $('#employees').html(options);
                        $("#employees").select2({
                    
                          multiple:'multiple'
                         });
                  }
              })
          }
      }
      @else
      $('form').submit(function(){
        event.preventDefault();
        url = "{{route('alotEquipmentByRequest')}}";
        if($(this).find('select').val())
        {
          var formData = $(this).serialize();
          var that = $(this);
          $.ajax({
            url: url,
            data: formData,
            dataType:'json',
            beforeSend: function(){
              $(that).closest('.card-body').find('.loading-ico').css('display', 'flex');
            },
            success: function(response)
            {
              if(response && response.status == "success"){
                  toastr.success("Assigned Successfully!");
                  $(that).closest('div.col-md-4').fadeOut( "slow", function()   {
                                                                                  $(this).remove();
                                                                                  window.location.reload();
                                                                              }
                                                      );
              }
            },
            complete: function(){
              $(that).closest('.card-body').find('.loading-ico').css('display', 'none');
            }
          });
        }
        else{
            toastr.error("Select Equipment");
            $(this).find('select').css({'border': '1px solid red'})
        }
      });
      @endif
  
  </script>
@endsection
