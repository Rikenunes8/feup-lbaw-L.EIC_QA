@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="ucs-page" data-id="{{ Auth::check()?Auth::user()->id:''}}">
  <div class="float-end">
    <form method="GET" action="{{ url('/ucs') }}">
      <input type="search" id="search-ucs-input" class="form-control" placeholder="Pesquisa UC..." aria-label="Search UC" name="search">
    </form>
  </div>
  <h2>Unidades Curriculares</h2> 

  <section class="error-msg"></section>

  @if (count($ucs) != 0)
  <div class="row"> 
    @each('partials.uc', $ucs, 'uc')
  </div>

  <div class="row">
    <div class="col-12 d-flex justify-content-end">
      {{ $ucs->appends(['search' => isset($search) ? $search : ''])->links() }}
    </div>
  </div>
  @else
  <p>Não existem Unidades Curriculares</p>
  @endif
  
</section>

@endsection