<header>
  <h1><a href="{{ url('/cards') }}">Thingy!</a></h1>
  @if (Auth::check())
  <a class="button" href="{{ url('/logout') }}"> Logout </a> <span>{{ Auth::user()->name }}</span>
  @endif
</header>

@yield('header')