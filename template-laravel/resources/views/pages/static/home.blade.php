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

  <br>

  <h3 class="mb-4">Funcionalidades Principais</h3>
  <div class="row my-2">
    <div class="col-1 home-icons-icon text-end"><i class="fas fa-users text-primary"></i></div>
    <div class="col-11"><p>Regista-te e tira dúvidas com os outros estudantes e professores nesta plataforma destinada ao curso L.EIC!</p></div>
  </div>
  <div class="row my-2">
    <div class="col-11"><p class="text-end">Comunica com os outros estudantes ao criar, responder e comentar questões.</p></div>
    <div class="col-1 home-icons-icon"><i class="fas fa-pencil-alt text-primary"></i></div>
  </div>
  <div class="row my-2">
    <div class="col-1 home-icons-icon text-end"><i class="fas fa-vote-yea text-primary"></i></div>
    <div class="col-11"><p>Vota nas intervenções dos outros participantes e contribui para identificar os melhores utilizadores da plataforma.</p></div>
  </div>
  <div class="row my-2">
    <div class="col-11"><p class="text-end">Segue as tuas UCs preferidas para não perderes nenhuma questão relacionada com ela!</p></div>
    <div class="col-1 home-icons-icon"><i class="fas fa-heart text-primary"></i></div>
  </div>
  <div class="row my-2">
    <div class="col-1 home-icons-icon text-end"><i class="fas fa-bell text-primary"></i></div>
    <div class="col-11"><p>Recebe notificações para estares sempre a par e não perderes nada do que passa na plataforma.</p></div>
  </div>
  <div class="row my-2">
    <div class="col-11"><p class="text-end">Pesquisa questões através da barra de pesquisa ou aplicando os diferentes filtros!</p></div>
    <div class="col-1 home-icons-icon"><i class="fas fa-search text-primary"></i></div>
  </div>
</section>
@endsection
