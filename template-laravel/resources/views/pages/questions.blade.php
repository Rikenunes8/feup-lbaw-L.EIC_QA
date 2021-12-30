@extends('layouts.app')

@section('title', 'Questões')

@section('content')

<section id="questions-page">

  <div class="row">
    <div class="col-12 user-action-add">
      <h2>Questões</h2>
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
  <div class="row">
    <div class="col-12">
      <span>{{ $nQuestions }} Questões</span>
      <button class="btn btn-info text-white mx-5" onclick="showFilterForm(this)"><i class="fas fa-filter"></i></button>
    </div>
    <div class="col-12 card filter-card bg-info my-3 d-none">
      <form class="filter-form">
        <div class="d-flex justify-content-around my-3">
          <div>
            <p>Filtrar por:</p>
            <div>
              <input type="radio" name="filter" value="none" checked>
              <label>Nenhum filtro</label>
            </div>
            <div>
              <input type="radio" name="filter" value="noAnswers">
              <label>Sem respostas</label>
            </div>
            <div>
              <input type="radio" name="filter" value="noValidations">
              <label>Sem validadações</label>
            </div>
          </div>
          
          <div>
            <p>Ordenar por:</p>
            <div>
              <input type="radio" name="sort" value="votes" checked>
              <label>Votos</label>
            </div>
            <div>
              <input type="radio" name="sort" value="date">
              <label>Data</label>
            </div>
            <div>
              <input type="radio" name="order" value="asc">
              <label>&#8593;</label>
              <input type="radio" name="order" value="desc" checked>
              <label>&#8595;</label>
            </div>
          </div>
          
          <div>
            <p>Escolher UCs:</p>
            <div class="dropdown">
              <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownTags" data-bs-toggle="dropdown">UCs</button>
              <ul class="dropdown-menu">
                @foreach ( $ucs as $uc )
                <li class="dropdown-item"><label><input type="checkbox" name="tags[]" class="mx-2" value="{{ $uc->id }}">{{ $uc->name }}</label></li>
                @endforeach
              </ul>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between my-3 mx-5">
          <input type="submit" class="btn btn-success" value="Apply">
          <div class="btn btn-danger text-white me-1" onclick="showFilterForm(this)"> <i class="fas fa-times"></i></div>
        </div>
      </form>
    </div>
  </div>
  <hr>
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