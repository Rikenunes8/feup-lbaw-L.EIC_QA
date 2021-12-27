@extends('layouts.app')

@section('title', 'Unidades Curriculares')

@section('content')

<section id="uc-page">
  <div class="row uc-card" data-id="{{ $uc->id }}"> 
    <div class="col-12 position-relative">
      <h2 class="me-4">{{ $uc->name }}</h2> 
      <span class="badge bg-info text-dark mt-1 mb-2">{{ $uc->code }}</span>
      
      <p>{{ $uc->description }}</p>

      <div class="table-responsive">
      <table class="table table-striped table-bordered caption-top">
        <caption>Docentes da Unidade Curricular</caption>
        <thead>
          <tr>
            <th scope="col">Nome</th>
            <th scope="col">Email</th>
          </tr>
        </thead>
        <tbody>
          @php
            $teachers = $uc->teachers()->orderBy('name')->get();
          @endphp
          @foreach($teachers as $teacher)
            <tr>
              <th scope="row"><a href="{{ url('/users/'.$teacher->id) }}" class="app-link">{{ $teacher->name }}</a></th>
              <td>{{ $teacher->email }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
      </div>

      @if ( Auth::check() && Auth::user()->isStudent() )
      <p class="uc-card-icon pt-2 pe-4">
        
        <a href="#" class="uc-card-icon-follow">
          <i class="{{ in_array(Auth::user()->email, $uc->followers()->pluck('email', 'id')->toArray() )?'fas':'far' }} fa-heart text-danger"></i>
        </a>
        
      </p>
      @endif
    </div>
  </div>
</section>

@endsection
