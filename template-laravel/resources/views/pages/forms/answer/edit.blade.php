@extends('layouts.app')

@section('content')
<h2 class="text-center">Editar Resposta</h2> 

<form method="POST" action="{{ route('answers.edit', $answer->id) }}" id="form-answer-edit" class="row w-75 mx-auto">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <label for="text" class="form-label required">Texto</label>
        <textarea rows="20" class="form-control text-editor" name="text">{{ $answer->text }}</textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>
</form>
@endsection