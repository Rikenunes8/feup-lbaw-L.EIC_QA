<?php

namespace App\Http\Controllers;

use App;
use App\Models\Uc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{

    /**
     * Shows all users.
     *
     * @return Response
     */
    public function list(Request $request)
    {
        $search = $request->search;
        $query = User::where('type', '!=', "Admin")->orderBy('score', 'DESC');
        if(!empty($search)) {
            $query = User::where('type', '!=', "Admin")->where('name', 'ilike', '%'.$search.'%')->orderBy('score', 'DESC');
        }
        $users =  $query->paginate(12);
        return view('pages.users', ['users' => $users, 'search' => $search]);
    }

    public function show(Request $request, $id)
    {
        $user = User::find($id);
        if (is_null($user)) return App::abort(404);

        $active = 'questions';

        $searchQuestions = $request->searchQuestions;
        $queryQuestions = $user->interventions()->questions()->orderBy('votes', 'DESC');
        if(!empty($searchQuestions)) {
            $queryQuestions = $user->interventions()->questions()->where('title', 'ilike', '%'.$searchQuestions.'%')->orderBy('votes', 'DESC');
        }
        $questions = $queryQuestions->paginate(5, ['*'], 'questionsPage');

        $searchAnswers = $request->searchAnswers;
        $queryAnswers = $user->interventions()->answers()->orderBy('votes', 'DESC');
        if(!empty($searchAnswers)) {
            $queryAnswers = $user->interventions()->answers()->where('text', 'ilike', '%'.$searchAnswers.'%')->orderBy('votes', 'DESC');
            $active = 'answers';
        }
        $answers = $queryAnswers->paginate(5, ['*'], 'answersPage');

        $searchValidatedAnswers = $request->searchValidatedAnswers;
        $queryValidatedAnswers = $user->validates()->orderBy('votes', 'DESC');
        if(!empty($searchValidatedAnswers)) {
            $queryValidatedAnswers = $user->validates()->where('text', 'ilike', '%'.$searchValidatedAnswers.'%')->orderBy('votes', 'DESC');
            $active = 'validated-answers';
        }
        $validatedAnswers = $queryValidatedAnswers->paginate(5, ['*'], 'validatedAnswersPage');

        $searchUcs = $request->searchUcs;
        $queryUcs = [];
        if ($user->isStudent()) {
            $queryUcs = $user->follows()->orderBy('name');
        } else if ($user->isTeacher()) {
            $queryUcs = $user->teaches()->orderBy('name');
        }
        if(!empty($searchUcs)) {
            if ($user->isStudent()) {
                $queryUcs = $user->follows()->where('name', 'ilike', '%'.$searchUcs.'%')->orderBy('name');
            } else if ($user->isTeacher()) {
                $queryUcs = $user->teaches()->where('name', 'ilike', '%'.$searchUcs.'%')->orderBy('name');
            }
            $active = 'ucs';
        }
        $associatedUcs = $queryUcs->paginate(6, ['*'], 'associatedUcsPage');

        return view('pages.user', compact('user', 'questions', 'answers', 'validatedAnswers', 'associatedUcs', 'searchQuestions', 'searchAnswers', 'searchValidatedAnswers', 'searchUcs', 'active'));
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
            'username' => 'required|string|max:20|unique:users,username,'.$id,
            'password' => 'nullable|string|min:6',
        ]);

        $user->username = $request->input('username');

        $password = $request->input('password');
        $confirm = $request->input('confirm');
        if ($password != '') {
            if ($password != $confirm)
                return Redirect::back()->withErrors(['confirm' => 'The Password confirmation does not match.']); 
            $user->password = Hash::make($password);
        }

        if (!$user->isAdmin()) {
            $request->validate([
                'name' => 'required|string|max:255',
                'about' => 'nullable|string|max:500',
                'birthdate' => 'nullable|date_format:Y-m-d',
                'photo' => 'nullable|image|mimes:jpeg,jpg,png,bmp,tiff,gif|max:4096',
            ]);

            $user->name = $request->input('name');
            $user->about = $request->input('about');         
            $user->birthdate = date("Y-m-d H:i:s", strtotime($request->input('birthdate')));
            
            if ($request->hasFile('photo')) {
                $file = $request->photo;
                $filename = $user->id.'_'.time().'_'.Str::random(10).'.'.$file->getClientOriginalExtension();
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

        $self = false;
        if (Auth::user()->id == $id) $self = true;

        $user->delete();
        
        $url = $request->path();
        
        if ($self) 
            return redirect('/home'); 
        else if (substr_compare($url, "api", 0, 3) != 0) 
            return redirect("/users");
        else 
            return $user;
    }

    public function follow(Request $request, $user_id, $uc_id) 
    {
        if (!Auth::check()) return redirect('/login');
        
        $user = User::find($user_id);
        $uc = Uc::find($uc_id);

        if (is_null($user) || is_null($uc)) return App::abort(404);
        $this->authorize('follow', $user);
        
        $follow = $request->input('follow');
        if ($follow == 'true') {
            $user->follows()->attach($uc->id);
        } else {
            $user->follows()->detach($uc->id);
        }

        return $uc;
    }
}
