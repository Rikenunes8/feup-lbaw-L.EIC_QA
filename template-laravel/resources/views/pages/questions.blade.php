@extends('layouts.app')

@section('title', 'Questões')

@section('content')

<section id="questions-page">
  <h2>Questões</h2> 

  <div class="row"> 
    @each('partials.question', $questions, 'question')
  </div>
  
</section>

@endsection