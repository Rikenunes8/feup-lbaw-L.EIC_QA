<?php

namespace App\Http\Controllers;

use App\Models\Uc;
use Illuminate\Http\Request;

class UcController extends Controller
{
    /**
     * Shows all ucs.
     *
     * @return Response
     */
    public function list()
    {
      $this->authorize('show', Uc::class);
      $ucs = DB::table('uc')->orderBy('name')->get();
      return view('pages.ucs', ['ucs' => $ucs]); // TODO: this view doesn't exists yet
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
        $this->authorize('show', $uc);
        return view('pages.uc', ['uc' => $uc]); // TODO: this view doesn't exists yet
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateForm()
    {
        if (!Auth::check()) return redirect('/login');
        $this->authorize('create', Uc::class);
        return view('pages.ucCreateForm'); // TODO: this view doesn't exists yet
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Uc The uc created.
     */
    public function create(Request $request)
    {
        if (!Auth::check()) return redirect('/login');

        $uc = new Uc();
        $this->authorize('create', Uc::class);

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
        $this->authorize('update', $uc);
        return view('pages.ucEditForm', ['uc' => $uc]); // TODO: this view doesn't exists yet
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
        $this->authorize('update', $uc);

        $uc->update($request->all());
        /*
        $uc->name = $request->input('name');
        $uc->code = $request->input('code');
        $uc->description = $request->input('description');
        $uc->save();
        */

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
        $this->authorize('delete', $uc);
        
        $uc->delete();

        return redirect('/admin/ucs');
    }

    /**
     * Add a association between an uc and a teacher.
     * 
     * @param  int  $uc_id
     * @param  int  $user_id
     * @return bool The association was added or not.
     */
    public function addTeacher($uc_id, $user_id) 
    {
        if (!Auth::check()) return redirect('/login');

        $uc = Uc::find($uc_id);
        $teacher = User::find($user_id);
        $result = false;
    
        if (isset($uc) && isset($teacher)) {
            $uc->teachers()->save($teacher);
            /*
            $result =   DB::insert('INSERT INTO teacher_uc (id_uc, id_teacher) VALUES (:id_uc, :id_teacher)', [
                            'id_uc' => $uc->id, 
                            'id_teacher' => $teacher->id
                        ]);
            */
        }
        
        return $result;
    }

    /**
     * Delete a association between an uc and a teacher.
     * 
     * @param  int  $uc_id
     * @param  int  $user_id
     * @return bool The association was deleted or not.
     */
    public function deleteTeacher($uc_id, $user_id) 
    {
        if (!Auth::check()) return redirect('/login');

        $uc = Uc::find($uc_id);
        $teacher = User::find($user_id);
        $result = false;
    
        if (isset($uc) && isset($teacher)) {
            $uc->teachers()::where('id_teacher', $teacher->id)->delete();
            /*
            $result =   DB::delete ('DELETE FROM teacher_uc WHERE id_uc=:id_uc AND id_teacher=:id_teacher', [
                            'id_uc' => $uc->id, 
                            'id_teacher' => $teacher->id
                        ]);
            */
        }
        
        return $result;
    }
}