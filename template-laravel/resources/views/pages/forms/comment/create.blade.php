<h5>O meu Comentário</h5>

<form method="POST" action="{{ route('comments.create', $answer->id) }}" class="row mt-3">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <textarea rows="2" class="form-control" name="text"></textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Comentar</button>
    </div>
</form>