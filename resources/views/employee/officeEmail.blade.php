@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assign Office Email</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Assign Office Email</p>
                            <div class="col-12">
                                <table id="example1" class="table">

                                    <thead>
                                        <tr>
                                            <th scope="col">Name</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">Email</th>
                                            <th>Update</th>


                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($employees as $employee)
                                            <tr>
                                                {{ Form::open(['route' => 'updateOfficeEmail', 'method' => 'post']) }}
                                                <input type="hidden" class="form-control" name="id"
                                                    value="{{ $employee->id }}">
                                                <td>{{ $employee->name }}</td>
                                                <td>{{ $employee->department->name }}</td>
                                                <td><input type="text" class="form-control" name="email" value=""></td>

                                                <td>
                                                    <button type="submit" name="submit" class="btn btn-primary btn-rounded m-3">Update</button>
                                                </td>

                                                {{ Form::close() }}
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
        $(document).ready(function() {
            $('#example1').DataTable({
                columns: [
                    {
                        "name": "Name"
                    },
                    {

                        "name": "Department"
                    },
                    {
                        "name": "Email",
                        sorting: false,
                        searching: false
                    },
                    {
                        "name": "Update",
                        sorting: false,
                        searching: false
                    },


                ],

            });
            $.fn.dataTableExt.ofnSearch['html-input'] = function(value) {
                return $(value).val();
            };
        });
    </script>


@endsection
