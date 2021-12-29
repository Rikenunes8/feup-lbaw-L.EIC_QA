@extends('layouts.app')

@section('content')
<h2 class="text-center">Criar Questão</h2> 

<form method="POST" action="{{ route('questions.create') }}" id="form-question-create" class="row w-50 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="title" class="form-label">Titulo</label>
        <input type="text" id="title" class="form-control" name="title" required>
        @if ($errors->has('title'))
            @include('layouts.error', ['error' => $errors->first('title')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="text" class="form-label">Texto</label>
        <textarea rows="5" id="text-editor" class="form-control" name="text"></textarea>
        @if ($errors->has('text'))
            @include('layouts.error', ['error' => $errors->first('text')])
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="category" class="form-label">Unidade Curricular</label>
        <select id="category" class="form-select" name="category" aria-label="category" required>
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