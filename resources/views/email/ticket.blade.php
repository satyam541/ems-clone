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
   @if(!empty($ticketLog))
    <p>Ticket {{$ticketLog->action}} by {{$ticketLog->actionBy->name}}({{$ticketLog->actionBy->department->name}})</p> 
    <br>
    <b>Ticket No:</b> {{$ticketLog->ticket_id}}
    <br>
    <b>Type:</b> {{$ticketLog->ticket->ticketCategory->name}}
    <br>
    <b>Subject: </b> {{$ticketLog->ticket->subject}}
    <br>
    <b>Description: </b> {{$ticketLog->ticket->description}}
    <br>
        @if(!empty($ticketLog->remarks))
        <b>Remarks:- {{$ticketLog->remarks}}</b> 
        <br>
        @endif
    @else
    <p>Ticket raised by {{$ticket->employee->name}}({{$ticket->employee->department->name}})</p> 
    <br><br>
    <b>Ticket No:</b> {{$ticket->id}}
    <br>
    <b>Type:</b> {{$ticket->ticketCategory->name}}
    <br>
    <b>Subject: </b> {{$ticket->subject}}
    <br>
    <b>Description: </b> {{$ticket->description}}
    <br>    
    @endif
{{-- <p>
    {!! $emailMessage !!}

</p> --}}
<p>Click <a href="{{$link}}">here</a> to take action on ticket</p>



<p>
   Regards,<br>
   EMS<br>
</p>
</body>
</html>