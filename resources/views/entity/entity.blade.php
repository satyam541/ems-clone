@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Entity List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Entity List</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
@endsection
  
@section('footerScripts')
<script>

  
      var  updateEntity = "{{ route('updateEntity', ['entity' => ':id'])}}";
      var  deleteEntity = "{{ route('deleteEntity', ['entity' => ':id'])}}";
      var  addEntity = "{{ route('addEntity')}}";
      var myFields= [
            {
              title:"Name",
              name: "name",
              type:"text",
              width:40,
              editing:true,
              inserting:true
            },
            {
              title:"Total Stock",
              name:"total_stock",
              type:"number",
              width:40,
              editing:false,
              inserting:false,
              filtering:false
            },
            {
              title:"Available",
              name:"available",
              type:"number",
              width:40,
              editing:false,
              inserting:false,
              filtering:false
            },
            {
              title:"Assigned",
              name:"assigned",
              type:"number",
              width:40,
              editing:false,
              inserting:false,
              filtering:false
            },
            {
              type:"control",
            }
        ];


   var entity='{{route("entityList")}}';

    $("#jsGrid").jsGrid({
      fields:myFields,
      width: "100%",
      autoload: true,
      filtering:true,
      inserting: true,
      editing: true,
      deleting:true,
      paging:true,
      pageSize:10,
      pagerFormat: "Pages:  {pages}     {pageIndex} of {pageCount}",
      pageLoading:true,
      controller: {
        loadData: function(filter) {
          return $.ajax({
              type: "POST",
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              dataType:"json",
              url:entity,
              data: filter
          });
        },
        insertItem: function(item) {
          return $.ajax({
              type: "POST",
              dataType:"json",
              url: addEntity,
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              data:item,
          });
            
        },
        updateItem: function(item) {
          updateUrl = updateEntity.replace(':id',item['id']);
            return $.ajax({
                type: "POST",
                dataType:"json",
                url:updateUrl,
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: item
            });
        },
        deleteItem: function(item) {
          deleteUrl = deleteEntity.replace(':id',item['id']);
            return $.ajax({
                type: "DELETE",
                url:deleteUrl,
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: item
            });
        },
      },
      onItemInserted: function(args) {
        
        toastr.success('Entity Added Successfully')
      },
      onItemUpdated: function(args) {

        toastr.success('Entity Updated Successfully')
          
      },
      onItemDeleted: function(args) {

       toastr.success('Entity Deleted Successfully')

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
