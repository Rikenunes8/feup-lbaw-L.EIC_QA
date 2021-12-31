<?php

namespace App\Http\Controllers;

use App;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    /**
     * Shows all users.
     *
     * @return Response
     */
    public function list()
    {
        $users = User::where('type', '!=', "Admin")->orderBy('score', 'DESC')->paginate(18);
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
        $user = User::find($id);
        if (is_null($user)) return App::abort(404);
        $questions = $user->interventions()->questions()->orderBy('votes', 'DESC')->paginate(3, ['*'], 'questionsPage');
        $answers = $user->interventions()->answers()->orderBy('votes', 'DESC')->paginate(3, ['*'], 'answersPage');
        $validatedAnswers = $user->validates()->orderBy('votes', 'DESC')->paginate(3, ['*'], 'validatedAnswersPage');;

        return view('pages.user', compact('user', 'questions', 'answers', 'validatedAnswers'));
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
        if (is_null($user)) return App::abort(404);
        $this->authorize('update', $user);
        return view('pages.forms.user.edit', ['user' => $user]);
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

        $request->validate([
            'username' => 'required|string|max:20|unique:users,username',
            'password' => 'nullable|string|min:6',
        ]);

        $user->username = $request->input('username');

        $password = $request->input('password');
        $confirm = $request->input('confirm');
        if ($password != '') {
            if ($password != $confirm)
                return Redirect::back()->withErrors(['confirm' => 'Password não corresponde']); 
            $user->password = Hash::make($password);
        }

        if (!$user->isAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'about' => 'nullable|string|max:500',
                'birthdate' => 'nullable|date_format:Y-m-d',
            ]);

            $user->name = $request->input('name');
            $user->about = $request->input('about');         
            $user->birthdate = date("Y-m-d H:i:s", strtotime($request->input('birthdate')));
            
            if ($request->hasFile('photo')) {
                $file = $request->photo;

                $image = array('file' => $file);
                $rules = array('file' => 'image');
                $validator = Validator::make($image, $rules);
                if ($validator->fails())
                    return Redirect::back()->withErrors(['photo' => 'Não é uma imagem']); 

                $filename = $user->id.'_'.time().'.'.$file->getClientOriginalExtension();
                $request->photo->storeAs('users', $filename, 'images_uploads');
                $user->photo = $filename;
            }
        }
        $user->save();

        return redirect("/users/$user->id"); 
    }

    /**
     * Block user.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function block(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $user = User::find($id);
        $this->authorize('block', $user);

        if ($user->blocked) {
            $user->blocked = FALSE;
            $user->block_reason = NULL;
        } else {
            $user->blocked = TRUE;
            $user->block_reason = $request->input('block_reason');
        }
        $user->save();

        return $user;
    }

    /**
     * Delete user.
     * 
     * @param  int  $id
     * @return Response
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $user = User::find($id);
        $this->authorize('delete', $user);

        $user->delete();

        return $user;
    }
}
