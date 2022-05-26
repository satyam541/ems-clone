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
   Hi,<br><br>

</p>
{{-- {{dd($data['ticketLog']->actionBy)}} --}}
   @if(!empty($data['ticketLog']))
    <p>Ticket {{$data['ticketLog']->action}} by {{$data['ticketLog']->actionBy->name}}({{$data['ticketLog']->actionBy->employee->department->name}})</p> 
    <br>
    <b>Ticket No:</b> {{$data['ticketLog']->ticket_id}}
    <br>
    <b>Type:</b> {{$data['ticketLog']->ticket->ticketCategory->name}}
    <br>
    <b>Subject: </b> {{$data['ticketLog']->ticket->subject}}
    <br>
    <b>Description: </b> {{$data['ticketLog']->ticket->description}}
    <br>
        @if(!empty($data['ticketLog']->remarks))
        <b>Remarks:- {{$data['ticketLog']->remarks}}</b> 
        <br>
        @endif
    @else
    <p>Ticket raised by {{$data['ticket']->user->name}}({{$data['ticket']->user->employee->department->name}})</p> 
    <br><br>
    <b>Ticket No:</b> {{$data['ticket']->id}}
    <br>
    <b>Type:</b> {{$data['ticket']->ticketCategory->name}}
    <br>
    <b>Subject: </b> {{$data['ticket']->subject}}
    <br>
    <b>Description: </b> {{$data['ticket']->description}}
    <br>    
    @endif
{{-- <p>
    {!! $emailMessage !!}

</p> --}}
<p>Click <a href="{{$data['link']}}">here</a> to take action on ticket</p>



<p>
   Regards,<br>
   EMS<br>
</p>
</body>
</html>