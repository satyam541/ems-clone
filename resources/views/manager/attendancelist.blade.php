@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Attendace List</li>
            </ol>
        </nav>
    </div>

    <div class="col-12">
        <div class="card">
            
            <div class="card-body">
                <div class="float-left">
                    <p class="card-title">Department: {{$department->name}}(Attendance List)</p>
                </div>

                <div class="float-right">
                    <form action="" class="col-sm-2  pb-1 form-group">
                        
                        <select name="month" class="" id="month" placeholder="Month">
                            <option value="" readonly>Select Month</option>
                            @foreach($months as $index=>$month)
                                <option value="{{ $index }}"
                                    {{ (request()->month==$index )  ?'selected':' ' }}>
                                    {{ $month }}</option>
                            @endforeach
                        </select>
                </div>

                <div id="jsGrid"></div>

            </div>
        </div>
    </div>
</div>
  @endsection
  
@section('footerScripts')
<script>
 
    var d = new Date();
    var month = d.getMonth()+1;
    var employees='{!! $employees !!}';
    employees           =   JSON.parse(employees);
    employees['0']    =   'All';
    var attendanceURL = "{{route('managerAttendanceList')}}";
    var viewAttendanceRecordUrl = "{{ route('attendanceRecord', ['employee' => ':id','month'=>':month']) }}";
    var trigger = false;
    $('#month').change(function(){
        trigger = true;
        month = $(this).val();
        attendanceURL = "{{route('managerAttendanceList')}}"+"?month="+month;
        $('#jsGrid').jsGrid("render").done(function(){
            toastr.info('List Refreshed');
        });
    });
    if(!trigger)
    {
        if(month)
        {
            attendanceURL += "?month="+month;
            $('#jsGrid').jsGrid("render");
        }
    }
    var myFields= [
          
            {
              title:"Name",
              name: "id",
              type:"select",
              items: employees,
              valueType: "number|string",
              width:200,
               
            },      
            {
                title:"Total Absent",
                name: "total_absent",
                type:"text",
                filtering:false,
                width:150,
                align:'center',
               
            },
            {
                title:"Total Present",
                name: "total_present",
                type:"text",
                width:150,
                align:'center',
                filtering:false
               
            },
            {
                title:"Detail",
                width:150,
                align:'center',
                itemTemplate: function(value, item) {

                      var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                  
                      var $customEditButton = $("<a>")
                        .attr('href', viewAttendanceRecordUrl.replace(':id', item.id).replace(':month',month))
                        .attr({class:'btn btn-primary btn-rounded btn-fw'}).html("Detail")
                        .click(function(e) {
                            e.stopPropagation();
                        })
                      

                      return $result.add($customEditButton);
                      }

              }
          
        ];


    $("#jsGrid").jsGrid({
      
        fields:myFields,
        width: "100%",
        autoload: true,
        filtering:true,
        editing:false,
        paging:true,
        pageSize:10,
        pageLoading:true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:attendanceURL,
                    data: filter
                });
            },
    
         
        },
      
    });
</script>

@endsection
