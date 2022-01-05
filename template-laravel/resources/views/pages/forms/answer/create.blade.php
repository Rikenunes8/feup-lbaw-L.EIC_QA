<h5>A minha Resposta</h5>

<form method="POST" action="{{ route('answers.create', $question->id) }}" class="row mt-3">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <textarea rows="10" id="answer-textarea" class="form-control text-editor" name="text"></textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Responder</button>
    </div>
</form>