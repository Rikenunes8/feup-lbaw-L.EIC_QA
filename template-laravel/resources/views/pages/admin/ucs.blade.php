@extends('layouts.app')

@section('title', 'Administração UCs')

@section('content')

<section id="admin-ucs-page">
  <h2>Unidades Curriculares</h2> 

  <div class="row mt-3 mb-5">
    <div class="col-12 admin-action-add">
      <small class="text-mutted">Gestão de Unidadades Curriculares</small>
      <a href="{{ url('ucs/create') }}" class="btn btn-primary text-white"><i class="fas fa-plus"></i></a>
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
              <th scope="row"><a href="{{ url('/ucs/'.$uc->id) }}" class="app-link">{{ $uc->name }}</a></td>
              <td>{{ $uc->code }}</td>
              <td>
                {{ substr($uc->description, 0, 50) }}
                @if (strlen($uc->description) > 50)
                ...
                @endif
              </td>
              <td class="admin-table-uc-actions">
                <a href="{{ url('admin/ucs/'.$uc->id.'/teachers') }}" class="btn btn-info text-black me-1"><i class="far fa-address-card"></i></a>
                <a href="{{ url('ucs/'.$uc->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
                <a href="#" class="btn btn-danger text-white admin-table-delete"><i class="far fa-trash-alt"></i></a>
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
