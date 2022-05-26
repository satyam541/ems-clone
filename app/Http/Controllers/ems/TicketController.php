<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Ticket;
use App\Models\Employee;
use App\Models\Software;
// use App\Models\ItemRequestAssign;
use App\Models\TicketLog;
use Illuminate\Http\Request;
use App\Models\TicketCategory;
use App\Http\Controllers\Controller;
use App\Http\Requests\TicketRequest;
use Illuminate\Contracts\Mail\Mailer;

class TicketController extends Controller
{
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function ticketRaiseForm()
    {
        if (auth()->user()->hasRole('powerUser')) 
        {
            $data['ticketType']         = TicketCategory::orderBy('name')->pluck('type','type')->unique()->toArray();
            $data['ticketCategories']   = TicketCategory::orderBy('name')->pluck('name', 'name')->toArray();
            $data['itSubjects']         = ['Hardware' => 'Hardware', 'Software' => 'Software', 'Others' => 'Others'];
            $data['priority']           = ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'];
            $data['submitRoute']        = 'ticketRaise';
            $data['users']              = User::whereHas('employee', function ($employee) {
                $employee->where('department_id', auth()->user()->employee->department_id);
            })->pluck('name', 'id')->toArray();

            return view('tickets.ticketRaiseForm', $data);
        }
        else
        {
            $data['ticketCategories']   = TicketCategory::orderBy('name')->where('type','HR')->pluck('name', 'name')->toArray();
            $data['priority']           = ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'];
            $data['submitRoute']        = 'ticketRaise';

            return view('tickets.employeeTicketForm', $data);
        }
    }

    public function getTicketCategories(Request $request)
    {
        $data['ticketCategories']       = TicketCategory::orderBy('name')->where('type', $request->ticket_type)->pluck('name', 'name')->toArray();
        
        return $data['ticketCategories'];
    }

    public function ticketRaise(TicketRequest $request)
    {
        if (auth()->user()->hasRole('powerUser')) 
        {
            $category                   = TicketCategory::where('type', $request->department)->where('name', $request->category)->first();
        }
        else{
            $category                   = TicketCategory::where('type', 'HR')->where('name', $request->category)->first();
        }
        $ticket                         = new Ticket();
        $ticket->ticket_category_id     = $category->id;
        $ticket->subject                = $request->subject;
        $ticket->description            = $request->description;
        $ticket->remote_id              = $request->remote_id;
        $ticket->priority               = $request->priority;
        $ticket->barcode                = $request->barcode;
        $ticket->user_id                = !empty($request->user_id) ? $request->user_id : auth()->user()->id;
        $ticket->raised_by              = auth()->user()->id;
        $ticket->save();    

        $user_ids                       = User::havingRole('ticketManager');
 
        $notificationReceivers          = [];
        $email                          = [];

        if ($category->type == 'IT') 
        {
            $users                      = User::whereIn('id', $user_ids)->whereHas('employee.department', function ($query) 
                                            {
                                                $query->where('name', 'IT');
                                            })->get();
        }

        if ($category->type == 'HR') 
        {
            $users                      = User::whereIn('id', $user_ids)->whereHas('employee.department', function ($query) 
                                            {
                                                $query->where('name', 'HR');
                                            })->get()->where('email', '<>', 'martha.folkes@theknowledgeacademy.com');
        }
        if ($users->isNotEmpty()) 
        {
            $notificationReceivers      = $users->pluck('id', 'id')->toArray();

            $email                      = $users->pluck('email', 'email')->toArray();
        }   

        $subject                        = "Ticket No. " . $ticket->id . " Raised by " . auth()->user()->name;
        $link                           = route('itRaiseTicket');
        send_notification($notificationReceivers, $subject, $link);

        $data['ticket']                 = $ticket;
        $data['ticketLog']              = null;
        $data['link']                   = $link;
        $data['message']                = $subject;
        $data['users']                  = $users;
        $message                        = $subject;
        $message                        = (new Action($users, $data, $subject, 'email.ticket'))->onQueue('emails');

        $this->mailer->to($email)->later(Carbon::now(), $message);

        return redirect()->back()->with('success', 'Ticket Raised Successfully');
    }

    public function myTickets()
    {

        $data['tickets']                = Ticket::with('actionBy', 'ticketCategory', 'raisedBy')->
                                          where('user_id', auth()->user()->id)->orderByRaw("Field(status,'Pending','Assigned','Sorted', 'Closed')")
                                          ->orderBy('id', 'desc')->get();

        return view('tickets.myTickets', $data);
    }

