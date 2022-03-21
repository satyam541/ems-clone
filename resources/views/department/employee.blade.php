@extends('layouts.master')
@section('content')
<div class="content-wrapper">
    
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">Employee List </h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Employee List</li>
            </ol>
          </div>
        </div>
      </div>
    </div>
    <section class="content">

        <div class="row">
            <div class="col-12">
             
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Filter</h3>

                        <div class="card-tools">
                         
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="">
                            <div class="form-group">
                                <select name="department" class="form-control selectJS" placeholder="Select Department" id="department">
                                    <option value="" readonly>Select</option>

                                    @foreach($departments as $index=>$department)

                                    <option value="{{ $department->id }}"
                                        {{ (request()->department==$department->id )  ?' selected':'' }}>
                                        {{ $department->name }}</option>
                                @endforeach
                                </select>
                            </div>
                        </form>
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

        </section>

  </div>
  @endsection
  
@section('footerScripts')
<script>
 
    // var d = new Date();
    // var month = d.getMonth()+1;
    employeeURL='{{route("departmentEmployeeList")}}';
    var trigger = false;
    $('#department').change(function(){
        trigger = true;
        department = $(this).val();
        employeeURL = "{{route('departmentEmployeeList')}}"+"?department="+department;
        $('#jsGrid').jsGrid("render").done(function(){
            $('select').select2();
            toastr.info('List Refreshed');
        });
    });
    if(!trigger)
    {
      
            $('#jsGrid').jsGrid("render");
            $('select').select2();
        
    }
    var myFields= [
          
        {
                title:"Employee Id",
                name: "registration_id",
                type:"text",
                width:40
            
            },
           {
               title:"Name",
               name: "name",
               type:"text",
               width:40
              
           },
              
           {
               title:"Department",
               name: "department.name",
               type:"text",
               filtering:false,
               width:40
              
           },
          
        ];


    $("#jsGrid").jsGrid({
      
        fields:myFields,
        width: "100%",
        autoload: true,
        filtering:true,
        editing:false,
        paging:true,
        pageSize:10,
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
        pageLoading:true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:employeeURL,
                    data: filter
                });
            },
    
         
        },
      
    });
</script>

@endsection
