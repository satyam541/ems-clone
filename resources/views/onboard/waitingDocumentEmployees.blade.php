            {{-- Selected Employees --}}
            <div class="col-md-3 col-sm-6 grid-margin stretch-card" id="waitingDocumentEmployees">
                <div class="card" style="height:fit-content">
                    <div class="card-body">
                        <div class="g-2 g-lg-3 row m-0">
                            <i class="fa fa-ticket"></i>
                            <h4 class="card-title">Waiting on Document
                                ({{ $totalWaitingDocumentEmployees  }})
                            </h4>
                        </div>
                        <ul class="overflow-auto"
                            style="list-style-type: none;min-height:40vh !important; max-height:40vh !important">
                        @foreach ($waitingDocumentEmployees as $waitingDocumentEmployee)
                        <li>
                            <div class="mb-3"
                                style="border-left: 4px solid {{ $waitingDocumentEmployee->priority_color }};">

                                    <div class="comment-text">
                                         <ul
                                            class="todo-list card-inverse-light ui-sortable margin-bottom card-footer">
                                            <span
                                                class="text-muted pull-right">{{ getDateTime($waitingDocumentEmployee->created_at) }}</span>
                                            <span class="d-md-inline-flex" style="color: black">
                                                <strong> {{ $waitingDocumentEmployee->name ?? '' }}
                                                    <a class="btn btn-xs btn-danger"href="{{ route('sendDocumentReminder',['id'=>$waitingDocumentEmployee->id]) }} ">Send Reminder</a></strong>
                                            </span><!-- /.username -->

                                            {{-- <li style="list-style-type: none;">
                                                <span style="color: black">
                                                    <strong>Responsible People:</strong>
                                                </span>{{ implode(', ', $ticketNew->ticketType->responsiblePeople->pluck('name', 'name')->toArray()) ?? '' }}
                                            </li> --}}
                                            {{-- <li style="list-style-type: none;">
                                                <span style="color: black"><strong>Raised
                                                        by:
                                                    </strong>{{ $ticketNew->createdBy->name ?? '' }}</span>
                                            </li> --}}
                                        </ul>
                       </div>

                            </div>
                        </li>
                        @endforeach
                        @if($totalWaitingDocumentEmployees != $waitingDocumentEmployees->count())
                        <i class="btn btn-sm btn-primary" onclick="loadMoreTickets('waitingDocumentEmployees','{{$waitingDocumentEmployees->count()}}')">Load More</i>
                        @endif
                        </ul>
                    </div>
                </div>
            </div>