    public function itRaiseTicket(Request $request)
    {
        $this->authorize('ticketView', new Ticket());
        $data['tickets']                = Ticket::with('user.employee.department', 'ticketCategory', 'ticketLogs')
                                          ->whereNotIn('status', ['Sorted', 'Closed'])->whereHas('user', function ($query) {
                                              $query->where('is_active', '1');
                                          })->orderByRaw("Field(status,'Pending','Reopen','Assigned','Forward')");

        $data['ticketTypes']            =  TicketCategory::pluck('type', 'type')->unique()->toArray();
        $data['ticketCategory']         =  TicketCategory::pluck('name', 'name')->toArray();

        $data['priority']               = ['Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'];
        // $data['employees']              = Employee::where('user_id', '<>', auth()->user()->id)->with('department')->wherehas('user.roles', function ($query) {
        //     $query->where('name', 'ticketSolver');
        // })->get();

        $data['users']              = User::where('id', '<>', auth()->user()->id)->where('user_type','Employee')->with('employee.department')->wherehas('roles', function ($query) {
            $query->where('name', 'ticketSolver');
        })->get();

        if (auth()->user()->hasRole('IT')) {
            $data['tickets']    = $data['tickets']->wherehas('ticketCategory', function ($query) {
                $query->where('type', 'IT');
            });
        }

        if (auth()->user()->hasRole('HR')) {
            $data['tickets']    = $data['tickets']->wherehas('ticketCategory', function ($query) {
                $query->where('type', 'HR');
            });
        }

        if (!empty($request->ticket_category_id)) {
            $data['tickets']     = $data['tickets']->wherehas('ticketCategory', function ($query) {
                $query->where('type', request()->ticket_category_id);
            });
        }

        if (!empty($request->priority)) {
            $data['tickets']    = $data['tickets']->where('priority', $request->priority);
        }
        if (!empty($request->dateFrom) && ($request->dateTo)) {
            $data['tickets']->whereBetween('created_at', [$request->dateTo, $request->dateFrom]);
        }

        $data['tickets']    = $data['tickets']->paginate(10);
        return view('tickets.raiseTicketList', $data);
    }

    public function ticketHistory()
    {
        $this->authorize('ticketHistory', new Ticket());
        $data['tickets'] = Ticket::with('user', 'ticketLogs.actionBy')->whereIn('status', ['Closed', 'Sorted'])->orderBy('id', 'desc')->get();
        // dd($data['tickets']->first() );
        return view('tickets.ticketHistory', $data);
    }

    public function raiseTicketAction(Request $request)
    {
        $ticket            = Ticket::find($request->id);
        $ticket->status    = $request->action;
        $ticket->save();

        $ticketLog               = new TicketLog();
        $ticketLog->ticket_id    = $ticket->id;
        $ticketLog->action       = $request->action;
        $ticketLog->assigned_to  = $request->assigned_to ?? null;
        $ticketLog->remarks      = $request->remarks ?? null;
        $ticketLog->action_by    = auth()->user()->id;
        $ticketLog->save();
        switch ($request->action) {
            case ($request->action == 'Assigned' || $request->action == 'Forward'):
               
                $notificationReceivers  = [$ticketLog->assignedTo->id];
                $email                  =   [$ticketLog->assignedTo->email];
                $link                   = route('assignedTicket');
                break;

            case 'Sorted':
                $notificationReceivers  = [$ticket->user_id];
                $email                  = [$ticket->user->email];
                $link                   = route('myTickets');
                break;
                

            case 'Reopen':
                $user_ids       = User::havingRole('ticketManager');
                if ($ticket->ticketCategory->type == 'IT') {
                    $users = User::whereIn('id', $user_ids)->whereHas('employee.department', function ($query) {
                        $query->where('name', 'IT');
                    })->get();
                } else {
                    $users = User::whereIn('id', $user_ids)->whereHas('employee.department', function ($query) {
                        $query->where('name', 'HR');
                    })->get()->where('email', '<>', 'martha.folkes@theknowledgeacademy.com');
                }
                if ($users->isNotEmpty()) {
                    $notificationReceivers  = $users->pluck('id', 'id')->toArray();

                    $email  = $users->pluck('email', 'email')->toArray();
                }
                $link   = route('itRaiseTicket');
                break;
        }

        $subject        = "Ticket No. " . $ticket->id . " " . $request->action . " by " . auth()->user()->name;
        // $email  = $employees->pluck('office_email','office_email')->toArray();
        if ($request->action != 'Closed') {
            $data['ticket']     = $ticket;
            $data['ticketLog']  = $ticketLog;
            $data['link']       = $link;
            $message            = $subject;
            $data['message'] = $subject;
            send_notification($notificationReceivers, $subject, $link);
            // send_email("email.ticket", $data, $subject, $message,$email,null);
   
            $message = (new Action(null, $data, $subject, 'email.ticket'))->onQueue('emails');
            $this->mailer->to($email)->later(Carbon::now(), $message);
        }
        return back()->with('success', 'Action Performed');
    }

