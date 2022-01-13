<header class="py-2 bg-light border-bottom sticky-top">
  <div id="header-container" class="container-fluid d-grid gap-0 align-items-center">
  
    <a href="{{ url('/home') }}" id="header-logo-column" class="d-block text-decoration-none">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" id="logo-img" class="w-auto">
    </a>
    
    <div id="header-options-column" class="d-flex align-items-center">
      <form class="w-100 me-3" action="/search">
        <div class="input-group mb-0">
          <span class="input-group-text bg-white">
            @include('partials.help', ['placement' => 'bottom', 'content' => 'Pesquise questões por palavras chave que estejam contidas no título e/ou no corpo da questão, ou por expressões exatas colocando estas entre aspas ("). Os resultados são mostrados sempre por ordem de importância, dando prioridade à existência das expressões exatas, caso existam, seguida da identificação das palavras chave no título e no corpo.'])
          </span>

          <input type="search" id="search-input" class="form-control" placeholder="Pesquisa..." aria-label="Search" name="q">
        </div>
      </form>

      @if (Auth::check())
      <a href="{{ url('/notifications') }}" id="header-notification-icon" class="d-flex me-2 link-dark text-decoration-none fs-4 far fa-bell position-relative" data-toogle="tooltip" title="As suas Notificações">
        @php
          $nots = count(Auth::user()->notifications()->wherePivot('read', false)->get());
        @endphp
        @if ( $nots != 0 )
        <span class="position-absolute top-0 translate-middle badge rounded-pill bg-danger">
          {{ $nots>99? '+99':$nots }}
        </span>
        @endif
      </a>
            
      <div class="flex-shrink-0 dropdown">
        <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdown-user" data-bs-toggle="dropdown" aria-expanded="false">
          @if ( !is_null(Auth::user()->photo) && file_exists( 'images/users/'.Auth::user()->photo) )
          <img src="{{ asset('images/users/'.Auth::user()->photo) }}" alt="profile-photo" id="profile-photo" class="rounded-circle w-32">
          @else
          <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo" id="profile-photo" class="rounded-circle w-32">
          @endif
        </a>
        <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdown-user">
          <li><a class="dropdown-item" href="{{ url('/users/' . Auth::user()->id) }}">O meu Perfil</a></li>
          <li><a class="dropdown-item border-top" href="{{ url('/logout') }}">Terminar sessão</a></li>
        </ul>
      </div>
      @else
      <a class="btn btn-outline-primary me-2" href="{{ url('/register') }}"> Registar </a>
      <a class="btn btn-primary" href="{{ url('/login') }}"> Entrar </a>
      @endif

    </div>
  </div>
</header>

@yield('header')