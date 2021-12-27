@extends('layouts.app')

@section('title', 'Administração Docentes UC')

@section('content')

<section id="admin-uc-teachers-page">
  <h2>Docentes</h2> 

  <p>Gestão de Docentes da Unidade Curricular: {{ $uc->name }} <span class="badge bg-info text-dark">{{ $uc->code }}</span></p>

  <!-- TODO dropdown de possibilidades e adiconar botao -->
  <!-- API add and delete -->

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered caption-top">
          <thead>
            <tr>
              <th scope="col">Nome</th>
              <th scope="col">Email</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @if (count($teachers) != 0)
              @foreach($teachers as $teacher)
              <tr data-id="{{ $teacher->id }}">
                <th scope="row"><a href="{{ url('/users/'.$teacher->id) }}" class="app-link">{{ $teacher->name }}</a></td>
                <td>{{ $teacher->email }}</td>
                <td class="admin-table-teacher-actions">
                  <a href="#" class="btn btn-danger text-white admin-table-delete"><i class="far fa-trash-alt"></i></a>
                </td>
              </tr>
              @endforeach
            @else
              <tr>
                <td colspan="3">Nenhum Docente</td>
              </tr>
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>

@endsection
