@extends('layouts.app')

@section('content')
<h2 class="text-center">Editar UC</h2> 

<form method="POST" action="{{ route('ucs.edit', $uc->id) }}" id="form-uc-edit" class="row w-50 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="name" class="form-label">Nome</label>
        <input type="text" id="name" class="form-control" name="name" value='{{ $uc->name }}' required>
        @if ($errors->has('name'))
        <div class="mt-1 py-2  alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('name') }}
        </div>
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="code" class="form-label">Sigla</label>
        <input type="text" id="code" class="form-control" name="code" value='{{ $uc->code }}' required>
        @if ($errors->has('code'))
        <div class="mt-1 py-2 alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('code') }}
        </div>
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="description" class="form-label">Descrição</label>
        <textarea rows="3" id="description" class="form-control" name="description" required>{{ $uc->description }}</textarea>
        @if ($errors->has('description'))
        <div class="mt-1 py-2 alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('description') }}
        </div>
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
