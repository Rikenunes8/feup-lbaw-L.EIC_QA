@extends('layouts.app')

@section('title', 'Notificações')

@section('content')

<section id="notifications-page">

  <div class="row">
    <div class="col-12">
      <h2>Notificações</h2>
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