@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Attendance List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Attendance List of {{$employee->name}}</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>

@endsection
  
@section('footerScripts')
<script>


      var myFields= [
          
           {
                title:"Date",
                name: "attendance_date",
                type:"text",
                width:40
               
            },
               
            {
                title:"status",
                name: "status",
                type:"text",
                width:40
               
            },
            {
                title:"entry_time",
                name: "entry_time",
                type:"text",
                width:40,
                filtering:false
               
            },
            {
                filtering:false,
                title:"exit_time",
                name: "exit_time",
                type:"text",
                width:40
               
            },
        
            {
                filtering:false,
                title:"Punch Status",
                name: "punch_status",
                type:"text",
                width:40
               
            },
            {
                filtering:false,
                title:"Comment",
                name: "comment",
                type:"text",
                width:40
               
            },
         
        ];


   var attendanceURL='{{route("employeeAttendanceList",['employee'=>$employee->id])}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
       
        autoload: true,
        filtering:true,
        paging:true,
        pageSize:10,
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
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
