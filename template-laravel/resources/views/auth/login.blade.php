@extends('layouts.app')

@section('title', 'Iniciar Sessão')

@section('content')
<h2 class="text-center">Login</h2>

<form method="POST" action="{{ route('login') }}" id="form-login" class="row w-50 mx-auto">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <label for="email" class="form-label required">E-mail</label>
        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
            @include('layouts.error', ['error' => $errors->first('email')])
        @endif
    </div>

    <div class="mb-3 col-12">
        <label for="password" class="form-label required">Password</label>
        <input type="password" id="password" class="form-control" name="password" required>
        @if ($errors->has('password'))
            @include('layouts.error', ['error' => $errors->first('password')])
        @endif
    </div>

    <div class="col-12 mb-3">
        <div class="form-check">
            <input type="checkbox" id="remember" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Manter sessão iniciada</label>
        </div>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Iniciar Sessão</button>
    </div>

    <div class ="col-12" style="padding-top:5px;">
        <a href="{{ route('google.login') }}" class="btn btn-google btn-user btn-block">
            <i class="fab fa-google fa-fw"></i> Iniciar Sessão com Google
        </a>
    </div>

    <div class="col-12 d-flex justify-content-center mt-3">
        <a href="{{ url('/recover') }}" class="app-link">Recuperar Password?</a>
    </div>
</form>
@endsection
