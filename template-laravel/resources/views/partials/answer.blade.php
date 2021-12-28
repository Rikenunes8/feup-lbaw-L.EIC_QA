<div class="col-md-6 col-lg-4 mb-2 px-1 w-100">
  <div class="card answer-card h-100" data-id="{{ $answer->id }}">
    <div class="card-body">
      <div class="answer-votes align-top h-100 d-inline-block ">
        <span class="badge bg-primary h-100 align-middle">{{ $answer->votes }}</span>
      </div>
      <div class="h-100 d-inline-block">
        <h5 class="card-title me-4"><a href="{{ url('/question/'.$answer->id) }}">{{ $answer->parent->title }}</a></h5>
        <h6 class="card-subtitle mt-1 mb-2">
          <span class="badge bg-info text-dark">{{ $answer->parent->uc->code }}</span>
          <span class="">{{ date('d/m/Y H:i', strtotime($answer->date)); }}</span>
        </h6>
        <p class="card-text">
          <b>Resposta: </b>
          {{ substr($answer->text, 0, 100) }}
          @if (strlen($answer->text) > 100)
          ...
          @endif
        </p>
      </div>
    </div>
  </div>
</div>