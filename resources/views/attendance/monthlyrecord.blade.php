@extends('layouts.master')
@section('content')
<div class="content-wrapper">
    
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0 text-dark">Attendance List of {{$employee->name}} for {{$month}}</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item"><a href="{{route('attendanceView')}}">Attendence List</a></li>
              <li class="breadcrumb-item active">{{$employee->name}}</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
  
    <section class="content">
      <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div id="jsGrid"></div>
                </div>
            </div>
        </div>
      </div>
    </section>
  </div>
  @endsection
  
@section('footerScripts')
<script>

    
       var viewAttendanceRecordUrl= "{{ route('attendanceMonthlyRecord', ['employee' => $employee->id,'month'=>$monthNum])}}";
       var updateAttendance = '{{ route("updateAttendance",["id"=>":id"]) }}';
       var myFields= [
          
            {
             
                title:"Date",
                name: "attendance_date",
                type:"text",
                editing:false,
                width:40
               
            },
               
            {
                title:"entry_time",
                name: "entry_time",
                type:"text",
                editing:false,
                width:40
               
            },
            {
                title:"exit_time",
                name: "exit_time",
                type:"text",
                editing:false,
                width:40
               
            },
            {
                title:"status",
                name: "status",
                type:"text",
                editing:false,
                width:40
               
            },
            {
                title:"Punch status",
                name: "punch_status",
                type:"text",
                editing:false,
                width:40
               
            },
            @can('update',new  App\Models\Attendance())
            {
                title:"comment",
                name: "comment",
                type:"text",
                width:40,
                filtering:false
               
            },
            @endcan
            {
                type:"control",
                deleteButton:false,
              
               
            },
      
        ];


    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        editing:true,
        autoload: true,
        filtering:false,
        // paging:true,
        // pageSize:10,
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
        pageLoading:true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:viewAttendanceRecordUrl,
                    data: filter
                });
            },
            updateItem: function(item) {
               
              updateAttendance =updateAttendance.replace(':id',item['id']);
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url:updateAttendance,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item,
                    success:function(data)
                    {
                    toastr.success('Comment Added Successfully');
                    
                    }
  
                });
            },
            onError: function(args) {

                errors = args.args[0].responseJSON.errors;
                error = '';
                $.each(errors, function(key, value) {
                    
                    error += value + "\n";

                });
                toastr.warning(error)

            },
    
         
        },
      
    });
</script>

@endsection
