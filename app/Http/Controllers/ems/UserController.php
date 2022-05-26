<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Requests\PasswordRequest;
use App\User;
use App\Models\Role;
use Auth;
use Mail;
use Illuminate\Support\Facades\Session;
use \Rap2hpoutre\LaravelLogViewer\LogViewerController;
class UserController extends Controller
{
    public function view()
    {
        $this->authorize('view', new User());
        $data['users']   =     User::with('roles')->get();

        return view('user.user',$data);
    }

    public function editUser(User $user)
    {
        $this->authorize('update', $user);
        $data['user']   = $user;
        $data['roles']  = Role::all();
        $data['userTypes']      =   config('employee.userTypes');
        return view('user.userForm', $data);
    }

    public function updateUser(Request $request, User $user)
    {
        $this->authorize('update', $user);
        $inputs             = $request->all();
        $user->name         = $inputs['name'];
        $user->email        = $inputs['email'];
        $user->user_type    = $inputs['user_type'];
        $user->is_active    = empty($inputs['is_active'])? 0 : 1;
        $user->is_external    = empty($inputs['is_external'])? 0 : 1;
        $user->save();

        if (isset($inputs['resetPwd']))
        {
            $newpassword    = $this::generatePassword();
            $user->password = \Hash::make($newpassword);
            $user->save();
            $user->rawPassword = $newpassword;
            $this->sendPassword($user);
        }
        return redirect()->back()->with('success', 'User Updated');
    }

    public function assignRoles(Request $request)
    {
        $userid     = $request->input('user');
        $user       = User::find($userid);
        $roles      = $request->input('role'); // array of role ids
        if (empty($roles))
        {
            $roles = array();
        }
        $user->roles()->sync($roles);
        return redirect()->back()->with('success', 'Role Assigned Successful');
    }

    public function updatePasswordView()
    {
        $data['user']           = new User();
        $data['submitRoute']    = 'updatePassword';
        return view('user.updatePassword', $data);
    }

    public function updatePassword(PasswordRequest $request)
    {
        $user   = auth()->user();
        if (!password_verify($request->current_password,$user->password))
        {
            $data['current_password'] = "Current Password doesn't Matched!";
            return back()->withErrors($data);
        }

        $hash           = Hash::make($request->password);
        $user->password = $hash;
        $user->save();
        auth::logout();

        return redirect('/login');
    }

    private static function generatePassword():String
    {
        $length         = rand(8,10);
        $alphabet       = '@#-_$%^&+=!1234567890abcdefghijklmnopqrstuvwxyz1234567890@#-_$%^&+=!1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ@#-_$%^&+=!1234567890@#-_$%^&+=!';
        $pass           = array();
        $alphaLength    = strlen($alphabet) - 1;
        for ($i = 0; $i < $length; $i++)
        {
            $n      = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }

        return implode($pass);
    }

    public function sendPassword($user)
    {
        $data['user']   = $user;

        Mail::send('email.password', $data , function($message) use ($user){
            $message->to($user->email, $user->name)->subject('Password Reset');

        });
    }

    public function adminList()
    {
        $admin_ids      = User::havingRole(['admin']);
        $data['admins'] = User::select('id', 'name', 'email', 'is_active')->find($admin_ids);
        return view('user.adminsList', $data);
    }

    public function switchUser()
    {
        // abort_if(!auth()->user()->hasRole('admin'), 404);

        $data['users']=User::where('is_active','1')->pluck('name','id')->toArray();

        return view('user.switchUserList',$data);

    }

    public function loginWithAnotherUser(Request $request)
    {
        // abort_if(!auth()->user()->hasRole('admin'), 404);

        $input  = $request->input();
        $user   = User::find($input['id']);

        if(!empty($user))
        {
            if(! Session::has('orig_user'))
            {
                $request->session()->put('orig_user',Auth::id());
            }

            Auth::loginUsingId($input['id']);

            return redirect(route('dashboard'))->with('success', 'User Account switched Successful');
        }
    }

    public function switchUserLogout(Request $request)
    {
         $user      = $request->session()->get('orig_user');
         $orig_user = User::find($user);

        if(!empty($orig_user))
        {
             Auth::loginUsingId($user);
             $request->session()->forget('orig_user');
        }

         return redirect()->route('dashboard')->with('success', 'User Back to original account.');
    }


    public function laravelLogs()
    {

        if(in_array(strtolower(auth()->user()->email), User::$developers))
        {
        $log    =   new LogViewerController();
        return $log->index();
        }
        return abort(403);
    }
}
