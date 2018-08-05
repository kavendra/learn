<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\User;
use App\Role;
use App\Permission;
use App\RoleUser;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware('auth');
        $this->request = $request;

    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::latest()->paginate(2);
       
        return view('users.index', compact('users'));
    }
    public function create()
    {
        $roles = Role::get();        
        return view('users.create', compact('roles'));
    }
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
        $user = User::create($request->except('roles'));
        
        /*if($request->roles <> ''){
            $user->roles()->attach($request->roles);
        }*/
        return redirect()->route('users.index')->with('success','User has been created');            
        
    }
    public function edit($id) {

        $user = User::findOrFail($id);
        //$roles = Role::get();
        return view('users.edit', compact('user')); 
    }

    public function update(Request $request, $id) {
        $user = User::findOrFail($id);   
        $this->validate($request, [
            'name'=>'required',
            'email'=>'required|email|unique:users,email,'.$id,
            //'password'=>'required|min:6|confirmed'
        ]);
        //dd($user);
        $user->update($request->input());

        return redirect()->route('users.index')->with('success',
             'User successfully updated.');
    }

    public function destroy($id) {

        $user = User::findOrFail($id); 
        $user->delete();
        return redirect()->route('users.index')->with('success',
             'User successfully deleted.');
    }


}