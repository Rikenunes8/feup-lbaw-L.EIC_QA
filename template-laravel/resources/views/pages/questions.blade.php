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
      <button class="btn btn-info text-white me-3" onclick="showFilterForm(this)">Filtrar<i class="fas fa-filter ms-2"></i></button>
      <span>{{ $nQuestions }} Questões</span>
    </div>
    <div class="col-12">
      <div class="card filter-card bg-light my-3 d-none">
        <div class="card-body">
          <form class="filter-form">
            <button type="button" class="btn-close btn-sm float-end" onclick="showFilterForm(this)"></button>

            <div class="row">
              <div class="col-12 col-sm-6 col-md-3 text-center">
                  <div class="text-start mx-auto mt-3 filter-parameter">
                    <p>Filtrar por:</p>
                    <div>
                      <input type="radio" id="none" name="filter" value="none" >
                      <label class="form-check-label" for="none">Nenhum filtro</label>
                    </div>
                    <div>
                      <input type="radio" id="noAnswers" name="filter" value="noAnswers" >
                      <label class="form-check-label" for="noAnswers">Sem respostas</label>
                    </div>
                    <div>
                      <input type="radio" id="noValidations" name="filter" value="noValidations" >
                      <label class="form-check-label" for="noValidations">Sem validadações</label>
                    </div>
                  </div>
              </div>
           
              <div class="col-12 col-sm-6 col-md-3 text-center">
                <div class="text-start mx-auto mt-3 filter-parameter">
                  <p>Ordenar por:</p>
                  <div>
                    <input type="radio" id="votes" name="sort" value="votes" >
                    <label class="form-check-label" for="votes">Votos</label>
                  </div>
                  <div>
                    <input type="radio" id="date" name="sort" value="date" >
                    <label class="form-check-label" for="date">Data</label>
                  </div>
                </div>
              </div>

              <div class="col-12 col-sm-6 col-md-3 text-center">
                <div class="text-start mx-auto mt-3 filter-parameter">
                  <p>Ordem:</p>
                  <div>
                    <input type="radio" id="asc" name="order" value="asc" >
                    <label class="form-check-label" for="asc">&#8593; Ascendente</label>
                  </div>
                  <div>
                    <input type="radio" id="desc" name="order" value="desc" >
                    <label class="form-check-label" for="desc">&#8595; Descendente</label>
                  </div>
                </div>
              </div>
              
              <div class="col-12 col-sm-6 col-md-3 text-center">
                <div class="text-start mx-auto mt-3 filter-parameter">
                  <p>Escolher UCs:</p>
                  <div class="dropdown dropdown-keep-open">
                    <button class="btn btn-white dropdown-toggle" type="button" id="dropdownTags" data-bs-toggle="dropdown">UCs</button>
                    <ul class="dropdown-menu">
                      @foreach ( $ucs as $uc )
                        <li class="dropdown-item">
                          <input type="checkbox" id="uc-{{ $uc->id }}" name="tags[]" class="mx-2" value="{{ $uc->id }}">
                          <label for="uc-{{ $uc->id }}" class="w-100">{{ $uc->name }}</label>
                        </li>
                      @endforeach
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <div class="d-flex justify-content-end">
              <input type="submit" class="btn btn-success" value="Aplicar">
            </div>
          </form>
        </div>
      </div>
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