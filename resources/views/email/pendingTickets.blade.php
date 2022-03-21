<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title></title>
</head>

<body>
    <div style="width: 100%">
        <p>Hi ,</p>
        <strong>
            <h3>
                Pending Tickets
            </h3>
        </strong>
        <div style="margin-top: 10px;">
            <table style="width: 100%">
                <thead>
                    <tr style="background-color: #17365d; padding: 6px; color: #fff; font-size: 16px; width: 100%">
                        <th style="padding: 12px; text-align: left !important;">Employee</th>
                        <th style="padding: 12px; text-align: left !important;">Department</th>
                        <th style="padding: 12px; text-align: left !important;">Issue Type</th>
                        <th style="padding: 12px; text-align: left !important;">Priority</th>
                        <th style="padding: 12px; text-align: left !important;">Opened At</th>


                    </tr>
                </thead>
                <tbody>


                    @foreach($pendingTickets as $ticket)

                    <tr style="background-color: #e4f2f5; padding: 12px; color: black; font-size: 16px; width: 100%;">
                        <td style="padding: 12px">{{$ticket->employee->name}}</td>
                        <td style="padding: 12px">{{$ticket->employee->department->name}}</td>
                        <td style="padding: 12px">{{$ticket->issue_type}}</td>
                        <td style="padding: 12px">{{$ticket->priority}} </td>
                        <td style="padding: 12px">{{getFormatedDateTime($ticket->created_at)}}</td>


                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <br />
        <br />
        <br />


        <p><a href="{{route('itRaiseTicket')}}">Click here</a></p>

        <p>
            Regards,<br>
            EMS
        </p>
    </div>

</body>

</html>