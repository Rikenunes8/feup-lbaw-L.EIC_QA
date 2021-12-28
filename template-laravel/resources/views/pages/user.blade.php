@extends('layouts.app')

@section('title', 'Perfil')

@section('content')

<section id="user-page">
  <div class="row user-profile" data-id="{{ $user->id }}"> 
    <div class="col-12 position-relative">
      @if ( Auth::check() && Auth::user()->id == $user->id )
      <div class="float-end">
        <a href="{{ url('users/'.$user->id.'/edit') }}" class="btn btn-primary text-white"><i class="fas fa-edit"></i></a>
      </div>
      <h2 class="me-4">O meu Perfil</h2> 
      @else
      <h2 class="me-4">Perfil</h2> 
      @endif
      <div class="row mt-4">
        <div class="col-md-6 col-lg-4 mb-2 px-1">
          @if ( file_exists( asset('images/users/'.$user->photo) ) )
            <img src="{{ asset('images/users/'.$user->photo.'.jpg') }}" alt="profile-photo" id="profile-photo" class="w-75 h-75">
          @else
            <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo" id="profile-photo" class="w-75 h-75">
          @endif
        </div>
        <div class="col-md-6 col-lg-8 mb-2 px-1">
          <h3 class="me-4">{{ $user->name }}</h2> 
          <span class="badge bg-info text-dark mt-1 mb-2">{{ $user->type }}</span>
          <span class="mt-1 mb-2">Aderiu a {{ date('d/m/Y', strtotime($user->registry_date)); }}</span>
          <h4 class="mt-4">Sobre mim</h4>
          <div class="px-3"> 
            <p>{{ $user->about }}</p>
            @if (!is_null($user->birthdate))
            <p>Aniversário: {{ date('d/m/Y', strtotime($user->birthdate)); }}</p>
            @endif
          </div>
          <h4>Contactos</h4> 
          <div class="px-3">
            <p>Email: {{ $user->email }}</p>
          </div>
        </div>
      </div>
      <div class="row">
        <h3 class="me-4">As minhas Questões</h3>
        @php
          $questions = $user->interventions()->questions()->orderBy('votes', 'DESC')->get();
        @endphp
        @each('partials.question', $questions, 'question') <!-- TODO: 4th argument to view no elements -->
      </div>
      <div class="row">
        <h3 class="me-4">As minhas Respostas</h3>
        @php
          $answers = $user->interventions()->answers()->orderBy('votes', 'DESC')->get();
        @endphp
        @each('partials.answer', $answers, 'answer') <!-- TODO: 4th argument to view no elements -->
      </div>
    </div>
  </div>
</section>

@endsection
