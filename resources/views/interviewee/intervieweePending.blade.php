@extends('layouts.master')
@section('content')

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">

            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Interviewee Pending List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{url('dashboard')}}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Interviewee Pending List</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->


            <div class="row">
                <div class="col-12">
                    <div class="card">

                        <!-- /.card-header -->

                        <div class="card-body">
                            <div id="example2_wrapper" class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12 col-md-6"></div>
                                    <div class="col-sm-12 col-md-6"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">

                                        <div id="jsGrid"></div>

                                    </div>
                                </div>

                                <div class="col-sm-12 col-md-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- /.card -->


<!-- /.col -->

<!-- /.row -->


@endsection


@section('footerScripts')
<script>
    var intervieweeDetailUrl = "{{ route('intervieweeDetail', ['interviewee' => ':id'])}}";
    var intervieweeDeleteUrl = "{{ route('intervieweeDelete', ['interviewee' => ':id'])}}";
    var qualifications = '{!!$qualifications!!}';
    qualifications = JSON.parse(qualifications);
    var myFields = [
        
        {

            title: "name",
            name: "first_name",
            type: "text",
            width: 30

        },

        {
            title: "email",
            name: "email",
            type: "text",
            width: 40

        },
        {
            title: "Qualification",
            name: 'qualification_id',
            type: "text",
            width: 30,
            filtering: false,
            itemTemplate: function(value) {
                var qualification = $('<span>')
                $.each(qualifications, function(index, name){
                    if(value == index)
                    {
                        qualification.text(name);
                        // return;
                    }
                });
                return qualification;
            },

        },
        {
        title: "Applied on",
        name: "created_at",
        type: "text",
        width: 30,
        filtering: false
        },
        {
        title: "Phone No",
        name: "phone",
        type: "text",
        width: 30,
        filtering: false
        },


        {
            title: "Detail",
            width: 25,
            filtering:false,
            itemTemplate: function(value, item) {

                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);


                var $customEditButton = $("<a>")
                    .attr('href', intervieweeDetailUrl.replace(':id', item.id))
                    .attr({
                        class: 'btn btn-warning'
                    }).html("Detail")
                    .click(function(e) {
                        e.stopPropagation();
                    })


                return $result.add($customEditButton);
            }

        },
        {
            title: "CV",
            width: 20,
            filtering: false,
            itemTemplate: function(value, item) {

                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);
                var $customEditButton = $("<a>")
                    .attr({
                        class: 'fas fa-file-download fa-2x text-danger'
                    }).html("")
                    .click(function(e) {
                        preview(item.resume);
                        e.stopPropagation();
                    })


                return $result.add($customEditButton);
            }

        },
        {
            type: "control",
            editButton: false,
            deleteButton:false,
            title: "Delete",
            filtering:false,
            width: 40,
            itemTemplate: function(value, item) {

                var $result = jsGrid.fields.control.prototype.itemTemplate.apply(this, arguments);

                //var $iconPencil = $("<i>").attr({class: "fa fa-edit align-top pl-2"});
                var $customEditButton = $("<a>")
                    .attr('href', intervieweeDeleteUrl.replace(':id', item.id))
                    .attr({
                        title: 'Assign Permissions'
                    })
                    .click(function(e) {

                        e.stopPropagation();
                    })

                return $result.add($customEditButton);
            }

        },

    ];


    var intervieweeURL = '{{route("intervieweePendingList",["status"=>"pending"])}}';
    var response;
    $("#jsGrid").jsGrid({
        fields: myFields,
        width: "100%",
        autoload: true,
        filtering: true,
        paging: true,
        pageSize: 10,
        sorter:'string',
        pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
        pageLoading: true,
        controller: {
            loadData: function(filter) {
                return response= $.ajax({
                    type: "GET",
                    dataType: "json",
                    url: intervieweeURL,
                    data: filter,
                });                
            },
            // deleteItem: function(item) {
            //     deleteInterviewee = intervieweeDeleteUrl.replace(':id', item['id']);
            //     return $.ajax({
            //         type: "DELETE",
            //         url: deleteInterviewee,
            //         headers: {
            //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //         },
            //         data: item
            //     });
            // },


        },
        onItemDeleted: function(args) {

            toastr.success('Record Deleted Successfully')

        },

    });

    

    function preview(resume) {
        var url = '{{url("interviewee/download")}}/' + resume;
        window.open(url, '_blank');
    }
</script>

@endsection

