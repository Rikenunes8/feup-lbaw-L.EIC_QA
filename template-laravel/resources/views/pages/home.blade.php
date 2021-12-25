@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<section id="home-page">
  <h2>L.EIC Q&A</h2> 

  <div class="bg-image">
    <img src="{{ asset('images/home-QA.png') }}" class="img-fluid" alt="HomePage Q&A">
   
    <div class="mask">
      <div class="d-flex align-items-start h-100 mask-white">
        <p class="m-2">Plataforma onde toda a comunidade do curso L.EIC pode discutir e apresentar as suas dúvidas relativas às diferentes cadeiras.</p>
      </div>
    </div>
  </div>
</section>

@endsection
