@extends('layouts.app')

@section('title', 'Página Sobre')

@section('content')
<section id="about-page">
  <div class="row">
    <div class="col-lg-8">
      <h2>Motivação</h2>
      <p>A nossa motivação para o projeto passa por auxiliar os alunos do curso de L.EIC 
        criando uma plataforma que reúne num único local todas as dúvidas das várias 
        unidades curriculares que constituem este curso.</p>
      
      <h2>Utilidade</h2>
      <p>Esta aplicação será útil para alunos e professores que terão a possibilidade 
        de participar ativamente nesta plataforma de perguntas e respostas, categorizadas 
        por unidade curricular.</p>
    </div>
    <div class="col-lg-4 mb-3">
      <img src="{{ asset('images/about.png') }}" class="img-fluid" alt="About Page Q&A">
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <h2>Objetivos</h2> 
    </div>
  </div>

  <section id="about-page-objetives" class="py-3 text-center">
    <div class="row">

      <div class="col-md-4">
        <div class="about-icons-item mx-auto mb-2">
          <div class="about-icons-icon d-flex">
            <i class="fas fa-layer-group m-auto text-primary"></i>
          </div>
        </div>
        <h5 class="text-uppercase">Reunir</h5>
        <p class="mb-0">Encontrar num único local todas as dúvidas para as diferentes cadeiras do curso de L.EIC.</p>
      </div>

      <div class="col-md-4">
        <div class="about-icons-item mx-auto mb-2">
          <div class="about-icons-icon d-flex">
            <i class="fas fa-graduation-cap m-auto text-primary"></i>
          </div>
        </div>
        <h5 class="text-uppercase">Esclarecer</h5>
        <p class="mb-0">Disponibilizar um espaço seguro e completo para por todo o tipo de dúvidas.</p>
      </div>

      <div class="col-md-4">
        <div class="about-icons-item mx-auto mb-2">
          <div class="about-icons-icon d-flex">
            <i class="fas fa-award m-auto text-primary"></i>
          </div>
        </div>
        <h5 class="text-uppercase">Validar</h5>
        <p class="mb-0">Construir um mecanismo de validação de respostas por parte dos professores e reporte de intervenções.</p>
      </div>

    </div>
  </section>

</section>
@endsection
