<html>
<head>
    <style>
        body
        {
            font-family: monospace;
        }
    </style>
</head>
<body>
<p>
   Hi {{ $user->name }},<br><br>

 
</p>
     
<p>
      
     Your ems login email  is updated to {{ $user->email }}

</p>
<p>Click <a href="{{ route('login') }}">here to login</a></p>



<p>
   Regards,<br>
   EMS<br>
</p>
</body>
</html>