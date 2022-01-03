@extends('layouts.app')

@section('title', 'Página Inicial')

@section('content')
<section id="faq-page">
  <h2>Preguntas Frequentes</h2>

  <div class="accordion w-100 mt-3" id="faq-accordion">

    <div class="accordion-item">
      <h2 class="accordion-header" id="heading-question-one">
        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse"
          data-mdb-target="#question-one" aria-expanded="false">
          Preciso de andar na FEUP para usufruir da aplicação?
        </button>
      </h2>
      <div id="question-one" class="accordion-collapse collapse"
        aria-labelledby="heading-question-one" data-mdb-parent="#faq-accordion">
        <div class="accordion-body">
          <strong>Sim! Para fazeres ou responderes a questões. </strong> 
          Precisas de ter email institucional: <code>upXXXXXXXXX@g.uporto.pt</code> 
          ou <code>upXXXXXXXXX@f_.up.pt</code> para te conseguires registar.
        </div>
      </div>
    </div>

    <div class="accordion-item">
      <h2 class="accordion-header" id="heading-question-two">
        <button class="accordion-button collapsed" type="button" data-mdb-toggle="collapse"
          data-mdb-target="#question-two" aria-expanded="false">
          Como crio uma nova questão?
        </button>
      </h2>
      <div id="question-two" class="accordion-collapse collapse"
        aria-labelledby="heading-question-two" data-mdb-parent="#faq-accordion">
        <div class="accordion-body">
          Podes fazê-lo a partir do teu perfil e da página de questões. Desde que 
          te tenhas iniciado sessão à priori. 
        </div>
      </div>
    </div>
    
  </div>
</section>
@endsection
