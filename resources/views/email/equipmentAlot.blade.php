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
                 Equipments Alloted to {{$employee->name}}  Details:-
            </h3>
        </strong>
        <div style="margin-top: 10px;">
            <table style="width: 50%;">
                <thead>
                    <tr style=" background-color: #6d98cc; padding: 6px; color: #fff; font-size: 16px;">
                        <th style="padding: 12px; text-align: left !important;">Name</th>
                        <th style="padding: 12px; text-align: left !important;">Label</th>
                    </tr>
                </thead>
                <tbody>


                  
                    @foreach($allotedEquipments as $equipmentType=>$label)
                    <tr style="background-color: #adcef7; padding: 12px; color: black; font-size: 16px;">
                        <td style="padding: 12px">{{$equipmentType}}</td>
                        <td style="padding: 12px">{{$label}}</td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

        <br />
        <br />
        <br />


        <p><a href="{{$link}}">Click here</a></p>

        <p>
            Regards,<br>
            EMS
        </p>
    </div>

</body>

</html>