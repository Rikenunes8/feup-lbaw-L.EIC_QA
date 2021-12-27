<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card question-card h-100" data-id="{{ $question->id }}">
    <div class="card-body">
      <h5 class="card-title me-4"><a href="{{ url('/question/'.$question->id) }}">{{ $question->title }}</a></h5>
      <h6 class="card-subtitle mt-1 mb-2"><span class="badge bg-info text-dark">{{ $question->uc->code }}</span></h6>
      <p class="card-text">
        {{ substr($question->text, 0, 100) }}
        @if (strlen($question->text) > 100)
        ...
        @endif
      </p>
    </div>
  </div>
</div>