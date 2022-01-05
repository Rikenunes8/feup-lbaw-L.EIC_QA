<div class="col-12 mb-2">
  <div class="card question-card h-100" data-id="{{ $notification->id }}">
    <div class="card-body">
      
      <div class="row">
        <div class="col-1">
          @if ( !Auth::user()->notifications()->where('id_notification', $notification->id)->first()->pivot->read )
          <span class="question-card-score text-dark w-100 h-100">unread</span>
          @endif
        </div>
        <div class="col-11">
          <a href="{{ url('/notifications/'.$notification->id) }}" class="app-link">
            <h5 class="card-title me-4">New {{ $notification->type }}</h5>
            <p class="card-text">
              @include('partials.notifications.'.$notification->type)
            </p>
          </a>
        </div>
      </div>

    </div>
  </div>
</div>
