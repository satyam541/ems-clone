@extends('layouts.master')
@section('content')
<style>
    .approve {
        display: none;
    }
</style>

@if($errors->any())
<style>
    .approve {
        display: block;
    }
</style>
@endif

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Interviewee Details</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{route('IntervieweeView')}}">Interviewee</a></li>
                        <li class="breadcrumb-item active">{{$interviewee->name}}</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="col-md-12">
            <div class="card card-primary card-outline">

                <!-- /.card-header -->
                <div class="card-body p-0">

                    <div class="table-responsive mailbox-messages">
                        <table class="table ">
                            <tbody>
                                <tr>

                                    <td>Full Name</td>
                                    <td>{{$interviewee->first_name}} {{$interviewee->middle_name}} {{$interviewee->last_name}}</td>


                                </tr>

                                <tr>

                                    <td>Email</td>
                                    <td>{{$interviewee->email ?? ""}}</td>


                                </tr>
                                <tr>

                                    <td>Phone</td>
                                    <td>{{$interviewee->phone}}</td>


                                </tr>

                                <tr>

                                    <td>Applied on</td>
                                    <td>{{$interviewee->created_at}}</td>


                                </tr>
                                <tr>

                                    <td>Qualification</td>
                                    <td>{{$interviewee->qualification->name}}</td>


                                </tr>
                                <tr>

                                    <td>Department</td>
                                    <td>
                                        @if(empty($departments))
                                        <code class="text-danger">N/A</code>
                                        @else
                                        @foreach($departments as $department)
                                        <code class="btn bg-info">{{$department}}</code>
                                        @endforeach
                                        @endif
                                    </td>


                                </tr>
                                <tr>

                                    <td>Address</td>
                                    <td>{{$interviewee->address}}</td>


                                </tr>
                                <tr>

                                    <td>Comment</td>
                                    <td>@if($interviewee->comment==NULL)<code class="text-danger">N/A</code>@else{{$interviewee->comment}}@endif</td>


                                </tr>
                                <tr>

                                    <td>Referred by</td>
                                    <td>{{$interviewee->referred_by}}</td>


                                </tr>
                                <tr>

                                    <td>Status</td>
                                    <td>@if($interviewee->status=='pending')<code class="bg-warning btn">Pending</code>@elseif($interviewee->status=='approved')<code class="bg-success btn">Approved</code>@else<code class="bg-danger btn">Rejected</code>@endif</td>


                                </tr>

                            </tbody>
                        </table>
                        <!-- /.table -->
                    </div>
                    <!-- /.mail-box-messages -->
                </div>

            </div>

            <div class="card">

                <!-- /.card-header -->

                <div class="card-header bg-success text-white">HR Response</div>
                <div class="card-body">
                    {{Form::model($interviewee,array('route'=>$submitRoute,"files"=>"true"))}}
                    {{Form::hidden('interviewee_id',$interviewee->id)}}
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {{Form::label('status','Status')}}
                                @if($interviewee->status!='approved')
                                {{Form::select('status',$status,$interviewee->status,['class'=>'form-control','required','style'=>'width:100%','onchange'=>'show_hidden(this.value)'])}}
                                @else <b>:</b>
                                <b class="text-success float-right">APPROVED</b>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {{Form::label('response','Response')}}
                                @if($interviewee->status!='approved')
                                {{Form::textarea('response',$interviewee->response,['class'=>'form-control','placeholder'=>'Type Message here...','rows'=>'3'])}}
                                @else
                                <b>:</b>
                                <b class="text-success float-right text-uppercase">
                                {{$interviewee->response}}
                                </b>
                                @endif
                                @error('response')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        @if($interviewee->status=='approved'  && !empty($offer_letter))
                        <div class="col-md-12">
                            <div class="form-group">
                            {{Form::label('offer letter','Offer Letter')}} <b>:</b>
                            <a href='{{route('downloadDocument',['reference'=>$offer_letter->reference])}}' style="width:130px" class="btn form-control float-right btn-success"><i class="fas fa-download"></i> Download</a>
                        </div>
                        </div>
                        @endif 
                        @if($interviewee->status != 'approved')
                        <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('offer_letter','Offer Letter')}}
                             
                               <div style='border:1px solid silver;border-radius:5px;padding:2px'>
                               {{Form::file('offer_letter',null,['class'=>'form-control','required'])}}
                               </div>
                             
                                @error('offer_letter')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('department',' Choose Department')}}
                                {{ Form::select('department',$department_select,null,['class'=>'form-control','style'=>'width:100%', 'placeholder'=>'Select Department'])}}

                                @error('department')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('salary','Salary')}}
                                {{Form::text('salary',null,['class'=>'form-control','onkeyup'=>'if(isNaN(this.value)){this.value=""}','minlength'=>'4','maxlength'=>'8'])}}
                                @error('salary')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div> --}}

                        <div class="col-md-6 approve">
                            <div class="form-group">

                                {{Form::label('designation','Designation')}}
                                {{Form::text('designation',null,['class'=>'form-control','list'=>'designationList','maxlength'=>'50'])}}
                                <datalist id="designationList" >
                                @foreach($designations as $designation)
                                <option value='{{$designation}}'>
                                @endforeach
                                </datalist>
                                @error('designation')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        {{-- <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('birth_date','Birth Date')}}
                                {{Form::date('birth_date',null,['class'=>'form-control','id'=>'year_picker'])}}

                                @error('birth_date')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div> --}}
                        <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('join_date','Joining Date')}}
                                {{Form::date('join_date',null,['class'=>'form-control','id'=>'joindate'])}}

                                @error('join_date')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6 approve">
                            <div class="form-group">
                                {{Form::label('registration_id','Registration Id')}}
                                {{Form::text('registration_id',null,['class'=>'form-control','minlength'=>'2','maxlength'=>'20'])}}

                                @error('registration_id')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                            </div>
                        </div>
                        @error('personal_email')
                                <span class="text-danger">{{$message}}</span>
                                @enderror
                                @can('interviewee',new App\Models\Interviewee)
                        <div class="col-md-12">
                            <div class="form-group">
                                <button class="btn btn-primary after-approve float-right">Update</button>
                            </div>
                        </div>
                        @endcan
                        @endif
                    </div>
                    {{Form::close()}}

                </div>

            </div>
            <!-- /.card -->
        </div>
    </section>
    <script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.2.1.min.js"></script>
{{-- <script type="text/javascript">
 
</script> --}}

    <script>
        function show_hidden(status) {
            if (status == 'approved') {
                $('.approve').show();
            } else {
                $('.approve').hide();
            }
        }
    </script>
    <!-- /.content -->
</div>
@endsection