<?php

namespace App\Http\Controllers;

use App;
use App\Models\Uc;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class UcController extends Controller
{
    /**
     * Shows all ucs.
     *
     * @return Response
     */
    public function list()
    {
        $ucs = Uc::orderBy('name')->paginate(18);
        return view('pages.ucs', ['ucs' => $ucs]);
    }

     /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        return view('pages.uc', ['uc' => $uc]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateForm()
    {
        if (!Auth::check()) return redirect('/login');
        $this->authorize('showCreate', Uc::class);
        return view('pages.forms.uc.create');
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Response 
     */
    public function create(Request $request)
    {
        if (!Auth::check()) return redirect('/login');

        $uc = new Uc();
        $this->authorize('create', $uc);

        $request->validate([
            'name' => 'required|unique:uc,name|max:255',
            'code' => 'required|unique:uc,code',
            'description' => 'required',
        ]);

        $uc->name = $request->input('name');
        $uc->code = $request->input('code');
        $uc->description = $request->input('description');
        $uc->save();

        return redirect('/admin/ucs'); 
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
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $this->authorize('update', $uc);
        return view('pages.forms.uc.edit', ['uc' => $uc]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Uc The uc updated.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $this->authorize('update', $uc);

        $request->validate([
            'name' => 'required|unique:uc,name,'.$id.'|max:255',
            'code' => 'required|unique:uc,code,'.$id,
            'description' => 'required',
        ]);

        $uc->update($request->all());

        return redirect('/admin/ucs'); 
    }

    /**
     * Remove the specified resource from storage.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Uc The uc deleted.
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');
        $uc = Uc::find($id);
        if (is_null($uc)) return App::abort(404);
        $this->authorize('delete', $uc);
        $uc->delete();
        return $uc;
    }

    /**
     * Add a association between an uc and a teacher.
     * 
     * @param  int  $uc_id
     * @param  int  $user_id
     * @return Response|bool The association was added or not.
     */
    public function addTeacher(Request $request, $uc_id, $user_id) 
    {
        if (!Auth::check()) return redirect('/login');
        $uc = Uc::find($uc_id);
        $teacher = User::find($user_id);
        if (is_null($uc) || is_null($teacher)) return App::abort(404);

            
        $this->authorize('teacher', [$uc, $teacher]);
        $uc->teachers()->attach($user_id);
        return $teacher;
    }

    /**
     * Delete a association between an uc and a teacher.
     * 
     * @param  int  $uc_id
     * @param  int  $user_id
     * @return Response|bool The association was deleted or not.
     */
    public function deleteTeacher(Request $request, $uc_id, $user_id) 
    {
        if (!Auth::check()) return redirect('/login');
        $uc = Uc::find($uc_id);
        $teacher = User::find($user_id);
        if (is_null($uc) || is_null($teacher)) return App::abort(404);
        
        $this->authorize('teacher', [$uc, $teacher]);
        $uc->teachers()->detach($user_id);
        return $teacher;
    }
}
