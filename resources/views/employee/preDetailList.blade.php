@extends('layouts.master')
@section('content')

<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pre-Detail List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12 mb-3">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    {{ Form::open(['method' => 'GET']) }}
                    <div class="card-body">
                        <p class="card-title">Filter</p>
                        <div class="form-group row">
                            {{ Form::label('user_id', 'Select Name', ['class' => 'col-sm-2 col-form-label']) }}
                            <div class="col-sm-4">
                                {{ Form::select('user_id', $users, request()->user_id, ['class' => 'form-control selectJS', 'placeholder' => 'Select Name']) }}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                                <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                                {{ Form::close() }}
                            </div>
                        
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">

        </div>
    </div>
    <div class="col-12">
        <!-- Default box -->

        <div class="card">
            <div class="card-body ">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="col-md-8 float-right text-right">
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive ">
                        <table id="example1" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Contact Number</th>
                                    <th>Linked In</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($details as $detail)
                                <tr>
                                    <td>{{ $detail->user->name ?? ''}}</td>
                                    <td>{{ $detail->contact_number}}</td>
                                    <td>{{ $detail->linked_in}}</td>
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
@endsection


@section('footerScripts')
<script>
    $('#example1').dataTable({
        order: [
            [0, 'desc']
        ],

        columnsDefs: [{
                "name": "Date"
                , sorting: false
                , searching: false
            }
            , {
                "name": "Punch In"
                , sorting: false
                , searching: false
            }
            , {
                "name": "Punch Out"
                , sorting: false
                , searching: false
            },

        ],

    });

</script>

@endsection