    public function assignedTickets()
    {
        $data['assignedTickets']  =  Ticket::with('ticketLogs.actionBy', 'user.employee.department')->whereHas('ticketLogs', function ($query) {
            $query->where('assigned_to', auth()->user()->id);
        })->get();
        
        return view('tickets.assignedTickets', $data);
    }

    public function ticketDetail(Request $request, $id)
    {
        // $data['ticketDetail']   = Ticket::with('ticketLogs.actionBy','ticketLogs.assignedTo','user','ticketCategory')->find($id);
        $ticketDetail           = Ticket::with('ticketLogs.actionBy', 'ticketLogs.assignedTo', 'user', 'ticketCategory');
        $data['ticketLogs']     = TicketLog::with('actionBy', 'assignedTo')->where('ticket_id', $id)->orderBy('created_at', 'desc')->get();
        $data['status']         = array('Sorted' => 'Sorted', 'Forward' => 'Forward');
        $data['users']          = User::where('id', '<>', auth()->user()->id)->with('employee.department')->wherehas('roles', function ($query) {
            $query->where('name', 'ticketSolver');
        })->get();
        if (auth()->user()->hasRole('powerUser')) {
            $data['ticketDetail']   = $ticketDetail->whereHas('user.employee', function ($employee) {
                $employee->where('department_id', auth()->user()->employee->department_id);
            })->find($id);
        } 
        elseif(auth()->user()->hasRole('ticketSolver'))
        {
            $data['ticketDetail']   = $ticketDetail->find($id);;
        }
        else
        {
            $data['ticketDetail']   = $ticketDetail->where('user_id', auth()->user()->id)->find($id);
        }

        if (empty($data['ticketDetail'])) {
            abort(404);
        }

        return view('tickets.ticketDetail', $data);
    }

    public function cancelEquipmentProblem(Request $request)
    {
        Ticket::find($request->id)->update(['status' => 'Closed']);
    }

    public function sendReminder()
    {
        $pendingTickets = Ticket::where('status', 'Pending')->get();

        if ($pendingTickets->isNotEmpty()) {
            $user_ids               = User::havingRole('IT');
            $link                   = route('itRaiseTicket');
            $data['pendingTickets'] = $pendingTickets;
            $data['link']           = $link;
            $subject                = "Pending Tickets";
            $message                = "Pending Tickets";
            $email                  = User::find($user_ids)->pluck('email')->toArray();
            send_email("email.pendingTickets", $data, $subject, $message, $email, null);
        }

        return "done";
    }
    //ticket category
    public function categoryView()
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403);
        }
        return view('tickets.ticketCategoryList');
    }

    public function categoryList(Request $request)
    {

        $pageIndex      = $request->pageIndex;
        $pageSize       = $request->pageSize;
        $ticketCategory = TicketCategory::query();
        if (!empty($request->get('name'))) {
            $ticketCategory = $ticketCategory->where('name', 'like', '%' . $request->get('name') . '%');
        }
        if (!empty($request->get('type'))) {
            $ticketCategory = $ticketCategory->where('type', 'like', '%' . $request->get('type') . '%');
        }
        $data['itemsCount'] = $ticketCategory->count();
        $data['data']       = $ticketCategory->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
        return json_encode($data);
    }

    public function categoryInsert(Request $request)
    {
        $data       = new TicketCategory();
        $data->name = $request->name;
        $data->type = $request->type;
        $data->save();
        return json_encode($data);
    }

    public function categoryUpdate(Request $request)
    {
        $ticketCategory     =     $request->id;
        $data                 =     TicketCategory::find($ticketCategory);
        $data->name         =     $request->name;
        $data->type         =     $request->type;
        $data->save();
        return json_encode($data);
    }

    public function categoryDelete(Request $request)
    {
        $data     =     TicketCategory::find($request->id);
        $data->delete();
        return json_encode("done");
    }

    public function departmentTickets()
    {
        $this->authorize('powerUser', new User());
        $tickets    = Ticket::with('user', 'ticketCategory')->whereHas('ticketCategory', function ($query) {
            $query->where('type', 'IT');
        })->whereNotIn('status', ['Sorted', 'Closed'])->whereHas('user.employee', function ($departments) {
            $departments->where('department_id', auth()->user()->employee->department_id);
        });
        $data['tickets']    =   $tickets->get();

        return view('tickets.departmentTicket', $data);
    }
}
