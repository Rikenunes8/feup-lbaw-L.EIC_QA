<div class="col-md-6 col-lg-4 mb-2 px-1">
  <div class="card user-card h-100" data-id="{{ $user->id }}">
    <div class="card-body">
      <a href="#" class="link-dark text-decoration-none users-profile-photo" aria-expanded="false">
        @if ( file_exists( asset('images/users/'.Auth::user()->photo) && Auth::check()) )
        <img src="{{ asset('images/users/'.Auth::user()->photo.'.jpg') }}" alt="profile-photo" id="profile-photo" class="rounded-circle w-32">
        @else
        <img src="{{ asset('images/users/default.jpg') }}" alt="profile-photo" id="profile-photo" class="rounded-circle w-32">
        @endif
      </a>
      <h5 class="card-title me-4"><a href="{{ url('/users/'.$user->id) }}">{{ $user->name }}</a></h5>
      <h6 class="card-subtitle mt-1 mb-2"><span class="badge bg-info text-dark">{{ $user->type }}</span>&nbsp;&nbsp;<span class="badge bg-info text-dark">Score: {{ $user->score }}</span></h6>
      <p class="card-text">
        {{ substr($user->about, 0, 100) }}
        @if (strlen($user->about) > 100)
        ...
        @endif
      </p>

    </div>
  </div>
</div>
