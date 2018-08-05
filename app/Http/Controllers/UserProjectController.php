<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permissions;
use App\Role;
use App\Project;
use App\User;
use App\ProjectType;
use Auth;


class UserProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(User $user)
    {
        $this->middleware('auth');
        $this->user = $user;
    }

    public function index(User $user)
    {
        $projects = $user->projects;
        return view('user-project.index', compact('user', 'projects'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(User $user)
    {
        $allProjects = $user->projects->pluck('id')->toArray();

        $projects = Project::whereNotIn('id', $allProjects)->pluck('title', 'id')->toArray(); 
        
        return view('user-project.create',compact('user', 'projects'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->validate($request, [
            'project_id' => 'required',
        ]);
        $data = $request->input();
        $project = Project::findOrFail($request->project_id);
        $user->projects()->attach($project);
        //UserProject::create($data);
        return redirect()->back()->with('success','Project has been created');
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
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy(User $user, Project $project) {
        
        $user->projects()->detach($project);
        return redirect()->back()->with('success',
             'Project successfully deleted.');
    }
}
