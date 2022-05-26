<?php

namespace App\Http\Controllers\ems;

use App\User;
use Carbon\Carbon;
use App\Mail\Action;
use App\Models\Employee;
use App\Models\Interview;
use Illuminate\Mail\Mailer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // protected $today;
    // public $mailer;
    // public function __construct(Mailer $mailer)
    // {
    //     ini_set('max_execution_time', 300);
    //     $this->today  = Carbon::today();
    //     $this->mailer = $mailer;
    // }


    public function index()
    {
        $this->authorize('view', new Interview());
        $data['interviews']  =  Interview::with('addedBy')->get();

        return view('interview.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $interview              =  new Interview();
        $this->authorize('create', $interview);
        $data['interview']      =  $interview;
        $data['submitRoute']    =   ['interview.store'];
        $data['method']         =   'POST';
        return view('interview.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $interview                  =       new Interview();
        $this->authorize('create', $interview);
        $interview->name            =       $request->name;
        $interview->email           =       $request->email;
        $interview->added_by        =       auth()->user()->id;
        $interview->save();
        $user                       = User::firstWhere('email', request()->email);

        if (!empty($user)) {

            return back()->with('failure', 'Email already exists');
        }
        $this->createCredentials($interview);
        return redirect()->route('interview.index')->with('success', 'interview created');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $this->authorize('update', new Interview());
        $data['interview']      =  Interview::findOrFail($id);
        $data['submitRoute']    =   ['interview.update', ['interview' => $id]];
        $data['method']         =   'PUT';
        // $data['status']         =  ['0' => 'Rejected', '1' => 'Selected'];
        return view('interview.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', new Interview());
        $interview                  =       Interview::findOrFail($id);
        $interview->name            =       $request->name;
        $interview->email           =       $request->email;
        $interview->added_by         =      auth()->user()->id;
        $interview->save();
        $interview->is_selected     =       $request->is_selected;
        if (array_key_exists('is_selected', $interview->getDirty()) && $interview->is_selected == 1) {

            $user = User::firstWhere('email', request()->email);
            if (!empty($user)) {

                return back()->with('failure', 'Email already exists');
            }
            $this->createCredentials($interview);
        }
        if ($request->has('cv')) {
            $interview->cv          =   $this->uploadDocuments($request, 'cv', $interview->id, $interview->cv);
        }
        $interview->save();
        return redirect()->route('interview.index')->with('success', 'interview updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function createCredentials($interview)
    {
        $this->authorize('createCredentials', new Interview());
        // $user = User::firstWhere('email', request()->email);
        // if (!empty($user)) {

        //     return back()->with('failure', 'Email already exists');
        // }
        $user                               =    new User();
        $user->name                         =    $interview->name;
        $user->email                        =    strtolower($interview->email);
        $password                           =    $this::generatePassword();
        $user->password                     =   \Hash::make($password);
        $user->is_active                    =    "1";
        $user->save();
        $user->rawPassword                  =    $password;
        $employee                                   =   new Employee();
        $employee->name                             =   $user->name;
        $employee->user_id                          =   $user->id;
        $employee->office_email                     =   $user->email;
        $employee->onboard_status                   =   'asked for documents';
        $employee->is_active                        =   1;
        $employee->save();
        $this->sendPassword($user);
    }
    private function uploadDocuments($request, $fileName, $id, $old_file = null)
    {
        $this->authorize('create', new Interview());
        if (!empty($old_file)) {
            if (\Storage::exists("documents/interview/$id/$old_file")) {
                \Storage::delete("documents/interview/$id/$old_file");
            }
        }
        $file = $fileName . Carbon::now()->timestamp . '.' . $request->file($fileName)->getClientOriginalExtension();
        $request->file($fileName)->move(storage_path('app/documents/interview/' . $id), $file);
        return $file;
    }


    private static function generatePassword(): String
    {
        $length         = rand(8, 10);
        $alphabet       = '1234567890abcdefghijklmnopqrstuvwxyz12345678901234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass           = array();
        $alphaLength    = strlen($alphabet) - 1;

        for ($i = 0; $i < $length; $i++) {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public function sendPassword($user)
    {
        $data['user']           = $user;
        $data['link']           = route('onboardForm',['id'=>$user->employee->id]);
        $data['subject']        =  'Documents Fill';
        Mail::send('email.employeeSelected', $data, function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject('Documents Fill');
        });
    }
}
