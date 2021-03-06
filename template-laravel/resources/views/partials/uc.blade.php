<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card uc-card h-100" data-id="{{ $uc->id }}">
    <div class="card-body">
      <h5 class="card-title me-4"><a href="{{ url('/ucs/'.$uc->id) }}" class="app-link">{{ $uc->name }}</a></h5>
      <h6 class="card-subtitle mt-1 mb-2"><span class="badge bg-info text-dark">{{ $uc->code }}</span></h6>
      <p class="card-text">
        @php
          $str = str_replace("<p>", "", $uc->description);
          $str = str_replace("</p>", " ", $str);
          $str = str_replace("&nbsp;", "", $str);
        @endphp
        {!! substr($str, 0, 100) !!}
        @if (strlen($str) > 100)
        ...
        @endif
      </p>
      
      <p class="card-text">
        <small class="text-muted">Docentes: </small>
        @php
          $teachers = $uc->teachers()->orderBy('name')->take(5)->get();
        @endphp
        @foreach($teachers as $teacher)
          <small class="text-muted"> {{ $teacher->name }}; </small>
        @endforeach
      </p>
     
      @if ( Auth::check() && Auth::user()->isStudent() )
        @php
          $showFollow = true;
         
          $urlParts = explode('/', $_SERVER['REQUEST_URI']);
          if ($urlParts[1] == 'users' && strtok($urlParts[2], '?') != Auth::user()->id)
            $showFollow = false;
        @endphp 
        @if ($showFollow)
          <p class="uc-card-icon p-4">
            
            <a class="card-link uc-card-icon-follow">
              <i class="{{ in_array(Auth::user()->email, $uc->followers()->pluck('email', 'id')->toArray() )?'fas':'far' }} fa-heart text-danger"></i>
            </a>
            
          </p>
        @endif
      @endif
    </div>
  </div>
</div>
