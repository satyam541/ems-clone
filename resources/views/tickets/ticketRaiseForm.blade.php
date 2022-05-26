@extends('layouts.master')
@section('content')

    {{-- changed code --}}
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Ticket Form</li>
                </ol>
            </nav>
        </div>

        <div class="col-12 grid-margin">

            <div class="card">


                <div class="card-body">
                    <h4 class="card-title">Ticket Form</h4>
                    {{Form::open(array('route'=>$submitRoute,'onsubmit'=>"myButton.disabled = true; return true;"))}}
                    {{Form::hidden('id',null)}}

                    <div class="row">

                        @can('powerUser',  new App\User())
                            <div class="col-md-6">
                                <div class="form-group row">
                                    {{Form::label('department','Select Department', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::select('department',$ticketType,null,['class'=>'form-control selectJS','id'=>'ticket-type','required'=>'required','placeholder'=>'Select Department'])}}

                                        @error('department')
                                        <span class="text-danger">{{$message}}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        
                            <div class="col-md-6" id='user'>
                                <div class="form-group row" >
                                    {{Form::label('user_id','User', ['class' => 'col-sm-3 col-form-label']) }}
                                    <div class="col-sm-9">
                                        {{Form::select('user_id',$users,null,['id'=>'user_name','class' => 'form-control selectJS','placeholder'=>'Select User','data-placeholder'=>'Select User'])}}
                                    </div>
                                </div>
                            </div>
                        @endcan

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('category','Category', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::select('category', [],null,['class' => 'form-control selectJS ticket-category','placeholder'=>'Select Category','data-placeholder'=>'Select Category','required'=>'required',])}}
                                </div>
                            </div>
                        </div>

                         <div class="col-md-6 hr-subject">
                            <div class="form-group row">
                                {{Form::label('subject','Subject', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::text('subject',null,['class'=>'form-control','placeholder'=>'Enter Subject'])}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 it-subject" style="display: none;">
                            <div class="form-group row">
                                {{Form::label('subject','Subject', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <select class="form-control selectJS" id="it-subject">
                                        @foreach($itSubjects as $itSubject)
                                        <option value="{{$itSubject}}">{{$itSubject}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                       <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('description','Description', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    <textarea name="description"  rows="3" class="form-control" placeholder="Enter Description" required></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group row">
                                {{Form::label('priority','Select Priority', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::select('priority', $priority,null,['class' => 'form-control selectJS','required'=>'required','placeholder'=>'Select Priority'])}}

                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 anydesk" style="display: none;">
                            <div class="form-group row">
                                {{Form::label('remote_id','AnyDesk :', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::text('remote_id',null,['class'=>'form-control','placeholder'=>'Enter Anydesk Id', 'id'=>'remote_id'])}}
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6" id="barcode" style="display:none;">
                            <div class="form-group row">
                                {{Form::label('barcode','Barcode', ['class' => 'col-sm-3 col-form-label']) }}
                                <div class="col-sm-9">
                                    {{Form::text('barcode',null,['class' => 'form-control','placeholder'=>'Enter Barcode','data-placeholder'=>'Enter Barcode'])}}
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button type="submit" name="myButton" class="btn btn-primary">Submit</button>
                        </div>

                    </div>
                    {{Form::close()}}
                </div>

            </div>



        </div>
    </div>
@endsection

@section('footerScripts')
    <script>
        $('#user').hide();
        $('#it-subject').on('change',function(){
            var subject = $(this).val();

            if(subject == 'Software'){
                $('.anydesk').show();
                $('#remote_id').attr('required',true);
            }
            else{
                $('.anydesk').hide();
                $('#remote_id').attr('required',false);
            }
        });

        $('#ticket-type').on('change',function(){
                var ticket_type = $(this).val();
                if(ticket_type=='IT')
                {
                $('.it-subject').show().find('select').attr('name','subject');
                $('.hr-subject').find("input[type='text']").removeAttr('name');
                $('.hr-subject').hide();
                $('#barcode').show();
                $('#user').show();
                }
                else
                {
                $('.hr-subject').show().find("input[type='text']").attr('name','subject').attr('required',true);
                $('.it-subject').find("select").removeAttr('name');
                $('.it-subject').hide();
                $('#barcode').hide();
                $('#user_name').val('').change();
                $('#user').hide();
                
                }
                $.ajax({
                    url: "{{route('getTicketCategories')}}",
                    type: "GET",
                    data: {"ticket_type":ticket_type},

                    success: function(response){

                        $('.ticket-category').empty();
                        $.each(response,function(index,value){

                            $('.ticket-category').select2({
                                data:[{id:index,text:value}],
                            });
                        });

                    },
                });

        });



    </script>
@endsection
