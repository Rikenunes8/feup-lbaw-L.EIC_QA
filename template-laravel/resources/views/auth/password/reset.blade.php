@extends('layouts.app')

@section('title', 'Redifinir Password')

@section('content')
<h2 class="text-center">Redifinir Password</h2> 

<form  method="POST" action="{{ route('password.update') }}" id="form-reset-password" class="row w-50 mx-auto">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">

    <div class="mb-3 col-12">
        <label for="email_address" class="form-label required">E-mail</label>
        <input type="email" id="email_address" class="form-control" name="email" required autofocus>
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

    <div class="mb-3 col-12">
        <label for="password-confirm" class="form-label required">Confirme Password</label>
        <input type="password" id="password-confirm" class="form-control" name="password_confirmation" required>
        @if ($errors->has('password_confirmation'))
            @include('layouts.error', ['error' => $errors->first('password_confirmation')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Alterar Password</button>
    </div>
</form>
@endsection
