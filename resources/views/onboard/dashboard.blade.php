@extends('layouts.master')
@section('header')
    <style>
        .hidden-tickets {
            display: none;
        }

    </style>
@endsection
@section('content')

    <div class="row">
        <div class="col-xs-12 " id="error" style="display:none">
            <ul class=" alert alert-danger error">

            </ul>
        </div>
    </div>

    <div class="row mb-4">

            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary"></div>
                    <div class="card-body">
                        <div class="card-title">Filter</div>
                        {{ Form::open(['method' => 'GET']) }}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="responsibleUser">Select Email</label>
                                    {{ Form::text('email', request()->email ?? null, ['class' => 'form-control','data-placeholder' => 'Email','placeholder' => 'Select Email']) }}
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ request()->url() }}" class="btn btn-success">Clear</a>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>

    </div>
    {{-- <div class="row">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">Filter</div>
                        {{ Form::open(['method' => 'GET']) }}
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="responsibleUser">Select Email</label>
                                    {{ Form::text('email', request()->email ?? null, ['class' => 'form-control','data-placeholder' => 'Email','placeholder' => 'Select Email']) }}
                                </div>
                            </div>




                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary me-2">Filter</button>
                                    <a href="{{ request()->url() }}" class="btn btn-light">Clear</a>
                                </div>
                            </div>
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <div class="col-lg-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Ticket Chart</h4>
                <canvas id="chart1" width="200" height="100"></canvas>
            </div>
        </div>
    </div> --}}
    <div class="row" id="employees">

    </div>
@endsection


@section('footerScripts')
    <!-- TrustBox script -->
    <script type="text/javascript" src="//widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js" async></script>
    <!-- End TrustBox script -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $('#eventDateRange-btn').daterangepicker({

                opens: 'left',
                locale: {
                    cancelLabel: 'Clear'
                },
                ranges: {
                    'Today': [moment(), moment()],
                    'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
                    'Next 5 Days': [moment(), moment().add(4, 'days')],
                    'Next 14 Days': [moment(), moment().add(13, 'days')],
                    'Next 30 Days': [moment(), moment().add(29, 'days')],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Next Month': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function(start, end) {
                $('#eventDateRange-btn span').html(start.format('D/ M/ YY') + ' - ' + end.format('D/ M/ YY'))
                $('#dateFrom').val(start.format('YYYY-M-DD'));
                $('#dateTo').val(end.format('YYYY-M-DD'));
            }
        );
        $('#eventDateRange-btn').on('cancel.daterangepicker', function(ev, picker) {
            clearDateFilters('eventDateRange-btn', 'event');
        });

        $(window).on('load',function(){
            $.ajax({
                url:"{{route('loadEmployees',http_build_query(request()->query()))}}",
                method:'GET',
                success:function(response)
                {
                    console.log(response);
                    $.each(response,function(type,tickets){
                        $('#employees').append(tickets);
                    });
                }
            });
        });
        function loadMoreTickets(requestType,count)
        {
                count   =  eval(parseInt(count)+3);
            $.ajax({
                url:"{{route('loadEmployees',http_build_query(request()->query()))}}",
                method:'GET',
                data:{employee_load:requestType,count:count},
                success:function(response)
                {
                    $('#'+requestType).replaceWith(response);
                }
            });
        }

        function openModal(id,type)
        {
            $('#modal-default').modal('show');
            $('#chart-heading').html(type);
            $.ajax({

                url:"{{route('onboardStatusUpdateForm',http_build_query(request()->query()))}}",
                method:'GET',
                data:{id:id,type:type},
                success:function(response)
                {
                    console.log(response);
                    $('#modal-data').html(response);
                    $('.selectJS').select2({
                    placeholder: "Select an option",
                    allowClear: false,
                    width: '93%'
                    });
                }
            });
        }
    </script>
@endsection
