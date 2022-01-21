@extends('layouts.app')

@section('title', 'Página Contactos')

@section('content')
<section id="contact-page">
  <div class="row">
    <div class="col-md-12">
      <h2>Entre em Contacto Connosco</h2>
    </div>
  </div>

  <div class="row">
    <div class="col-md-6 mt-3">
      <p>Aplicação desenvolvida no âmbito da Unidade Curricular Laboratório de Bases de Dados e Aplicações Web.</p>

      <div class="">
        <b>Organização</b>:  <a href="https://sigarra.up.pt/feup/pt/web_page.inicial" target="blank" class="app-link">FEUP</a>
      </div>
      <div class="">
        <b>Grupo</b>: lbaw2185
      </div>
      <div class="">
        <b>Elementos</b>:
        <ul class="list-unstyled ms-3">
         <li>Henrique Nunes, up201906852@up.pt</li>
         <li>Patrícia Oliveira, up201905427@up.pt</li>
         <li>Alexandre Afonso, up201805455@up.pt</li>
         <li>Tiago Antunes, up201805327@up.pt</li>
        </ul>
      </div>
    </div>

    <div class="col-md-6 mt-3 email-form">

      @if (Session::has('success'))
      <div class="my-3 py-2 alert alert-success alert-dismissible fade show" role="alert">
        <button type="button" class="h-auto btn-close btn-sm" data-bs-dismiss="alert"></button>  
        {{ Session::get('success') }}
      </div>
      @endif

      <form method="POST" action="{{ route('send-contact') }}" id="form-contact" name="form-contact" data-toggle="validator">
        @csrf

        <div class="mb-3">
          <label for="name" class="form-label required">Nome</label>
          <input type="text" id="name" class="form-control" name="name" required data-error="Introduza o seu Nome">
          @if ($errors->has('name'))
              @include('layouts.error', ['error' => $errors->first('name')])
          @endif
        </div>

        <div class="mb-3">
          <label for="email" class="form-label required">E-mail</label>
          <input type="email" id="email" class="form-control" name="email" required data-error="Introduza o seu E-mail">
          @if ($errors->has('email'))
              @include('layouts.error', ['error' => $errors->first('email')])
          @endif
        </div>

        <div class="mb-3">
          <label for="subject" class="form-label required">Assunto</label>
          <input type="text" id="subject" class="form-control" name="subject" required data-error="Introduza o Assunto da sua mensagem">
          @if ($errors->has('subject'))
              @include('layouts.error', ['error' => $errors->first('subject')])
          @endif
        </div>

        <div class="mb-3">
          <label for="message" class="form-label required">Mensagem</label>
          <textarea rows="3" id="message" class="form-control" name="message" required data-error="Introduza a sua mensagem"></textarea>
          @if ($errors->has('message'))
              @include('layouts.error', ['error' => $errors->first('message')])
          @endif
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-block">Enviar</button>
        </div>
      </form>
    </div>
  </div>

  <div class="row">

  </div>
</section>
@endsection
