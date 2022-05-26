<?php

namespace App\Http\Controllers\ems;

use App\User;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\AnnouncementRequest;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', new Announcement());
        $data['announcements']      =   Announcement::all();

        return view('announcement.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $announcement                   =  new Announcement();
        $this->authorize('create', $announcement);
        $data['announcement']           =  $announcement;
        $data['submitRoute']            =   ['announcement.store'];
        $data['method']                 =   'POST';
        $data['users']                  =   User::where('is_active','1')->where('user_type','Employee')
        ->pluck('name','id')->toArray();

        return view('announcement.form',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AnnouncementRequest $request)
    {
        $announcement                   =   new Announcement();
        $this->authorize('create',  $announcement);
        $announcement->title            =   $request->title;
        $announcement->start_dt         =   $request->start_dt;
        $announcement->end_dt           =   $request->end_dt;
        $announcement->is_publish       =   (isset($request->is_publish) ? 1 : 0);
        $announcement->start_time       =   $request->start_time;
        $announcement->end_time         =   $request->end_time;
        $announcement->description      =   $request->description;

        if($request->hasFile('attachment'))
        {
            $files                      =       $request->title.'.'.$request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(public_path('announcements/'), $files);
            $announcement['attachment'] =       $files;
        }
        $announcement->save();

        $announcement->users()->sync($request['user_id']);

        return redirect()->route('announcement.index')->with('success', 'Announcement Created');
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
        $this->authorize('update', new Announcement());
        $data['announcement']       =   Announcement::findOrFail($id);
        $data['submitRoute']        =   ['announcement.update',$id];
        $data['method']             =   'PUT';
        $data['users']              =   User::pluck('name','id')->toArray();

        return view('announcement.form',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AnnouncementRequest $request, $id)
    {
        $this->authorize('update', new Announcement());
        $announcement                   =   Announcement::findOrFail($id);
        $announcement->title            =   $request->title;
        $announcement->start_dt         =   $request->start_dt;
        $announcement->end_dt           =   $request->end_dt;
        $announcement->is_publish       =   (isset($request->is_publish) ? 1 : 0);
        $announcement->start_time       =   $request->start_time;
        $announcement->end_time         =   $request->end_time;
        $announcement->description      =   $request->description;

        if($request->hasFile('attachment'))
        {
            if(!empty($announcement->attachment))
            {
                unlink(public_path('announcements/'.$announcement->attachment));
            }
            $files                         =  $request->title.'.'.$request->file('attachment')->getClientOriginalExtension();
            $request->file('attachment')->move(public_path('announcements/'), $files);
            $announcement['attachment']    =  $files;
        }
        $announcement->update();

        $announcement->users()->sync($request['user_id']);

        return redirect(route('announcement.index'))->with('success','Announcement Successfully Updated');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('delete', new Announcement());
        $announcement      =   Announcement::with('users')->findOrFail($id);
        if(!empty($announcement->users))
        {
            $announcement->users()->detach();
        }
        if(!empty($announcement->attachment))
        {
            $path       =   $announcement->attachment;
            unlink(public_path('announcements/'.$path));
        }

        $announcement->delete();
    }

    public function downloadAnnouncement(Request $request)
    {
        $file       = public_path("announcements/$request->reference");

        return response()->file($file);
    }
}
