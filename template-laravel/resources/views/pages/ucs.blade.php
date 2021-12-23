@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="cards">
  @each('partials.uc', $ucs, 'uc')
</section>

@endsection