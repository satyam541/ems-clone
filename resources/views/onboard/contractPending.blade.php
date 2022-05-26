            {{-- Selected Employees --}}
            <div class="col-md-3 col-sm-6 grid-margin stretch-card" id="contractPending">
                <div class="card" style="height:fit-content">
                    <div class="card-body">
                        <div class="g-2 g-lg-3 row m-0">
                            <i class="fa fa-ticket"></i>
                            <h4 class="card-title">Contract Pending
                                ({{ $totalContractPendingEmployees }})
                            </h4>
                        </div>
                        <ul class="overflow-auto"
                            style="list-style-type: none;min-height:40vh !important; max-height:40vh !important">
                            @foreach ($contractPendings as $contractPending)
                                <li>
                                    <div class="mb-3"
                                        style="border-left: 4px solid;">

                                        <div class="comment-text">
                                            <ul
                                                class="todo-list card-inverse-light ui-sortable margin-bottom card-footer">
                                                <span
                                                    class="text-muted pull-right">{{ getDateTime($contractPending->created_at) }}</span>
                                                <span class="d-md-inline-flex" style="color: black">
                                                    <strong> {{ $contractPending->name}}
                                                        <a target="_blank" href="{{ route('onboardForm',['id'=>$contractPending->id])}}"><span class="fa fa-edit m-1"></span></a></strong>
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
                            @if ($totalContractPendingEmployees != $contractPendings->count())
                                <i class="btn btn-sm btn-primary"
                                    onclick="loadMoreTickets('contractPending','{{ $contractPendings->count() }}')">Load
                                    More</i>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
