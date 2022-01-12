<section class="col-12 mb-2">
  @php 
    $read = Auth::user()->notifications()->where('id_notification', $notification->id)->first()->pivot->read;
  @endphp
  <div class="card notification-card {{ $read? 'notification-read':'notification-unread'}} h-100" data-id="{{ $notification->id }}">
    <div class="card-body p-2">
      @php 
        if ($notification->type == 'account_status') {
          $type = 'Novo Estado de Conta';
        } 
        else {
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
        @php
          \Carbon\Carbon::setLocale('pt');
        @endphp
        <span class="text-muted">{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i', date('d/m/Y H:i', strtotime($notification->date)))->diffForHumans(); }}</span>
        </h5>
      </a>
      <p class="card-text">
        @include('partials.notifications.'.$notification->type)
      </p>

      <div class="text-center notifications-page-actions p-3">
        @if ($read)
          <a class="btn btn-outline-dark bg-white text-black notifications-page-envelope me-1"><i class="far fa-envelope"></i></a>
        @else
          <a class="btn btn-outline-dark bg-white text-black notifications-page-envelope me-1"><i class="far fa-envelope-open"></i></a>
        @endif
        <a class="btn btn-outline-dark bg-white text-black notifications-page-remove me-1"><i class="far fa-trash-alt"></i></a>
      </div>

      <form method="POST" id="redirect-form-{{ $notification->id }}" class="d-none" 
            action="{{ route('notifications.read', $notification->id)}}">
        @csrf
      </form>
    </div>
  </div>
</section>
