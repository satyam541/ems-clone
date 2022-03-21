<!DOCTYPE html>
<html lang="en">
  <head>
    <title>The Knowledge Academy</title>
    <link rel="icon" href="{{url('img/favicon.ico')}}" type="image/x-icon" /> 
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{url('adminLTE/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{url('adminLTE/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{url('adminLTE/dist/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{url('adminLTE/bootstrap-4.min.css')}}">
    <link rel="stylesheet" href="{{url('adminLTE/toastr.min.css')}}">
    <link rel="stylesheet" href="{{url('adminLTE/plugins/select2/css/select2.min.css')}}">
    <style>
      .select2-container .select2-selection--single{
        height: 38px;
      }
      .select2-container--default .select2-selection--multiple .select2-selection__choice{
        background-color: #007bff;
      }
      .card-title{
        float: none;
      }
      body
      {
        background-image:url('{{url("img/cool-background.svg")}}');
        
        background-size: cover;
        background-repeat: no-repeat;
      }
    </style>
  </head>
  <body>
    <div>
      <div class="card-header" style="background-color:#0F1C70;">
              <!-- <img src="{{url('img/tka-white.svg')}}" width="200" style="position:absolute;top:10px;left:10px">   -->

        <h3 class="card-title text-center text-white">
        Job Application Form</h3>
      </div>
      <div class="card-body">
        <div class="col-md-12">
          <div>
         
            {{Form::model($interviewee,array('route'=>$submitRoute,"files"=>"true"))}}
              <div class="card-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('first_name','First Name*')}}
                      {{Form::text('first_name',null,['class'=>'form-control','required'=>"required"])}}
                      @error('first_name')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('middle_name','Middle Name')}}
                      {{Form::text('middle_name',null,['class'=>'form-control'])}}
                      @error('middle_name')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('last_name','Last Name',['class'=>'text-white'])}}
                      {{Form::text('last_name',null,['class'=>'form-control'])}}

                      @error('last_name')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('email','Email ID*')}}
                      {{ Form::email('email',$email,['class'=>'form-control','readonly'=>true,'required'=>"required"])}}
                      @error('email')
                        <span class="text-danger">{{$message}}</span>
                      @enderror
                    </div>
                  </div> 
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('phone','Phone No*')}}
                      {{Form::tel('phone',null,['class'=>'form-control','required'=>"required"])}}

                      @error('phone')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div> 
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('qualification_id','Qualification*',['class'=>'text-white'])}}
                      {{Form::select('qualification_id',$qualification,null,['class'=>'form-control single','required'=>"required"])}}
                        
                      @error('qualification_id')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('interested_in','Interested In')}}
                      {{Form::select('interested_in[]',$department,null,['class'=>'form-control mulitple',"multiple"=>"multiple"])}}

                      @error('interested_in')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      {{Form::label('referred_by','Referred by')}}
                      {{Form::text('referred_by',null,['class'=>'form-control'])}}

                      @error('referred_by')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-group">
                      <div class="custom-file">
                        {{Form::label('resume','Upload Resume*',['class'=>'text-white'])}}
                        {{Form::file('resume',['accept'=>'application/pdf','class'=>'form-control','required'=>"required"])}}

                        @error('resume')
                          <span class="text-danger">{{$message}}</span>  
                        @enderror
                      </div>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      {{Form::label('address','Address*')}}
                      {{Form::textarea('address',null,['class'=>'form-control','rows'=>'3','required'=>"required"])}}

                      @error('address')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      {{Form::label('comment','Comment')}}
                      {{Form::textarea('comment',null,['class'=>'form-control','rows'=>'3'])}}

                      @error('comment')
                        <span class="text-danger">{{$message}}</span>  
                      @enderror
                    </div>
                  </div>
                </div>
                <br>
                <div class="text-center">
                  <button type="submit" class="btn text-white" style="background-color:#0F1C70;">Submit</button>
                </div>
              {{Form::close()}}
            </div>
          </div>
        </div>
      </div>
    </div>
    <script src="{{url('adminLTE/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{url('adminLTE/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{url('adminLTE/toastr.min.js')}}"></script>
    <script>
      $('select').select2();
    </script>
  </body>
</html>