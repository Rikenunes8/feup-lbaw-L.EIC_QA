@extends('layouts.app')

@section('title', 'Perfil')

@section('content')

<section id="user-page">
  <div class="row user-card" data-id="{{ $user->id }}"> 
    <div class="col-12 position-relative">
      @if ( Auth::check() && Auth::user()->id == $user->id )
        <h2 class="me-4">O meu Perfil</h2> 
      @else
        <h2 class="me-4">Perfil</h2> 
      @endif
      <div>
        <h3 class="me-4">{{ $user->name }}</h2> 
        <span class="badge bg-info text-dark mt-1 mb-2">{{ $user->type }}</span>
        <span class="mt-1 mb-2">Aderiu a {{ date('d/m/Y', strtotime($user->registry_date)); }}</span>
        <h4>Sobre mim</h4> 
        <p>{{ $user->about }}</p>
        <p>Aniversário: {{ date('d/m/Y', strtotime($user->birthdate)); }}</p>
        <h4>Contactos</h4> 
        <p>Email: {{ $user->email }}</p>
      </div>
      <div class="row">
        <h3 class="me-4">As minhas Questões</h3>
        @php
          $questions = $user->interventions()->questions()->orderBy('votes', 'DESC')->get();
        @endphp
        @each('partials.question', $questions, 'question') <!-- TODO: 4th argument to view no elements -->
      </div>
    </div>
  </div>
</section>

@endsection
