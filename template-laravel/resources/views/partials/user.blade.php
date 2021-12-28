<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card user-card h-100" data-id="{{ $user->id }}">
    <div class="card-body">

      <div class="row">
        <div class="col-3">
          <a href="{{ url('/users/'.$user->id) }}" class="link-dark text-decoration-none" aria-expanded="false">
            @if ( file_exists( asset('images/users/'.Auth::user()->photo) && Auth::check()) )
            <img src="{{ asset('images/users/'.Auth::user()->photo.'.jpg') }}" alt="profile-photo" class="w-100">
            @else
            <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo" class="w-100">
            @endif
          </a>
        </div>
        <div class="col-9">
          <h5 class="card-title"><a href="{{ url('/users/'.$user->id) }}" class="app-link">{{ $user->name }}</a></h5>
          <h6 class="card-subtitle mt-1 mb-2"><span class="badge {{ $user->isTeacher()?'bg-warning':'bg-info' }} text-dark">{{ $user->type }}</span></h6>
          <p class="card-text">
            Score: <b>{{ $user->score }}</b><br>
            Email: <b>{{ $user->email }}</b>
          </p>
        </div>
      </div>
     
    </div>
  </div>
</div>
