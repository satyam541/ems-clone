<html>

<head>
    <style>
        body {
            font-family: monospace;
            color: white;
            margin: 0;
            background-color: #383457;
        }
        .name
        {
            font-size: 16px;
        }
        .wrap
        {
            padding: 10px;
        }
        .status
        {
            padding: 10px;
            background-color: white;
            color: #383457;
            width: auto;
            border-radius: 5px;
        }
        .response
        {
            border-bottom: 1px solid white;
        }
    </style>
</head>

<body>
    <div class="wrap">
    <label class="name"> Hi, {{$name}}</label>
     <br><br>
    <div class="status">Your Job Application Has Been {{$status}}</div>
    </div>
    <hr>
    <div class="wrap">
        
    HR Response :
     <label class="response">{{$response}}</label>.
    
    <p>
        Regards,<br>
        EMS - MSP.<br>
    </p>
    </div>
</body>

</html>