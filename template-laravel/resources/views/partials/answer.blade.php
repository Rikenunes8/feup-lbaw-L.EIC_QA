<div class="col-12 mb-2 px-1">
  <div class="card question-card h-100" data-id="{{ $answer->id }}">
    <div class="card-body">
      
      <div class="row">
        <div class="col-1">
          <span class="question-card-score text-dark w-100 h-100">{{ $answer->votes }}<br>votos</span>
        </div>

        <div class="col-11 p-relative">
          <h5 class="card-title me-4"><a href="{{ url('/question/'.$answer->parent->id) }}" class="app-link">{{ $answer->parent->title }}</a></h5>
          <h6 class="card-subtitle mt-1 mb-2">
            <span class="badge bg-info text-dark">{{ $answer->parent->uc->code }}</span>
            <span class="text-muted">{{ date('d/m/Y H:i', strtotime($answer->parent->date)); }}, por {{ $answer->parent->author->username }}</span>
          </h6>
          <h6 class="card-subtitle mt-3 mb-2">
            <b>Resposta:</b><br>
            <span class="text-muted">{{ date('d/m/Y H:i', strtotime($answer->date)); }}, por {{ $answer->author->username }}</span>
          </h6>
          <p class="card-text">
            {{ substr($answer->text, 0, 70) }}
            @if (strlen($answer->text) > 70)
            ...
            @endif
          </p>
          
          @php
            $valid = null;
            foreach ($answer->valid as $validation) {
              if ($validation->pivot->valid) $valid = true;
              else $valid = false;
            }
          @endphp
          @if ($valid == true)
          <p class="question-card-icon p-4">
            <i class="fas fa-check question-valid-icon"></i>
          </p>
          @elseif ($valid == false)
          <p class="question-card-icon p-4">
            <i class="fas fa-times question-invalid-icon"></i>
          </p>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>