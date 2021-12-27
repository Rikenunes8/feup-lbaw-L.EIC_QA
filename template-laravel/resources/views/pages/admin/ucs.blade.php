@extends('layouts.app')

@section('title', 'Administração UCs')

@section('content')

<section id="admin-ucs-page">
  <h2>Unidades Curriculares</h2> 

  <div class="row"> 
    <div class="col-12">

      <table id="admin-ucs-table" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
        <thead>
          <tr>
            <th class="th-sm">Nome</th>
            <th class="th-sm">Sigla</th>
            <th class="th-sm">Descrição</th>
            <th class="th-sm">Ações</th>
          </tr>
        </thead>
        <tbody>
          @foreach($ucs as $uc)
          <tr>
            <th scope="row"><a href="{{ url('/ucs/'.$uc->id) }}" class="app-link">{{ $uc->name }}</a></td>
            <td>{{ $uc->code }}</td>
            <td>{{ substr($uc->description, 0, 50) }}
                @if (strlen($uc->description) > 50)
                ...
                @endif
            </td>
            <!-- mais um com o dropdown de lista dedocentes? -->
            <td>Editar/eliminar/editarprofessores</td>
          </tr>
          @endforeach
        </tbody>
      </table>

    </div>
  </div>

  <!-- bitao nova uc -->
  
</section>

@endsection
