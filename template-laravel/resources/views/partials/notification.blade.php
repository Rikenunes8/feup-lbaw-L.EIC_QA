<section class="col-12 mb-2">
  @php 
    $read = Auth::user()->notifications()->where('id_notification', $notification->id)->first()->pivot->read;
  @endphp
  <div class="card notification-card {{ $read? 'notification-read':'notification-unread'}} h-100" data-id="{{ $notification->id }}">
    <div class="card-body">
      @php 
        if (!is_null($notification->intervention)) {
          $intervention = $notification->intervention;
          if ($intervention->isAnswer()) $intervention = $intervention->parent;
          else if ($intervention->isComment()) $intervention = $intervention->parent->parent;
        }
        if ($notification->type == 'account_status') {
          $type = 'Novo Estado de Conta';
          $link = url('/users/'.Auth::user()->id);
        } 
        else {
          $link = url('/questions/'.$intervention->id);          
          if ($notification->type == 'question') {
            $type = 'Nova Questão';
          } else if ($notification->type == 'answer') {
            $type = 'Nova Resposta';
          } else if ($notification->type == 'comment') {
            $type = 'Novo Comentário';
          } else if ($notification->type == 'validation') {
            $type = 'Nova Validação';
          } else if ($notification->type == 'report') {
            $type = 'Nova Denúncia';
          }
        }
      @endphp
      <a href="#" class="app-link" onclick="document.getElementById('redirect-form-{{ $notification->id }}').submit();">
        <h5 class="card-title me-4"> {{ $type }}
        <span class="text-muted">{{ date('d/m/Y H:i', strtotime($notification->date)); }}</span>
        </h5>
      </a>
      <p class="card-text">
        @include('partials.notifications.'.$notification->type)
      </p>

      <div class="text-center notifications-page-actions p-3">
        @if ($read)
          <a href="#" class="btn btn-outline-dark  text-black notifications-page-envelope me-1"><i class="far fa-envelope"></i></a>
        @else
          <a href="#" class="btn btn-outline-dark  text-black notifications-page-envelope me-1"><i class="far fa-envelope-open"></i></a>
        @endif
        <a href="#" class="btn btn-outline-dark text-black notifications-page-remove me-1"><i class="far fa-trash-alt"></i></a>
      </div>

      <form method="POST" id="redirect-form-{{ $notification->id }}" class="d-none" 
            action="{{ route('notifications.read', $notification->id)}}">
        @csrf
        <input type="text" value="{{ $link }}" name="link">
      </form>
    </div>
  </div>
</section>
