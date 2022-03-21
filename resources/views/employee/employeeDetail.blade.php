@extends('layouts.master')
@section('content')
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Employee Profle</li>
                </ol>
            </nav>
        </div>
        <div class="col-12 grid-margin">
            <div class="col-12 m-auto text-center bg-white">
                <div class="widget-user-image">
                    <br><img class="img-circle elevation-2" height="120px" width="120px" style="border-radius: 100%" src="{{ $employee->getImagePath() }}" alt="User Avatar">
                </div>
                <div class="row pt-3 pb-4">
                    <div class="col-sm-12">
                        <div class="description-block">
                            <div class="description-header text-uppercase"><span class="">{{ $employee->name }}</span>@can('hrUpdateEmployee', $employee) &nbsp; <a href="{{route('editEmployee',['employee'=>$employee->id])}} "data-toggle="tooltip" data-placement="top" title="Edit Profile"><i class="fa fa-user-edit"></i> </a> @endcan</div>
                            <span class="description-text">{{ $employee->department->name }}</span>
                        </div>
                        <!-- /.description-block -->
                    </div>
                    <!-- /.col -->

                </div>
                <!-- /.row -->
            </div>

            <div class="row">

                @if(Auth::user()->can('viewFullProfile', new App\Models\Employee()) || Auth::user()->employee->id == $employee->id)
                <div class="col-md-6">
                    <div class="card card-outline mb-2">
                        <div class="card-header bg-primary text-white">
                            Personal Information
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mailbox-messages">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td>D-O-B</td>
                                            <td>{{ getFormatedDate($employee->birth_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td> Personal Email</td>
                                            <td>
                                                @if ($employee->personal_email != null)
                                                    {{ $employee->personal_email }}@else <code
                                                        class="text-danger">N/A</code>@endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Contact</td>
                                            <td>
                                                @if ($employee->phone != null)
                                                    {{ $employee->phone }}@else <code class="text-danger">N/A</code>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Qualification</td>
                                            <td>{{ optional($employee->qualification)->name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                @endif

                <div class="col-md-6">
                    <div class="card card-outline mb-2">
                        <div class="card-header bg-primary text-white">
                            Office Details
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mailbox-messages">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td>Join Date</td>
                                            <td>{{ getFormatedDate($employee->join_date) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Employee ID</td>
                                            <td>{{ $employee->registration_id }}</td>
                                        </tr>
                                        <tr>
                                            <td>Office Email</td>
                                            <td>
                                                @if ($employee->user_id == null)<code
                                                        class="text-danger">N/A</code>@else{{ $employee->user->email }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Pf Number</td>
                                            <td>
                                                @if ($employee->pf_no == null)<code
                                                        class="text-danger">N/A</code>@else{{ $employee->pf_no }}
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

                <div class="col-md-6">
                    <div class="card card-outline mb-2">
                        <div class="card-header bg-primary text-white">
                            Department Details
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mailbox-messages">
                                <table class="table ">
                                    <tbody>
                                        <tr>
                                            <td>Manager</td>
                                            <td>{{ $employee->department->deptManager->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td>{{ $employee->department->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Designation</td>
                                            <td>{{ $employee->designation->name ?? 'N/A'}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>

                @if ($employee->equipmentAssigned->isNotEmpty())
                <div class="col-md-6">
                    <div class="card card-outline mb-2">
                        <div class="card-header bg-primary text-white">
                            Equipment Details
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive mailbox-messages">
                                <table class="table ">
                                    <tbody>
                                        @foreach($employee->equipmentAssigned as $equipmentAssigned)
                                        <tr>
                                            <td>{{$equipmentAssigned->stockItemDetail->equipment_type }}</td>
                                            <td>{{$equipmentAssigned->stockItemDetail->equipment_label }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
                @endif

                @if ($equipments->isNotEmpty())
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-laptop"></i> Office Equipments
                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Device</th>
                                                <th>Alloted No</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipments as $equipment)
                                                <tr>
                                                    <td>{{ $equipment->entity->name }}</td>
                                                    <td>{{ $equipment->alloted_no }}</td>
                                                    <td>
                                                        @if ($equipment->isWorking == '1')
                                                        Working @else Not Working @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                            @if (count($equipments) == 0)
                                                <tr>
                                                    <td colspan="4" class="text-left"><code class="text-danger">N/A</code>
                                                    </td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                @endif
                
                @if(Auth::user()->can('viewFullProfile', new App\Models\Employee()) || Auth::user()->employee->id == $employee->id)
                @if (!empty($documents))
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-file"></i> Documents
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>Aadhaar Number</td>
                                                <td>{{ $employee->documents->aadhaar_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Aadhaar File</td>
                                                <td>
                                                    @if(!empty($employee->documents->aadhaar_file))
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->aadhaar_file])}}">
                                                    <i class="fa fa-eye text-primary"></i>
                                                </a> 
                                                @else
                                                N/A
                                                @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Pan Number</td>
                                                <td>{{ $employee->documents->pan_number }}</td>
                                            </tr>
                                            <tr>
                                                <td>Pan File</td>
                                                <td>
                                                 @if(!empty($employee->documents->pan_file))
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->pan_file])}}">
                                                <i class="fa fa-eye text-primary"></i></a>
                                                @else
                                                N/A
                                                @endif
                                                 </td>
                                            </tr>
                                            <tr>
                                                <td>CV</td>
                                                <td>
                                                @if(!empty($employee->documents->cv))
                                                <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->documents->cv])}}">
                                                <i class="fa fa-eye text-primary"></i></a>
                                                @else
                                                N/A
                                                @endif
                                                </td>
                                            </tr>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>
                @endif
                
                @if (!empty($employee->bankdetail))
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bank"></i> Bank Details
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>Account Holder</td>
                                                <td>
                                                    @if ($employee->bankdetail->account_holder != null)
                                                        {{ $employee->bankdetail->account_holder }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Bank Name</td>
                                                <td>
                                                    @if ($employee->bankdetail->bank_name != null)
                                                        {{ $employee->bankdetail->bank_name }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Account Number</td>
                                                <td>
                                                    @if ($employee->bankdetail->account_no != null)
                                                        {{ $employee->bankdetail->account_no }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Ifsc Code</td>
                                                <td>
                                                    @if ($employee->bankdetail->ifsc_code != null)
                                                        {{ $employee->bankdetail->ifsc_code }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                @endif
                

                @if (!empty($employee->employeeEmergencyContact))
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bank"></i> Emergency Contact Details
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>Name of the person</td>
                                                <td>
                                                    @if ($employee->employeeEmergencyContact->person_name != null)
                                                        {{ $employee->employeeEmergencyContact->person_name }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Relation with employee</td>
                                                <td>
                                                    @if ($employee->employeeEmergencyContact->person_relation != null)
                                                        {{ $employee->employeeEmergencyContact->person_relation }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Contact number</td>
                                                <td>
                                                    @if ($employee->employeeEmergencyContact->person_contact != null)
                                                        {{ $employee->employeeEmergencyContact->person_contact }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Address</td>
                                                <td>
                                                    @if ($employee->employeeEmergencyContact->person_address != null)
                                                        {{ $employee->employeeEmergencyContact->person_address }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                @endif

                @if (!empty($employee->employeeExitDetail))
                    <div class="col-md-6">
                        <div class="card card-outline mb-2">
                            <div class="card-header bg-primary text-white">
                                <i class="fa fa-bank"></i> Exit Details
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive mailbox-messages">
                                    <table class="table ">
                                        <tbody>
                                            <tr>
                                                <td>Exit Date</td>
                                                <td>
                                                    @if ($employee->employeeExitDetail->exit_date != null)
                                                        {{ getFormatedDate($employee->employeeExitDetail->exit_date) }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Reason</td>
                                                <td>
                                                    @if ($employee->employeeExitDetail->reason != null)
                                                        {{ $employee->employeeExitDetail->reason }}@else <code
                                                            class="text-danger">N/A</code>@endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Status</td>
                                                <td>
                                                    {{$employee->employeeExitDetail->status()}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Experience Letter</td>
                                                <td>
                                                    @if ($employee->employeeExitDetail->experience_file != null)
                                                    <a target="_blank" href="{{route('downloadDocument', ['employee' => $employee->id, 'reference' => $employee->employeeExitDetail->experience_file])}}">
                                                        <i class="fa fa-eye text-primary"></i>
                                                    </a> 
                                                    @else 
                                                        <code class="text-danger">N/A</code>
                                                    @endif
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <!-- /.card -->
                    </div>

                @endif
                @endif

            </div>
        </div>
    </div>
@endsection
