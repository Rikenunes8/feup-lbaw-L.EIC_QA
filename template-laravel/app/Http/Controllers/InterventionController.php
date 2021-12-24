<?php

namespace App\Http\Controllers;

use App\Models\Intervention;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InterventionController extends Controller
{
    /**
     * Shows all questions.
     *
     * @return Response
     */
    public function list()
    {
        // $this->authorize('show', Intervention::class); // Anyone can see the list of questions
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
        if (is_null($question)) return App::abort(404);
        $this->authorize('show', $question);
        return view('pages.question', ['question' => $question]);
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
        return view('pages.questionCreateForm');
    }

    /**
     * Create a resource in storage.
     *
     * @param  Request  $request
     * @return Response
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
        $question = Intervention::questions()::find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('update', $question);
        return view('pages.questionEditForm', ['question' => $question]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function updateQuestion(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $question =  Intervention::question()::find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('update', $question);

        $question->text = $request->input('text');
        $question->save(); // TODO: Is this right?

        return redirect('/questions/{{ $question->id }}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Intervention  $intervention
     * @return Response
     */
    public function deleteQuestion(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $question =  Intervention::questions()::find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('delete', $question);
        $question->delete();

        return redirect('/questions');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function showCreateAnswerForm($id)
    {   
        if (!Auth::check()) return redirect('/login');

        $question = Intervention::questions()::find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('create', Intervention::class);
        return view('pages.answerCreateForm', ['question' => $question]);
    }

    /**
     * Create a resource in storage.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function createAnswer(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer = new Intervention();
        $question = Intervention::question()::find($id);
        if (is_null($question)) return App::abort(404);

        $this->authorize('create', $answer);

        $answer->text = $request->input('text');
        $answer->id_author = Auth::user()->id;
        $answer->id_intervention = $id;
        $answer->type = 'answer';
        $answer->save();

        return redirect('questions/{{ $question->id }}');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditAnswerForm($id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer = Intervention::answers()::find($id);
        if (is_null($answer)) return App::abort(404);
        $this->authorize('update', $answer);
        return view('pages.answerEditForm', ['answer' => $answer]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function updateAnswer(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer =  Intervention::answers()::find($id);
        if (is_null($answer)) return App::abort(404);
        $this->authorize('update', $answer);

        $answer->text = $request->input('text');
        $answer->update(); // TODO: Is this right?

        return redirect('questions/{{ $answer->id_intervention }}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Intervention  $intervention
     * @return Response
     */
    public function deleteAnswer(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer =  Intervention::answers()::find($id);
        $this->authorize('delete', $answer);
        $asnwer->delete();

        return $answer;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showCreateCommentForm($id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer = Intervention::answers()::find($id);
        if (is_null($answer)) return App::abort(404);
        $this->authorize('create', Intervention::class);
        return view('pages.commentCreateForm', ['asnwer' => $answer]);
    }

    /**
     * Create a resource in storage.
     * 
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function createComment(Request $request, $id)
    {
        $comment = new Intervention();

        $answer = Intervention::answers()::find($id);
        if (is_null($answer)) return App::abort(404);
        $question = Intervention::questions()::find($answer->id_intervention);
        if (is_null($question)) return App::abort(404);
        $this->authorize('create', $comment);

        $comment->text = $request->input('text');
        $comment->id_author = Auth::user()->id;
        $comment->id_intervention = $id;
        $comment->type = 'comment';
        $comment->save();

        return redirect('/questions/{{ $question->id }}');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function showEditCommentForm($id)
    {
        $comment = Intervention::comments()::find($id);
        if (is_null($comment)) return App::abort(404);


        $this->authorize('update', $comment);
        return view('pages.commentEditForm', ['asnwer' => $answer]);
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
        $comment =  Intervention::comments()::find($id);
        if (is_null($comment)) return App::abort(404);

        $answer = Intervention::answers()::find($comment->id_intervention);
        if (is_null($answer)) return App::abort(404);

        $this->authorize('update', $comment);

        $comment->text = $request->input('text');
        $comment->save(); // TODO: Is this right?

        return redirect('question/{{ answer->id_intervention }}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Intervention  $intervention
     * @return Intervention The question deleted.
     */
    public function deleteComment(Request $request, $id)
    {
        $comment =  Intervention::comments()::find($id);

        $answer = Intervention::answers()::find($comment->id_intervention);
        if (is_null($answer)) return App::abort(404);

        $this->authorize('delete', $comment);
        $comment->delete();

        return redirect('question/{{ answer->id_intervention }}');
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
