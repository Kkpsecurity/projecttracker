<?php
namespace App\Http\Controllers\Admin\Users;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

     public function __construct()
     {
          $this->middleware('auth');
     }

     public function index()
     {
          $users = User::paginate(10);
          return view('admin.users.table_list', compact('users'));
     }

     public function show(User $user)
     {
          return view('admin.users.show', compact('user'));
     }


     public function create()
     {
          return view('admin.users.create');
     }

     public function store(Request $request)
     {
         $request->validate([
             'name' => 'required|string|max:255',
             'email' => 'required|email|unique:users,email',
             'password' => 'required|string|confirmed|min:8',
         ]);

         // Create the user
         User::create([
             'name' => $request->name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);

         return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
     }


     public function edit(User $user)
     {
          return view('admin.users.edit', compact('user'));
     }



     /**
      * Update the specified resource in storage.
      *
      * @param  \Illuminate\Http\Request  $request
      * @param  \App\User  $user
      * @return \Illuminate\Http\Response
      */
     public function update(Request $request, User $user)
     {
          $request->validate([
               'name' => 'required',
               'email' => 'required',
          ]);

          $user->name = $request->name;
          $user->email = $request->email;
          if ($request->has('password') && $request->password) {
               $user->password = bcrypt($request->password);
          }
          $user->save();

          return redirect('admin/users')->with('success', 'User updated successfully');
     }

     public function destroy(User $user)
     {
          $user->delete();
          return redirect('admin/users')->with('success', 'User deleted successfully');
     }



}
