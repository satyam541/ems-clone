@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Leave Approvals List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Leave Approvals</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
  @endsection
  
@section('footerScripts')

<script type="text/javascript">
  
  var employees='{!! $employees !!}';
  var departments='{!! $departments !!}';
  employees           =   JSON.parse(employees);
  employees['0']    =   'All';
  departments           =   JSON.parse(departments);
  departments['0']    =   'All';
  
  var DateField = function(config) {
    jsGrid.Field.call(this, config);
  };

  DateField.prototype = new jsGrid.Field({
      sorter: function(date1, date2) {
          return new Date(date1) - new Date(date2);
      },    
      
      itemTemplate: function(value) {
          date = $.datepicker.formatDate( "d/m/yy",new Date(value));
          return date;
      },
      
      filterTemplate: function() {
          this._fromPicker = $("<input>").datepicker({ defaultDate: new Date(), 
          dateFormat: 'dd/mm/yy',
          prevText: "Prev",
          nextText: "Nxt" });
          return $("<div>").append(this._fromPicker);
      },  
      filterValue: function() {
          return $.datepicker.formatDate("yy-mm-dd", this._fromPicker.datepicker("getDate"));
          // return this._fromPicker.datepicker("getDate");
      },
  });

  jsGrid.fields.date = DateField;

  editLeaveApprovalUrl = "{{ route('editLeaveApproval', ['leave' => ':id'])}}";
  
  var myFields= [
          
    {
             
             title:"Name",
             name: "employee_id",
             type:"select",
             items: employees,
             valueType: "number|string",
             width:300,
              
           },
           {
            
             title:"Department",
             name: "department_id",
             type:"select",
             items: departments,
             width:200,
           
            },
            {
                title:"Type",
                name: "type",
                type:"text",
                width:150,
               
            },
            {
                title:"From",
                name: "from_date",
                type: "date", 
                width: 150, 
                align: "center",
                autosearch: true
               
            },
            {
                title:"To",
                name: "to_date",
                type: "date", 
                width: 150, 
                align: "center",
                autosearch: true
               
            },        
            {
                title:"Status",
                name: "status",
                type:"text",
                width: 100, 
               
            },
            {
              type:"control",
              editButton:false,
              deleteButton:false,
              width:100,
              @can('approval', new App\Models\Leave())
              itemTemplate: function(value, item) {

                      var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                      var $iconPencil = $("<i>").attr({class: "fa fa-edit align-top pl-2"});
                      var $customEditButton = '';
                      if (item.status == 'Pending') {
                        var $customEditButton = $("<a>")
                          .attr('href', editLeaveApprovalUrl.replace(':id', item.id))
                          .attr({title: 'Edit'})
                          .click(function(e) {
                              e.stopPropagation();
                          })
                          .append($iconPencil);
                      }

                      return $result.add($customEditButton);
                }
                @endcan
            },
     
        ];

   var leaveApprovalListURL='{{route("leaveApprovalList")}}';

    $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        paging: true,
        autoload: true,
        pageSize: 10,
        filtering:true,
        paging:true,
        deleteConfirm: "Do you really want to delete Leave?",
        pageLoading: true,
        controller: {
            loadData: function(filter) {
                return $.ajax({
                    type: "GET",
                    dataType:"json",
                    url:leaveApprovalListURL,
                    data: filter
                });
            }
        },
        
        onItemDeleted: function(args) {

        toastr.success('Leave Deleted Successfully')

        },
  
    });

</script>
@endsection
