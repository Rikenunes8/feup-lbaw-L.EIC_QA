@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="ucs-page" data-id="{{ Auth::check()?Auth::user()->id:''}}">
  <div class="float-end">
    <form method="GET" action="{{ url('/ucs') }}">
      <div class="input-group mb-0">
        <input type="search" id="search-ucs-input" class="form-control" placeholder="Pesquisar Unidade Curricular..." aria-label="Search UC" name="search">
      
        <span class="input-group-text">
          @include('partials.help', ['placement' => 'bottom', 'content' => 'Pesquise por uma Unidade Curricular em específico através do seu Nome!'])
        </span>
      </div>
    </form>
  </div>
  <h2>
    Unidades Curriculares
    @if ( Auth::check() && Auth::user()->isStudent() )
      @include('partials.help', ['placement' => 'right', 'title' => 'Opção de Seguir', 'content' => 'Ao clicar no coração começa a seguir uma Unidade Curricular (coração cheio). Para deixar de a seguir basta clicar outra vez (coração vazio). Quando segue uma UC passa a ser notificado de todas intervenções que lhe dizem respeito.'])
    @elseif ( Auth::check() && Auth::user()->isTeacher() )
      @include('partials.help', ['placement' => 'right', 'content' => 'Como docente é automaticamente notificado de todas as intervenções relativas às Unidades Curriculares que leciona.'])
    @endif
  </h2> 

  <div class="error-msg"></div>

  @if (count($ucs) != 0)
  <div class="row"> 
    @each('partials.uc', $ucs, 'uc')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {{ $ucs->appends(['search' => isset($search) ? $search : ''])->links() }}
    </div>
  </div>
  @else
  <p>Não existem Unidades Curriculares</p>
  @endif
  
</section>

@endsection