@extends('layouts.app')

@section('title', 'Utilizadores')

@section('content')

<section id="users-page">
  <div class="float-end">
    <form method="GET" action="{{ url('/users') }}">
      <input type="search" id="search-users-input" class="form-control" placeholder="Pesquisar Utilizador..." aria-label="Search User" name="search">
    </form>
  </div>
  <h2>Utilizadores</h2> 

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