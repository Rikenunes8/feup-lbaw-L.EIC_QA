<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    /**
     * Shows all users.
     *
     * @return Response
     */
    public function list()
    {
        $users = User::where('type', '!=', "Admin")->orderBy('score', 'DESC')->get();
        return view('pages.users', ['users' => $users]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        if (!Auth::check()) return redirect('/login');
        $user = User::find($id);
        $this->authorize('show', $user);
        return view('pages.user', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditForm($id)
    {
        if (!Auth::check()) return redirect('/login');
        $user = User::find($id);
        $this->authorize('update', $user);
        return view('pages.admin.forms.user.edit', ['user' => $user]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        
        $user = User::find($id);
        $this->authorize('update', $user);

        $uc->update($request->all());

        return redirect('/users/{{ $user->id }}'); 
    }
}
