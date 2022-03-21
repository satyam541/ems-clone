@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Module Trash List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Module Trash List</p>
  
                <div id="jsGrid"></div>
  
            </div>
        </div>
    </div>
  </div>
@endsection

@section('footerScripts')
<script type = "text/javascript">

  var restoreUrl = "{{ route('restoreModule', ['module' => ':id'])}}";
  var destroyUrl = "{{ route('forceDeleteModule', ['module' => ':id'])}}";
  var trashUrl = '{{route("trashModuleList")}}';

var myFields = [

  {
      title: "Name",
      name: "name",
      type: "text",
      width: 50,
      validate: "required"

  },
  {
      title: "Description",
      name: "description",
      type: "text",
      width: 100,
      filtering: false,

  },
  @can('restore', new App\ Models\ Module())
  {
      title: "Restore",
      width: 50,
      align: "center",
      itemTemplate: function (value, item) {

          var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);


          var $customEditButton = $("<a>")
              .attr({
                  class: 'btn btn-primary'
              }).html("Restore")
              .click(function (e) {
                  restore(item.id);
                  e.stopPropagation();
              })


          return $result.add($customEditButton);
      }
      
    },
    @endcan
  @can('destroy', new App\ Models\ Module())
  {
      title: "Destroy",
      width: 50,
      align: "center",
      itemTemplate: function (value, item) {

          var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);


          var $customEditButton = $("<a>")
              .attr({
                  class: 'btn btn-warning'
              }).html("Destroy")
              .click(function (e) {
                  destroy(item.id);
                  e.stopPropagation();
              })


          return $result.add($customEditButton);
      }
      
    },
    @endcan

];

$("#jsGrid").jsGrid({
  fields: myFields,
  width: "100%",
  paging: true,
  autoload: true,
  paging: true,
  pageSize: 10,
  pagerFormat: "pages:: {first} {prev} {pages} {pageIndex} of {pageCount}",
  pageLoading: true,
  deleteConfirm: "Do you really want to delete Module?",
  controller: {
      loadData: function (filter) {
          return $.ajax({
              type: "GET",
              dataType: "json",
              url: trashUrl,
              data: filter
          });
      },
  },

  onError: function (args) {

      errors = args.args[0].responseJSON.errors;
      error = '';
      $.each(errors, function (key, value) {

          error += value + "\n";

      });
      toastr.warning(error);
  },

});

function restore(id)
{
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $.ajax({
     url: restoreUrl.replace(':id',id),
     type:'POST',
     data:{module : id},
     success:function(data)
     {
        toastr.success('Restored');
        $('#jsGrid').jsGrid("render").done(function(){
            toastr.info('List Refreshed');
            $('select').select2();
        });
     }
    });
}

function destroy(id)
{
    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $.ajax({
     url: destroyUrl.replace(':id',id),
     type:'DELETE',
     data:{module : id},
     success:function(data)
     {
        toastr.success('Permanently Deleted');
        $('#jsGrid').jsGrid("render").done(function(){
            toastr.info('List Refreshed');
            $('select').select2();
        });
     }
    });
}

</script>
@endsection
