@extends('layouts.app')

@section('title', 'Editar Questão')

@section('content')
<h2 class="text-center">Editar Questão</h2> 

<form method="POST" action="{{ route('questions.edit', $question->id) }}" id="form-question-edit" class="row w-75 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="title" class="form-label required">Titulo</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ is_null(old('title'))?$question->title:old('title') }}" required>
        @if ($errors->has('title'))
            @include('layouts.error', ['error' => $errors->first('title')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="text" class="form-label required">Texto</label>
        <textarea id="text" rows="15" class="form-control text-editor" name="text">{{ is_null(old('text'))?$question->text:old('text') }}</textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="category" class="form-label">Unidade Curricular</label>
        <select id="category" class="form-select" name="category" aria-label="category" disabled>
            <option value="{{ $question->category }}" selected>[ {{ $question->uc->code }} ] {{ $question->uc->name }}</option>
        </select>
        @if ($errors->has('category'))
            @include('layouts.error', ['error' => $errors->first('category')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>
</form>
@endsection