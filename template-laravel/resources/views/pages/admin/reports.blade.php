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
              <th scope="col">Título</th>
              <th scope="col">Texto</th>
              <th scope="col">Autor</th>
              <th scope="col">Ações</th>
            </tr>
          </thead>
          <tbody>
            @foreach($reports as $report)
            <tr data-id="{{ $report->id }}">
              @php 
              $intervention = $report->intervention;
              if ($intervention->isQuestion())    { $question = $intervention;                 $type = "Questão";    }
              else if ($intervention->isAnswer()) { $question = $intervention->parent;         $type = "Resposta";   }
              else                                { $question = $intervention->parent->parent; $type = "Comentário"; }
              @endphp
              <th scope="row"><a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="app-link">{{ $intervention->id }}</a></th>
              <td>{{ $type }}</td>
              <td><a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="app-link">
                @php
                  if ($intervention->isQuestion()) $title = $intervention->title;
                  else $title = '';
                @endphp
                {!! substr($title, 0, 25) !!}
                @if (strlen($title) > 25)
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
                  {!! substr($str, 0, 30) !!}
                  @if (strlen($str) > 30)
                  ...
                  @endif
                </a>
              </td>
              <td><a href="{{ url('/users/'.$intervention->author->id) }}" class="app-link">{{ $intervention->author->name }}</a></td>
              <td class="text-center admin-table-reports-actions">
                <section class="actions-buttons">
                  <a href="{{ url('/questions/'.$question->id.'#'.$intervention->id) }}" class="btn btn-info text-black me-1"><i class="fas fa-glasses"></i></a>
                  <a href="#" class="btn btn-danger text-white reports-page-remove me-1"><i class="far fa-trash-alt"></i></a>
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
