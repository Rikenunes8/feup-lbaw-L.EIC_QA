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
          @php 
          if ($notification->type == 'question') {
            $type = 'Nova Questão';
            $link = url('/questions/'.$notification->intervention->id);
          } else if ($notification->type == 'answer') {
            $type = 'Nova Resposta';
            $link = url('/questions/'.$notification->intervention->parent->id);
          } else if ($notification->type == 'comment') {
            $type = 'Novo Comentário';
            $link = url('/questions/'.$notification->intervention->parent->parent->id);
          } else if ($notification->type == 'validation') {
            $type = 'Nova Validação';
            $link = url('/questions/'.$notification->intervention->parent->id);
          } else if ($notification->type == 'report') {
            $type = 'Nova Denúncia';
            $link = url('/home');
          } else {
            $type = 'Novo Estado de Conta';
            $link = url('/users/'.Auth::user()->id);
          }
          @endphp
          <a href="#" class="app-link" onclick="event.preventDefault();
                                                document.getElementById('redirect-form-{{ $notification->id }}').submit();">
            <h5 class="card-title me-4"> {{ $type }}</h5>
            <h6>{{ date('d/m/Y H:i', strtotime($notification->date)); }}</h6>
          </a>
          <p class="card-text">
            @include('partials.notifications.'.$notification->type)
          </p>
          <form method="POST" id="redirect-form-{{ $notification->id }}" class="d-none" 
                action="{{ route('notifications.read', $notification->id)}}">
            @csrf
            <input type="text" value="{{ $link }}" name="link">
          </form>
        </div>
      </div>

    </div>
  </div>
</div>
