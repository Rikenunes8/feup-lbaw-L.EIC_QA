@extends('layouts.app')

@section('content')
<h2 class="text-center">Criar Nova UC</h2> 

<form method="POST" action="{{ route('ucs.create') }}" id="form-uc-create" class="row w-50 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="name" class="form-label required">Nome</label>
        <input type="text" id="name" class="form-control" name="name" required>
        @if ($errors->has('name'))
            @include('layouts.error', ['error' => $errors->first('name')])
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="code" class="form-label required">Sigla</label>
        <input type="text" id="code" class="form-control" name="code" required>
        @if ($errors->has('code'))
            @include('layouts.error', ['error' => $errors->first('code')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="description" class="form-label required">Descrição</label>
        <textarea rows="3" id="description" class="form-control" name="description" required></textarea>
        @if ($errors->has('description'))
            @include('layouts.error', ['error' => $errors->first('description')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Adicionar</button>
    </div>
</form>
@endsection
