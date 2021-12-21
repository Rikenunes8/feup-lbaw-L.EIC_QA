<?php

namespace App\Http\Controllers;

use App\Models\Uc;
use Illuminate\Http\Request;

class UcController extends Controller
{
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
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreate()
    {
        $this->authorize('create');
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEdit($id)
    {
        $uc = Uc::find($id);
        $this->authorize('update', $uc);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Uc The uc created.
     */
    public function create(Request $request)
    {
        $uc = new Uc();
        $this->authorize('create');

        $uc->name = $request->input('name');
        $uc->code = $request->input('code');
        $uc->description = $request->input('description');
        $uc->save();

        return $uc;
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
        $uc = Uc::find($id);
        $this->authorize('update', $uc);

        $uc->name = $request->input('name');
        $uc->code = $request->input('code');
        $uc->description = $request->input('description');
        $uc->save(); // TODO: Is this right?

        return $uc;
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
        $uc =  Uc::find($id);
        $this->authorize('delete', $uc);
        
        $uc->delete();

        return $uc;
    }
}
