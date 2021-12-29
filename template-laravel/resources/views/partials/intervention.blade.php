<section class="intervention-detail {{ $intervention->type }}-detail {{ $intervention->isComment()?'mt-2':'mt-3' }} {{ $intervention->isQuestion()?'':'d-flex flex-row-reverse' }}" data-id="{{ $intervention->id }}">
  <div class="row">
    <div class="col-1 intervention-votes">
      @if (!$intervention->isComment())
      <a href="#" class="app-link intervention-vote intervention-upvote"><h3 class="text-center">&#x25B2;</h3></a>
      <h3 class="text-center intervention-votes-number">{{ $intervention->votes }}</h3>
      <a href="#" class="app-link intervention-vote intervention-downvote"><h3 class="text-center">&#x25BC;</h3></a>
      @endif
    </div>
    <div class="col-11 card">
      <div class="card-body">

        <p>{!! $intervention->text !!}</p>
        @if (!$intervention->isQuestion())
        <p class="text-muted mb-0">{{ date('d/m/Y H:i', strtotime($intervention->date)); }}, por {{ $intervention->author->username }}</p>
        @endif
        
        @if ( Auth::check() )

          @if ($intervention->isAnswer())
            <div class="text-center question-card-icon p-3">
              @php
                $check = 'fa-check';
                $times = 'fa-times';
                $isTeacherResponsible = $intervention->parent->uc->teachers()->wherePivot('id_teacher', '=', Auth::user()->id)->exists();
                
                $valid = null;
                foreach ($intervention->valid as $validation) {
                  if ($validation->pivot->valid) $valid = true;
                  else $valid = false;
                }
              @endphp

              @if ( is_null($valid) )
                @if ( $isTeacherResponsible )
                <a href="#" class="btn btn-success text-white me-1"> <i class="fas {{ $check }} "></i></a>
                <a href="#" class="btn btn-danger text-white me-1"> <i class="fas {{ $times }} "></i></a>
                @endif
              @else
                @if ( $isTeacherResponsible )
                <a href="#" class="btn {{ $valid ? 'btn-success' : 'btn-danger' }} text-white me-1"> <i class="fas {{ $valid ? $check : $times }} "></i></a>
                @else
                <i class="fas {{ $valid ? $check.' question-valid-icon' : $times.' question-invalid-icon' }}"></i>
                @endif
              @endif
            </div>
          @endif

          <div class="text-center question-page-actions p-3">
            @if ( !Auth::user()->isAdmin() && !$intervention->isComment())
              @if ($intervention->isQuestion())
                <a href="#answer-form" class="btn btn-primary text-white me-1"><i class="fas fa-reply"></i></a>
              @else
                <button class="btn btn-primary text-white me-1" onclick="showCommentCreateForm(this)" value="{{ $intervention->id }}"><i class="fas fa-reply"></i></button>
              @endif
            @endif
            @if ( Auth::user()->id == $intervention->id_author )
            <a href="{{ url($intervention->type.'s/'.$intervention->id.'/edit') }}" class="btn btn-warning text-black me-1"><i class="far fa-edit"></i></a>
            @endif
            <a href="#" class="btn btn-dark text-white question-page-report me-1"><i class="fas fa-exclamation-triangle"></i></a>
            @if ( Auth::user()->id == $intervention->id_author || Auth::user()->isAdmin() )
            <a href="#" class="btn btn-danger text-white question-page-delete me-1"><i class="far fa-trash-alt"></i></a>
            @endif
          </div>
        @endif
      </div>  
    </div>
  </div>
</section>

@if (!$intervention->isComment())
  @each('partials.intervention', $intervention->childs, 'intervention')

  @if ($intervention->isAnswer())
    
    <section class="comment-detail mt-2 d-none comment-answer-form-{{ $intervention->id }}">
      <div class="row">
        <div class="col-1">
        </div>
        <div class="col-11">
        <hr>
        @include('pages.forms.comment.create', ['answer' => $intervention])
        <hr>  
      </div>
    </section>
  @endif
@endif