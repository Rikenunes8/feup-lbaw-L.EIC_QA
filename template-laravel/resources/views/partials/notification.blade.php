<section class="col-12 mb-2">
  @php 
    $read = Auth::user()->notifications()->where('id_notification', $notification->id)->first()->pivot->read;
  @endphp
  <div class="card notification-card {{ $read? 'notification-read':'notification-unread'}} h-100" data-id="{{ $notification->id }}">
    <div class="card-body py-2">
      @php 
        if ($notification->isAccount_status()) {
          $type = 'Novo Estado de Conta';
        } 
        else {
          if ($notification->isQuestion()) {
            $type = 'Nova Questão';
          } else if ($notification->isAnswer()) {
            $type = 'Nova Resposta';
          } else if ($notification->isComment()) {
            $type = 'Novo Comentário';
          } else if ($notification->isValidation()) {
            $type = 'Nova Validação';
          } else if ($notification->isReport()) {
            $type = 'Nova Denúncia';
          }
        }
      @endphp
      <a href="{{ url('notifications/'.$notification->id.'/read') }}" class="app-link">
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
          <a class="btn btn-outline-dark bg-white text-black notifications-page-envelope me-1" data-toogle="tooltip" title="Marcar como Não Lida"><i class="far fa-envelope"></i></a>
        @else
          <a class="btn btn-outline-dark bg-white text-black notifications-page-envelope me-1" data-toogle="tooltip" title="Marcar como Lida"><i class="far fa-envelope-open"></i></a>
        @endif
        <a class="btn btn-outline-dark bg-white text-black notifications-page-remove me-1" data-toogle="tooltip" title="Eliminar"><i class="far fa-trash-alt"></i></a>
      </div>
    </div>
  </div>
</section>
