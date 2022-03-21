@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Department List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Department List</p>

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
                title:"name",
                name: "name",
                type:"text",
                width:300
               
            },
               
            {
                title:"description",
                name: "description",
                filtering:false,
                type:"text",
                width:400
               
            },
            @canany(['update','delete'],new  App\Models\Department())
            {
                type:"control",
                width:100
            }
           @endcanany
     
        ];

   var deleteDepartment = '{{ route("deleteDepartment") }}';
   var insertURL = '{{ route("insertDepartment") }}';
   var updateDepartment = '{{ route("updateDepartment") }}';
   var departmentURL='{{route("departmentList")}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        inserting: true,
        @can('update',new  App\Models\Department())
        editing: true,
        @endcan
        filtering:true,
        paging:true,
        pageSize:10,
        pageLoading:true,
        deleteConfirm: "Do you really want to delete Department?",
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:departmentURL,
                    data: filter
                });
            },
            insertItem: function(item) {
              console.log(item);
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: insertURL,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   
                    data:item
                });
            },
            updateItem: function(item) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url:updateDepartment,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            },
            deleteItem: function(item) {
                return $.ajax({
                    type: "DELETE",
                    url:deleteDepartment,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            }
        },
        onItemInserted: function(args) {
        
          toastr.success('Department Added Successfully')
        },
        onItemUpdated: function(args) {

          toastr.success('Department Updated Successfully')
            
        },
        onItemDeleted: function(args) {

         toastr.success('Department Deleted Successfully')
  
        },
        onError: function(args) {

          errors = args.args[0].responseJSON.errors;
          error = '';
          $.each(errors, function(key, value) {
              
              error += value + "\n";

          });
          toastr.warning(error)

        },
     
      
    });
</script>

@endsection
