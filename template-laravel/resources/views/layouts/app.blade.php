<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LEIC Q&A') }} - @yield('title')</title>

    <!-- Styles -->
    <!-- Bootstrap -->
    <link 
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" 
      rel="stylesheet" 
      integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" 
      crossorigin="anonymous"
    />
    <!-- Font Awesome -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css"
      rel="stylesheet"
    />
    <!-- Google Fonts -->
    <link
      href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap"
      rel="stylesheet"
    />
    <!-- MDB -->
    <link
      href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/3.10.1/mdb.min.css"
      rel="stylesheet"
    />
    <!-- DataTable -->
    <link
      href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css"
      rel="stylesheet"
    />
    <!-- Select2 -->
    <link 
      href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" 
      rel="stylesheet" 
    />
    
    <!-- App CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">


    <!-- JQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- DataTable -->
    <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <!-- Text Editor -->
    <script src="https://cdn.tiny.cloud/1/l963coctjgncao7e2p2fv27rcx394sxc74mhd0srm6vgldn3/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}" defer></script>
  </head>
  <body>
    <main>
      @include('layouts.header')
      
      <div class="container-fluid px-0">
        <div id="navbar-content-container" class="d-grid gap-0">
          <div class="border-end" id="navbar-column">
            @include('layouts.navbar')
          </div>
          <div class="container py-3 px-4" id="content-column">
            @include('layouts.breadcrumbs')
            @yield('content')
          </div>
        </div>
      </div>
      
      @include('layouts.footer')
      @include('layouts.scripts')
    </main>
  </body>
</html>
