<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card user-card h-100" data-id="{{ $user->id }}">
    <div class="card-body">

      <div class="row">
        <div class="col-3">
          <a href="{{ url('/users/'.$user->id) }}" class="link-dark text-decoration-none" aria-expanded="false">
            @if ( Auth::check() && !is_null($user->photo) && file_exists( public_path('images/users/'.$user->photo) ) )
            <img src="{{ asset('images/users/'.$user->photo) }}" alt="profile-photo" class="w-100">
            @else
            <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo" class="w-100">
            @endif
          </a>
        </div>
        <div class="col-9">
          <h5 class="card-title"><a href="{{ url('/users/'.$user->id) }}" class="app-link">{{ $user->name }}</a></h5>
          <h6 class="card-subtitle mt-1 mb-2"><span class="badge {{ $user->isTeacher()?'bg-warning':'bg-info' }} text-dark">{{ $user->type }}</span></h6>
          <p class="card-text">
            Pontuação: <b>{{ $user->score }}</b><br>
            @auth
            Email: <b>{{ $user->email }}</b>
            @endauth
          </p>
        </div>
      </div>
     
    </div>
  </div>
</div>
