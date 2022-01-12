@extends('layouts.app')

@section('title', 'Administração UCs')

@section('content')

<section id="admin-ucs-page">
  <h2>Unidades Curriculares</h2> 

  <div class="row mt-3 mb-5">
    <div class="col-12 admin-action-add">
      <small class="text-mutted">Gestão de Unidadades Curriculares</small>
      <a href="{{ url('ucs/create') }}" class="btn btn-primary text-white" data-toogle="tooltip" title="Nova UC"><i class="fas fa-plus"></i></a>
    </div>
  </div>

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered caption-top">
          <thead>
            <tr>
              <th scope="col">Nome</th>
              <th scope="col">Sigla</th>
              <th scope="col">Descrição</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($ucs as $uc)
            <tr data-id="{{ $uc->id }}">
              <th scope="row"><a href="{{ url('/ucs/'.$uc->id) }}" class="app-link">{{ $uc->name }}</a></th>
              <td>{{ $uc->code }}</td>
              <td>
                @php
                  $str = str_replace("<p>", "", $uc->description);
                  $str = str_replace("</p>", " ", $str);
                  $str = str_replace("&nbsp;", "", $str);
                @endphp
                {!! substr($str, 0, 50) !!}
                @if (strlen($str) > 50)
                ...
                @endif
              </td>
              <td class="text-center admin-table-uc-actions">
                <section class="actions-buttons">
                  <a href="{{ url('admin/ucs/'.$uc->id.'/teachers') }}" class="btn btn-info text-black me-1" data-toogle="tooltip" title="Gerir os seus docentes"><i class="far fa-address-card"></i></a>
                  <a href="{{ url('ucs/'.$uc->id.'/edit') }}" class="btn btn-warning text-black me-1" data-toogle="tooltip" title="Editar"><i class="far fa-edit"></i></a>
                  
                  <button type="button" class="btn btn-danger text-white" data-toogle="tooltip" title="Eliminar" data-bs-toggle="modal" data-bs-target="#deleteUc{{ $uc->id }}Modal">
                    <i class="far fa-trash-alt"></i>
                  </button>
                </section>
                  
                <section class="actions-modals">
                  @include('partials.modal', ['id' => 'deleteUc'.$uc->id.'Modal', 
                                              'title' => 'Eliminar '.$uc->name , 
                                              'body' => 'Tem a certeza que quer eliminar permanentemente esta Unidade Curricular?',
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
