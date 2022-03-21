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
  
    <div class="status">{{$interviewee->first_name}} has been Registered</div>
    <p>
        Please Click on the Below Link to see Detail
    </p>
    <p>
    <a href="{{route('IntervieweeView')}}"><button>Click Here</button></a>
    </p>
    </div>
    <hr>
    <div class="wrap">
    <p>
        Regards,<br>
        EMS - MSP.<br>
    </p>
    </div>
</body>

</html>