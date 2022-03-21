@extends('layouts.master')
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
          <h1 class="m-0 text-dark">Entity Request List</h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
              <li class="breadcrumb-item active">Entity Request List</li>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
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

      var entity='{!! $entity !!}';
      var equipment_status='{!! $status !!}';
      var  updateEntity = "{{ route('alloteEquipments', ['entity' => ':id'])}}";
      var myFields= [
            {
              title:"Entity",
              name:"entity_id",
              type:"select",
              items:JSON.parse(entity),
              width:35,
              editing:false,
              filtering:false,
              inserting:false,
              valueType: "number|string"
            },
            {
              title:"Requested quantity",
              name:"requested_quantity",
              type:"number",
              editing:false,
              inserting:false,
              filtering:false,
              width:35
            },
            {
              title:"Manager Comment",
              name:"manager_comment",
              type:"text",
              editing:false,
              inserting:false,
              filtering:false,
              width:40
            },
            {
              title:"Status",
              name:"status",
              editing:true,
              filtering:true,
              inserting:false,
              type:"select",
              items:JSON.parse(equipment_status),
              valueType:'number|string',
              width:40
            },
            {
              title:"Approved Quantity",
              name:'approved_quantity',
              type:"number",
              editing:true,
              filtering:false,
              inserting:false,
              width:35
            },
            {
              title:"Remarks",
              name:"remarks",
              type:"text",
              editing:true,
              filtering:false,
              inserting:false,
              width:40
            },
            {
              title:"Applied on",
              name:"created_at",
              type:"text",
              editing:false,
              filtering:false,
              inserting:false,
              width:40
            },
            {
              type:"control",
              deleteButton: false,
            }
        ];


   var entity='{{route("allEntityRequestList")}}';

    $("#jsGrid").jsGrid({
      fields:myFields,
      width: "100%",
      autoload: true,
      filtering:true,
      @can('itEntityRequestList',new App\Models\EquipmentRequests())
      editing:true,
      @else
      editing:false,
      @endcan
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
        updateItem: function(item) {
          updateUrl = updateEntity.replace(':id',item['id']);
           if(item['approved_quantity']>item['requested_quantity'])
           {
            toastr.warning("Approved quantity must be less than equal to requested quantity"); 
           }
           else if((item['approved_quantity']>0 && item['status']=='rejected'))
           {
            toastr.warning("Approved quantity cann't be >0 when status is rejected"); 
           }
          else if(item['approved_quantity']>0 && (item['status']=='approved' ||item['status']=='pending'))
          {
            return $.ajax({
                type: "POST",
                dataType:"json",
                url:updateUrl,
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: item
            });
          }
          else
          {
            toastr.warning("Irrelevant Data<br>Approved quanitity must be >0 if approved or should be 0 if rejected");
          }
        },
      },
      onItemUpdated: function(args) {
        var approved_quantity=args.item.approved_quantity;
        var requested_quantity=args.item.requested_quantity;
        if((args.item.status=='pending' || args.item.status=='approved') && approved_quantity>0 && approved_quantity<=requested_quantity)
        {
          toastr.success('Entity Request Updated Successfully')
        }
          
      },
      onError: function(args) {
   
       errors = args[0].responseJSON.errors;
       error = '';
        $.each(errors, function(key, value) {
            
            error += value + "\n";

        });
        toastr.warning(error);

      },
    });

</script>
@endsection
