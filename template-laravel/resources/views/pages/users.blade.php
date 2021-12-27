@extends('layouts.app')

@section('title', 'Utilizadores')

@section('content')

<section id="users-page">
  <h2>Utilizadores</h2> 

  <div class="row"> 
    @each('partials.user', $users, 'user')
  </div>
  
</section>

@endsection