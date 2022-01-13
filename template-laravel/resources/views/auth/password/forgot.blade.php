@extends('layouts.app')

@section('title', 'Recuperar Password')

@section('content')
<h2 class="text-center">Recuperar Password</h2> 

<form  method="POST" action="{{ route('password.email') }}" id="form-forgot-password" class="row w-50 mx-auto">
    @csrf

    @if (Session::has('message'))
        <div class="my-3 py-2  alert alert-success alert-dismissible fade show" role="alert">
            <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
            {{ Session::get('message') }}
        </div>
    @endif

    <div class="mb-3 col-12">
        <label for="email_address" class="form-label required">E-mail</label>
        <input type="email" id="email_address" class="form-control" name="email" required autofocus>
        @if ($errors->has('email'))
            @include('layouts.error', ['error' => $errors->first('email')])
        @endif
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary btn-block">Recuperar Password</button>
    </div>
</form>
@endsection
