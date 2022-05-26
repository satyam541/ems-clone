@extends('layouts.master')
@section('content')
<style>
    .select2-container .select2-selection--single {
        height: 38px;
    }

    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #007bff;
    }

</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Document</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active"><a href="">Document Upload </a></li>
                   
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>

    <section class="content">
        <div class="container-fluid">
          <div class="row">
            <!-- left column -->
            <div class="col-md-12">
              <!-- general form elements -->
              <div class="card card">
                <div class="card-header">
             
                  <h3 class="card-title">Document</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
            
                {{Form::model($document,array('route'=>$submitRoute,"files"=>"true"))}}
                <input type="hidden" name="id" value="{{ $document->id }}" >
                  <div class="card-body">
               
              
                    <div class="form-group">
                        {{Form::label('name','Name')}}
                        {{Form::text('document_name',null,['class'=>'form-control','required'=>'required','id'=>'name'])}}

                    
                    </div>
              
                  

                      <div class="form-group">
                        {{Form::label('file','file')}}
                        {{Form::file('file',null,['class'=>'form-control'])}}
                 
                      </div>
                      </div>
  
                  <div class="card-footer">
                    <button type="submit" class="btn btn-info">Submit</button>
                  </div>
                  {{Form::close()}}

            
          
            </div>
    
          </div>
       
        </div>
      </section>
  

</div>
</div>
</div>
</div>

</div>
</section>


</div>

@endsection
@section('footerScripts')



@endsection
