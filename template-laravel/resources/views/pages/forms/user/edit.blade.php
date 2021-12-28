@extends('layouts.app')

@section('content')
<h2 class="text-center">Editar Perfil</h2> 

<form method="POST" action="{{ route('users.edit', $user->id) }}" id="form-user-edit" class="row w-50 mx-auto">
    {{ csrf_field() }}
    
    <div class="mb-3 col-12">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" class="form-control" name="username" value='{{ $user->username }}' disabled>
    </div>

    <div class="mb-3 col-12">
        <label for="email" class="form-label">Email</label>
        <input type="text" id="email" class="form-control" name="email" value='{{ $user->email }}' disabled>
    </div>

    <div class="mb-3 col-12">
        <label for="name" class="form-label">Nome</label>
        <input type="text" id="name" class="form-control" name="name" value='{{ $user->name }}' required>
        @if ($errors->has('name'))
        <div class="mt-1 py-2  alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('name') }}
        </div>
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="about" class="form-label">Sobre mim</label>
        <textarea rows="3" id="about" class="form-control" name="about">{{ $user->about }}</textarea>
        @if ($errors->has('about'))
        <div class="mt-1 py-2 alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('about') }}
        </div>
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="birthdate" class="form-label">Aniversário</label>
        <input type="date" id="birthdate" class="form-control" name="birthdate" placeholder="dd/mm/yyyy" value="{{ is_null($user->birthdate) ? '' : date('Y-m-d', strtotime($user->birthdate)); }}">
        @if ($errors->has('birthdate'))
        <div class="mt-1 py-2 alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('birthdate') }}
        </div>
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="photo" class="form-label">Foto</label>
        <input type="text" id="photo" class="form-control" name="photo" value='{{ $user->photo }}'>
        @if ($errors->has('photo'))
        <div class="mt-1 py-2  alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('photo') }}
        </div>
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>
</form>
@endsection
