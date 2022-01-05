@extends('layouts.app')

@section('title', 'Administração Utilizadores')

@section('content')

<section id="admin-ucs-page">
  <section class="error-msg"></section>

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
                  @if (!$user->isAdmin())
                  <input type="text" name="reason" value="{{ $user->block_reason }}" {{ is_null($user->block_reason)?'':'disabled' }}>
                  @endif
                </td>
                <td class="text-center admin-table-user-actions">
                  <section class="actions-buttons">
                    @if (!$user->isAdmin())
                      @if (is_null($user->block_reason))
                        <button type="button" class="btn btn-info text-dark me-1 block-btn" data-bs-toggle="modal" data-bs-target="#blockUser{{ $user->id }}Modal">
                          <i class="fas fa-lock"></i><span class="d-none">Lock</span>
                        </button>
                      @else 
                        <button type="button" class="btn btn-dark text-white me-1 block-btn" data-bs-toggle="modal" data-bs-target="#blockUser{{ $user->id }}Modal">
                          <i class="fas fa-unlock"></i><span class="d-none">Unlock</span>
                        </button>
                      @endif
                    @endif
                    <a href="{{ url('users/'.$user->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                    
                    <button type="button" class="btn btn-danger text-white" data-bs-toggle="modal" data-bs-target="#deleteUser{{ $user->id }}Modal">
                      <i class="far fa-trash-alt"></i>
                    </button>
                  </section>
                  
                  <section class="actions-modals">
                    @include('partials.modal', ['id' => 'blockUser'.$user->id.'Modal', 
                                                'title' => 'Bloqueio '.$user->name , 
                                                'body' => 'Tem a certeza que quer alterar o estado de bloqueio deste Utilizador?',
                                                'href' => '#',
                                                'action' => 'admin-table-block',
                                                'cancel' => 'Cancelar',
                                                'confirm' => 'Sim'])

                    @include('partials.modal', ['id' => 'deleteUser'.$user->id.'Modal', 
                                                'title' => 'Eliminar '.$user->name , 
                                                'body' => 'Tem a certeza que quer eliminar permanentemente este Utilizador?',
                                                'href' => '#',
                                                'action' => 'admin-table-delete',
                                                'cancel' => 'Cancelar',
                                                'confirm' => 'Sim'])
                  </section>
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