@extends('layouts.app')

@section('content')
<form method="POST" action="{{ route('login') }}" id="form-login" class="row w-50 mx-auto">
    {{ csrf_field() }}

    <div class="mb-3 col-12">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" id="email" class="form-control" name="email" value="{{ old('email') }}" required autofocus>
        @if ($errors->has('email'))
        <div class="mt-1 py-2  alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('email') }}
        </div>
        @endif
    </div>
    
    <div class="mb-3 col-12">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" class="form-control" name="password" required>
        @if ($errors->has('password'))
        <div class="mt-1 py-2 alert alert-danger alert-dismissible fade show">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ $errors->first('password') }}
        </div>
        @endif
    </div>

    <div class="col-12">
        <div class="mb-3 form-check">
            <input type="checkbox" id="remember" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">Manter sessão iniciada</label>
        </div>
    </div>
    <div class="col-12">
        <button type="submit" class="btn btn-primary">Iniciar Sessão</button>
    </div>
</form>
@endsection
