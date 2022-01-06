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
     * Filter query questions according to url query request.
     * 
     * @param Request $request
     * @param $questions
     * @return $query
     */
    private function listFilter(Request $request, $questions) {
        $filter = $request->query('filter');
        $sort = $request->query('sort');
        $order = $request->query('order') == 'asc'? 'ASC' : 'DESC';
        $tags = $request->query('tags');
        
        if ($tags) {
            $questions = $questions->whereIn('category', $tags);
        }
        
        if ($filter == 'noAnswers') {
            $questions = $questions->has('childs', '=', 0);
        } 
        else if ($filter == 'noValidations') {
            $validations = DB::table('validation')->where('valid', true)->pluck('id_answer')->all();

            $questionsValidated = Intervention::questions()->whereHas('childs', function ($q1) use ($validations) {
                $q1->whereIn('id', $validations);
            });

            $questions = $questions->whereNotIn('id', $questionsValidated->pluck('id')->all());
        } 
        else if ($filter == 'withAnswers') {
            $questions = $questions->has('childs');
        } 
        else if ($filter == 'withValidations') {
            $validations = DB::table('validation')->where('valid', true)->pluck('id_answer')->all();

            $questions = $questions->whereHas('childs', function ($q1) use ($validations) {
                $q1->whereIn('id', $validations);
            });
        }


        if ($sort == 'date') {
            $questions = $questions->orderBy('date', $order);
        } else {
            $questions = $questions->orderBy('votes', $order);
        }

        return $questions;
    }

    /**
     * Shows all questions.
     * 
     * @param Request $request
     * @return Response
     */
    public function list(Request $request)
    {   
        $questions = $this::listFilter($request, Intervention::questions())->paginate(15);
        $ucs = Uc::orderBy('name')->get();

        return view('pages.questions', ['questions' => $questions, 'ucs' => $ucs]);
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
        if ($request->query('q')) {
            $search = $request->input('q');
            
            $questions = $questions->search($search);
            $questions = $questions->paginate(15);
        }
        else {
            $questions = $questions->orderBy('votes', 'DESC')->paginate(15);
        }
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
        $question = Intervention::questions()->find($id);
        if (is_null($question)) return redirect('/questions');
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

        $request->validate([
            'title' => 'required|max:255',
            'text' => 'required',
        ]);

        $question->id_author = Auth::user()->id;
        $question->title = $request->input('title');
        $question->text = $request->input('text');
        $question->category = $request->category;
        $question->type = 'question';
        $question->save();

        $uc = Uc::find($question->category);
        $followers = $uc->followers();
        $usersNotified = $uc->teachers()->union($followers);
        // $notification = Notification:: TODO
        event(new NotifyUsersEvent($usersNotified));

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

        $request->validate([
            'title' => 'required|max:255',
            'text' => 'required',
        ]);

        $question->title = $request->input('title');
        $question->text = $request->input('text');
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

        $request->validate([
            'text' => 'required', 
        ]);

        $answer->id_author = Auth::user()->id; 
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

        $request->validate([
            'text' => 'required', 
        ]);

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

        $request->validate([
            'text' => 'required', 
        ]);

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

        $request->validate([
            'text' => 'required', 
        ]);

        $answer = $comment->parent()->first();
        $comment->text = $request['text'];
        $comment->save(); 

        return redirect('questions/'.$answer->id_intervention);
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
        if (is_null($intervention) || is_null($user)) return App::abort(404);
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
        if (is_null($intervention) || is_null($user)) return App::abort(404);
        $validAux = $request->input('valid');
        $valid = null;
        if ($validAux == 'true') $valid = true;
        else if ($validAux == 'false') $valid = false;

        $this->authorize('valid', $intervention);

        if (is_null($valid)) {
            DB::table('validation')->where('id_answer', $intervention->id)->delete();
        }
        else {
            $intervention->valid()->attach($user->id, ['valid' => $valid]);
        }

        return [$intervention, $valid];
    }
}
