@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="ucs-page">
  <h2>Unidades Curriculares</h2> 

  <section class="error-msg"></section>

  @if (count($ucs) != 0)
  <div class="row"> 
    @each('partials.uc', $ucs, 'uc')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {!! $ucs->links() !!}
    </div>
  </div>
  @else
  <p>Não existem Unidades Curriculares</p>
  @endif
  
</section>

@endsection