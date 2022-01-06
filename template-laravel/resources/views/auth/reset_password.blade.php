@extends('layouts.app')

@section('title', 'Alterar Password')

@section('content')

<div class="flex-center position-ref full-height">
    <form class="form-container" action="api/password/reset" method="POST">
        <h2>Alterar Password?</h2>

        <input name="email" placeholder="Inserir email" value="{{request()->get('email')}}">
        <input name="password" placeholder="Nova password">
        <input name="password_confirmation" placeholder="Confirmar nova password">
        <input hidden name="token" placeholder="token" value="{{request()->get('token')}}">

        <button type="submit">Alterar</button>
    </form>
</div>
@endsection