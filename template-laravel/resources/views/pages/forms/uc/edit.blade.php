@extends('layouts.app')

@section('content')
<h2 class="text-center">Editar UC</h2> 

<form method="POST" action="{{ route('ucs.edit', $uc->id) }}" id="form-uc-edit" class="row w-50 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="name" class="form-label required">Nome</label>
        <input type="text" id="name" class="form-control" name="name" value="{{ is_null(old('name'))?$uc->name:old('name') }}" required>
        @if ($errors->has('name'))
            @include('layouts.error', ['error' => $errors->first('name')])
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="code" class="form-label required">Sigla</label>
        <input type="text" id="code" class="form-control" name="code" value="{{ is_null(old('code'))?$uc->code:old('code') }}" required>
        @if ($errors->has('code'))
            @include('layouts.error', ['error' => $errors->first('code')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="description" class="form-label required">Descrição</label>
        <textarea rows="3" id="description" class="form-control" name="description" required>{{ is_null(old('description'))?$uc->description:old('description') }}</textarea>
        @if ($errors->has('description'))
            @include('layouts.error', ['error' => $errors->first('description')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>

    <div class="col-12 d-flex justify-content-center mt-3">
        <a href="{{ url('/admin/ucs/'.$uc->id.'/teachers') }}" class="app-link">Editar Docentes</a>
    </div>
</form>
@endsection
