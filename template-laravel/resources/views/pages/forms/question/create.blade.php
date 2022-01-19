@extends('layouts.app')

@section('title', 'Nova Questão')

@section('content')
<h2 class="text-center">Criar Questão</h2> 

<form method="POST" action="{{ route('questions.create') }}" id="form-question-create" class="row w-75 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="title" class="form-label required">Titulo</label>
        <input type="text" id="title" class="form-control" name="title" value="{{ is_null(old('title'))?'':old('title') }}" required>
        @if ($errors->has('title'))
            @include('layouts.error', ['error' => $errors->first('title')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="text" class="form-label required">Texto</label>
        <textarea id="text" rows="15" class="form-control text-editor" name="text">{{ is_null(old('text'))?'':old('text') }}</textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="category" class="form-label required">Unidade Curricular</label>
        <select id="category" class="form-select" name="category" aria-label="category" {{ is_null(old('category'))?'':'value="'.old('category').'"' }} required>
          @foreach ($ucs as $uc)
            <option value="{{ $uc->id }}">[ {{ $uc->code }} ] {{ $uc->name }}</option>
          @endforeach
        </select>
        @if ($errors->has('category'))
            @include('layouts.error', ['error' => $errors->first('category')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Adicionar</button>
    </div>
</form>
@endsection