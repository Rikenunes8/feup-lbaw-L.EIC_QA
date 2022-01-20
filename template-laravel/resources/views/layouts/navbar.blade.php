<nav id="sidebar" class="navbar shadow-none">
  <div class="container-fluid">
    <ul class="navbar-nav w-100">
      <li class="nav-item">
        <a class="nav-link text-uppercase" href="{{ url('/home') }}">Início</a>
      </li>
      
      <li class="navbar-text text-uppercase pt-3">Público</li>
      <li>
        <ul class="text-decoration-none list-unstyled ps-3">
          <li><a class="nav-link pt-0" href="{{ url('/questions') }}"><i class="fas fa-question"></i> Questões</a></li>
          <li><a class="nav-link pt-0" href="{{ url('/ucs') }}"><i class="fas fa-book"></i> Unidades Curriculares</a></li>
          <li><a class="nav-link pt-0" href="{{ url('/users') }}"><i class="fas fa-user-circle"></i> Utilizadores</a></li>
          <li><a class="nav-link pt-0" href="#" onclick="focusSearchInput()"><i class="fas fa-search"></i> Pesquisar</a></li>
        </ul>
      </li>
          
      @if (Auth::check() && Auth::user()->isAdmin()) 
        <li class="navbar-text text-uppercase pt-3">Administração</li>
        <li>
          <ul class="text-decoration-none list-unstyled ps-3">
            <li><a class="nav-link pt-0" href="{{ url('/admin/ucs') }}"><i class="fas fa-tags"></i> Gerir UCs</a></li>  
            <li><a class="nav-link pt-0" href="{{ url('/admin/users') }}"><i class="fas fa-user-cog"></i> Gerir Contas</a></li>
            <li><a class="nav-link pt-0" href="{{ url('/admin/requests') }}"><i class="fas fa-user-check"></i> Pedidos Ativação</a></li>
            <li><a class="nav-link pt-0" href="{{ url('/admin/reports') }}"><i class="fas fa-exclamation-triangle"></i> Reportes</a></li>
          </ul>
        </li>
      @endif

      <li><hr></li>
      
      <li class="nav-item">
        <a class="nav-link text-uppercase" href="{{ url('/about') }}">Sobre</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase" href="{{ url('/faq') }}">FAQ</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-uppercase" href="{{ url('/contact') }}">Contactos</a>
      </li>

    </ul>
  </div>
</nav>

@yield('navbar')