<div class="col-md-6 col-lg-4 mb-2 px-1 w-100">
  <div class="card question-card h-100" data-id="{{ $question->id }}">
    <div class="card-body">
      <div class="question-votes align-top h-100 d-inline-block ">
        <span class="badge bg-primary h-100 align-middle">{{ $question->votes }}</span>
      </div>
      <div class="h-100 d-inline-block">
        <h5 class="card-title me-4"><a href="{{ url('/question/'.$question->id) }}">{{ $question->title }}</a></h5>
        <h6 class="card-subtitle mt-1 mb-2">
          <span class="badge bg-info text-dark">{{ $question->uc->code }}</span>
          <span class="">{{ date('d/m/Y H:i', strtotime($question->date)); }}</span>
        </h6>
        <p class="card-text">
          {{ substr($question->text, 0, 100) }}
          @if (strlen($question->text) > 100)
          ...
          @endif
        </p>
      </div>
    </div>
  </div>
</div>