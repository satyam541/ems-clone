@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Exit Employees</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <!-- Default box -->

            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-8">
                            <h4 class="">Exit Employees List</h4>
                        </div>
                        @can('hrUpdateEmployee', new App\Models\Employee())
                        <div class="col-2">
                            <a href="{{url('records/old_exit_employees_list.pdf')}}" target="_blank" class="btn btn-success btn-rounded float-right">View old records</a>
                        </div>
                        <div class="col-2">
                            <a href="{{route('exitForm')}}" class="btn btn-primary btn-rounded float-right">Add new record</a>
                        </div>
                        @endcan
                    </div>
                    <div class="table-responsive  col-12">
                        <table id="example1" class="table table-hover">

                            <thead>
                                <tr>
                                    <th>Picture</th>
                                    <th>Name</th>
                                    <th>Department</th>
                                    <th>Exit Date</th>
                                    <th>Details</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>

                                @foreach ($employees as $employee)
                                    <tr>
                                        <td><a target="_blank" href="{{ $employee->image_source }}"><img
                                                    src="{{ $employee->image_source }}" width="42" height="42"></a></td>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->department->name ?? null }}</td>
                                        
                                        <td>{{getFormatedDate($employee->employeeExitDetail->exit_date)}}</td>
                                        <td><a href="{{ route('employeeDetail', ['employee' => $employee->id]) }}"
                                                class="p-2 text-primary fas fa-address-card"
                                                style="font-size:20px;border-radius:5px;">
                                            </a>
                                        </td>
                                        <td>
                                            @if ($employee->employeeExitDetail->status() == 'Experience Pending')
                                            <button class="btn btn-lg p-3 btn-warning btn-rounded" onclick='uploadExperience("{{$employee->id}}","{{$employee->name}} ")' data-toggle="modal" data-target="#exampleModal"> Upload Experience</button>
                                            @else
                                                {{$employee->employeeExitDetail->status()}}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
   
    </div>

   <!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Upload Experience: <span></span></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{Form::open(['route' =>'uploadExperience','files' => 'true'])}}
        <div class="modal-body">
           
            {{Form::hidden('employee_id', null,['id'=>'employee_id'])}}
            {{Form::file('experience_file')}}
            
        </div>
        @can('hrUpdateEmployee', new App\Models\Employee())
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
        </div>
        @endcan
        {{Form::close()}}
      </div>
    </div>
  </div>
@endsection

@section('footerScripts')
<script>
    $('#example1').dataTable({
        ordering: false,
        fixedColumns: true,

        columnsDefs: [{
                "name": "Picture",
                sorting: false,
                searching: false
            },
            {
                "name": "Name"
            },
            
            {
                "name": "Department"
            },
            {
                "name": "Exit Date"
            },
            {
                "name": "Details",
                sorting: false,
                searching: false
            },
            {
                "name": "Status",
                sorting: false,
                searching: false
            }
        ],
        initComplete: function() {
            var data = this;
            this.api().columns([1, 2]).every(function() {
                var column = this;
                var columnName = $(column.header()).text();
                var select = $('<select class="selectJS form-control" data-placeholder="' +
                        columnName + '"><option value=""></option></select>')
                    .appendTo($(column.header()).empty())
                    .on('change', function() {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
                        if (val == "all") {
                            val = "";
                        }
                        column
                            .search(val ? '^' + val + '$' : '', true, true)
                            .draw();
                    });
                select.append('<option value="all">All</option>')
                column.data().unique().each(function(d, j) {
                    select.append('<option value="' + d + '">' + d +
                        '</option>')
                });
            });
        }
    });

    function uploadExperience(employee_id, employee_name){
        $('#employee_id').val(employee_id);
        $('#exampleModalLabel span').text(employee_name);
    }
</script>

@endsection
