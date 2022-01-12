<div class="col-12 mb-2">
  <div class="card question-card h-100" data-id="{{ $answer->id }}">
    <div class="card-body">
      
      <div class="row">
        <div class="col-1">
          <span class="question-card-score text-dark w-100 h-100">
            {{ $answer->votes }}<br>votos

            @php
              $icon = '';
              $validations = DB::table('validation')->where('id_answer', $answer->id)->get();
              foreach ($validations as $validation) {
                if ($validation->valid) $icon = 'fa-check question-valid-icon';
                else $icon = 'fa-times question-invalid-icon';
              }
            @endphp
            @if ($icon != '') 
            <br>
            <i class="fas {{ $icon }}"></i>
            @endif
          </span>
        </div>

        <div class="col-11 p-relative">
          <h5 class="card-title"><a href="{{ url('/questions/'.$answer->parent->id) }}" class="app-link">{{ $answer->parent->title }}</a></h5>
          <h6 class="card-subtitle mt-1 mb-2">
            <a href="{{ url('ucs/'.$answer->parent->uc->id) }}" class="badge bg-info text-dark">{{ $answer->parent->uc->code }}</a>
            <span class="text-muted">{{ date('d/m/Y H:i', strtotime($answer->parent->date)); }}, por 
              @if (is_null($answer->parent->author))
                Anónimo
              @else
                <a href="{{ url('users/'.$answer->parent->author->id) }}" class="app-link">{{ $answer->parent->author->username }}</a>
              @endif
            </span>
          </h6>
          <h6 class="card-subtitle mt-3 mb-2">
            <b>Resposta:</b><br>
            <span class="text-muted">{{ date('d/m/Y H:i', strtotime($answer->date)); }}, por 
              @if (is_null($answer->author))
                Anónimo
              @else
                <a href="{{ url('users/'.$answer->author->id) }}" class="app-link">{{ $answer->author->username }}</a>
              @endif
            </span>
          </h6>
          <p class="card-text">
            @php
              $str = str_replace("<p>", "", $answer->text);
              $str = str_replace("</p>", " ", $str);
              $str = str_replace("&nbsp;", "", $str);
            @endphp
            {!! substr($str, 0, 70) !!}
            @if (strlen($str) > 70)
            ...
            @endif
          </p>
          
        </div>
      </div>
    </div>
  </div>
</div>