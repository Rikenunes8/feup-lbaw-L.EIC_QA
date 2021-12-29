<?php

namespace App\Http\Controllers;

use App;
use App\Models\Intervention;
use App\Models\Uc;
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
        $questions = Intervention::questions()->orderBy('votes', 'DESC')->paginate(15);

        return view('pages.questions', ['questions' => $questions]);
    }

    /**
     * Display the specified questions.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        $intervention = Intervention::find($id);
        while (!$intervention->isQuestion() && !is_null($intervention)) {
            $intervention = $intervention->parent();
        }
        if (is_null($intervention)) return App::abort(404);

        $question = $intervention;
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
        $this->authorize('showCreate', Intervention::class);
        $ucs = Uc::orderBy('name')->get();
        return view('pages.forms.question.create', ['ucs' => $ucs]);
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
        $this->authorize('create', $question);

        $question->id_author = Auth::user()->id;
        $question->title = $request->input('title');
        $question->text = $request->input('text');
        $question->category = $request->category;
        $question->type = 'question';
        $question->save();

        return redirect('questions/'.$question->id);
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
        $question = Intervention::questions()->find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('update', $question);
        $ucs = Uc::orderBy('name')->get();
        return view('pages.forms.question.edit', ['question' => $question, 'ucs' => $ucs]);
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
        $question =  Intervention::questions()->find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('update', $question);
        $question->title = $request->input('title');
        $question->text = $request->input('text');
        $question->category = $request->category;
        $question->save();
        return redirect('questions/'.$question->id);
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
        return view('pages.forms.answer.create', ['question' => $question]);
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
        return view('pages.forms.answer.edit', ['answer' => $answer]);
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
        $answer->save(); // TODO: Is this right?

        return redirect('questions/{{ $answer->id_intervention }}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  int  $id
     * @return Response
     */
    public function deleteAnswer(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $answer =  Intervention::answers()::find($id);
        $this->authorize('delete', $answer);
        $asnwer->delete();

        return redirect('questions/{{ $id }}');
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
        return view('pages.forms.comment.create', ['answer' => $answer]);
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
        if (!Auth::check()) return redirect('/login');

        $comment = new Intervention();

        $answer = Intervention::answers()::find($id);
        if (is_null($answer)) return App::abort(404);
        $question = $answer->parent();

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
        if (!Auth::check()) return redirect('/login');

        $comment = Intervention::comments()::find($id);
        if (is_null($comment)) return App::abort(404);
        $answer = $comment->parent();

        $this->authorize('update', $comment);
        return view('pages.forms.comment.edit', ['comment' => $comment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function updateComment(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $comment =  Intervention::comments()::find($id);
        if (is_null($comment)) return App::abort(404);
        $answer = $comment->parent();

        $this->authorize('update', $comment);

        $comment->text = $request->input('text');
        $comment->save(); // TODO: Is this right?

        return redirect('question/{{ answer->id_intervention }}');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function deleteComment(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $comment =  Intervention::comments()::find($id);
        if (is_null($comment)) return App::abort(404);
        $answer = $comment->parent();

        $this->authorize('delete', $comment);
        $comment->delete();

        return redirect('question/{{ answer->id_intervention }}');
    }

    public function report(Request $request, $id)
    {
        $intervention = Intervention::find($id);
        // TODO: notification
        return true;
    }

    /**
     * Vote an intervention.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function vote(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $intervention = Intervention::find($id);
        $user = Auth::user();
        $vote = $request->input('vote'); // TODO:
        
        $this->authorize('vote', $intervention);

        $intervention->votes()->save($user, ['vote', $vote]);
        return redirect('questions/{{ $intervention->id }}'); // TODO: Maybe not to return redirect
    }
    
    /**
     * Validate an answer.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function valid(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $intervention = Intervention::answers()::find($id);
        $user = Auth::user();
        $valid = $request->input('valid'); // TODO:
        
        $this->authorize('validate', $intervention);

        $intervention->valid()->save($user, ['valid', $valid]);
        return redirect('questions/{{ $intervention->id }}'); // TODO: Maybe not to return redirect
    }
}
