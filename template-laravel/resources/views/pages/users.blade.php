@extends('layouts.app')

@section('title', 'Utilizadores')

@section('content')

<section id="users-page">
  <h2>Utilizadores</h2> 

  @if (count($users) != 0)
  <div class="row"> 
    @each('partials.user', $users, 'user')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {!! $users->links() !!}
    </div>
  </div>
  @else
  <p>Não existem Utilizadores</p>
  @endif
  
</section>

@endsection