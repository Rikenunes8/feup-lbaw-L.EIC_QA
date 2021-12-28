@extends('layouts.app')

@section('title', 'Questão')

@section('content')

<section id="question-page">
  <div class="row question-card" data-id="{{ $question->id }}"> 
    <div class="col-12 position-relative">
      <h2 class="me-4">{{ $question->title }}</h2> 
      <span class="badge bg-info text-dark mt-1 mb-2">{{ $question->uc->code }}</span>
      <span class="text-muted">{{ date('d/m/Y H:i', strtotime($question->date)); }}, por {{ $question->author->username }}</span>
      
      <section class="question-detail">
        <div class="row">
          <div class="col-1">
            <a><h3 class="text-center">&#x25B2;</h3></a>
            <h3 class="text-center">{{ $question->votes }}</h3>
            <a><h3 class="text-center">&#x25BC;</h3></a>
          </div>
          <div class="col-11 card">
            <div class="card-body">
              <p>{{ $question->text }}</p>
              <div class="text-center question-page-actions">
                <a href="{{ url('questions/'.$question->id.'/answers/create') }}" class="btn btn-info text-black me-1"><i class="fas fa-reply"></i></a>
                <a href="{{ url('questions/'.$question->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
              </div>
            </div>          
          </div>
        </div>
      </section>
      
      @foreach($question->childs as $answer)
      <section class="answer-detail mt-3 d-flex flex-row-reverse">
        <div class="row">
          <div class="col-1">
            <a><h3 class="text-center">&#x25B2;</h3></a>
            <h3 class="text-center">{{ $answer->votes }}</h3>
            <a><h3 class="text-center">&#x25BC;</h3></a>
          </div>
          <div class="col-11 card">
            <div class="card-body">
              <p>{{ $answer->text }}</p>
              <p class="text-muted mb-0">{{ date('d/m/Y H:i', strtotime($answer->date)); }}, por {{ $answer->author->username }}</p>
              <div class="text-center question-page-actions">
                <a href="{{ url('questions/'.$question->id.'/answers/create') }}" class="btn btn-info text-black me-1"><i class="fas fa-reply"></i></a>
                <a href="{{ url('questions/'.$question->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
              </div>
            </div>          
          </div>
        </div>
      </section>

      @foreach($answer->childs as $comment)
      <section class="comment-detail mt-2 d-flex flex-row-reverse">
        <div class="row">
          <div class="col-1">
          </div>
          <div class="col-11 card">
            <div class="card-body">
              <p>{{ $comment->text }}</p>
              <p class="text-muted mb-0">{{ date('d/m/Y H:i', strtotime($comment->date)); }}, por {{ $comment->author->username }}</p>
              <div class="text-center question-page-actions">
                <a href="{{ url('questions/'.$question->id.'/answers/create') }}" class="btn btn-info text-black me-1"><i class="fas fa-reply"></i></a>
                <a href="{{ url('questions/'.$question->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
              </div>
            </div>
          </div>
        </div>
      </section>
      @endforeach
      @endforeach


    </div>
  </div>
</section>

@endsection