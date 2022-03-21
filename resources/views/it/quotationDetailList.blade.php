@extends('layouts.master')
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb" class="float-right">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('quotationList') }}">Quotation List</a></li>
                <li class="breadcrumb-item active" aria-current="page">My Quotation's Details</li>  
            </ol>
        </nav>
    <h3>Quotation Details</h1>
    </div>

    @foreach($quotationDetails as $quotationDetail)
    @if(count($quotationDetails)==1)    
    <div class="col-12 mb-3">
    @else
    <div class="col-6 mb-3">
    @endif
        <div class="card">
            <div class="card-body">
                <p class="card-title float-left">Item:- {{$quotationDetail->quotation->item}} ({{$quotationDetail->quotation->quantity}})</p>
                <div>
                    <div class="float-right">
                        @if(!auth()->user()->hasRole('hr'))
                        <a href="{{route('quotationDetailEdit',['quotationDetail'=>$quotationDetail->id])}}" class="mdi mdi-table-edit mdi-lg" 
                            style="font-size:20px;border-radius:5px;margin:-53px -9px;font-size:30px;@if($quotationDetail->quotation->status =='sent')pointer-events:none; @endif "></a>

                        <a class="mdi mdi mdi-delete" 
                        onclick="itemDelete('{{route('deleteQuotationDetail',['quotationDetail_id'=>$quotationDetail->id])}}')" 
                        style="font-size:22px;border-radius:5px;font-size:30px;color:red;cursor: pointer;@if($quotationDetail->quotation->status =='sent')pointer-events:none; @endif "></a>
                        @endif
                    </div>
                </div><br><br>
                <span>Price Per Item:- {{$quotationDetail->price_per_item}}</span>
                <span style="float: right;">Total Price:- {{$quotationDetail->total_price}}</span>
                <br><br>
                <span>Vendor Detail:- {{$quotationDetail->vendor_detail}}</span>
                <span style="float: right;">Date:- {{getFormatedDate($quotationDetail->created_at)}}</span>
                <br>
                @if($quotationDetail->item_detail)
                <hr>
                <p class="card-title">Specifications:-</p>
                <br>
                @foreach($quotationDetail->item_detail as $item_type => $item_value)
                <span>{{ucfirst($item_type)}}</span>
                <span style="float:right;">{{$item_value}}</span>
                <br><br>
                @endforeach
                @endif
            </div>
            @if(is_null($quotationDetail->is_approved))
            @can('quotationAction', new App\Models\Stock())
            <div class="col-md-12">
                <div class="form-group row">
                    {{Form::label('comment', "Remarks: ",['class'=>'col-sm-3 col-form-label'])}}
                    <div class="col-sm-9">
                        <textarea id="{{$quotationDetail->id}}" class="form-control" placeholder="Optional" name="comment" value=""></textarea>
                    </div>
                </div>
            </div>
            <div class="form-group text-center">
                <button class="approve btn btn-primary btn-rounded btn-fw mr-5 {{$quotationDetail->id}}"
                 onclick="action({{$quotationDetail->id}}, true)">Approve</button>
                <button class="reject btn btn-danger btn-rounded btn-fw {{$quotationDetail->id}}"
                 onclick="action({{$quotationDetail->id}}, false)">Reject</button>
            </div>
            @endcan
             @else
             <div class="form-group text-center">
                @if($quotationDetail->is_approved)
                <button class="btn btn btn-primary btn-rounded btn-fw mr-5" disabled>Approved</button>
                @else
                <button class="btn btn btn-danger btn-rounded btn-fw mr-5" disabled>Rejected</button>
                @endif
            </div>
            @endif
            
            {{-- @endcan --}}
        </div>
    </div>
    @endforeach
</div>
@endsection
@section('footerScripts')
<script>
    function action(quotation_id, is_approved)
    {   
        $('.'+quotation_id).attr('disabled',true);
        var target = event.target;
        var comment=$('#'+quotation_id).val();
        $(target).siblings('button').remove();
        var url = "{{route('quotationAction')}}";
        $.ajax({
            url: url,
            type: 'post',
            headers: { 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content') },
            data: {quotation_id: quotation_id, action: is_approved,comment:comment},
            success: function(response){
                $('#'+quotation_id).closest('div.col-md-12').remove();
                if(response==1)
                {
                    
                    $(target).html('Approved').siblings('.reject').remove();
                    toastr.success('Quotation: Approved');
                    
                }
                else if(response==0)
                {
                    $(target).html('Rejected').siblings('.approve').remove();
                    toastr.info('Quotation: Rejected');
                    
                }
            },

        })
    }
</script>
@endsection


