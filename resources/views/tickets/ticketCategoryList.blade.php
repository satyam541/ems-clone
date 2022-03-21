@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Ticket Category List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Ticket Category List</p>

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
              title:"Type",
              name: "type",
              type: "text",
              width:300
             
          },
        
          {
              title:"Name",
              name: "name",
              type:"text",
              width:300
             
          },
         
          {
              type:"control",
              width:100
          }
       
   
      ];

 var deleteCategory   = '{{ route("categoryDelete") }}';
 var insertCategory   = '{{ route("categorySubmit") }}';
 var updateCategory   = '{{ route("categoryUpdate") }}';
 var categoryURL      = '{{route("categoryList")}}';

  $("#jsGrid").jsGrid({
      fields:myFields,
      width: "100%",
      paging: true,
      autoload: true,
      inserting: true,
      editing: true,
      filtering:true,
      paging:true,
      pageSize:10,
      pageLoading:true,
      deleteConfirm: "Do you really want to delete category?",
      controller: {
          loadData: function(filter) {
              return $.ajax({
                  type: "GET",
                  dataType:"json",
                  url:categoryURL,
                  data: filter
              });
          },
          insertItem: function(item) {
            console.log(item);
              return $.ajax({
                  type: "POST",
                  dataType:"json",
                  url: insertCategory,
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
                  url:updateCategory,
                  headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  data: item
              });
          },
          deleteItem: function(item) {
              return $.ajax({
                  type: "DELETE",
                  url:deleteCategory,
                  headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  data: item
              });
          }
      },
      onItemInserted: function(args) {
      
        toastr.success('Ticket Category Added Successfully')
      },
      onItemUpdated: function(args) {

        toastr.success('Ticket Category Updated Successfully')
          
      },
      onItemDeleted: function(args) {

       toastr.success('Ticket Category Deleted Successfully')

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
