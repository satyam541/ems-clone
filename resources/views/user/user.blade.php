@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">User List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">User List</p>
                            <div class="table">
                                <table id="example1" style="width: 100%" class="table table-responsive">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th>Role</th>
                                            <th>Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($users as $user)
                                            <tr>
                                                <td>{{ $user->name}}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>@if($user->is_active) Active @else Inactive @endif</td>
                                                <td>{{implode(', ', $user->roles->pluck('name')->toArray())}}</td>
                                                @can('insert', auth()->user())
                                                <td><a class="mdi mdi-table-edit" style="font-size:20px;border-radius:5px;" 
                                                    href="{{route('editUser',['user'=>$user->id])}}"></a></td>
                                                @endcan
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        @endsection


        @section('footerScripts')
        <script>
            $('#example1').dataTable({
                ordering: false,
                fixedColumns: true,
    
                columnsDefs: [
                    {
                        "name": "Name"
                    },
                    {
                        "name": "Email"
                    },
                    {
                        "name": "Status",
                        sorting: false,
                        searching: false
                    },
                    {
                        "name": "Role",
                        sorting: false,
                        searching: false
                    },
                    {
                        "name": "Edit",
                        sorting: false,
                        searching: false
                    },
    
                ],
                initComplete: function() {
                    var data = this;
                    this.api().columns([0, 1, 2,3]).every(function() {
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
        </script>

        @endsection
