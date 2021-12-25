
<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card h-100" data-id="{{ $uc->id }}">
    <div class="card-body">
      <h5 class="card-title"><a href="{{ url('/ucs/'.$uc->id) }}">{{ $uc->name }}</a></h5>
      <h6 class="card-subtitle mt-1 mb-2"><span class="badge bg-info text-dark">{{ $uc->code }}</span></h6>
      <p class="card-text">
        {{ substr($uc->description, 0, 100) }}
        @if (strlen($uc->description) > 100)
        ...
        @endif
      </p>
      
      <p class="card-text">
        <small class="text-muted">Docentes: </small>
      </p>
      <!-- TODO listar os primeiros 3 docentes docentes -->
      <?php //echo $uc->teachers()->orderBy('code')->get() ?>

      @if (Auth::check()) <!-- TODO e nao admin-->
      <p class="text-end mb-0">
        <!-- TODO se ainda ja segue, este user esta nos followers icon: fas fa-heart, senao far fa-heart -->
        <!-- TODO seguir -->
        <a href="#" class="card-link"><i class="far fa-heart text-danger"></i></a>
      </p>
      @endif
    </div>
  </div>
</div>
