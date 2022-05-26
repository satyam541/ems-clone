            {{-- Selected Employees --}}
            <div class="col-md-3 col-sm-6 grid-margin stretch-card" id="askedForDocuments">
                <div class="card" style="height:fit-content">
                    <div class="card-body">
                        <div class="g-2 g-lg-3 row m-0">
                            <i class="fa fa-ticket"></i>
                            <h4 class="card-title">Asked For Documents
                                ({{ $totalAskedForDocuments }})
                            </h4>
                        </div>
                        <ul class="overflow-auto"
                            style="list-style-type: none;min-height:40vh !important; max-height:40vh !important">
                            @if($askedForDocuments->isNotEmpty())
                            @foreach ($askedForDocuments as $askedForDocument)
                                <li>
                                    <div class="mb-3"
                                        style="border-left: 4px solid">

                                        <div class="comment-text">
                                            <ul
                                                class="todo-list card-inverse-light ui-sortable margin-bottom card-footer">
                                                <span
                                                    class="text-muted pull-right">{{ getDateTime($askedForDocument->created_at) }}</span>
                                                    <span class="d-md-inline-flex" style="color: black">
                                                        <strong> {{ $askedForDocument->name}}
                                                            <a class="btn btn-xs btn-danger"href="{{ route('sendDocumentReminder',['id'=>$askedForDocument->id]) }} ">Send Reminder</a></strong>
                                                    </span>
                                            </ul>
                                        </div>

                                    </div>
                                </li>
                            @endforeach
                            @if ($totalAskedForDocuments != $askedForDocuments->count())
                                <i class="btn btn-sm btn-primary"
                                    onclick="loadMoreTickets('askedForDocuments','{{ $askedForDocuments->count() }}')">Load More</i>
                            @endif
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
