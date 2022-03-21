@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Equipment Trash List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Equipment Trash List</p>
  
                <div id="jsGrid"></div>
  
            </div>
        </div>
    </div>
  </div>
@endsection
@section('footerScripts')
<script>

      var entity          =   '{!! $entity !!}';
      var equipment     =   '{{route("trashEquipmentList")}}';
      var forceDelete   =   '{{route("forceDeleteEquipment")}}';
      var restore_url       =   '{{route("restoreEquipment")}}';
      var myFields= [
            {
              title:"Alloted No.",
              name:"alloted_no",
              filtering:false,
              type:"text",
              width:20,
            },
            {
              title:"Entity",
              name: "entity.name",
              type:"select",
              items: JSON.parse(entity),
              valueType: "number|string",
              width:20,
            }, 
            @can('restore',new App\Models\Equipment)
            {
              title:"Restore",
              width:15,
              itemTemplate: function(value, item) {
                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var icon = $("<span>")
                        .attr({class:'fa fa-trash-restore text-success fa-2x'});
                var $customEditButton = $("<a>")
                                        .attr('data-toggle','modal')
                                        .attr({class:'btn'}).html(icon)
                                        .click(function(e) {
                                          e.preventDefault();
                                          restore_equipment(item['id']);
                                        });
                return $result.add($customEditButton);
              }
            },
           @endcan
           @can('destroy',new App\Models\Equipment)
            {
              title:"Force Delete",
              width:5,
              sorting:false,
              autoload:true,
              itemTemplate: function(value, item) {

                  var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);               
                  var icon = $("<span style='color:red'>")
                            .attr({class:'fa fa-trash fa-2x'});
                  var $customEditButton = $("<a>")
                     .attr({class:'btn'}).html(icon)
                     .click(function(e) {
                        if(confirm('Are you sure to delete this equipment permanently?'))
        {
            $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
        $.ajax({
                type: "post",
                url:forceDelete,
                data:{equipment_id:item['id']},
                success:function(response)
                {
                    toastr.success(response);
                    $('#jsGrid').jsGrid("render").done();
                    $('select').select2();
                }
            });
        }
                       e.stopPropagation();
                     })
                   return $result.add($customEditButton);
                   }
           },
           @endcan
        ];



   $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        autoload: true,
        filtering:true,
        sorting:true,
        editing:false,
        deleting:true,
        paging:true,
        pageSize:10,
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount} {next} {last}",
        pageLoading:true,
        controller: {
          loadData: function(filter) {
            return $.ajax({
                type: "POST",
                dataType:"json",
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url:equipment,
                data: filter
            });
          },
        },
    });

   
    function restore_equipment(equipment_id)
    {
        if(confirm('Are you sure to restore this equipment?'))
        {
            $.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
        $.ajax({
                type: "post",
                url:restore_url,
                data:{equipment_id:equipment_id},
                success:function(response)
                {
                   toastr.success(response);
                   $('#jsGrid').jsGrid("render").done();
                   $('select').select2();
                }
            });
        }
    }

</script>

@endsection
