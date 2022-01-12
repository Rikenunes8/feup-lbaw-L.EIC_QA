@extends('layouts.app')

@section('title', 'Administração Pedidos de Ativação de Contas')

@section('content')

<section id="admin-requests-page">
  <section class="error-msg"></section>

  <h2>Pedidos de Ativação de Contas</h2> 

  <div class="row mt-3 mb-4">
    <div class="col-12">
      <small class="text-mutted">Gestão de pedidos para ativação de contas</small>
    </div>
  </div>

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered">
          <thead>
            <tr>
              <th scope="col"></th>
              <th scope="col">Tipo</th>
              <th scope="col">Nome</th>
              <th scope="col">Email</th>
              <th scope="col">Username</th>
              <th scope="col">Data do Pedido</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr data-id="{{ $user->id }}" data-details="{{ $user }}">
                <td class="{{ ($user->isAdmin()?'':'expand-button') }}">{{ ($user->isAdmin()?'':'+') }}</td> 
                <td>
                  @php 
                    $bg = 'bg-info';
                    if ($user->isTeacher()) $bg = 'bg-warning';
                    elseif ($user->isAdmin()) $bg = 'bg-danger';
                  @endphp
                  <span class="badge {{ $bg }} text-dark">{{ $user->type }}</span>
                </td>
                <th scope="row">{{ $user->name }}</th>
                <td>{{ $user->email }}</td>
                <td>{{ $user->username }}</td>
                <td>{{ date('d/m/Y H:i', strtotime($user->registry_date)) }}</td>
                <td class="text-center admin-table-user-actions">
                  <section class="actions-buttons">
                    <button type="button" class="btn btn-success text-white me-1 block-btn" data-toogle="tooltip" title="Ativar Conta" data-bs-toggle="modal" data-bs-target="#activeUser{{ $user->id }}Modal">
                      <i class="fas fa-check"></i>
                    </button>
                    <button type="button" class="btn btn-danger text-white" data-toogle="tooltip" title="Rejeitar Pedido" data-bs-toggle="modal" data-bs-target="#deleteUser{{ $user->id }}Modal">
                      <i class="fas fa-times"></i>
                    </button>
                  </section>
                  
                  <section class="actions-modals">
                    @include('partials.modal', ['id' => 'activeUser'.$user->id.'Modal', 
                                                'title' => 'Activar '.$user->email , 
                                                'body' => 'Tem a certeza que quer aceitar o pedido para ativação da conta deste Utilizador?',
                                                'href' => '#',
                                                'action' => 'admin-table-active',
                                                'cancel' => 'Cancelar',
                                                'confirm' => 'Sim'])

                    @include('partials.modal', ['id' => 'deleteUser'.$user->id.'Modal', 
                                                'title' => 'Eliminar '.$user->email , 
                                                'body' => 'Tem a certeza que quer recusar permanentemente o pedido para ativação da conta deste Utilizador?',
                                                'href' => '#',
                                                'action' => 'admin-table-delete',
                                                'cancel' => 'Cancelar',
                                                'confirm' => 'Sim'])
                  </section>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
  
</section>

@endsection