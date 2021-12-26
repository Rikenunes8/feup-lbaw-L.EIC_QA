@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="ucs-page">
  <h2>Unidades Curriculares</h2> 

  <div class="row"> 
    @each('partials.uc', $ucs, 'uc')
  </div>
  
</section>

@endsection