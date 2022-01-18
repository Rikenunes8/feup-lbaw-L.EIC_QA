<section id="{{ $intervention->id }}" class="intervention-detail {{ $intervention->type }}-detail {{ $intervention->isComment()?'mt-2 comment-parent-'.$intervention->parent->id:'mt-3' }} {{ $intervention->isQuestion()?'':'d-flex flex-row-reverse' }}" data-id="{{ $intervention->id }}">
  <div class="row">
      @if ($intervention->isAnswer())
        <div class="col-1 question-details-validation-icon">
        @php 
          $check = 'fa-check';
          $times = 'fa-times';

          $validations = DB::table('validation')->where('id_answer', $intervention->id)->get();
          $valid = null;
          foreach ($validations as $validation) {
            if ($validation->valid) $valid = true;
            else $valid = false;
          }

          if (Auth::check()) {
            $isTeacherResponsible = $intervention->parent->uc->teachers()->wherePivot('id_teacher', '=', Auth::user()->id)->exists();
          }
        @endphp
        @if ( !is_null($valid) &&  ( !Auth::check() || (Auth::check() && !$isTeacherResponsible) || (Auth::check() && Auth::user()->id == $intervention->id_author) ) )
          <i class="fas {{ $valid ? $check.' question-valid-icon' : $times.' question-invalid-icon' }}"></i>
        @endif
        </div>
      @endif
    <div class="col-1 intervention-votes">
      @if (!$intervention->isComment())
      <a class="app-link intervention-vote intervention-upvote"><h3 class="text-center">&#x25B2;</h3></a>
      <h3 class="text-center intervention-votes-number">{{ $intervention->votes }}</h3>
      <a class="app-link intervention-vote intervention-downvote"><h3 class="text-center">&#x25BC;</h3></a>
      @endif
    </div>
    <div class="{{ $intervention->isAnswer() ? 'col-10':'col-11' }} card">
      <div class="card-body ps-2 pe-1">

        @if ( $intervention->isAnswer() && Auth::check() && $isTeacherResponsible && Auth::user()->id != $intervention->id_author )
          <div class="text-center question-card-icon-validate p-0 ms-1">

            @if ( is_null($valid) )
              <a class="btn btn-outline-success text-success me-1 validate-valid" data-toogle="tooltip" title="Validar"> <i class="fas {{ $check }} "></i></a>
              <a class="btn btn-outline-danger text-danger me-1 validate-invalid" data-toogle="tooltip" title="Invalidar"> <i class="fas {{ $times }} "></i></a>
            @else
              <a class="btn {{ $valid ? 'btn-success text-white invalidate' : 'btn-outline-success text-success validate-valid' }} me-1" data-toogle="tooltip" title="{{ $valid ? 'Remover Validação' : 'Validar' }}"> <i class="fas {{ $check }} "></i></a>
              <a class="btn {{ $valid ? 'btn-outline-danger text-danger validate-invalid' : 'btn-danger text-white invalidate' }} me-1" data-toogle="tooltip" title="{{ $valid ? 'Invalidar' : 'Remover Invalidação' }}"> <i class="fas {{ $times }} "></i></a>
            @endif
          </div>
        @endif

        <p>{!! $intervention->text !!}</p>
        @if (!$intervention->isQuestion())
        <p class="text-muted mb-0">{{ date('d/m/Y H:i', strtotime($intervention->date)); }}, por 
          @if (is_null($intervention->author))
            Anónimo
          @else
            <a href="{{ url('users/'.$intervention->author->id) }}" class="app-link">{{ $intervention->author->username }}</a>
          @endif
        </p>
        @endif
        
        @if ( Auth::check() )
           
          <div class="text-center question-page-actions p-3">
            @if ( !Auth::user()->isAdmin() && !$intervention->isComment())
              @if ($intervention->isQuestion())
                <a href="#answer-form" class="btn btn-primary text-white me-1" data-toogle="tooltip" title="Responder" onclick="focusAnswerInput()"><i class="fas fa-reply"></i></a>
              @else
                <a class="btn btn-primary text-white me-1" data-toogle="tooltip" title="Responder" data-value="{{ $intervention->id }}" onclick="showCommentCreateForm(this)"><i class="fas fa-reply"></i></a>
              @endif
            @endif
            @if ( Auth::user()->id == $intervention->id_author )
            <a href="{{ url($intervention->type.'s/'.$intervention->id.'/edit') }}" class="btn btn-warning text-black me-1" data-toogle="tooltip" title="Editar"><i class="far fa-edit"></i></a>
            @endif
            <button type="button" class="btn btn-dark text-white me-1" data-toogle="tooltip" title="Denunciar" data-bs-toggle="modal" data-bs-target="#reportIntervention{{ $intervention->id }}Modal">
              <i class="fas fa-exclamation-triangle"></i>
            </button>
            @if ( Auth::user()->id == $intervention->id_author || Auth::user()->isAdmin() ) 
            <button type="button" class="btn btn-danger text-white me-1" data-toogle="tooltip" title="Eliminar" data-bs-toggle="modal" data-bs-target="#deleteIntervention{{ $intervention->id }}Modal">
              <i class="far fa-trash-alt"></i>
            </button>
            @endif
          </div>

          <div class="question-page-actions-modals">
            @include('partials.modal', ['id' => 'reportIntervention'.$intervention->id.'Modal', 
                                        'title' => 'Denunciar '.$intervention->title , 
                                        'body' => 'Tem a certeza que quer denunciar esta Intervenção?',
                                        'href' => '#',
                                        'action' => 'question-page-report',
                                        'cancel' => 'Cancelar',
                                        'confirm' => 'Sim'])

            @if ( Auth::user()->id == $intervention->id_author || Auth::user()->isAdmin() ) 
              @include('partials.modal', ['id' => 'deleteIntervention'.$intervention->id.'Modal', 
                                          'title' => 'Eliminar '.$intervention->title , 
                                          'body' => 'Tem a certeza que quer eliminar permanentemente esta Intervenção?',
                                          'href' => '#',
                                          'action' => 'question-page-delete',
                                          'cancel' => 'Cancelar',
                                          'confirm' => 'Sim'])
            @endif
          </div> 

        @endif
      </div>  
    </div>
  </div>
  @if ($intervention->isQuestion())
  <hr>
  @endif
</section>

@if (!$intervention->isComment())

  @if ($intervention->isAnswer())
    <section id="comment-answer-form-{{ $intervention->id }}" class="comment-detail mt-2 comment-parent-{{ $intervention->id }} d-none">
      <div class="row">
        <div class="col-1"></div>
        <div class="col-11">
          <hr>
          <button type="button" class="btn-close btn-sm float-end" data-value="{{ $intervention->id }}" onclick="showCommentCreateForm(this)"></button>
          @include('pages.forms.comment.create', ['answer' => $intervention])
          <hr>  
        </div>
      </div>
    </section>

    @each('partials.intervention', $intervention->childs()->orderBy('date', 'DESC')->get(), 'intervention')
  @else 
    @each('partials.intervention', $intervention->childs()->orderBy('votes', 'DESC')->get(), 'intervention')
  @endif
@endif