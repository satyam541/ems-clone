@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Employee Trash List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Employee Trash List</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
  @endsection
  
@section('footerScripts')
<script>
  
     var  restoreUrl = "{{ route('restoreEmployee', ['employee' => ':employee'])}}";
     var  forceDeleteUrl = "{{ route('forceDeleteEmployee', ['employee' => ':employee'])}}";
     var  employeeURL='{{route("trashEmployeeList")}}';
  
      var myFields= [
          
            {
                title:"name",
                name: "name",
                type:"text",
                width:40
               
            },
            {
                title:"Date",
                name: "deleted_at",
                type:"text",
                width:40
               
            },
              
            @can('restore',new  App\Models\Employee())   
            {
                 title:"Restore",
                 width:20,
                 itemTemplate: function(value, item) {

                      var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                  
                      var $customEditButton = $("<a>")
                        // .attr('href', restoreUrl.replace(':id', item.id))
                        .attr({class:'btn btn-primary'}).html("Restore")
                        .click(function(e) {
                          console.log(item.id);
                            restore(item.id);
                            e.stopPropagation();
                            
                        })
                      

                      return $result.add($customEditButton);
                      }

              },
              @endcan
              @can('destroy',new  App\Models\Employee()) 
              {
                 title:"Destroy",
                 width:20,
                 itemTemplate: function(value, item) {

                      var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                  
                      var $customEditButton = $("<a>")
                        // .attr('href', restoreUrl.replace(':id', item.id))
                        .attr({class:'btn btn-warning'}).html("Destroy")
                        .click(function(e) {
                            destroy(item.id);
                            e.stopPropagation();
                            
                        })
                      

                      return $result.add($customEditButton);
                      }

              },
              @endcan
        
         
            {

             type:"control",
             editButton:false,
             deleteButton:false,  
            }
         
     
        ];

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        paging:true,
        pageSize:20,
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
        pageLoading:true,
        deleteConfirm: "Do you really want to delete Department?",
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url:employeeURL,
                    data: filter
                });
            },
     
         
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
    function restore(id)
       {

        $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
    });

    $.ajax({
     url: restoreUrl.replace(':employee',id),
     type:'POST',
     data:{employee:id},
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
     url: forceDeleteUrl.replace(':employee',id),
     type:'POST',
     data:{employee:id},
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
