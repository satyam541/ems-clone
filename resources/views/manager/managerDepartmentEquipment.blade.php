@extends('layouts.master')
@section('content')
<div class="row">
  <div class="col-12">
      <nav aria-label="breadcrumb" class="float-right">
          <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
              <li class="breadcrumb-item active" aria-current="page">Equipment List</li>
          </ol>
      </nav>
  </div>
  <div class="col-12">
      <div class="card">
          <div class="card-body">
              <p class="card-title">Equipment List</p>

              <div id="jsGrid"></div>

          </div>
      </div>
  </div>
</div>
@endsection
@section('footerScripts')
<script>

      var status          =   '{!! $status !!}';
      var entity          =   '{!! $entity !!}';
      var manufacturer    =   '{!! $manufacturer !!}';
      var employees       =   '{!! $employees !!}';
      employees           =   JSON.parse(employees);
      employees['0']      =   'All';
      var equipment       =   '{{route("managerDepartmentEquipmentList")}}';
      var editEquipment   =   '{{route("editEquipment", ["equipment" => ":id"])}}';
      var myFields= [
            {
              title:"Alloted No.",
              name:"alloted_no",
              type:"number",
              width:100,
            },
            {
              title:"Entity",
              name: "entity.name",
              type:"select",
              items: JSON.parse(entity),
              valueType: "number|string",
              width:150,
            },
            {
              title:"Status",
              name: "isWorking",
              type:"select",
              items: JSON.parse(status),
              valueType: "number|string",
              width:150,
              itemTemplate: function(value, item)
              {
                var status = "";
                if(item.isWorking == 1)
                {
                  status = "Working";
                }
                else{
                  status = "Not Working";
                }
                return $("<span>").text(status);
              }
            },
            {
              title:"Manufacturer",
              name: 'manufacturer',
              type:'select',
              items: JSON.parse(manufacturer),
              valueType: "number|string",
              width:150,
            },
            {
              title:"Specifications",
              width:200,
              sorting:false,
              itemTemplate: function(value, item) 
              {
                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var icon = $("<span>")
                          .attr({class:'fa fa-list'});
                var $customEditButton = $("<a>")
                                          .attr('data-toggle','modal')
                                          .attr({class:'btn'}).html(icon)
                                          .click(function(e) {
                                            e.preventDefault();
                                            showpopup('specifications', item);
                                          });
                return $result.add($customEditButton);
              }
            },
            {
              title:"Repair",
              width:100,
              itemTemplate: function(value, item) {
                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var icon = $("<span>")
                        .attr({class:'fa fa-list'});
                var $customEditButton = $("<a>")
                                        .attr('data-toggle','modal')
                                        .attr({class:'btn'}).html(icon)
                                        .click(function(e) {
                                          e.preventDefault();
                                          showpopup('repair',item);
                                        });
                return $result.add($customEditButton);
              }
            },
            {
              title:"Alloted To",
              name:"employee_id",
              width:200,
              type:"select",
              items: employees,
              valueType: "number|string",
              itemTemplate: function(value, item) {
                if(item.employee)
                {
                  return item.employee.name;
                }
                return "";
              },
            },
            
        ];



   $("#jsGrid").jsGrid({
        fields:myFields,
        width: "100%",
        autoload: true,
        filtering:true,
        sorting:true,
        editing:false,
        paging:true,
        pageSize:10,
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

    function showpopup(modelname, item)
    {
      $('.modal-title.text-center').text(ucfirst(modelname));
      if(modelname == "specifications")
        $('#model_body').html(getSpecificationsHtml(item.specifications));
      else if(modelname == "repair")
        $('#model_body').html(getRepairRecordHtml(item.repairs));
      $('#myModal').modal('show');
    }
    function getSpecificationsHtml(specifications)
    {
      var starthtml ="<div class='row'>";
      var html = "";
      var endhtml = "</div>";
      if(specifications.length >0)
      {
        $.each(specifications, function(index, specification){
          html += "<div class='col-sm-6'>"+specification.name+": </div><div class='col-sm-6'>"+specification.description+"</div>";
        });
        return starthtml+html+endhtml;
      }
      else 
      {
        html = "<p>No Specification Available</p>";
        return html;
      }
      
      return starthtml+html+endhtml;
    }
    function getRepairRecordHtml(repairs)
    {
      var starthtml ="<div class='row'>";
      var header = "";
      var html = "";
      var endhtml = "</div>";
      var loop = 1;
      if(repairs.length > 0){
        
        header += "<div class='col-sm-4 text-bold'><u>Date</u></div>\
                   <div class='col-sm-4 text-bold'><u>Part</u></div>\
                   <div class='col-sm-4 text-bold'><u>Cost</u></div>"
        $.each(repairs, function(index, repair){
            html += "<div class='col-sm-4'>"+repair.date+"</div>";
            html += "<div class='col-sm-4'>"+repair.part+"</div>";
            html += "<div class='col-sm-4'>"+repair.cost+"</div>";
        });
        return starthtml+header+html+endhtml;
      }
      else{
        html = "<p>No Repair Record</p>";
        return html;
      }
    }

function ucfirst(word)
{
  return word[0].toUpperCase() + word.substr(1)
}

$(document).ready(function(){
    var option = "<option value='All'>All</option>";
    var select = $("select[name=employee_id]");
    select.append(option);
    select.trigger("change");
  });
</script>

@endsection


