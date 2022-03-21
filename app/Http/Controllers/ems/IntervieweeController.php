<?php

namespace App\Http\Controllers\ems;

use App\Http\Controllers\Controller;
use App\Http\Requests\IntervieweeApprovedRequest;
use Illuminate\Http\Request;
use App\Models\Interviewee;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use App\Http\Requests\IntervieweeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Qualification;
use App\Models\Designation;
use App\Models\Document;
use App\User;
use Auth;
use Str;
use FileVault;

class IntervieweeController extends Controller
{
	public function generateLink(Request $request)
	{

		$email                =   $request->email;
		$interviewee          =   Interviewee::where('email', $email)->first();
		if (empty($interviewee)) {
			$formlink           =   URL::temporarySignedRoute('jobApplicationForm', now()->addDay(), ['email' => $request->email]);
			$subject            =   "Registration For New Employee";
			$data['formlink']   =   $formlink;
			$message            =   "";
			$emailSent          =   send_email("email.registered", $data, $subject, $message, array($email), null);
			$interviewee        =   new Interviewee();
			$interviewee->email =   $email;
			$interviewee->save();
			if ($emailSent) {
				return back()->with('success', "Email With Link Sent");
			} else {
				return back()->withErrors('Unable to Send Email. Please Check Email and Try Again');
			}
		} else {
			return back()->withErrors('Interviewee already registered with this email id');
		}
	}

	public function view(Request $request)
	{
		if (!$request->hasValidSignature()) {
			return view('error.410');
		}
		$interviewee = Interviewee::where('email', $request->email)->first();

		if (!empty($interviewee->first_name)) {
			return view('error.responseSaved');
		}
		$data['interviewee'] = $interviewee;
		$data['submitRoute'] = 'insertJobApplication';
		$data['email']       = $interviewee->email;
		$data['qualification'] = Qualification::all()->pluck('name', 'id')->toArray();
		$data['department'] = Department::all()->pluck('name', 'name')->toArray();
		return view('interviewee.intervieweeForm', $data);
	}

	public function insert(IntervieweeRequest $request)
	{

		$interviewee                    =     Interviewee::where('email', $request->email)->first();
		$interviewee->first_name        =     $request->first_name;
		$interviewee->middle_name       =     $request->middle_name;
		$interviewee->last_name         =     $request->last_name;
		$interviewee->phone             =     $request->phone;
		$interviewee->address           =     $request->address;
		$interviewee->qualification_id  =     $request->qualification_id;
		$interviewee->interested_in     =     json_encode($request->interested_in);
		$interviewee->referred_by       =     $request->referred_by;
		$interviewee->comment           =     $request->comment;
		$filename                       =     Storage::putFile('document', $request->resume);
		FileVault::encrypt($filename);
		$filename                       =     str_replace('document/', '', $filename);
		$interviewee->resume            =     $filename;
		$interviewee->save();
		//send notification to hrs
		$users = User::havingRole('HR');
		$message = 'New interviewee Registered';
		$route = ['name' => 'intervieweeDetail', 'parameter' => ["interviewee" => $interviewee->id]];
		send_notification($users, $message, $route);
		$email = User::whereIn('id', $users)->pluck('email')->toArray();
		$subject = "New Interviewee Registered";
		$message = $interviewee->name . "is registered";
		$data['interviewee'] = $interviewee;
		send_email("email.intervieweeRegistered", $data, $subject, $message, $email, null);
		return redirect()->route('errorResponse');
	}

	public function viewInterviewee()
	{
		$this->authorize('interviewee', new Interviewee());
		$data['status']            = json_encode(['all' => 'all', 'pending' => 'pending', 'approved' => 'approved', 'rejected' => 'rejected']);
		$qualifications            = Qualification::all()->pluck('name', 'name')->toArray();
		$qualifications            =  ['0' => 'All'] + $qualifications;
		$data['qualifications']    = json_encode($qualifications, JSON_HEX_APOS);
		return view('interviewee.interviewee', $data);
	}
	public function pendingList()
	{
		$qualifications = Qualification::all()->pluck('name', 'id')->toArray();
		$data['qualifications']    = json_encode($qualifications, JSON_HEX_APOS);
		return view('interviewee.intervieweePending', $data);
	}
	public function list(Request $request)
	{



		$pageIndex =  $request->pageIndex;
		$pageSize = $request->pageSize;
		$interviewee = Interviewee::query()->with('qualification');

		if (!empty($request->first_name)) {
			$interviewee = $interviewee->where('first_name', 'like', '%' . $request->first_name . '%');
		}
		if (!empty($request->email)) {

			$interviewee = $interviewee->where('email', 'like', '%' . $request->email . '%');
		}
		if ($request->status != 'all') {
			$interviewee = $interviewee->where('status', $request->status);
		}

		$data['itemsCount'] = $interviewee->count();
		$data['data'] = $interviewee->orderBy('created_at', 'desc')->limit($pageSize)->offset(($pageIndex - 1) * $pageSize)->get();
		return json_encode($data);
	}



	public function delete(Interviewee $interviewee)
	{
		$this->authorize('interviewee', $interviewee);
		$interviewee->delete();
		return json_encode("Record deleted successfully");
	}

