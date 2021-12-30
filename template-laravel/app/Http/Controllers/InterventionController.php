<?php

namespace App\Http\Controllers;

use App;
use App\Models\Intervention;
use App\Models\Uc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

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
     * Shows all questions.
     *
     * @param Request $request
     * @return Response
     */
    public function searchList(Request $request)
    {
        $questions = Intervention::questions();
        if ($request->input('q')) {
            $search = $request->input('q');
        }
        else {
            $questions = $questions->orderBy('votes', 'DESC')->paginate(15);
            return view('pages.questions', ['questions' => $questions]);    
        }

        $questions = $questions->search($search);
        $questions = $questions->paginate(15);
        return view('pages.search', ['questions' => $questions]);
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
        while (!is_null($intervention) && !$intervention->isQuestion()) {
            $intervention = $intervention->parent();
        }
        if (is_null($intervention)) return redirect('/questions');

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
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']); 
        $question->text = $request['text'];
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
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']); 
        $question->text = $request->input('text');
        $question->category = $request->category;
        $question->save();

        return redirect('questions/'.$question->id);
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
        $question = Intervention::questions()->find($id);
        if (is_null($question)) return App::abort(404);
        $this->authorize('create', $answer);
        $answer->id_author = Auth::user()->id;
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']); 
        $answer->text = $request['text'];
        $answer->id_intervention = $question->id;
        $answer->type = 'answer';
        $answer->save();

        return redirect('questions/'.$question->id);
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

        $answer = Intervention::answers()->find($id);
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

        $answer =  Intervention::answers()->find($id);
        if (is_null($answer)) return App::abort(404);
        $this->authorize('update', $answer);
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']); 
        $answer->text = $request['text'];
        $answer->save();

        return redirect('questions/'.$answer->id_intervention);
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
        $this->authorize('create', $comment);
        $answer = Intervention::answers()->find($id);
        if (is_null($answer)) return App::abort(404);
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']);
        $comment->id_author = Auth::user()->id;
        $comment->text = $request['text'];
        $comment->id_intervention = $answer->id;
        $comment->type = 'comment';
        $comment->save();

        return redirect('questions/'.$answer->id_intervention);
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

        $comment = Intervention::comments()->find($id);
        if (is_null($comment)) return App::abort(404);
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

        $comment =  Intervention::comments()->find($id);
        if (is_null($comment)) return App::abort(404);
        $this->authorize('update', $comment);
        if (is_null($request['text']))
            return Redirect::back()->withErrors(['text' => 'É obrigatório ter uma mensagem de texto!']);
        $answer = $comment->parent()->get();
        $comment->text = $request['text'];
        $comment->save(); 

        return redirect('questions/'.$answer[0]->id_intervention);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  Request $request
     * @param  int  $id
     * @return Response
     */
    public function delete(Request $request, $id)
    {
        if (!Auth::check()) return redirect('/login');

        $intervention =  Intervention::find($id);
        if (is_null($intervention)) return App::abort(404);
        $this->authorize('delete', $intervention);
        $intervention->delete();

        return $intervention;
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
        $vote = $request->input('vote');
        $vote = $vote=='true'? true : false;

        $this->authorize('vote', $intervention);
        $association = $intervention->votes()->where('id_user', $user->id);

        if (!$association->exists()) {
            $intervention->votes()->attach($user->id, ['vote' => $vote]);
        }
        else if ($association->first()->pivot->vote !== $vote) {
            $intervention->votes()->updateExistingPivot($user->id, ['vote' => $vote]);
        }
        
        $intervention = Intervention::find($id);

        return $intervention;
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

        $intervention = Intervention::answers()->find($id);
        $user = Auth::user();
        $validAux = $request->input('valid');
        $valid = null;
        if ($validAux == 'true') $valid = true;
        else if ($validAux == 'false') $valid = false;

        $this->authorize('valid', $intervention);

        
        if (is_null($valid)) {
            $oldUserValidation = $intervention->valid()->first();
            $intervention->valid()->detach($oldUserValidation->id_teacher);
        }
        else {
            $intervention->valid()->attach($user->id, ['valid' => $valid]);
        }
        return [$intervention, $valid];
    }
}
