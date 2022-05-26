@extends('layouts.master')
@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Assets List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">

                {{ Form::open(['method' => 'GET']) }}
                <div class="card-body">
                    <p class="card-title">Filter</p>
                    <div class="form-group row">

                        {{ Form::label('type', 'Select Type', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('type', $types, request()->type, ['class' => 'form-control selectJS','placeholder' => 'Select Type']) }}
                        </div>

                        {{ Form::label('sub_type', 'Select Sub Type', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('sub_type', $sub_types, request()->sub_type, ['class' => 'form-control selectJS','placeholder' => 'Select Sub Type']) }}
                        </div>

                        {{ Form::label('status', 'Select Status', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::select('status', $statuses, request()->status, ['class' => 'form-control selectJS','placeholder' => 'Select Status']) }}
                        </div>
                        {{ Form::label('Bar Code', 'Bar Code', ['class' => 'col-sm-2 col-form-label']) }}
                        <div class="col-sm-4">
                            {{ Form::text('bar_code', request()->bar_code, ['class' => 'form-control', 'placeholder' => 'Enter Bar Code']) }}
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            {{ Form::submit('Filter', ['class' => 'btn m-2 btn-primary']) }}
                            <a href="{{ request()->url() }}" class="btn m-2 btn-success">Clear Filter</a>
                        </div>
                    </div>

                </div>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Assets List
                                <div class="float-right">
                                    @can('create',new App\Models\Asset())
                                        <a href="{{ route('asset.create') }}" class="btn btn-sm btn-primary"> <i
                                                class="fa fa-plus"></i> Create </a>
                                    @endcan
                                </div>
                            </div>
                            @php
                                $page = 0;
                                if (!empty(request()->page) && request()->page != 1) {
                                    $page = request()->page - 1;
                                    $page = $page * 25;
                                }
                            @endphp
                            <div class="table-responsive">
                                <table id="example1" class="table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Type</th>
                                            <th>Sub Type</th>
                                            <th>Barcode</th>
                                            <th>Status</th>
                                            <th>Description</th>
                                            @can('update',new App\Models\Asset())
                                                <th>Edit</th>
                                            @endcan
                                            <th>Detail</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($assets as $asset)
                                            <tr>
                                                <td>{{ $loop->iteration + $page }}</td>
                                                <td>{{ $asset->assetSubType->assetType->name ?? '' }}</td>
                                                <td>{{ $asset->assetSubType->name ?? '' }}</td>
                                                <td>{{ $asset->barcode }}</td>
                                                <td>{{ $asset->status ?? '' }}</td>
                                                @if (!empty($asset->description))
                                                    <td>
                                                        <textarea class="form-control" disabled cols="2">{{ $asset->description }}</textarea>
                                                    </td>
                                                @else
                                                    <td>N/A</td>
                                                @endif
                                                @can('update',new App\Models\Asset())
                                                    <td><a href="{{ route('asset.edit', ['asset' => $asset->id]) }}"><i
                                                                class="fa fa-edit"></i></a>
                                                    </td>
                                                @endcan
                                                <td>
                                                    <a href="{{ route('asset.show', ['asset' => $asset->id]) }}"
                                                        class="btn btn-warning btn-lg p-3">Detail</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="float-left">
                                <b>Total Results: </b>{{ $assets->total() }}
                            </div>
                            <div class="float-right">
                                {{ $assets->appends(request()->query())->links() }}
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

    </script>
@endsection
