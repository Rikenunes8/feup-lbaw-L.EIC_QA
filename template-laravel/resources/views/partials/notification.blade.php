<section class="col-12 mb-2">
  @php 
    $read = Auth::user()->notifications()->where('id_notification', $notification->id)->first()->pivot->read;
  @endphp
  <div class="card notification-card {{ $read? 'notification-read':'notification-unread'}} h-100" data-id="{{ $notification->id }}">
    <div class="card-body">
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
