@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12 grid-margin">
        <!-- general form elements -->
        <div class="card">
          {{ Form::model($equipment, ['route'=>$submitRoute, 'files' => 'true', 'class' => 'form-sample']) }}
            <div class="card-body">
              <h4 class="card-title">Equipment</h4>
              <!-- form start -->
              {{ Form::hidden('id', null) }}
              <div class="row">
                <div class="col-md-6">
                    <div class="form-group row">
                            {{ Form::label('entity_id', 'Entity:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{Form::select('entity_id', $entity, null, ['placeholder' => 'Select','class' => 'form-control selectJS'])}}

                            @error('entity_id')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if(!empty($equipment->id))
                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('alloted_no', 'Alloted Number:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{ Form::text('alloted_no', null, ['placeholder' => 'Enter Alloted Number','class' => 'form-control', 'id'=>'allotedno',"autocomplete"=>"off"]) }}
                            <span class="col-sm-12" id="alloted_no" style="display:none"></span>

                              @error('alloted_no')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('manufacturer', 'Manufacturer:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{ Form::text('manufacturer', null, ['class' => 'form-control', 'id' => 'manufacturer', 'placeholder' => 'Enter Company']) }}

                            @error('manufacturer')
                            <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('buy_date', 'Buy Date: ', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{ Form::date('buy_date', null, ['class' => 'form-control', 'placeholder' => 'Select']) }}

                            @error('buy_date')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                        </div>
                    </div>
                </div>

                @if(empty($equipment->id))
                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('quantity', 'Quantity:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{Form::number('quantity', null, ['placeholder' => 'Enter Quantity','min'=>'0','max'=>'100',"class"=>"form-control"])}}


                            @error('quantity')
                                 <span class="text-danger">{{$message}}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                @if(!empty($equipment->id))
                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('isWorking', 'Status:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{Form::select('isWorking', ['1'=>'Working','0'=>'Not Working'], null, ['placeholder' => 'Select Status','class'=>'form-control selectJS'])}}
                        </div>
                    </div>
                </div> 
                @endif

                @if(!empty($equipment->id))
                <div class="col-md-6">
                    <div class="form-group row">
                        {{ Form::label('employee_id', 'Employee:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            {{Form::select('employee_id', $employees, null, ['placeholder' => 'Select','class'=>'form-control selectJS','id'=>'employee'])}}

                        @error('employee_id')
                            <span class="text-danger">{{$message}}</span>
                        @enderror
                        </div>
                    </div>
                </div>
                @endif

                <div class="col-md-12">
                    <div class="form-group row">
                        {{ Form::label('specifications', 'Specifications:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            <button class="btn float-right" type="button" id="add_specification"><i class="mdi mdi-plus-circle" style="font-size: x-large"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12" id="specifications">
                        @php $i=1; @endphp
                        @if(!empty($specifications))
                            @foreach($specifications as $specification)
                                    <span class="col-sm-1 text-center">#</span>
                                    @if(!empty($specification->id))
                                    <input type='hidden' name='specifications[{{$i}}][id]' value='{{$specification->id}}'>
                                    @endif
                                     <input  class="form-control-sm mr-3 col-md-4" placeholder="Enter Name" name="specifications[{{$i}}][name]" type="text" value="{{$specification->name}}">
                                     <input  class="form-control-sm mr-3 col-md-4" placeholder="Enter Detail" name="specifications[{{$i}}][description]" type="text" value="{{$specification->description}}">
                                     <button type="button" class="removeSpecification btn" data-href="{{route('deleteSpecification', ['specification'=>$specification->id])}}"><i class="fa fa-trash text-danger"></i></button>
                                @php $i++; @endphp
                            @endforeach
                        @endif
                    </div>
                </div> 
              
                @if(!empty($equipment->id))
                <div class="col-md-12">
                    <div class="form-group row">
                        {{ Form::label('repair', 'Repair:', ['class' => 'col-sm-3 col-form-label']) }}
                        <div class="col-sm-9">
                            <button class="btn float-right" type="button" id="add_repair"><i class="mdi mdi-plus-circle" style="font-size: x-large"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12" id="repairs">
                        @if(!empty($repairs))
                            @php $i=1; @endphp
                            @foreach($repairs as $repair)
                                <span class="col-sm-1 text-center"> # </span>
                                @if(!empty($repair->id))
                                <input type='hidden' name="repairs[{{$i}}][id]" value='{{$repair->id}}'>
                                @endif
                                <input class="form-control-sm mr-3 col-md-4" placeholder="Select Date" name="repairs[{{$i}}][date]" type="date" value="{{\Carbon\Carbon::parse($repair->date)->format('Y-m-d')}}">
                                <input class="form-control-sm mr-3 col-md-4" placeholder="Enter Part" name="repairs[{{$i}}][part]" type="text" value="{{$repair->part}}">
                                <input class="form-control-sm mr-3 col-md-4" placeholder="Enter Cost" name="repairs[{{$i}}][cost]" type="number" value="{{$repair->cost}}">
                                <button type="button" class="removeRepair btn" data-href="{{route('deleteRepair', ['repair'=>$repair->id])}}"><i class="fa fa-trash text-danger"></i></button>
                            @php $i++; @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif
              
                @if(!empty($equipment->id))
                <div class="col-md-12">
                    <div class="row form-group">
                        {{Form::label('problem', 'Problem:',['class'=>'col-sm-3 col-form-label'])}}
                        {{-- <span class="col-sm-8 text-right"><button class="btn" type="button" id="add_problem"><i class="mdi mdi-plus-circle"></i></button></span> --}}
                        <div class="col-sm-9">
                            <button class="btn float-right" type="button" id="add_problem"><i class="mdi mdi-plus-circle" style="font-size: x-large"></i></button>
                        </div>
                    </div>
                    <div class="col-md-12" id="problems">
                        @if(!empty($problems))
                            @php $i=1; @endphp
                            @foreach($problems as $problem)
                                <span class="col-sm-1 text-center"> # </span>
                                @if(!empty($problem->id))
                                <input type='hidden' name="problems[{{$i}}][id]" value='{{$problem->id}}'>
                                @endif
                                <input class="form-control-sm mr-3 col-md-4" placeholder="Select Problem" name="problems[{{$i}}][name]" type="text" value="{{$problem->name}}" required>

                                <button type="button" class="removeProblem btn" data-href="{{route('deleteProblem', ['problem'=>$problem->id])}}"><i class="fa fa-trash text-danger"></i></button>
                            @php $i++; @endphp
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif
               

                </div>

              <button type="submit" class="btn btn-primary mr-2">Add Equipment</button>
            </div>
          {{ Form::close() }}
          <!-- /.card-body -->
        </div>
    </div>
</div>

<div class="modal fade show" id="myModal" style="display: none; padding-right: 17px;" aria-modal="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title text-center"></h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
        </div>
        <div class="modal-body" id="model_body">
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

@section('footerScripts')

<script>
    var autocompleteURL = "{{route('manufacturerAutocomplete')}}";
  // var getAllottedNumber={{route('hardwareAllotedno')}};
    var $input = $('input#allotedno');
    var typingTimer;                //timer identifier
    var doneTypingInterval = 300;  //time in ms,
    //on keyup, start the countdown
    $input.on('keyup', function () {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function(){
            allotednumber();
        }, doneTypingInterval);
    });

    //on keydown, clear the countdown
    $input.on('keydown', function () {
        clearTimeout(typingTimer);
    });
function allotednumber(){
  var alloted=$('input#allotedno').val();
  var id = $('input[name="id"]').val();
  $.ajax({
    url:"{{ route('equipmentNumberCheck') }}",
    type: "GET",
    dataType:'json',
    data:{allotedno:alloted,id: id},
    success: function(data){
        if(data.status == false)
        {
            $('#alloted_no').hide();
            $('button[type="submit"]').attr('disabled',false);
            return;
        }
        if(data.alloted == true)
        {
            $('#alloted_no').text("This is Already Alloted").addClass('text-danger').removeClass('text-success');
            $('button[type="submit"]').attr('disabled',true);
            $('#alloted_no').show();
        }
        else{
            $('#alloted_no').text("Available").addClass('text-success').removeClass('text-danger');
            $('button[type="submit"]').attr('disabled',false);
            $('#alloted_no').show();
        }
    },
  });
}
$('#add_specification').click(function(){
    var count = $('#specifications').find('div').length;
    var html = '<div class="row form-group">\
                    <span class="col-sm-1 text-center">#</span>\
                    <input type="text" placeholder="Enter Name"   style="border:1px solid grey"  class="form-control-sm mr-3 col-md-4" name="specifications['+ (count+1) +'][name]">\
                    <input  class="form-control-sm mr-3 col-md-4" style="border:1px solid grey" placeholder="Enter Detail" name="specifications['+ (count+1) +'][description]" type="text">\
                    <button type="button" class="removeSpecification btn"><i class="fa fa-trash text-danger"></i></button>\
                </div>';
    $('#specifications').append(html);
    $('#specifications').show();
});
function checkElementCount(selector)
{
    var count = $(selector).find('div.row.form-group').length;
    if(count == 0)
        $(selector).hide();
}
$("#specifications").on('click','.removeSpecification.btn',function(){
    if($(this).data('href') == undefined)
    {
        $(this).closest('div.row.form-group').remove();
        checkElementCount("#specifications");
        return;
    }
    var conf = confirm('Do You want to Delete this record?');
    var deleteSpecification = $(this).data('href');
    var select = this;
    if(conf){
        $.ajax({
            url: deleteSpecification,
            data:{},
            type:'get',
            dataType:'json',
            success:function(response){
                if(response.success)
                    $(select).closest('div.row.form-group').remove();
            },
            error:function(error){
                console.log(error);
            }
        });
        checkElementCount("#specifications");
    }
    else{
        return;
    }
});
$('#add_repair').click(function(){
    var count = $('#repairs').find('div').length;
    var html = '<div class="row form-group">\
                    <span class="col-sm-1 text-center"> # </span>\
                    <input class="form-control-sm mr-3 col-md-4" style="border:1px solid grey"  placeholder="Select Date" name="repairs['+ (count+1) +'][date]" type="date">\
                    <input class="form-control-sm mr-3 col-md-2" style="border:1px solid grey"  required placeholder="Enter Part" name="repairs['+ (count+1) +'][part]" type="text">\
                    <input class="form-control-sm mr-3 col-md-2" style="border:1px solid grey" placeholder="Enter Cost" name="repairs['+ (count+1) +'][cost]" type="number" >\
                    <button type="button" class="removeRepair btn"><i class="fa fa-trash text-danger"></i></button>\
                </div>';
    $('#repairs').append(html);
    $('#repairs').show();
});
$('#add_problem').click(function(){
    var count = $('#problems').find('div').length;
    var html = '<div class="row form-group">\
                    <span class="col-sm-1 text-center"> # </span>\
                    <input class="form-control col-md-9" style="border:1px solid grey" placeholder="Enter problem" name="problems['+ (count+1) +'][name]" type="text" required>\
                    <button type="button" class="removeProblem btn"><i class="fa fa-trash text-danger"></i></button>\
                </div>';
    $('#problems').append(html);
    $('#problems').show();
});
$("#repairs").on('click','.removeRepair.btn',function(){
    if($(this).data('href') == undefined)
    {
        $(this).closest('div.row.form-group').remove();
        checkElementCount("#repairs");
        return;
    }
    var conf = confirm('Do You want to Delete this record?');
    var deleteRepair = $(this).data('href');
    if(conf){
        var select = this;
        $.ajax({
            url: deleteRepair,
            data:{},
            type:'get',
            dataType:'json',
            success:function(response){
                if(response.success)
                    $(select).closest('div.row.form-group').remove();
            },
            error:function(error){
                console.log(error);
            }
        });
        checkElementCount("#repairs");
    }
    else{
        return;
    }
});
$("#problems").on('click','.removeProblem.btn',function(){
    if($(this).data('href') == undefined)
    {
        $(this).closest('div.row.form-group').remove();
        checkElementCount("#problems");
        return;
    }
    var conf = confirm('Do You want to Delete this record?');
    var deleteProblem = $(this).data('href');
    if(conf){
        var select = this;
        $.ajax({
            url: deleteProblem,
            data:{},
            type:'get',
            dataType:'json',
            success:function(response){
                if(response.success)
                    $(select).closest('div.row.form-group').remove();
            },
            error:function(error){
                console.log(error);
            }
        });
        checkElementCount("#problems");
    }
    else{
        return;
    }
});

$('#manufacturer').on('input',function(){
    $( this ).autocomplete({
      source: autocompleteURL,
    });
})
$('#employee').change(function(){
    var employee_id = $(this).val();
    if(employee_id == "")
    {
        return;
    }
    var entity_id = $("select[name=entity_id]").val();
    var equipmentCheck = "/equipment/check/"+employee_id+"/"+entity_id;
    $.ajax({
        url: equipmentCheck,
        type: 'get',
        dataType: 'json',
        success:function(response){
            if(response.status)
            {
                popup(response.equipment);
            }
        },
        error: function(){

        }
    })
})
function popup(equipment)
{
    var html = "<div class='col-sm-12'>\
                    <div class='row'>\
                        <div class='col-sm-4 text-right'><label class='control-label'>Entity:</label></div>\
                        <div class='col-sm-8'>"+equipment.entity.name+"</div>\
                    </div>\
                    <div class='row'>\
                        <div class='col-sm-4 text-right'><label class='control-label'>Alloted Number:</label></div>\
                        <div class='col-sm-8'>"+equipment.alloted_no+"</div>\
                    </div>\
                    <div class='row'>\
                        <div class='col-sm-4 text-right'><label class='control-label'>Status:</label></div>\
                        <div class='col-sm-8'>Already Assigned To "+equipment.employee.name+"</div>\
                    </div>\
                    <div class='text-center'>\
                        <label class='control-label'>Note: </label>  Ignore if you want to re-assign\
                    </div>\
                </div>";
    $('#model_body').html(html);
    $('#myModal').modal('show');
}

</script>
@endsection




