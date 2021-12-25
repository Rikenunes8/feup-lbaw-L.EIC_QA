<header class="py-2 bg-light border-bottom sticky-top">
  <div id="header-container" class="container-fluid d-grid gap-0 align-items-center">
  
    <a href="{{ url('/home') }}" class="d-block text-decoration-none">
      <img src="{{ asset('images/logo.png') }}" alt="Logo" id="logo-img" class="w-auto">
    </a>
    
    <div class="d-flex align-items-center">
      <form class="w-100 me-3">
        <input type="search" class="form-control" placeholder="Pesquisa..." aria-label="Search">
      </form>

      @if (Auth::check())
      <a href="{{ url('/users/' . Auth::user()->id . '/notifications') }}" class="d-flex me-2 link-dark text-decoration-none fs-4 far fa-envelope"></a>
            
      <div class="flex-shrink-0 dropdown">
        <a href="#" class="d-block link-dark text-decoration-none dropdown-toggle" id="dropdown-user" data-bs-toggle="dropdown" aria-expanded="false">
          <!-- TODO o src da image vai corresponder á localizacao da Auth::user()->photo -->
          @if ( file_exists( asset('images/users/'.Auth::user()->id) ) )
          <img src="{{ asset('images/users/'.Auth::user()->id.'.jpg') }}" alt="profile-photo" id="profile-photo" class="rounded-circle w-32">
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