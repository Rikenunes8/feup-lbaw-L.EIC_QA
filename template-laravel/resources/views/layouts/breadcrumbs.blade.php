
@if ($_SERVER['REQUEST_URI'] != '/login')
<div class="row">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="#">Home</a></li>
      <li class="breadcrumb-item active" aria-current="page">Users</li>
    </ol>
  </nav>
</div>
@endif

<!-- TODO -->

@yield('breadcrumbs')