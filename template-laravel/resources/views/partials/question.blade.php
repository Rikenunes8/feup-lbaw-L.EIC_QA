<div class="col-12 mb-2">
  <div class="card question-card h-100" data-id="{{ $question->id }}">
    <div class="card-body">
      
      <div class="row">
        <div class="col-1">
          <span class="question-card-score text-dark w-100 h-100">
            {{ $question->votes }}<br>votos

            @php
            $valid = false;
            foreach ($question->childs as $answer) {
              $validations = DB::table('validation')->where('id_answer', $answer->id)->get();
              foreach ($validations as $validation) {
                if ($validation->valid) $valid = true;
              }
            }
            @endphp
            @if ($valid)
            <br>
            <i class="fas fa-check question-valid-icon"></i>
            @endif
          </span>
        </div>

        <div class="col-11 p-relative">
          <h5 class="card-title"><a href="{{ url('/questions/'.$question->id) }}" class="app-link">{{ $question->title }}</a></h5>
          <h6 class="card-subtitle mt-1 mb-2">
            <a href="{{ url('ucs/'.$question->uc->id) }}" class="badge bg-info text-dark">{{ $question->uc->code }}</a>
            <span class="text-muted">{{ date('d/m/Y H:i', strtotime($question->date)); }}, por 
              @if (is_null($question->author))
                Anónimo
              @else
                <a href="{{ url('users/'.$question->author->id) }}" class="app-link">{{ $question->author->username }}</a>
              @endif
            </span>
          </h6>
          <p class="card-text">
            @php
              $str = str_replace("<p>", "", $question->text);
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
