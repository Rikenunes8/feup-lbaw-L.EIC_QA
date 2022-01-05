@extends('layouts.app')

@section('title', 'Resultados de Pesquisa')

@section('content')

<section id="search-page">

  <div class="row">
    <div class="col-12 user-action-add">
      <h2>Pesquisa</h2>
      @if (Auth::check() && !Auth::user()->isAdmin())
      <div>
        <a href="{{ url('questions/create') }}" class="btn btn-primary text-white">Nova Questão<i class="fas fa-plus ms-2"></i></a>
      </div>
      @endif
    </div>
  </div>
  @php
    $nQuestions = count($questions);
  @endphp
  @if ( $nQuestions != 0 )
  <div class="my-3">
    <span>{{ $nQuestions }} Resultados</span>
  </div>

  <div class="row"> 
    @each('partials.question', $questions, 'question')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {!! $questions->links() !!}
    </div>
  </div>
  @else 
  <p>Não existem Questões</p>
  @endif
  
</section>

@endsection