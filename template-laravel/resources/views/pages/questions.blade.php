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

  <div class="row"> 
    @each('partials.question', $questions, 'question')
  </div>
  
</section>

@endsection