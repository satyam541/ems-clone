@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Quotations</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <h4 class="">My Quotations</h4>
                    </div>
                    <div class="col-md-4">
                        <a href="{{route('quotationCreate')}}" class="btn float-right btn-success">Add new
                            Quotaion</a>
                    </div>
                </div>
                <div class="table table-responsive">
                    <table id="example1" class="table table-hover">

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Total Quotations</th>
                                <th>Date</th>
                                <th>Action</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($quotations as $quotation)
                                <tr>
                                    <td>{{ $quotation->item}}</td>
                                    <td>{{ $quotation->quantity}}</td>
                                    <td>{{ count($quotation->quotationDetails)}}</td>
                                    <td>{{ getFormatedDate($quotation->created_at)}}</td>
                                    <td><a href="{{route('quotationDetails',['quotation'=>$quotation->id])}}"
                                        class="mdi mdi-table-edit" style="font-size:21px;border-radius:5px;"></a>
                                    @if($quotation->status!='sent')
                                    <a class="mdi mdi mdi-delete" onclick="itemDelete('{{route('quotationDelete',['quotation_id'=>$quotation->id])}}')" style="font-size:22px;border-radius:5px;
                                    color:red;cursor: pointer;"></a>
                                    @endif
                                    </td>
                                        @if($quotation->status!='sent')
                                        <td><button id="{{$quotation->id}}" class="btn btn-primary btn-lg p-3">Send For Approval</button></td>
                                        @else
                                        @if(empty($quotation->quotationStatus()))
                                        <td><button class="btn btn-danger btn-lg p-3" disabled>Approval Pending</button></td>
                                        @elseif($quotation->quotationStatus()=='rejected')
                                        <td>Rejected</td>
                                        @else
                                        <td>Approved <label class="badge badge-success">{{$quotation->quotationStatus()}}</label></td>
                                        @endif
                                        @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
                "name": "Quantity",
                 sorting:false,
                searching:false,
            },
            {
                "name": "Total Quotations",
                 sorting:false,
                searching:false,
            },
            {
                "name": "Date",
                 sorting:false,
                searching:false,
            },
            {
                "name": "Details",
                 sorting:false,
                searching:false,
            },
            {
                "name": "Status",
                 sorting:false,
                searching:false,
            },

        ],

    });
    $('tbody').on('click', 'button', function() {
        var button = this;
        $(button).attr('disabled', true).html('Sending').append(
            '<i class="mdi mdi-rotate-right mdi-spin ml-1" aria-hidden="true"></i>');
        var employeeId = $(button).attr('id');
        var link = "{{ route('sendForApproval', '') }}" + '/' + employeeId;
        $.ajax({
            url: link,
            method: 'GET',
            success: function() {
                $(button).html('Approval Pending').removeClass('btn-primary').addClass('btn-danger').find('i').remove();
                toastr.success('Request Send');
            },
        });
    });
</script>

@endsection
