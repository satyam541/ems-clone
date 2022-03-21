@extends('layouts.master')
@section('content')
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0 text-dark">Entity Request</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Home</a></li>
            <li class="breadcrumb-item active">Entity Request/Update</li>
          </ol>
        </div><!-- /.col -->
      </div><!-- /.row -->
    </div><!-- /.container-fluid -->
  </div>
  <!-- /.content-header -->
<div class="col-md-12">
    <div class="card card-purple">
      <div class="card-header">
        <h3 class="card-title">Requested Entity &nbsp<strong class="text-warning">{{$entity_name}}</strong></h3>
      </div>
      <div class="card-body">

      <label>Requested by <b>:</b> <span class="text-success">{{$entity_request->employee->name}}</span> &nbsp<span style="color:silver">({{$entity_request->employee->department->name}})</span></label>
     <label class="float-right">Applied On <b>:</b> <span class="text-danger">{{$entity_request->created_at}}</span></label> 
      {{Form::Model($entity_request,['route'=>$submitRoute])}}
      {{Form::hidden('entity_request_id',$entity_request->id)}}
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                {{Form::label('status','Status')}}
                {{Form::select('status',$status,null,['class'=>'form-control'])}}
                <span style="color:grey"> &nbspCurrent Status : {{$entity_request->status}}</span>

             @error('status')
             <span class="text-danger">{{$message}}</span>
             @enderror 
            </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                {{Form::label('equipments','Select Entity')}}
                {{Form::select('equipments[]',$equipments,$alloted_no,['class'=>'form-control multiple entity_select','multiple'=>'multiple'])}}
                <span style="color:grey">Requested Quantity : {{$entity_request->requested_quantity}}</span>
                @error('equipments')
             <span class="text-danger">{{$message}}</span>
             @enderror 
            </div>
            </div>
</div>
<div class="row">
            <div class="col-md-12">
              <div class="form-group">
                {{Form::label('remarks','Remarks')}}
                {{Form::textarea('remarks',null,['class'=>'form-control','rows'=>'3','placeholder'=>'Remarks'])}}
             @error('remarks')
             <span class="text-danger">{{$message}}</span>
             @enderror 
            </div>
            </div>
</div>

          </div>
          <div class="card-footer">
            <div class="col-md-12 text-right">
              <div class="form-group">
                <button class="btn bg-navy">Update</button>
              </div>
        </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
</div>
</div>
<!-- /.row -->
<!-- Main row -->

<!-- /.row (main row) -->
</div><!-- /.container-fluid -->
</section>

<!-- /.content -->
</div>

@endsection
@section('footerScripts')
<script>
$('.entity_select').on('change', function() {
        if ($(this).val().length > {{$entity_request->requested_quantity}}) {
            $(this).val($(this).data('value'));
            $('select').material_select();
        } else {
            $(this).data('value', $(this).val());
        }
    });
</script>
@endsection