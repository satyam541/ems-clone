@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Quotation List</li>
            </ol>
        </nav>
    </div>
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <p class="card-title">Quotation List</p>
  
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table table-responsive">
                                    <table id="example1" class="table table-hover">
    
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Quantity</th>
                                                <th>Total Quotations</th>
                                                <th>Created By</th>
                                                <th>Date</th>
                                                <th>Details</th>
                                            </tr>
                                        </thead>
                                        <tbody>
    
                                            @foreach ($quotations as $quotation)
                                                <tr>
                                                    <td>{{ $quotation->item}}</td>
                                                    <td>{{ $quotation->quantity}}</td>
                                                    <td>{{ count($quotation->quotationDetails)}}</td>
                                                    <td>{{$quotation->employee->name}}</td>
                                                    <td>{{ getFormatedDate($quotation->created_at)}}</td>
                                                    <td><a href="{{route('quotationDetails',['quotation'=>$quotation->id])}}"
                                                     class="mdi mdi-table-edit" style="font-size:20px;border-radius:5px;"></a></td>
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
                "name": "Created By",
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
