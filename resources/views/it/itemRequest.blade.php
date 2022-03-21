@extends('layouts.master')
@section('content')

    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Item Request List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-title">Item Request List</p>
                            <div class="table table-responsive">
                                <table style="width: 100%" class="table">

                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Requested By</th>
                                            <th>Requested For</th>
                                            <th>Availability</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemRequests as $itemRequest)
                                            <tr id="{{$itemRequest->id}}">
                                                <td>{{ $itemRequest->item->name}}</td>
                                                <td>{{$itemRequest->requestedByEmployee->name ?? null}}</td>
                                                <td>{{$itemRequest->requestedForEmployee->name ?? null}}</td>
                                                <td>{{count($itemRequest->availableEquipments())}}</td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <div class="row">
                                                        <div class="col-4 remarks">
                                                            {{Form::label('remarks','Remarks',['class'=>'font-weight-bold'])}}
                                                            {{ Form::textarea('remarks', null, ['rows'=>'1','cols'=>'20','class'=>'form-control remarks-field']) }}
                                                        </div>
                                                        <div class="col-6 action">
                                                            <button type="submit" @if(count($itemRequest->availableEquipments())==0) disabled @endif onclick="action({{$itemRequest->id}},true)" value="approve" class="btn btn-primary btn-rounded m-3 leave-action">Approve</button>
                                                            <button type="submit" onclick="action({{$itemRequest->id}},false)" value="reject" class="btn btn-danger btn-rounded m-3 leave-action">Reject</button>
                                                        </div>
                                                    </div>
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
        </div>
    </div>
@endsection


@section('footerScripts')
    <script>

    function action(item_id,is_approved)
    {
        var route="{{route('itemRequestAction')}}";
        var target=event.target;
        var remarks=$(target).closest('.action').siblings('.remarks').find('.remarks-field').val();
        if(is_approved==false)
        {
            if(remarks=='')
            {
                alert('Remarks Required');
                return false;
            }
        }
        $(target).attr('disabled',true).siblings('button').attr('disabled',true);
        $.ajax({
            url:route,
            type:'post',
            headers: { 'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content') },
            data:{item_id:item_id,action:is_approved,remarks:remarks},
            success:function(response){
                toastr.info('Request ' + response);
                location.reload();
            }
        });
        
    }
    </script>

@endsection
