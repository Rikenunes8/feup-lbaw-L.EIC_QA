@extends('layouts.app')

@section('title', 'Administração Docentes UC')

@section('content')

<section id="admin-uc-teachers-page">
  <h2>Docentes de <a href="{{ url('ucs/'.$uc->id) }}" class="badge bg-info text-dark">{{ $uc->code }}</a></h2> 

  <p>Gestão de Docentes da Unidade Curricular: {{ $uc->name }}</p>

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered caption-top" data-id="{{ $uc->id }}">
          <thead>
            <tr>
              <th scope="col">Nome</th>
              <th scope="col">Email</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody>
            @if (count($teachersAssoc) == 0 && count($teachersNotAssoc) == 0)
              <tr>
                <td colspan="3">Nenhum Docente</td>
              </tr>
            @else
              @foreach($teachersAssoc as $teacher)
              <tr data-id="{{ $teacher->id }}">
                <th scope="row"><a href="{{ url('/users/'.$teacher->id) }}" class="app-link">{{ $teacher->name }}</a></th>
                <td>{{ $teacher->email }}</td>
                <td class="text-center admin-table-teacher-actions">
                  <a href="#" class="btn btn-danger text-white admin-table-delete"><i class="fas fa-minus"></i> <span class="d-none">Eliminar</span></a>
                </td>
              </tr>
              @endforeach

              @foreach($teachersNotAssoc as $teacher)
              <tr data-id="{{ $teacher->id }}">
                <th scope="row"><a href="{{ url('/users/'.$teacher->id) }}" class="app-link">{{ $teacher->name }}</a></td>
                <td>{{ $teacher->email }}</td>
                <td class="text-center admin-table-teacher-actions">
                  <a href="#" class="btn btn-primary text-white admin-table-add"><i class="fas fa-plus"></i> <span class="d-none">Adicionar</span></a>
                </td>
              </tr>
              @endforeach
            @endif
          </tbody>
        </table>
      </div>
    </div>
  </div>

</section>

@endsection
