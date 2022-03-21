@extends('layouts.master')
@section('content')

<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Permission List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Permission List</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
@endsection
  
@section('footerScripts')

<script type="text/javascript">
$(window).on('load', function() {
 $('#jsGrid').find('select').addClass('selectJS');
 $('.selectJS').select2({
      placeholder: "Select an option",
      allowClear: false
    });
});
  var modules = '{!!$modules!!}';
  modules           =   JSON.parse(modules);


  var myFields= [
          
            {
                title:"module",
                name: "module_id",
                type: "select",
                items: modules,
                valueType: "number",
                validate: "required",
                width:200
            },
            {
                title:"access",
                name: "access",
                type:"text",
                width:200,
                validate:"required"
               
            },
            {
                title:"description",
                name: "description",
                type:"text",
                width:300
               
            },
            {
             type:"control",
             width:100,
             @cannot('update', new App\Models\Permission())
             editButton:false,
             @endcan
             @cannot('delete', new App\Models\Permission())
             deleteButton:false,
             @endcan
            },
     
        ];

   var deletePermission = '{{ route("deletePermission") }}';
   var insertPermission = '{{ route("insertPermission") }}';
   var updatePermission = '{{ route("updatePermission") }}';
   var permissionURL='{{route("permissionList")}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        @can('insert', App\Models\Permission::class)
        inserting: true,
        @endcan
        editing: true,
        filtering:true,
        paging:true,
        pageSize: 10,
        pageLoading: true,
        deleteConfirm: "Do you really want to delete Role?",
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:permissionURL,
                    data: filter
                });
            },
            insertItem: function(item) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: insertPermission,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                   
                    data:item
                });
            },
            updateItem: function(item) {
              // updatePermission = updatePermission.replace(':id',item['id']);
                return $.ajax({
                    type: "POST",
                    url:updatePermission,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            },
            deleteItem: function(item) {
              // deletePermission = deletePermission.replace(':id',item['id']);
                return $.ajax({
                    type: "POST",
                    url:deletePermission,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: item
                });
            }
        },
        onItemInserted: function(args) {
        
        toastr.success('Permission Added Successfully')
      },
      onItemUpdated: function(args) {

        toastr.success('Permission Updated Successfully')
          
      },
      onItemDeleted: function(args) {

       toastr.success('Permission Deleted Successfully')

      },
        onError: function(args) {

          errors = args.args[0].responseJSON.errors;
          error = '';
          $.each(errors, function(key, value) {
              
              error += value + "\n";

          });
          toastr.warning(error);

        },
      
    });

</script>
@endsection
