<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;

class InterventionController extends Controller
{
    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function list()
    {
        $this->authorize('show', Intervention::class);
        $questions = Intervention::questions()->orderBy('votes', 'DESC')->get();
        // $questions = DB::table('intervention')->where('type', 'question')->orderBy('votes')->get();
        return view('pages.questions', ['questions' => $questions]); // TODO: this view doesn't exists yet
    }

    /**
     * Display the specified questions.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $question = Intervention::questions()::find($id);
        $this->authorize('show', $question);
        return view('pages.question', ['question' => $question]); // TODO: this view doesn't exists yet
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateQuestionForm()
    {
        if (!Auth::check()) return redirect('/login');
        $this->authorize('create', Intervention::class);
        return view('pages.questionCreateForm'); // TODO: this view doesn't exists yet
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Intervention The question created.
     */
    public function createQuestion(Request $request)
    {
        if (!Auth::check()) return redirect('/login');

        $question = new Intervention();
        $this->authorize('create', Intervention::class);

        $question->id_author = Auth::user()->id;
        $question->title = $request->input('title');
        $question->text = $request->input('text');
        $question->category = $request->input('category');
        $question->type = 'question';
        $question->save();

        return redirect('qustions/{{ $question->id }}');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditQuestionForm($id)
    {
        if (!Auth::check()) return redirect('/login');
        $intervention = Intervention::find($id);
        $this->authorize('update', $intervention);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Intervention The intervention updated.
     */
    public function updateQuestion(Request $request, $id)
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
    public function deleteQuestion(Request $request, $id)
    {
        $intervention =  Intervention::find($id);

        $this->authorize('delete', $intervention);
        $intervention->delete();

        return $intervention;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateAnswerForm()
    {
        $this->authorize('create', Intervention::class);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Create a resource in storage.
     * 
     * @param  Request  $request
     * @param  int  $parent_id
     * @return Intervention The answer created.
     */
    public function createAnswer(Request $request, $id)
    {
        $intervention = new Intervention();

        $this->authorize('create', $intervention);

        $intervention->text = $request->input('text');
        $intervention->id_author = Auth::user()->id;
        $intervention->id_intervention = $parent_id;
        $intervention->type = 'answer';
        $intervention->save();

        return $intervention;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditAnswerForm($id)
    {
        $intervention = Intervention::find($id);
        $this->authorize('update', $intervention);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Intervention The intervention updated.
     */
    public function updateAnswer(Request $request, $id)
    {
        $intervention =  Intervention::find($id);
        $this->authorize('update', $intervention);

        $intervention->text = $request->input('text');
        $intervention->update(); // TODO: Is this right?

        return $intervention;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Intervention  $intervention
     * @return Intervention The question deleted.
     */
    public function deleteAnswer(Request $request, $id)
    {
        $intervention =  Intervention::find($id);

        $this->authorize('delete', $intervention);
        $intervention->delete();

        return $intervention;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateCommentForm()
    {
        $this->authorize('create');
        return view(); // TODO: this view doesn't exists yet
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

        $this->authorize('create', $intervention);

        $intervention->text = $request->input('text');
        $intervention->id_author = Auth::user()->id;
        $intervention->id_intervention = $parent_id;
        $intervention->type = 'comment';
        $intervention->save();

        return $intervention;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditCommentForm($id)
    {
        $intervention = Intervention::find($id);
        $this->authorize('update', $intervention);
        return view(); // TODO: this view doesn't exists yet
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Intervention The intervention updated.
     */
    public function updateComment(Request $request, $id)
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
    public function deleteComment(Request $request, $id)
    {
        $intervention =  Intervention::find($id);

        $this->authorize('delete', $intervention);
        $intervention->delete();

        return $intervention;
    }

    public function report($id)
    {
        return true;
    }

    public function vote($id)
    {
        return true;
    }

    public function validate($id)
    {
        return true;
    }
}
