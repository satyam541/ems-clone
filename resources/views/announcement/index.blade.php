@extends('layouts.master')
@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Announcement List</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title">Announcement
                                <div class="float-right">
                                    @can('create',new App\Models\Announcement())
                                        <a href="{{ route('announcement.create') }}" class="btn btn-sm btn-primary"> <i
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
                                            <th>Title</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Is Publish</th>
                                            <th>Start Time</th>
                                            <th>End Time</th>
                                            @canany(['update','delete'],new App\Models\Announcement())
                                                <th>Edit</th>
                                            @endcanany
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($announcements as $announcement)
                                            <tr>
                                                <td>{{ $loop->iteration + $page }}</td>
                                                <td>{{ $announcement->title }}</td>
                                                <td>{{ $announcement->start_dt }}</td>
                                                <td>{{ $announcement->end_dt }}</td>
                                                <td>{{ $announcement->is_publish }}</td>
                                                <td>{{ $announcement->start_time }}</td>
                                                <td>{{ $announcement->end_time }}</td>
                                                @canany(['update','delete'],new App\Models\Announcement())
                                                    <td>
                                                        @can('update',new App\Models\Announcement())
                                                        <a href="{{ route('announcement.edit', ['announcement' => $announcement->id]) }}"><i
                                                                class="fa fa-edit"></i></a>
                                                        @endcan
                                                        @can('delete',new App\Models\Announcement())
                                                        <a href="javascript:void(0);" onclick="deleteItem('{{ route('announcement.destroy', $announcement->id) }}')">
                                                                    <i class="fa fa-trash text-danger"></i></a>
                                                        @endcan
                                                    </td>
                                                @endcanany
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
        $('#example1').dataTable(
        {
            ordering: false,
        });
    </script>
@endsection
