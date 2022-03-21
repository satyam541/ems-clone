@extends('layouts.master')
@section('content')
       
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="float-right">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Interviewee List</li>
                </ol>
            </nav>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <p class="card-title">Interviewee List</p>

                    <div id="jsGrid"></div>

                </div>
            </div>
        </div>
    </div>



@endsection


@section('footerScripts')
    <script>
        var intervieweeDetailUrl = "{{ route('intervieweeDetail', ['interviewee' => ':id']) }}";
        var intervieweeDeleteUrl = "{{ route('intervieweeDelete', ['interviewee' => ':id']) }}";
        var status = '{!! $status !!}';
        var qualifications = '{!! $qualifications !!}';
        var myFields = [

            {

                title: "name",
                name: "first_name",
                type: "text",
                width: 200

            },

            {
                title: "Personal Email",
                name: "email",
                type: "text",
                width: 300

            },
            {
                title: "Status",
                name: "status",
                type: "select",
                items: JSON.parse(status),
                valueType: "number|string",
                width: 150

            },
            {
                title: "Qualification",
                name: "qualification.name",
                type: "select",
                width:200,
                items: JSON.parse(qualifications),
                valueType: "number|string",


            },
            {
                title: "Applied on",
                name: "created_at",
                type: "text",
                filtering: false,
                width:200
            },
            {
                title: "Phone No",
                name: "phone",
                type: "text",
                filtering: false
            },


            {
                title: "Detail",
                width: 100,
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
            @can('interviewee', new App\Models\Interviewee())
                {
                title: "CV",
                width: 100,
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
                @endcan
            @can('interviewee', new App\Models\Interviewee())
                {
                type: "control",
                editButton: false,
                deleteButton:true,
                title: "Delete",
                width: 100,
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
                    @endcan

        ];


        var intervieweeURL = '{{ route('intervieweeList') }}';
        var response;
        $("#jsGrid").jsGrid({
            fields: myFields,
            width: "100%",
            autoload: true,
            filtering: true,
            paging: true,
            pageSize: 10,
            pagerFormat: "Pages: {first} {prev} {pages}     {pageIndex} of {pageCount}",
            pageLoading: true,
            controller: {
                loadData: function(filter) {
                    return response = $.ajax({
                        type: "GET",
                        dataType: "json",
                        url: intervieweeURL,
                        data: filter,
                    });

                },
                deleteItem: function(item) {
                    deleteInterviewee = intervieweeDeleteUrl.replace(':id', item['id']);
                    return $.ajax({
                        type: "DELETE",
                        url: deleteInterviewee,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: item
                    });
                },


            },
            onItemDeleted: function(args) {

                toastr.success('Record Deleted Successfully')

            },

        });



        function preview(resume) {
            var url = '{{ url('interviewee/download') }}/' + resume;
            window.open(url, '_blank');
        }
    </script>

@endsection