	public function detail(Interviewee $interviewee)
	{
		$this->authorize('interviewee', $interviewee);
		$data['interviewee'] = $interviewee->load('qualification');
		$data['departments'] = json_decode($interviewee->interested_in);
		$data['department_select'] = Department::all()->pluck('name', 'id')->toArray();
		$data['designations'] = Designation::all()->pluck('name')->toArray();
		//get employee id
		$employee = Employee::where('personal_email', $interviewee->email)->first();
		if (!empty($employee)) {
			$data['offer_letter'] = $employee->documents->where('document_name', 'Offer Letter')->first();
		}
		$data['submitRoute'] = 'updateInterviewee';
		$data['status'] = ['rejected' => 'rejected', 'approved' => 'approved'];
		return view('interviewee.intervieweeDetail', $data);
	}

	public function downloadResume(Request $request)
	{
		$this->authorize('interviewee', new Interviewee());
		$resume = Interviewee::where('resume', $request->resume)->first();
		if (empty($resume)) {
			abort(404);
		} else {
			$file = $resume->resume . '.enc';
		}
		$path = '/document/' . $file;
		return response()->streamDownload(function () use ($path) {
			FileVault::streamDecrypt(trim($path));
		}, Str::replaceLast('.enc', '', $file));
	}

	public function downloadOfferLetter(Request $request)
	{
		$offer_letter = Document::where('document_name', $request->offer_letter)->first();
		if (empty($offer_letter)) {
			abort(404);
		}
		$file = $request->offer_letter;
		return response()->streamDownload(function () use ($file) {
			$path = '/document/ ';
			FileVault::streamDecrypt(trim($path) . $file);
		}, Str::replaceLast('.enc', '', $file));
	}

	public function createLink()
	{
		$data['interviewee'] = new Interviewee();
		$data['submitRoute'] = 'sendJobLink';
		if (empty(auth()->user())) {
			return view('interviewee.getJobApplicationLink', $data);
		}
		$this->authorize('interviewee', new Interviewee());
		return view('interviewee.sendJobApplicationLink', $data);
	}

	public function updateInterviewee(IntervieweeApprovedRequest $request)
	{
		$this->authorize('interviewee', new Interviewee());
		$interviewee = Interviewee::find($request->interviewee_id);
		if ($request->status == 'approved') {
			$check = Employee::withoutGlobalScopes()->where('personal_email', $interviewee->email)->first();
			if (!empty($check)) {
				$data['personal_email'] = 'Email Id already exists in Employee table';
				return back()->withErrors($data);
			}
			$employee = new Employee();
			$employee->name = $interviewee->first_name . ' ' . $interviewee->middle_name . ' ' . $interviewee->last_name;
			$employee->registration_id = $request->registration_id;
			$employee->personal_email = $interviewee->email;
			$employee->department_id = $request->department;
			$employee->phone = $interviewee->phone;
			$employee->birth_date = $request->birth_date;
			$employee->join_date = $request->join_date;
			$employee->qualification_id = $interviewee->qualification_id;
			$employee->is_active = '1';
			$designation = Designation::firstOrCreate(['name' => $request->designation]);
			$employee->designation_id = $designation->id;
			$employee->save();

			$users = User::havingRole('IT');
			$message = 'New Employee Registered, Assign Office Email to ' . $employee->name;
			$route = ['name' => 'employeeDetail', 'parameter' => $employee->id];
			send_notification($users, $message, $route);


			$document             =    new Document();
			$document->employee_id = $employee->id;
			$reference = Str::random(10);
			$document->reference = $reference;
			$document->document_name = "Offer Letter";
			$filename = Storage::putFile('document', $request->offer_letter);
			FileVault::encrypt($filename);
			$filename = str_replace('document/', '', $filename);
			$document->filename = $filename . '.enc';
			//save documents to document table
			$document->document_type = "Office";
			$document->save();
		}
		$interviewee->status = $request->status;

		$interviewee->response = $request->response;

		$data['response'] = $request->response;
		$data['status'] = strtoupper($request->status);
		$data['name'] = strtoupper($interviewee->first_name);
		$subject = 'Job Recruitment';
		$message = $request->respone;
		$email = $interviewee->email;




		if ($request->has('offer_letter') && $request->status == 'approved') {

			$document             =    new Document();
			$reference = Str::random(10);
			$document->reference = $reference;
			$document->document_name = "Offer Letter";
			$filename = Storage::putFile('document', $request->offer_letter);
			FileVault::encrypt($filename);
			$filename = str_replace('document/', '', $filename);
			$document->filename = $filename . '.enc';
			//save documents to document table
			$document->document_type = "Office";
			$document->save();
			$employee->documents()->attach($document->id);
		}

		$emailSent          =   send_email("email.response", $data, $subject, $message, array($email), null);
		$interviewee->save();
		if ($emailSent) {
			return back()->with('success', 'Data saved successfully');
		}
		return back()->with('success', "Data saved but couldn't send email");
	}


	public function sendUpdatePasswordLink()
	{
		$email = Auth::User()->email;
		$reset_link = URL::temporarySignedRoute('updatePasswordForm', now()->addHour(), ['email' => $email]);
		$subject = 'Reset Password Link';
		$data['formlink'] = $reset_link;
		$message = 'Please use the link to update your password for EMS.';
		$emailSent          =   send_email("email.updatePassword", $data, $subject, $message, array($email), null);
		if ($emailSent) {
			return back()->with('success', "Email With Link Sent");
		} else {
			return back()->withErrors('Unable to Send Email. Please Check Email and Try Again');
		}
	}
}
