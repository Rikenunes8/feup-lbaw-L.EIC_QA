@extends('layouts.app')

@section('title', 'Utilizadores')

@section('content')

<section id="users-page">
  <div class="float-end">
    <form method="GET" action="{{ url('/users') }}">
      <div class="input-group mb-0">
        <input type="search" id="search-users-input" class="form-control" placeholder="Pesquisar Utilizador..." aria-label="Search User" name="search">
      
        <span class="input-group-text">
          @include('partials.help', ['placement' => 'bottom', 'content' => 'Pesquise por um Utilizador em específico através do seu Nome!'])
        </span>
      </div>
    </form>
  </div>
  <h2>
    Utilizadores
    @include('partials.help', ['placement' => 'right', 'title' => 'Pontuação de um Utilizador', 'content' => 'Os utilizadores são distinguidos e ordenados na plataforma pelos pontos que têm. A pontuação de um utilizador reflete a soma dos votos de todas as suas intervenções.'])
  </h2> 

  @if (count($users) != 0)
  <div class="row"> 
    @each('partials.user', $users, 'user')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {{ $users->appends(['search' => isset($search) ? $search : ''])->links() }}
    </div>
  </div>
  @else
  <p>Não existem Utilizadores</p>
  @endif
  
</section>

@endsection