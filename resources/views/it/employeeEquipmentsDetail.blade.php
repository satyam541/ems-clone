@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee Equipment Details</li>
                </ol>
            </nav>
            <h3>{{ $employee->name }}'s Equipment Details </h1>
        </div>
        <div class="col-12 mb-3">
            <div class="card">
                <div class="card-body">
                    {{-- <a onclick="return confirm('Are you sure?');" class="mdi mdi-delete mdi-lg"
                        style="font-size:18px;border-radius:5px;color:red;float:right; cursor: pointer;"></a>
                    <br><br> --}}
                    {{ Form::open(['route'=>'employeeEquipmentAction'])}}
                    {{Form::hidden('id',$employee->id)}}
                    <div class="form-group row">
                        @foreach ($equipmentTypes as $assignableEquipments)
                            @foreach ($assignableEquipments as $assignableEquipment)
                                @if($assignableEquipment->stockDetails->isNotEmpty())
                                <label class="col-sm-2 col-form-label">Select
                                    {{ ucfirst($assignableEquipment->item->name) }}</label>
                                <div class="col-4">
                                    @php $availableEquipments = $assignableEquipment->stockDetails->pluck('equipment_label', 'id')->toArray() @endphp
                                    {{ Form::select('available_equipment_ids[]', $availableEquipments,$employee->matchedAssignedEquipment($availableEquipments), ['class' => 'form-control selectJS', 'placeholder' => 'select an option']) }}
                                </div>
                                @endif
                            @endforeach

                        @endforeach
                    </div>
                    <br>
                </div>
                @if(!empty($availableEquipments))
                <div class="form-group">
                    <button class="btn btn-primary btn-rounded btn-fw ml-5">Submit</button>
                </div>
                @else
                <h4><marquee behavior="alternate" direction="right">No equipment available to assign</marquee></h4>
                @endif
                {{ Form::close() }}
            </div>
        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        $(document).ready(function(){
            $('.selectJS').select2({
                placeholder: "Select an option",
                allowClear: true,
                width: '93%'
            });
        });
    </script>
@endsection
