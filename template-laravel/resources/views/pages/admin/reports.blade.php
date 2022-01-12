@extends('layouts.app')

@section('title', 'Administração Denúncias')

@section('content')

<section id="admin-reports-page">
  <h2>Denúncias</h2> 

  <div class="row mt-3 mb-5">
    <div class="col-12">
      <small class="text-mutted">Gestão de Intervenções Reportadas</small>
    </div>
  </div>

  <div class="row"> 
    <div class="col-12">
      <div class="table-responsive">
        <table id="admin-table" class="table table-striped table-bordered caption-top">
          <thead>
            <tr>
              <th scope="col">ID</th>
              <th scope="col">Tipo</th>
              <th scope="col">Título da Questão</th>
              <th scope="col">Texto</th>
              <th scope="col">Autor da Intervenção</th>
              <th scope="col">Autor da Denúncia</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reports as $report)
            <tr data-id="{{ $report->id }}">
              @php 
                $intervention = $report->intervention;
                if ($intervention->isQuestion())    { $question = $intervention;                 $type = 'Questão';    $bg = 'bg-danger'; }
                else if ($intervention->isAnswer()) { $question = $intervention->parent;         $type = 'Resposta';   $bg = 'bg-warning'; }
                else                                { $question = $intervention->parent->parent; $type = 'Comentário'; $bg = 'bg-info'; }
              @endphp
              <th scope="row"><a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="app-link">{{ $intervention->id }}</a></th>
              <td><span class="badge {{ $bg }} text-dark">{{ $type }}</span></td>
              <td><a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="app-link">
                {!! substr($question->title, 0, 35) !!}
                @if (strlen($question->title) > 35)
                ...
                @endif
                </a>
              </td>
              <td><a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="app-link">
                  @php
                    $str = str_replace("<p>", "", $intervention->text);
                    $str = str_replace("</p>", " ", $str);
                    $str = str_replace("&nbsp;", "", $str);
                  @endphp
                  {!! substr($str, 0, 70) !!}
                  @if (strlen($str) > 70)
                  ...
                  @endif
                </a>
              </td>
              <td>
                <a href="{{ url('/admin/users?searchDt='.$intervention->author->email) }}" class="app-link" data-toogle="tooltip" title="Gerir Utilizador"><i class="fas fa-user-cog"></i></a>
                <a href="{{ url('/users/'.$intervention->author->id) }}" class="app-link" data-toogle="tooltip" title="Visitar Perfil">{{ $intervention->author->username }}</a>
              </td>
              <td>
                <a href="{{ url('/admin/users?searchDt='.$report->user->email) }}" class="app-link" data-toogle="tooltip" title="Gerir Utilizador"><i class="fas fa-user-cog"></i></a>
                <a href="{{ url('/users/'.$report->user->id) }}" class="app-link" data-toogle="tooltip" title="Visitar Perfil">{{ $report->user->username }}</a>
              </td>
              <td class="text-center admin-table-reports-actions">
                <section class="actions-buttons">
                  <a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="btn btn-info text-black me-1" data-toogle="tooltip" title="Visitar Intervenção"><i class="fas fa-search	"></i></a>
                  <a class="btn btn-danger text-white reports-page-remove me-1" data-toogle="tooltip" title="Descartar Denúncia"><i class="far fa-trash-alt"></i></a>
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
