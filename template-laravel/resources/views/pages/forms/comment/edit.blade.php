@extends('layouts.app')

@section('title', 'Editar Comentário')

@section('content')
<h2 class="text-center">Editar Comentário</h2> 

<form method="POST" action="{{ route('comments.edit', $comment->id) }}" id="form-comment-edit" class="row w-75 mx-auto">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <label for="text" class="form-label required">Texto</label>
        <textarea rows="5" class="form-control" name="text">{{ $comment->text }}</textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>
</form>
@endsection