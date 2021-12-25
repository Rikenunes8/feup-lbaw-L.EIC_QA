@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="ucs-page">
  <div class="row"> 
    @each('partials.uc', $ucs, 'uc')
  </div>
  
</section>

@endsection