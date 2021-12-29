@extends('layouts.app')

@section('title', 'Questão')

@section('content')

<section id="question-page">
  <div class="row question-card" data-id="{{ $question->id }}"> 
    <div class="col-12 position-relative">
      <div class="float-end">
        <a href="{{ url('questions/create') }}" class="btn btn-primary text-white">Nova Questão <i class="fas fa-plus ms-2"></i></a>
      </div>
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
              @if ( Auth::check() )
              <div class="text-center question-page-actions p-3">
                @if ( !Auth::user()->isAdmin() )
                <a href="{{ url('questions/'.$question->id.'/answers/create') }}" class="btn btn-info text-black me-1"><i class="fas fa-reply"></i></a>
                @endif
                @if ( Auth::user()->id == $question->id_author )
                <a href="{{ url('questions/'.$question->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                @endif
                <a href="#" class="btn btn-dark text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                @if ( Auth::user()->id == $question->id_author || Auth::user()->isAdmin() )
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
                @endif
              </div>
              @endif
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
              @if ( Auth::check() )
              <div class="text-center question-card-icon p-3">
                @php
                  $check = 'fa-check';
                  $times = 'fa-times';
                  $isTeacherResponsible = $question->uc->teachers()->wherePivot('id_teacher', '=', Auth::user()->id)->exists();
                  
                  $valid = null;
                  foreach ($answer->valid as $validation) {
                    if ($validation->pivot->valid) $valid = true;
                    else $valid = false;
                  }
                @endphp

                @if ( is_null($valid) )
                  @if ( $isTeacherResponsible )
                  <a href="#" class="btn btn-success text-white me-1"> <i class="fas {{ $check }} "></i></a>
                  <a href="#" class="btn btn-danger text-white me-1"> <i class="fas {{ $times }} "></i></a>
                  @endif
                @else
                  @if ( $isTeacherResponsible )
                  <a href="#" class="btn {{ $valid ? 'btn-success' : 'btn-danger' }} text-white me-1"> <i class="fas {{ $valid ? $check : $times }} "></i></a>
                  @else
                  <i class="fas {{ $valid ? $check : $times }}"></i>
                  @endif
                @endif
              </div>
              <div class="text-center question-page-actions p-3">
                @if ( !Auth::user()->isAdmin() )
                <a href="{{ url('answers/'.$answer->id.'/comments/create') }}" class="btn btn-info text-black me-1"><i class="fas fa-reply"></i></a>
                @endif
                @if ( Auth::user()->id == $answer->id_author )
                <a href="{{ url('answers/'.$answer->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                @endif
                <a href="#" class="btn btn-dark text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                @if ( Auth::user()->id == $answer->id_author || Auth::user()->isAdmin() )
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
                @endif
              </div>
              @endif
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
              @if ( Auth::check() )
              <div class="text-center question-page-actions p-3">
                @if ( Auth::user()->id == $comment->id_author )
                <a href="{{ url('comments/'.$comment->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                @endif
                <a href="#" class="btn btn-dark text-white question-page-delete me-1"><i class="fas fa-exclamation-triangle"></i></a>
                @if ( Auth::user()->id == $comment->id_author || Auth::user()->isAdmin() )
                <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
                @endif
              </div>
              @endif
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