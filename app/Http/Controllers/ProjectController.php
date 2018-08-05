<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permissions;
use App\Role;
use App\Project;
use App\User;
use App\UserProject;
use App\ProjectType;
use Auth;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {

        $projects = Project::get();
        
        return view('projects.index', compact('projects'));
    }
    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $projects = Project::get(); //Get all Project
        $projectTypes = ProjectType::pluck('label', 'id');
        return view('projects.create',compact('projects','projectTypes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required',
            'type_id' => 'required',
            'url' => 'required',
            
        ]);
        $data = $request->input();
        $project = Project::create($request->input());
       // dd($projectId);
        if($data['projectUser']){
            $data['projectUser']['project_id'] = $project->id;
            UserProject::create($data['projectUser']);
        }
        
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
        $project = Project::findOrFail($id); //Get all Project
        $projectTypes = ProjectType::pluck('label', 'id');
        return view('projects.edit', compact('project','projectTypes'));
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
        //dd($request);
        // dd($id);
        $project = Project::findOrFail($id);
        $project->update($request->input());
        return redirect()->route('projects.index')->with('success','Project updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function destroy($id) {
        $project = Project::findOrFail($id);

        $project->delete();
        return redirect()->back()->with('success',
             'Project successfully deleted.');
    }

}
