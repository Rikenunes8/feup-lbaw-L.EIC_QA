@extends('layouts.app')

@section('title', 'Questão')

@section('content')

<section id="question-page">
  <div class="row question-card" data-id="{{ $question->id }}"> 
    <div class="col-12 position-relative">
      @if ( Auth::check() && !Auth::user()->isAdmin())
      <div class="float-end">
        <a href="{{ url('questions/create') }}" class="btn btn-primary text-white">Nova Questão <i class="fas fa-plus ms-2"></i></a>
      </div>
      @endif
      <h2 class="me-4">{{ $question->title }}</h2> 
      <span class="badge bg-info text-dark mt-1 mb-2">{{ $question->uc->code }}</span>
      <span class="text-muted">{{ date('d/m/Y H:i', strtotime($question->date)); }}, por {{ $question->author->username }}</span>
      

      @include('partials.intervention', ['intervention' => $question])

      @if (Auth::check() && !Auth::user()->isAdmin())
      <br><hr>
      <section id="answer-form" class="mt-4">
        @include('pages.forms.answer.create', ['question' => $question])
      </section>
      @endif

    </div>
  </div>
</section>

@endsection