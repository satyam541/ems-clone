<!DOCTYPE html>
<html>
  <head>
    <title>Register form</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.4.1/css/all.css" integrity="sha384-5sAR7xN1Nv6T6+dT2mhtzEpVJvfS3NScPQTrOxhwjIuvcA67KV2R5Jz6kr4abQsz" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet">
    <link rel="stylesheet" href="{{url('adminLTE/toastr.min.css')}}">
    <style>
      html, body {
      min-height: 100%;
      padding: 0;
      margin: 0;
      font-family: Roboto, Arial, sans-serif;
      font-size: 14px;
      color: #666;
      }
      h1 {
      margin: 0 0 20px;
      font-weight: 400;
      color: #1c87c9;
      }
      p {
      margin: 0 0 5px;
      }
      .main-block {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: #1c87c9;
      }
      form {
      padding: 25px;
      margin: 25px;
      box-shadow: 0 2px 5px #f5f5f5; 
      background: #f5f5f5; 
      }
      .fas {
      margin: 25px 10px 0;
      font-size: 72px;
      color: #fff;
      }
      .fa-envelope {
      transform: rotate(-20deg);
      }
      .fa-at , .fa-mail-bulk{
      transform: rotate(10deg);
      }
      input, textarea {
      width: calc(100% - 18px);
      padding: 8px;
      margin-bottom: 20px;
      border: 1px solid #1c87c9;
      outline: none;
      }
      input::placeholder {
      color: #666;
      }
      button {
      width: 40%;
      padding: 10px;
      border: none;
      background: #1c87c9; 
      font-size: 16px;
      font-weight: 400;
      color: #fff;
      }
      button:hover {
      background: #2371a0;
      }    
    
      .left-part, form {
      width: 50%;
      }
      
      h1{
        color:blue;
      };
      }
    </style>
  </head>
  <body>
   
    <div class="main-block">
      
      {{Form::model($interviewee,array('route'=>$submitRoute))}}
  
      <h1>Register</h1>
      <div class="info">
      
        {{ Form::text('email',null,['class'=>'form-control','required','placeholder'=>'Enter Email Id'])}}

        @error('email')
        <span class="text-danger">{{$message}}</span>
        @enderror

      </div>
   
      <button type="submit" >Send </button>
      {{Form::close()}}
    </div>
    <script src="{{url('adminLTE/plugins/jquery/jquery.min.js')}}"></script>
{{-- <!-- jQuery UI 1.11.4 --> --}}
    <script src="{{url('adminLTE/plugins/jquery-ui/jquery-ui.min.js')}}"></script>
    <script src="{{url('adminLTE/toastr.min.js')}}"></script>
    <script type="text/javascript">
      $(function() {
    
        @if($message=Session::get('success'))
          toastr.success('{{$message}}');
        @endif
        @if($errors->any())
          @foreach($errors->all() as $error)
            toastr.warning('{{$error}}');
          @endforeach
        @endif
    
    
      });
    
    </script>
  </body>
</html>