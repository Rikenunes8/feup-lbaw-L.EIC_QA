@extends('layouts.app')

@section('title', 'Verificar Email')

@section('content')

@if (Session::has('success'))
    <div class="my-3 py-2  alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
        {{ Session::get('success') }}
    </div>
@endif

<h2>Verificação de Email</h2>

<p>Antes de continuar, verifique que recebeu no seu email um link de verificação!</p>
<p>Se não recebeu nenhum email, clique no botão sequinte.</p>

<form method="POST" action="{{ route('verification.request') }}">
    @csrf
    <button type="submit" class="btn btn-primary">Reenviar Email</button>
</form>

@endsection
