@extends('layouts.app')

@section('title', 'Notificações')

@section('content')

<section id="notifications-page">

  <div class="row">
    <div class="col-12">
      <h2>
        Notificações
        @include('partials.help', ['placement' => 'right', 'content' => 'As notificações são lidas quando se visita a intervenção a que diz respeito ou através do botao para a marcar como lida. Existe sempre a possibilidade de reverter esta operação marcando-a como não lida novamente.'])
      </h2>
    </div>
  </div>

  @php
    $nNotifications = count($notifications);
  @endphp
  @if ( $nNotifications != 0 )
  <div class="row"> 
    @each('partials.notification', $notifications, 'notification')
  </div>

  @else 
  <p>Não existem Notificações</p>
  @endif
  
</section>

@endsection