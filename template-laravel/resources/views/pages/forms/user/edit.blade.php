@extends('layouts.app')

@section('content')
<h2 class="text-center">Editar Perfil</h2> 

<form method="POST" action="{{ route('users.edit', $user->id) }}" id="form-user-edit" class="row" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="mb-3 col-12 col-lg-6">
        <label for="email" class="form-label required">Email</label>
        <input type="email" id="email" class="form-control" name="email" value='{{ $user->email }}' disabled>
    </div>
    
    <div class="mb-3 col-12 col-lg-6">
        <label for="username" class="form-label required">Username</label>
        <input type="text" id="username" class="form-control" name="username" value='{{ $user->username }}' required>
        @if ($errors->has('username'))
            @include('layouts.error', ['error' => $errors->first('username')])
        @endif
    </div>

    <div class="mb-3 col-12 col-lg-6">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" class="form-control" name="password">
        @if ($errors->has('password'))
            @include('layouts.error', ['error' => $errors->first('password')])
        @endif
    </div>

    <div class="mb-3 col-12 col-lg-6">
        <label for="confirm" class="form-label">Repetir Password</label>
        <input type="password" id="confirm" class="form-control" name="confirm">
        @if ($errors->has('confirm'))
            @include('layouts.error', ['error' => $errors->first('confirm')])
        @endif
    </div>
   
    @if (!Auth::user()->isAdmin())
    <div class="col-12 col-lg-6">
        <div class="mb-3 row">
            <div class="col-12">
                <label for="name" class="form-label required">Nome</label>
                <input type="text" id="name" class="form-control" name="name" value='{{ $user->name }}' required>
                @if ($errors->has('name'))
                    @include('layouts.error', ['error' => $errors->first('name')])
                @endif
            </div>
        </div>

        <div class="mb-3 row">
            <div class="col-12">
                <label for="photo" class="form-label">Foto</label>
                <input type="file" id="photo" class="form-control" name="photo" value='{{ $user->photo }}'>
                @if ($errors->has('photo'))
                    @include('layouts.error', ['error' => $errors->first('photo')])
                @endif
            </div>
        </div>

        <div class="mb-3 row">
            <div class="col-12">
                <label for="birthdate" class="form-label">Aniversário</label>
                <input type="datetime-local" id="birthdate" class="form-control" name="birthdate" value="{{ is_null($user->birthdate)?'':date('Y-m-d\TH:i', strtotime($user->birthdate)) }}">
                @if ($errors->has('birthdate'))
                    @include('layouts.error', ['error' => $errors->first('birthdate')])
                @endif
            </div>
        </div>
    </div>
    
    <div class="mb-3 col-12 col-lg-6">
        <label for="about" class="form-label">Sobre mim</label>
        <textarea rows="8" id="about" class="form-control" name="about">{{ $user->about }}</textarea>
        @if ($errors->has('about'))
            @include('layouts.error', ['error' => $errors->first('about')])
        @endif
    </div>
    @endif
    
    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar</button>
    </div>
</form>
@endsection
