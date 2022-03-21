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
      <!-- Default box -->
      
      <div class="card">

          
          @can('it',new App\Models\Equipment())
          <div class="card-body">
          <form action="{{route('exportEquipment')}}">
              @csrf
                  <div class="row">
                      <div class="col-md-3">
                        <select name="entity" class="form-control selectJS" placeholder="Select Entity" required >
                          <option value="" readonly>Select</option>
                          @foreach($entities as $item)
                              <option value="{{ $item->id }}"> {{$item->name}}</option>
                          @endforeach
                      </select>
                      </div>
                
                      <div class="col-md-3">
                            
                             
                              <button type="submit" class="btn btn-danger">Download Excel</button>

                          </div>
                  </div>

           </form>
          </div>
            
      </div>


      <div class="row">
          <div class="col-12">
              <div class="card">
                  <div class="card-body">
                      <p class="card-title">Equipment List</p>

                      <div id="jsGrid"></div>

                  </div>
              </div>
          </div>
      </div>
  </div>
  @endcan
</div>
@endsection
@section('footerScripts')
<script>

      var status          =   '{!! $status !!}';
      var entity          =   '{!! $entity !!}';
      var manufacturer    =   '{!! $manufacturer !!}';
      var employees       =   '{!! $employee !!}';
      var availability    =   '{!! $availability !!}';
      employees           =   JSON.parse(employees);
      employees['0']      =   'All';
      var equipment       =   '{{route("equipmentList")}}';
      var editEquipment   =   '{{route("editEquipment", ["equipment" => ":id"])}}';
      var myFields= [
            {
              title:"Alloted No.",
              name:"alloted_no",
              type:"number",
              width:20,
            },
            {
              title:"Alloted To",
              name:"employee_id",
              width:35,
              type:"select",
              items: employees,
              valueType: "number|string",
              itemTemplate: function(value, item) {
                if(item.employee && item.employee.is_active == 1)
                {
                  return item.employee.name;
                }
                if(item.employee && item.employee.is_active == 0)
                {
                  return item.employee.name + "(Deactivated)";
                }
                return "";
              },
            },
            {
              title:"Entity",
              name: "entity.name",
              type:"select",
              items: JSON.parse(entity),
              valueType: "number|string",
              width:20,
            },
            {
              title:"Manufacturer",
              name: 'manufacturer',
              type:'select',
              items: JSON.parse(manufacturer),
              valueType: "number|string",
              width:30,
            },
            {
              title:"Status",
              name: "isWorking",
              type:"select",
              items: JSON.parse(status),
              valueType: "number|string",
              width:25,
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
              title: 'Availability',
              name: 'availability',
              width: 35,
              type: 'select',
              items: JSON.parse(availability),
              valueType: "number|string",
              itemTemplate: function(value, item)
              {
                var availability = "Un-Assigned";
                if(item.employee)
                {
                  availability = "Assigned";
                }
                return $("<span>").text(availability);
                // console.log(item);
               }
              
            },
            {
              title:"Specifications",
              width:21,
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
              width:15,
              itemTemplate: function(value, item) {
                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var icon = $("<span>")
                        .attr({class:'fa fa-tools'});
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
              title:"Edit",
              width:15,
              itemTemplate: function(value, item) {

                  var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                  var url = editEquipment.replace(':id',item['id']);

                  var icon = $("<span>")
                            .attr({class:'fa fa-edit'});
                  var $customEditButton = $("<a>")
                     .attr('href',url)
                     .attr({class:'btn'}).html(icon)
                     .click(function(e) {
                       e.stopPropagation();
                     })
                   return $result.add($customEditButton);
                   }
           },
            {
              title:"Delete",
              width:15,
              itemTemplate: function(value, item) {

                  var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                  var url = editEquipment.replace(':id',item['id']);

                  var icon = $("<span>")
                            .attr({class:'fa fa-trash text-red'});
                  var $customEditButton = $("<a>")
                     .attr({class:'btn'}).html(icon)
                     .attr('onclick', "deleteEquipment("+item['id']+")")
                   return $result.add($customEditButton);
                   }
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

function deleteEquipment(equipmentId)
{
  var confirm = window.confirm("Do You Want to Delete The Equipment?");
  var url = "{{route('deleteEquipment')}}";
  if(confirm)
  {
    $.ajax({
      url: url,
      data: {'equipment': equipmentId},
      dataType: 'json',
      success: function(response)
      {
        toastr.success(response.status);
        setTimeout(function() {
          location.reload();
        }, 800);
      }
    });
  }
}

$(document).ready(function(){
    var option = "<option value='All'>All</option>";
    var select = $("select[name=employee_id]");
    select.append(option);
    select.trigger("change");
  });
</script>

@endsection
