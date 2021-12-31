@extends('layouts.app')

@section('title', 'Administração Utilizadores')

@section('content')

<section id="admin-ucs-page">
  <h2>Utilizadores</h2> 

  <div class="row mt-3 mb-4">
    <div class="col-12">
      <small class="text-mutted">Gestão de Utilizadores</small>
    </div>
  </div>

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th scope="col">Tipo</th>
              <th scope="col">Nome</th>
              <th scope="col">Email</th>
              <th scope="col">Razão Bloqueio</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr data-id="{{ $user->id }}">
                <td>
                  @php 
                    $bg = 'bg-info';
                    if ($user->isTeacher()) $bg = 'bg-warning';
                    elseif ($user->isAdmin()) $bg = 'bg-danger';
                  @endphp
                  <span class="badge {{ $bg }} text-dark">{{ $user->type }}</span>
                </td>
                <th scope="row"><a href="{{ url('/users/'.$user->id) }}" class="app-link">{{ $user->name }}</a></th>
                <td>{{ $user->email }}</td>
                <td>
                  <input type="text" name="reason" value="{{ $user->block_reason }}" {{ is_null($user->block_reason)?'':'disabled' }}>
                </td>
                <td class="text-center admin-table-user-actions">
                  @if (is_null($user->block_reason))
                    <a href="#" class="btn btn-info text-dark me-1 admin-table-block"><i class="fas fa-lock"></i><span class="d-none">Lock</span></a>
                  @else 
                    <a href="#" class="btn btn-warning text-dark me-1 admin-table-block"><i class="fas fa-unlock"></i><span class="d-none">Unlock</span></a>
                  @endif
                  <a href="#" class="btn btn-danger text-white admin-table-delete"><i class="far fa-trash-alt"></i></a>
                </td>
              </tr><span class="d-none">Eliminar</span>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
</section>

@endsection