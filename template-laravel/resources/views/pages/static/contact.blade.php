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
        <div class="error-msg"></div>
        <form id="form-contact" name="form-contact" data-toggle="validator">
          <div class="mb-3">
            <label for="name" class="form-label required">Nome</label>
            <input type="text" id="name" class="form-control" name="name" required data-error="Introduza o seu Nome">
          </div>
          <div class="mb-3">
            <label for="email" class="form-label required">E-mail</label>
            <input type="email" id="email" class="form-control" name="email" required data-error="Introduza o seu E-mail">
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label required">Assunto</label>
            <input type="text" id="subject" class="form-control" name="subject" required data-error="Introduza o Assunto da sua mensagem">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label required">Mensagem</label>
            <textarea rows="3" id="message" class="form-control" name="message" required data-error="Introduza a sua mensagem"></textarea>
          </div>

          @csrf

          <a class="btn btn-primary btn-block submit_email">Enviar</a>
        </form>
    </div>
  </div>

  <div class="row">

  </div>
</section>
@endsection
