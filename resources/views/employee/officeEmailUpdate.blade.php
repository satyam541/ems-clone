@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Office Email List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Update Office Email</p>
  
                <div id="jsGrid"></div>
  
            </div>
        </div>
    </div>
</div>
@endsection

@section('footerScripts')
<script>
      var departments ='{!! $departments !!}';
      var updateURL = '{{ route("updateAllotedOfficeEmail") }}';
      var officeEmailURL = '{{ route("allotedOfficeEmailList") }}';
      departments =JSON.parse(departments);
      departments['0']='All';
      var myFields= [
          
           
            {
                title:"Employee",
                name: "name",
                type:"text",
                editing:false,
                width:40
               
            },
            {
                title:"Office Email",
                name: "user.email",
                type:"text",
                width:40,
                filtering:false
               
            },
            {
            title: "Department",
            name: "department",
            type: "select",
            items:departments,
            editing: false,
            width: 40,
            itemTemplate: function(value, item) {
                if(item.department)
                {
                  return item.department.name;
                }
                return "";
              },
        },
            @can('it', new App\Models\Equipment)
            {
            type: "control",
            deleteButton: false,
            title: "Edit",
            width: 40,
            itemTemplate: function(value, item) {

                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
               
                var $customEditButton = $("<a>")
                    .attr('href', officeEmailURL.replace(':id', item.id))
                    .attr({
                        title: 'Assign Permissions'
                    })
                    .click(function(e) {

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
        paging: true,
        autoload: true,
        pageSize: 10,
        filtering:true,
        editing:true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:officeEmailURL,
                    data: filter
                });
            },
            updateItem: function(item) {
                return $.ajax({
                    type: "POST",
                    dataType:"json",
                    url: updateURL,
                    headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data:item
                   
                });
                
            },

        },
        onItemUpdated: function(args) {
          toastr.success('Office Email Addded Successfully');
          $("#jsGrid").jsGrid("render");
          $('select').select2();
        },
        onError: function (args) {
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
