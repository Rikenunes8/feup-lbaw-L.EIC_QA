<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $intervention = Intervention::find($id);
        $this->authorize('show', $intervention);
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
        $intervention = Intervention::find($id);
        $this->authorize('update', $intervention);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Intervention The question created.
     */
    public function createQuestion(Request $request)
    {
        $intervention = new Intervention();

        $this->authorize('create');

        $intervention->text = $request->input('text');
        $intervention->title = $request->input('title');
        $intervention->category = $request->input('category');
        $intervention->id_author = Auth::user()->id;
        $intervention->type = 'question';
        $intervention->save();

        return $intervention;
    }
    /**
     * Create a resource in storage.
     * 
     * @param  Request  $request
     * @param  int  $parent_id
     * @return Intervention The answer created.
     */
    public function createAnswer(Request $request, $parent_id)
    {
        $intervention = new Intervention();

        $this->authorize('create');

        $intervention->text = $request->input('text');
        $intervention->id_author = Auth::user()->id;
        $intervention->id_intervention = $parent_id;
        $intervention->type = 'answer';
        $intervention->save();

        return $intervention;
    }
    /**
     * Create a resource in storage.
     * 
     * @param  Request  $request
     * @param  int  $parent_id
     * @return Intervention The comment created.
     */
    public function createComment(Request $request, $parent_id)
    {
        $intervention = new Intervention();

        $this->authorize('create');

        $intervention->text = $request->input('text');
        $intervention->id_author = Auth::user()->id;
        $intervention->id_intervention = $parent_id;
        $intervention->type = 'comment';
        $intervention->save();

        return $intervention;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Intervention The intervention updated.
     */
    public function update(Request $request, $id)
    {
        $intervention =  Intervention::find($id);
        $this->authorize('update', $intervention);

        $intervention->text = $request->input('text');
        $intervention->save(); // TODO: Is this right?

        return $intervention;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Intervention  $intervention
     * @return Intervention The question deleted.
     */
    public function delete(Request $request, $id)
    {
        $intervention =  Intervention::find($id);

        $this->authorize('delete', $intervention);
        $intervention->delete();

        return $intervention;
    }
}
