@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
    <nav aria-label="breadcrumb" class="float-right">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Equipemnt Request List</li>
        </ol>
    </nav>
  </div>
  <div class="col-12">
      
       <div class="card">
        
        <div class="card-body">
          <div class="float-left">
            <p class="card-title">Equipemnt Request List</p>
          </div>
              <div class="float-right pb-2">
                @can('managerEntityRequestList', new App\Models\EquipmentRequests() )
                  <a href="{{route('requestEquipments')}}"><button class="btn btn-primary btn-rounded btn-fw" type="button">Request Equipemnt</button></a>
                @endcan
              </div>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>

@endsection
  
@section('footerScripts')
<script>

      var entity='{!! $entity !!}';
      var  updateEntity = "{{ route('updateEntityRequest', ['entity' => ':id'])}}";
      var  deleteEntity = "{{ route('deleteEntityRequest', ['entity' => ':id'])}}";
      var  equipmentDetails = "{{ route('equipmentDetails', ['entity' => ':id'])}}";
      var  addEntity = "{{ route('insertEntityRequest')}}";
      var myFields= [
            {
              title:"Entity",
              name:"entity_id",
              type:"select",
              items:JSON.parse(entity),
              width:35,
              valueType: "number|string"
            },
            {
              title:"Requested quantity",
              name:"requested_quantity",
              type:"number",
              filtering:false,
              width:35
            },
            {
              title:"Department",
              name:"department",
              type:"text",
              filtering:false,
              width:40
            },
            {
              title:"Manager Comment",
              name:"comment",
              type:"text",
              filtering:false,
              width:40
            },
            {
              title:"Applied on",
              name:"applied_on",
              type:"text",
              filtering:false,
              width:40
            },
            @if($isAssigner)
            {
              title: "Action",
              filtering:false,
              width:20,
              itemTemplate: function(value, item)
              {
                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var $customEditButton = $("<button>")
                                          .attr('type', 'button')
                                          .attr({class:'btn btn-info'})
                                          .text("Assign")
                                          .click(function(e) {
                                            open_request(item);
                                          });
                return $result.add($customEditButton);
              }

            }
            @endif
        ];


   var entity='{{route("entityRequestList")}}';

    $("#jsGrid").jsGrid({
      fields:myFields,
      width: "100%",
      autoload: true,
      filtering:true,
      editing:false,
      inserting:false,
      deleting:false,
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
<script>
function open_request(item)
{
  window.location.href="{{route('requestEquipments')}}/"+item.request_id;
}
</script>

@endsection
